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
        $payload = file_get_contents('php://input');
        $data = json_decode($payload, true);

        if (!$data) {
            header('HTTP/1.1 400 Bad Request');
            die('Invalid JSON');
        }

        // Log the payload for debugging
        $this->db->insert(db_prefix() . 'sepay_logs', [
            'content' => $payload,
            'invoice_id' => 0,
        ]);

        $order_invoice_number = $data['order']['order_invoice_number'];
        // Extract invoice ID from order_invoice_number (assuming format PREFIX-NUMBER)
        // Perfex invoice number might contain prefix, need to handle this carefully.
        // Best strategy: Search invoice by number and prefix logic or custom field if possible.
        // For simplicity, we query by full number string if possible, or we need exact match logic.

        // Actually, in process_payment we sent: $data['invoice']->prefix . $data['invoice']->number
        // So we should search for invoice where concat(prefix, number) = order_invoice_number
        // Or better, just loop invoices? No, performant query needed.

        // Try to match exact invoice
        $this->db->where('concat(prefix, number) =', $order_invoice_number);
        $invoice = $this->db->get(db_prefix() . 'invoices')->row();

        if ($invoice) {
            // Check payment status
            if ($data['transaction']['transaction_status'] == 'APPROVED' || $data['transaction_status'] == 'APPROVED') { // Check both structures just in case
                $payment_data = [
                    'amount' => $data['transaction']['transaction_amount'] ?? $data['transaction_amount'],
                    'invoiceid' => $invoice->id,
                    'paymentmode' => 'sepay',
                    'transactionid' => $data['transaction']['transaction_id'] ?? $data['transaction_id'],
                    'note' => 'Payment via SePay. Transaction ID: ' . ($data['transaction']['transaction_id'] ?? $data['transaction_id']),
                ];

                $this->sepay_gateway->addPayment($payment_data);

                // Update log with invoice id
                $this->db->where('id', $this->db->insert_id());
                $this->db->update(db_prefix() . 'sepay_logs', ['invoice_id' => $invoice->id]);
            }
        }

        echo json_encode(['success' => true]);
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
