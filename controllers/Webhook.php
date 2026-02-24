<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Webhook controller - Handle SePay bank transaction webhooks
 * URL: /sepay/webhook
 * 
 * This handles the bank transaction notification from SePay
 * and processes it as a payment IPN
 */
class Webhook extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('sepay/sepay_gateway');
    }

    public function index()
    {
        // Log that webhook endpoint was called
        log_activity('SePay Webhook: Endpoint called at ' . date('Y-m-d H:i:s'));
        
        // Get raw payload
        $payload = file_get_contents('php://input');
        
        // Log raw payload for debugging
        log_activity('SePay Webhook: Raw payload received - ' . substr($payload, 0, 200));
        
        $data = json_decode($payload, true);

        // Validate JSON
        if (!$data) {
            log_activity('SePay Webhook Error: Invalid JSON payload - ' . $payload);
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
            return;
        }

        // Log the payload for debugging
        try {
            $this->db->insert(db_prefix() . 'sepay_logs', [
                'content' => $payload,
                'invoice_id' => 0,
            ]);
            $log_id = $this->db->insert_id();
        } catch (Exception $e) {
            log_activity('SePay Webhook Warning: Could not save to sepay_logs - ' . $e->getMessage());
            $log_id = 0;
        }

        // Extract transaction code from bank webhook
        // SePay gửi: "code":"PAY1324699C198D604F0"
        $transaction_code = $data['code'] ?? $data['content'] ?? null;
        
        if (!$transaction_code) {
            log_activity('SePay Webhook Error: Missing transaction code');
            echo json_encode(['success' => false, 'message' => 'Missing transaction code']);
            return;
        }

        // Extract order_invoice_number from transaction code
        // Code format: PAY{order_id}{random} 
        // Need to match with invoice based on content/code
        
        // Find invoice by matching the transaction code in description or other fields
        // For now, try to find by matching prefix in the code
        
        // Alternative: Match by amount and recent invoices
        $transfer_amount = $data['transferAmount'] ?? 0;
        
        if ($transfer_amount == 0) {
            log_activity('SePay Webhook Error: Invalid transfer amount');
            echo json_encode(['success' => false, 'message' => 'Invalid amount']);
            return;
        }

        // Try to find invoice by amount (this is not ideal, but works for testing)
        // Better: Parse the transaction code to get invoice number
        
        // Check if content contains invoice number pattern (INV-XXXXXX)
        preg_match('/INV-\d+/', $transaction_code, $matches);
        
        if (empty($matches)) {
            // Try to extract from content field
            $content = $data['content'] ?? '';
            preg_match('/INV-\d+/', $content, $matches);
        }
        
        if (empty($matches)) {
            log_activity('SePay Webhook: Could not extract invoice number from transaction code - ' . $transaction_code . ' - Trying amount matching');
            
            // Fallback: Find by amount
            $this->db->where('total', $transfer_amount);
            $this->db->where('status !=', 2); // Not paid
            $this->db->order_by('id', 'DESC');
            $invoice = $this->db->get(db_prefix() . 'invoices')->row();
            
            if (!$invoice) {
                log_activity('SePay Webhook Error: Invoice not found for amount ' . $transfer_amount . ' - Available unpaid invoices: ' . $this->db->where('status !=', 2)->count_all_results(db_prefix() . 'invoices'));
                echo json_encode(['success' => false, 'message' => 'Invoice not found']);
                return;
            }
            
            log_activity('SePay Webhook: Found invoice by amount matching - Invoice ID: ' . $invoice->id . ' - Number: ' . $invoice->prefix . $invoice->number);
        } else {
            // Extract invoice number (just the number part, e.g., "INV-000008")
            $order_invoice_number = $matches[0];
            
            log_activity('SePay Webhook: Extracted invoice number pattern - ' . $order_invoice_number);
            
            // Parse to get prefix and number separately
            // Format: INV-8 → prefix="INV-", number="8" (can be "8" or "000008")
            // Also handle: INV-000008/02/2026 → extract just "INV-8"
            preg_match('/^([A-Z-]+)(\d+)/', $order_invoice_number, $parts);
            
            if (count($parts) >= 3) {
                $prefix = $parts[1];
                $number = $parts[2];
                
                log_activity('SePay Webhook: Searching invoice - Prefix: ' . $prefix . ', Number: ' . $number);
                
                // Find invoice by prefix and number (try multiple methods)
                // Method 1: Exact string match
                $this->db->where('prefix', $prefix);
                $this->db->where('number', $number);
                $invoice = $this->db->get(db_prefix() . 'invoices')->row();
                
                if (!$invoice) {
                    // Method 2: Try with leading zeros (6 digits)
                    $number_padded = str_pad($number, 6, '0', STR_PAD_LEFT);
                    $this->db->where('prefix', $prefix);
                    $this->db->where('number', $number_padded);
                    $invoice = $this->db->get(db_prefix() . 'invoices')->row();
                    
                    log_activity('SePay Webhook: Trying with padded number - ' . $number_padded . ' - Found: ' . ($invoice ? 'Yes' : 'No'));
                }
                
                if (!$invoice) {
                    // Method 3: Cast both to int
                    $this->db->where('prefix', $prefix);
                    $this->db->where('CAST(number AS UNSIGNED)', intval($number));
                    $invoice = $this->db->get(db_prefix() . 'invoices')->row();
                    
                    log_activity('SePay Webhook: Trying with int cast - Number: ' . intval($number) . ' - Found: ' . ($invoice ? 'Yes' : 'No'));
                }
            } else {
                // Fallback: Try exact match on CONCAT
                $this->db->where("CONCAT(prefix, number) = ", $order_invoice_number);
                $invoice = $this->db->get(db_prefix() . 'invoices')->row();
            }

            if (!$invoice) {
                log_activity('SePay Webhook: Invoice not found with pattern ' . $order_invoice_number . ' - Trying amount matching as fallback');
                
                // Fallback to amount matching
                $this->db->where('total', $transfer_amount);
                $this->db->where('status !=', 2); // Not paid
                $this->db->order_by('id', 'DESC');
                $invoice = $this->db->get(db_prefix() . 'invoices')->row();
                
                if (!$invoice) {
                    log_activity('SePay Webhook Error: Invoice not found - Pattern: ' . $order_invoice_number . ', Amount: ' . $transfer_amount);
                    echo json_encode(['success' => false, 'message' => 'Invoice not found']);
                    return;
                }
                
                log_activity('SePay Webhook: Found invoice by amount - Invoice ID: ' . $invoice->id);
            } else {
                log_activity('SePay Webhook: Found invoice by number - Invoice ID: ' . $invoice->id . ' - Number: ' . $invoice->prefix . $invoice->number);
            }
        }

        // Check for duplicate payment
        $this->db->where('invoiceid', $invoice->id);
        $this->db->where('transactionid', $transaction_code);
        $existing_payment = $this->db->get(db_prefix() . 'invoicepaymentrecords')->row();

        if ($existing_payment) {
            log_activity('SePay Webhook: Duplicate payment detected for Invoice ' . $invoice->id . ' - Transaction: ' . $transaction_code);
            echo json_encode(['success' => true, 'message' => 'Payment already recorded']);
            return;
        }

        // Format amount properly
        $amount = number_format($transfer_amount, 2, '.', '');

        // Add payment using gateway method (IMPORTANT!)
        $payment_data = [
            'amount' => $amount,
            'invoiceid' => $invoice->id,
            'transactionid' => $transaction_code,
        ];

        $success = $this->sepay_gateway->addPayment($payment_data);

        if ($success) {
            // Update log with invoice id
            if ($log_id > 0) {
                try {
                    $this->db->where('id', $log_id);
                    $this->db->update(db_prefix() . 'sepay_logs', ['invoice_id' => $invoice->id]);
                } catch (Exception $e) {
                    // Ignore DB update errors
                }
            }
            
            log_activity('SePay Webhook: Payment Added Successfully - Invoice: ' . $invoice->id . ' - Amount: ' . $amount . ' - Transaction: ' . $transaction_code);
            
            // Return HTTP 200 (important for SePay to consider webhook successful)
            header('HTTP/1.1 200 OK');
            echo json_encode(['success' => true, 'message' => 'Payment recorded']);
        } else {
            log_activity('SePay Webhook Error: Failed to add payment for Invoice ' . $invoice->id);
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['success' => false, 'message' => 'Failed to record payment']);
        }
    }
}
