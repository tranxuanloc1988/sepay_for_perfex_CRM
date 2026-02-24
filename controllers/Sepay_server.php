<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sepay_server extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('sepay/sepay_gateway');
    }

    public function ipn()
    {
        // Log that IPN endpoint was called
        log_activity('SePay IPN: Endpoint called at ' . date('Y-m-d H:i:s'));
        
        // Get raw payload
        $payload = file_get_contents('php://input');
        
        // Log raw payload for debugging
        log_activity('SePay IPN: Raw payload received - ' . substr($payload, 0, 200));
        
        $data = json_decode($payload, true);

        // Validate JSON
        if (!$data) {
            log_activity('SePay IPN Error: Invalid JSON payload - ' . $payload);
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
            return;
        }

        // Log the payload for debugging (try-catch to handle DB errors)
        try {
            $this->db->insert(db_prefix() . 'sepay_logs', [
                'content' => $payload,
                'invoice_id' => 0,
            ]);
            $log_id = $this->db->insert_id();
        } catch (Exception $e) {
            log_activity('SePay IPN Warning: Could not save to sepay_logs - ' . $e->getMessage());
            $log_id = 0;
        }

        // Extract invoice number
        $order_invoice_number = $data['order']['order_invoice_number'] ?? null;
        
        if (!$order_invoice_number) {
            log_activity('SePay IPN Error: Missing order_invoice_number');
            echo json_encode(['success' => false, 'message' => 'Missing invoice number']);
            return;
        }

        // Find invoice by matching prefix + number
        $this->db->where("CONCAT(prefix, number) = ", $order_invoice_number);
        $invoice = $this->db->get(db_prefix() . 'invoices')->row();

        if (!$invoice) {
            log_activity('SePay IPN Error: Invoice not found - ' . $order_invoice_number);
            echo json_encode(['success' => false, 'message' => 'Invoice not found']);
            return;
        }

        // Check transaction status
        $transaction_status = $data['transaction']['transaction_status'] ?? $data['transaction_status'] ?? null;
        
        if ($transaction_status !== 'APPROVED') {
            log_activity('SePay IPN: Payment not approved for Invoice ' . $invoice->id . ' - Status: ' . $transaction_status);
            echo json_encode(['success' => false, 'message' => 'Payment not approved']);
            return;
        }

        // Extract transaction details
        $transaction_id = $data['transaction']['transaction_id'] ?? $data['transaction_id'] ?? null;
        $transaction_amount = $data['transaction']['transaction_amount'] ?? $data['transaction_amount'] ?? 0;

        if (!$transaction_id) {
            log_activity('SePay IPN Error: Missing transaction_id for Invoice ' . $invoice->id);
            echo json_encode(['success' => false, 'message' => 'Missing transaction ID']);
            return;
        }

        // Check for duplicate payment
        $this->db->where('invoiceid', $invoice->id);
        $this->db->where('transactionid', $transaction_id);
        $existing_payment = $this->db->get(db_prefix() . 'invoicepaymentrecords')->row();

        if ($existing_payment) {
            log_activity('SePay IPN: Duplicate payment detected for Invoice ' . $invoice->id . ' - Transaction: ' . $transaction_id);
            echo json_encode(['success' => true, 'message' => 'Payment already recorded']);
            return;
        }

        // Format amount properly
        $amount = number_format($transaction_amount, 2, '.', '');

        // Add payment using gateway method (IMPORTANT!)
        $payment_data = [
            'amount' => $amount,
            'invoiceid' => $invoice->id,
            'transactionid' => $transaction_id,
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
            
            log_activity('SePay Payment Added Successfully - Invoice: ' . $invoice->id . ' - Amount: ' . $amount . ' - Transaction: ' . $transaction_id);
            
            // Return HTTP 200 (important for SePay to consider IPN successful)
            header('HTTP/1.1 200 OK');
            echo json_encode(['success' => true, 'message' => 'Payment recorded']);
        } else {
            log_activity('SePay IPN Error: Failed to add payment for Invoice ' . $invoice->id);
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['success' => false, 'message' => 'Failed to record payment']);
        }
    }

    public function success()
    {
        $invoice_id = $this->input->get('invoice_id');
        $hash = $this->input->get('hash');
        set_alert('success', _l('sepay_notice_payment_success'));
        redirect(site_url('invoice/' . $invoice_id . '/' . $hash));
    }

    public function error()
    {
        $invoice_id = $this->input->get('invoice_id');
        $hash = $this->input->get('hash');
        set_alert('danger', 'Payment failed');
        redirect(site_url('invoice/' . $invoice_id . '/' . $hash));
    }

    public function cancel()
    {
        $invoice_id = $this->input->get('invoice_id');
        $hash = $this->input->get('hash');
        set_alert('warning', 'Payment cancelled');
        redirect(site_url('invoice/' . $invoice_id . '/' . $hash));
    }
}
