<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sepay_gateway extends App_gateway
{
    public function __construct()
    {
        /**
         * Call App_gateway __construct function
         */
        parent::__construct();

        /**
         * Gateway unique id
         * The The id must be alphanumeric and to be entirely in lowercase
         */
        $this->setId('sepay');

        /**
         * Gateway name
         */
        $this->setName('Sepay');

        /**
         * Add gateway settings
         */
        $this->setSettings([
            [
                'name' => 'api_key',
                'encrypted' => true,
                'label' => 'sepay_api_key',
                'type' => 'input',
            ],
            [
                'name' => 'secret_key',
                'encrypted' => true,
                'label' => 'sepay_secret_key',
                'type' => 'input',
            ],
            [
                'name' => 'account_number',
                'label' => 'sepay_account_number',
                'type' => 'input',
            ],
            [
                'name' => 'description',
                'label' => 'settings_paymentmethod_description',
                'type' => 'textarea',
                'default_value' => 'Thanh toán qua SePay. <span style="color:red">' . _l('sepay_payment_instructions') . '</span>',
            ],
            [
                'name' => 'currencies',
                'label' => 'settings_paymentmethod_currencies',
                'default_value' => 'VND',
            ],
            [
                'name' => 'test_mode_enabled',
                'type' => 'yes_no',
                'default_value' => 1,
                'label' => 'settings_paymentmethod_testing_mode',
            ],
        ]);
    }

    /**
     * Get description
     * @return string
     */
    public function getSetting($name)
    {
        if ($name == 'description') {
            return 'Thanh toán qua SePay. <span style="color:red">' . _l('sepay_payment_instructions') . '</span>';
        }
        return parent::getSetting($name);
    }



    /**
     * Process the payment
     * @param  array $data
     * @return mixed
     */
    public function process_payment($data)
    {
        $this->ci->session->set_userdata(['pay_invoice_total' => $data['amount']]);

        $merchant_id = $this->decryptSetting('api_key');
        $secret_key = $this->decryptSetting('secret_key');

        if ($this->getSetting('test_mode_enabled') == '1') {
            $url = 'https://pay-sandbox.sepay.vn/v1/checkout/init';
        } else {
            $url = 'https://pay.sepay.vn/v1/checkout/init';
        }

        $post_data = [
            'merchant' => trim($merchant_id),
            'currency' => $data['invoice']->currency_name,
            'order_amount' => round($data['amount']),
            'operation' => 'PURCHASE',
            'order_description' => 'Payment for Invoice ' . $data['invoice']->number,
            'order_invoice_number' => $data['invoice']->prefix . $data['invoice']->number,
            'customer_id' => $data['invoice']->clientid,
            'success_url' => site_url('sepay/sepay_server/success?invoice_id=' . $data['invoiceid'] . '&hash=' . $data['invoice']->hash),
            'error_url' => site_url('sepay/sepay_server/error?invoice_id=' . $data['invoiceid'] . '&hash=' . $data['invoice']->hash),
            'cancel_url' => site_url('sepay/sepay_server/cancel?invoice_id=' . $data['invoiceid'] . '&hash=' . $data['invoice']->hash),
        ];

        $data['url'] = $url;
        $data['post_data'] = $post_data;
        $data['signature'] = $this->create_signature($post_data, trim($secret_key));

        $this->ci->load->view('sepay/redirect', $data);
    }

    public function create_signature($post_data, $secret_key)
    {
        $signed = [];
        $signedFields = array_values(array_filter(array_keys($post_data), fn($field) => in_array($field, [
            'merchant',
            'operation',
            'payment_method',
            'order_amount',
            'currency',
            'order_invoice_number',
            'order_description',
            'customer_id',
            'success_url',
            'error_url',
            'cancel_url'
        ])));

        foreach ($signedFields as $field) {
            if (!isset($post_data[$field]))
                continue;
            $signed[] = $field . '=' . ($post_data[$field] ?? '');
        }

        return base64_encode(hash_hmac('sha256', implode(',', $signed), $secret_key, true));
    }
}
