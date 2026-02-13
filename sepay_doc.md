# SePay Payment Gateway

## SePay is a unified payment platform, supporting diverse payment methods from bank transfers via QR code to international cards. The system helps individuals and businesses easily integrate, expand business operations and optimize payment costs.

---

**API Overview:**

SePay Payment Gateway API supports multiple payment methods including bank transfer via QR code, NAPAS QR, and international cards.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Authentication:** All APIs use Basic Authentication with `merchant_id` and `secret_key`.


---

### Why choose SePay?

* Quick integration with simple API
* Support for diverse payment methods
* Competitive service fees

<ButtonLink href="/en/thanh-toan-demo" variant="primary">Try demo payment</ButtonLink>

***

### Diverse payment methods

* Bank transfer QR code scanning
* NAPAS bank transfer QR code scanning
* International credit/debit cards Visa, Mastercard, JCB

***

### Easy integration

* Simple RESTful API
* [SDK for PHP](/sdk/php) and [JavaScript (Node.js)](/sdk/nodejs)
* Detailed and comprehensive documentation

# Payment Flow

## Explore two flexible payment options - one-time and recurring, helping businesses optimize experience and automate payment collection processes

---

**API Overview:**

SePay Payment Gateway API supports multiple payment methods including bank transfer via QR code, NAPAS QR, and international cards.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Authentication:** All APIs use Basic Authentication with `merchant_id` and `secret_key`.


---

### One-time Payment

The one-time payment flow allows customers to pay immediately for a specific order.

<Mermaid title="Payment Processing Sequence">
sequenceDiagram
  participant C as Customer
  participant M as Merchant Website
  participant S as SePay Gateway
  participant B as Bank/Card

  C->>M: 1. Select product & checkout
  M->>M: 2. Create order
  M->>M: 3. Create checkout form with signature
  M->>S: 4. POST /v1/checkout/init
  S->>S: 5. Validate signature
  S->>C: 6. Redirect to payment page

  C->>S: 7. Select payment method
  S->>B: 8. Process payment
  B->>S: 9. Payment result

  alt Payment successful
      S->>M: 10a. Callback success_url
      S->>M: 11a. IPN notification
      S->>C: 12a. Redirect to success page
  else Payment failed
      S->>M: 10b. Callback error_url
 S->>C: 12b. Redirect to error page
  else Customer cancelled
      S->>M: 10c. Callback cancel_url
      S->>C: 11c. Redirect to cancel page
  end

  M->>C: 13. Display final result
</Mermaid>

#### Detailed steps:

1. **Customer selects product**: Add to cart and click "Checkout"
2. **Website creates order**: Save order information to database
3. **Create checkout form**: Prepare data and create HMAC-SHA256 signature
4. **Send request to SePay**: POST form to endpoint `/v1/checkout/init`
5. **Validate signature**: SePay checks the validity of the signature
6. **Redirect customer**: To SePay payment page
7. **Select payment method**: Card, QR Banking, QR NAPAS
8. **Process payment**: SePay communicates with bank/card
9. **Receive result**: From banking system
10. **Callback to website**: Call corresponding callback URLs
11. **IPN notification**: Notify payment result via IPN
12. **Redirect customer**: Back to result page on website
13. **Display result**: Success/error/cancel page

***

### Recurring Payment

<Callout type="info" title="Coming Soon">
Recurring payment is currently being finalized and will be released soon.
Please follow SePay updates for official launch timing.
</Callout>

# Quick Start

## SePay Payment Gateway is an intermediary platform connecting your website/application with banks and payment organizations. The payment gateway helps securely process online payment transactions from your customers.

---

**API Overview:**

SePay Payment Gateway API supports multiple payment methods including bank transfer via QR code, NAPAS QR, and international cards.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Authentication:** All APIs use Basic Authentication with `merchant_id` and `secret_key`.


---

<iframe width="100%" height="400" src="https://www.youtube.com/embed/RZnw2VU5J9U" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen />

### Main Features

* **Payment processing**: Receive payment information from customers
* **Transaction security**: Encrypt and protect payment data
* **Bank connection**: Communicate with banks and card organizations
* **Result notification**: Send transaction information to your system

***

### General Operation Flow

<Mermaid title="Payment Flow">
flowchart LR
A[Customer] -->|1. Select product| B[Your Website/App]
B -->|2. Create order| C[SePay Gateway]
C -->|3. Display payment page| A
A -->|4. Payment| C
C -->|5. Process transaction| D[Bank/Card]
D -->|6. Result| C
C -->|7. Notification #40;IPN#41;| B
C -->|8. Redirect| A

style A fill:#e3f2fd
style B fill:#fff3e0
style C fill:#c8e6c9
style D fill:#f3e5f5
</Mermaid>

***

### Getting Started with Bank Transfer QR Code Scanning

#### Step 1: Register an account

Visit [https://my.sepay.vn/register](https://my.sepay.vn/register?onboarding=payment-gateway) and register a SePay account. Choose a suitable service package after registration.

If you already have an account, visit [https://my.sepay.vn/pg/payment-methods](https://my.sepay.vn/pg/payment-methods) to activate the Payment Gateway.

**Activate Payment Gateway:**

In the "PAYMENT GATEWAY" section, go to "Register". On the "Payment Methods" screen, select "Start now":

<Image src="/images/quick_start/step_1_1.png" alt="Payment Flow Diagram" caption="Payment methods screen" />

You can choose to start with Sandbox and click "Start integration guide":

<Image src="/images/quick_start/step_1_8.png" alt="Payment Flow Diagram" caption="Start integrating SePay payment gateway" />

SePay supports integration via API with PHP SDK and NodeJS SDK. Click continue:

<Image src="/images/quick_start/step_1_9.png" alt="Payment Flow Diagram" caption="Integration method" />

You will receive integration information (copy `MERCHANT ID` and `SECRET KEY` for later use), keep this screen and proceed with the following steps:

<Image src="/images/quick_start/step_1_10.png" alt="Payment Flow Diagram" caption="Integration information" />

***

#### Step 2: Create payment form on your system

**Install SDK (choose PHP or NodeJS)**

<CodeTabs>
  <Code label="PHP">
    ```php
    composer require sepay/sepay-pg
    ```
  </Code>
  <Code label="NodeJS">
    ```js
    npm i sepay-pg-node
    ```
  </Code>
</CodeTabs>

<Callout type="info" title="Note">
See more details on integration with PHP SDK 
Here
 or NodeJS SDK 
Here
</Callout>

**Initialize payment form with order information and security signature:**

* **YOUR\_MERCHANT\_ID**: MERCHANT ID you copied from integration information in **step 1**
* **YOUR\_MERCHANT\_SECRET\_KEY**: SECRET KEY you copied from integration information in **step 1**

<CodeTabs>
  <Code label="SDK PHP">
    ```php
    <?php
    
    require_once 'vendor/autoload.php';
    
    use SePay\SePayClient;
    use SePay\Builders\CheckoutBuilder;
    
    // Initialize client
    $sepay = new SePayClient('YOUR_MERCHANT_ID', 'YOUR_MERCHANT_SECRET_KEY', 'sandbox');
    
    // Create checkout data
    $checkoutData = CheckoutBuilder::make()
        ->currency('VND')
        ->orderInvoiceNumber('INV-' . time())
        ->orderAmount(100000)
        ->operation('PURCHASE')
        ->orderDescription('Test payment')
        ->successUrl('https://example.com/order/DH123?payment=success')
        ->errorUrl('https://example.com/order/DH123?payment=error')
        ->cancelUrl('https://example.com/order/DH123?payment=cancel')
        ->build();
    
    // Render checkout form to UI
    echo $sepay->checkout()->generateFormHtml($checkoutData);
    ```
  </Code>
  <Code label="SDK NodeJS">
    ```js
    import { SePayPgClient } from 'sepay-pg-node-sdk';
    
    const client = new SePayPgClient({
      env: 'sandbox',
      merchant_id: 'YOUR_MERCHANT_ID',
      secret_key: 'YOUR_MERCHANT_SECRET_KEY'
    });
    
    const checkoutURL = client.checkout.initCheckoutUrl();
    
    const checkoutFormfields = client.checkout.initOneTimePaymentFields({
      operation: 'PURCHASE',
      payment_method: 'BANK_TRANSFER',
      order_invoice_number: 'DH123',
      order_amount: 10000,
      currency: 'VND',
      order_description: 'Thanh toan don hang DH123',
      success_url: 'https://example.com/order/DH123?payment=success',
      error_url: 'https://example.com/order/DH123?payment=error',
      cancel_url: 'https://example.com/order/DH123?payment=cancel',
    });
    
    return (
      <form action={checkoutURL} method="POST">
        {Object.keys(checkoutFormfields).map(field => (
          <input type="hidden" name={field} value={checkoutFormfields[field]} />
        ))}
        <button type="submit">Pay now</button>
      </form>
    );
    ```
  </Code>
  <Code label="PHP">
    ```php
    <?php
    
    $merchantId = 'YOUR_MERCHANT_ID';
    $secretKey = 'YOUR_MERCHANT_SECRET_KEY';
    
    $fields = [
        'merchant' => $merchantId,
        'currency' => 'VND',
        'order_amount' => '100000',
        'operation' => 'PURCHASE',
        'order_description' => 'Payment for order #12345',
        'order_invoice_number' => 'INV_' . time(),
        'customer_id' => 'CUST_001',
        'success_url' => 'https://example.com/order/DH123?payment=success',
        'error_url' => 'https://example.com/order/DH123?payment=error',
        'cancel_url' => 'https://example.com/order/DH123?payment=cancel',
    ];
    
    // Generate signature
    $signature = signFields($fields, $secretKey);
    $fields['signature'] = $signature;
    
    // Render form
    echo '<form method="POST" action="https://pay-sandbox.sepay.vn/v1/checkout/init">';
    foreach ($fields as $key => $value) {
        echo '<input type="hidden" name="' . $key . '" value="' . $value . '">';
    }
    echo '<button type="submit">Pay now</button>';
    echo '</form>';
    
    // Signature generation function
    function signFields(array $fields, string $secretKey): string {
        $signed = [];
        $signedFields = array_values(array_filter(array_keys($fields), fn ($field) => in_array($field, [
            'merchant','operation','payment_method','order_amount','currency',
            'order_invoice_number','order_description','customer_id',
           'success_url','error_url','cancel_url'
        ])));
    
        foreach ($signedFields as $field) {
            if (! isset($fields[$field])) continue;
            $signed[] = $field . '=' . ($fields[$field] ?? '');
        }
    
        return base64_encode(hash_hmac('sha256', implode(',', $signed), $secretKey, true));
    }
    ```
  </Code>
</CodeTabs>

**Result: payment form received** (Customize the interface to match your system):

<Image src="/images/quick_start/step_1_6.png" alt="Payment Flow Diagram" caption="Example of created payment form" />

When submitting the payment form, it will redirect to SePay's payment gateway:

<Image src="/images/quick_start/step_1_7.png" alt="Payment Flow Diagram" caption="SePay payment gateway after form submission" />

<Callout type="warn" title="Note">
When payment ends, SePay will return results: 
Success (success_url)
, 
Failure (error_url)
 and 
Customer cancelled (cancel_url)
. You need to create endpoints to handle callbacks from SePay.
</Callout>

**Create endpoints to receive callbacks from SePay:**

<Php title="PHP">
```php
// success_url - Handle successful payment
Route::get('/payment/success', function() {
  // Show success page to customer
  return view('payment.success');
});

// error_url - Handle failed payment
Route::get('/payment/error', function() {
  // Show error page to customer
  return view('payment.error');
});

// cancel_url - Handle canceled payment
Route::get('/payment/cancel', function() {
  // Show cancel page to customer
  return view('payment.cancel');
});
```
</Php>

Add the endpoints you created to `success_url`, `error_url`, `cancel_url` when creating the payment form.

***

#### Step 3: Configure IPN

<Callout type="info" title="What is IPN (Instant Payment Notification)?">
IPN is an endpoint on your system used to receive real-time transaction notifications from SePay payment gateway. 
Learn more about IPN
</Callout>

On the integration information screen from **step 1**, enter your IPN endpoint:

<Image src="/images/quick_start/step_1_4.png" alt="Payment Flow Diagram" caption="Create IPN configuration" />

Save the IPN configuration.

<Callout type="light" title="Note">
When a successful transaction occurs, SePay will return JSON via your IPN:
</Callout>

<Response title="IPN JSON">
```json
{
  "timestamp": 1759134682,
  "notification_type": "ORDER_PAID",
  "order": {
      "id": "e2c195be-c721-47eb-b323-99ab24e52d85",
      "order_id": "NQD-68DA43D73C1A5",
      "order_status": "CAPTURED",
      "order_currency": "VND",
      "order_amount": "100000.00",
      "order_invoice_number": "INV-1759134677",
      "custom_data": [],
      "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36",
      "ip_address": "14.186.39.212",
      "order_description": "Test payment"
  },
  "transaction": {
      "id": "384c66dd-41e6-4316-a544-b4141682595c",
      "payment_method": "BANK_TRANSFER",
      "transaction_id": "68da43da2d9de",
      "transaction_type": "PAYMENT",
      "transaction_date": "2025-09-29 15:31:22",
      "transaction_status": "APPROVED",
      "transaction_amount": "100000",
      "transaction_currency": "VND",
      "authentication_status": "AUTHENTICATION_SUCCESSFUL",
      "card_number": null,
      "card_holder_name": null,
      "card_expiry": null,
      "card_funding_method": null,
      "card_brand": null
  },
  "customer": null,
  "agreement": null
}
```
</Response>

**Create IPN endpoint to receive JSON data from SePay**

The endpoint is the one you configured in IPN:

<Php title="PHP">
```php
Route::post('/payment/ipn', function(Request $request) {
  $data = $request->json()->all();

  if ($data['notification_type'] === 'ORDER_PAID') {
      $order = Order::where('invoice_number', $data['order']['order_invoice_number'])->first();
      $order->status = 'paid';
      $order->save();
  }

  // Return 200 to acknowledge receipt
  return response()->json(['success' => true], 200);
});
```
</Php>

***

#### Step 4: Testing

Now you can test by creating an order on the form integrated in **step 2**.

Then return to the integration information screen and click continue to check the results:

<Image src="/images/quick_start/step_1_12.png" alt="Payment Flow Diagram" caption="Check results" />

**Scenario:**

* When the user submits the payment form on your website, the system will redirect to SePay's payment page.
* On successful payment: SePay redirects to your `/payment/success` endpoint and sends data to the IPN endpoint you configured
* On failed payment: SePay redirects to your `/payment/error` endpoint
* On cancelled payment: SePay redirects to your `/payment/cancel` endpoint

***

#### Step 5: Go live

<Callout type="info" title="Requirements">
Have a personal/business bank account and completed integration and testing in Sandbox.
</Callout>

**Steps to perform:**

1. Link a real bank account
2. From **[https://my.sepay.vn/](https://my.sepay.vn/)** go to **Payment Gateway** and select **Register** → In "Bank transfer QR code scanning" select "Start now" and continue until the screen shown below and select "Switch to Production"

<Image src="/images/quick_start/step_1_11.png" alt="Payment Flow Diagram" caption="Switch to Production" />

3. After **Switching to Production**, you will receive the official "MERCHANT ID" and "SECRET KEY"

<Image src="/images/quick_start/step_1_2.png" alt="Payment Flow Diagram" caption="Integration information" />

4. Update endpoint to Production: **[https://pay.sepay.vn/v1/checkout/init](https://pay.sepay.vn/v1/checkout/init)**
5. For SDK users: update environment variables from **Sandbox** to **Production** (when initializing client)
6. Update Sandbox "MERCHANT ID" and "SECRET KEY" to official "MERCHANT ID" and "SECRET KEY"
7. Update **IPN URL** to **Production**
8. Update **Callback URLs** to **Production**

<Callout type="light" title="For NAPAS Bank Transfer QR Code Scanning">
Documents required - 
See details here
</Callout>

<iframe width="100%" height="400" src="https://www.youtube.com/embed/uThfz1cmwAE" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen />

<Callout type="light" title="For Card Payment">
Documents required - 
See details here
</Callout>

<iframe width="100%" height="400" src="https://www.youtube.com/embed/-I4t9VKqkLM" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen />

# API Overview

## Overview of SePay Payment Gateway API including base URLs, authentication and endpoints.

---

**API Overview:**

SePay Payment Gateway API supports multiple payment methods including bank transfer via QR code, NAPAS QR, and international cards.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Authentication:** All APIs use Basic Authentication with `merchant_id` and `secret_key`.


---

### Base URLs

<TextBlock title="URL">
```text
https://pgapi-sandbox.sepay.vn
```
</TextBlock>

***

### API Authentication

All SePay APIs use **Basic Authentication** for authentication.

<TextBlock title="Headers">
```text
Authorization: Basic base64(merchant_id:secret_key)
Content-Type: application/json
```
</TextBlock>

***

### Common Error Codes

<ErrorCodes
  hiddenHead={true}
  rows={[
  { code: 200, name: "Success",                 description: "Request processed successfully",                                              action: "—" },
  { code: 400, name: "Bad Request",                 description: "Invalid request data",                                              action: "Check parameters" },
  { code: 401, name: "Unauthorized",                description: "Authentication failed",                                                         action: "Check merchant_id and secret_key" },
  { code: 403, name: "Forbidden",                   description: "No access to this API",                                           action: "Confirm access/whitelist if needed" },
  { code: 404, name: "Not Found",                   description: "Requested resource not found",                                         action: "Check URL/path or id" },
  { code: 422, name: "Unprocessable Entity",        description: "Valid data but cannot be processed (validation errors)",                  action: "Fix validation errors as reported" },
  { code: 429, name: "Too Many Requests",           description: "Rate limit exceeded",                                              action: "Reduce frequency, apply retry/backoff" },
  { code: 500, name: "Internal Server Error",       description: "Server error",                                                                action: "Try again later; contact SePay for support" }
]}
/>

***

### Pagination

APIs returning lists support pagination:

<ParamsTable
  rows={[
  { "name": "per_page", "type": "integer", "required": false, "description": "Number of results per page (default: 20, max: 100)" },
  { "name": "page", "type": "integer", "required": false, "description": "Current page (default: 1)" }
]}
/>

***

### Response Format

<Response title="RESPONSE">
```json
{
  "data": "[...]",
  "meta": {
    "per_page": 20,
    "total": 100,
    "has_more": false,
    "current_page": 1,
    "page_count": 5
  }
}
```
</Response>

# Create Payment Form

## The create order API allows you to create one-time or recurring payment transactions through SePay. You need to submit an HTML form with parameters and signature to the checkout/init endpoint to redirect customers to the payment page.

---

**API Overview:**

SePay Payment Gateway API supports multiple payment methods including bank transfer via QR code, NAPAS QR, and international cards.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Authentication:** All APIs use Basic Authentication with `merchant_id` and `secret_key`.


---

<Callout type="info" title="What is an Order?">
In SePay payment gateway, an order is an information package describing a payment request with main attributes such as amount, transaction description, invoice number, customer, and callback URLs for system processing. The payment form initialization API uses this information package to create one-time transactions; you just need to create a valid HTML form and submit to the 
`checkout/init`
 endpoint to redirect customers to the payment page.
</Callout>

### Order Creation Flow

<Mermaid title="Payment form creation and signature verification flow">
flowchart TD
  A[Customer selects checkout] --> B[Website creates HTML form]
  B --> C[Collect order information]
  C --> D[Prepare form data]
  D --> E[Create HMAC-SHA256 signature]
  E --> F[Add signature to form]
  F --> G[Submit form POST to checkout/init]
  G --> H{SePay validates signature}
  H -->|Success| I[Redirect to payment page]
  H -->|Failure| J[Return validation error]
  I --> K[Customer selects payment method]
  K --> L[Process payment]
  L --> M[Callback to success/error/cancel URL]

  style A fill:#e1f5fe
  style I fill:#c8e6c9
  style J fill:#ffcdd2
  style M fill:#fff3e0
</Mermaid>

1. **Customer selects checkout**: User clicks payment button on website
2. **Website creates HTML form**: Server creates HTML form with required parameters
3. **Collect order information**: Get information from database or session
4. **Prepare form data**: Arrange parameters in correct format
5. **Create signature**: Use HMAC-SHA256 algorithm to create signature
6. **Add signature to form**: Add signature to form as hidden field
7. **Submit form**: Send POST request to `checkout/init` endpoint
8. **Validate signature**: SePay checks signature validity
9. **Redirect**: If valid, redirect to payment page
10. **Payment**: Customer makes payment on SePay page
11. **Callback**: SePay calls back IPN URL with result

***

### Endpoint

<Endpoint method="POST" path="https://pay-sandbox.sepay.vn/v1/checkout/init" />

<Callout type="warn" title="Note">
This is an endpoint for form submission, not an API endpoint.
</Callout>

***

### Parameter List

<ParamsTable rows={[{ "name": "merchant", "type": "string", "required": true, "description": "Your merchant ID (Example: MERCHANT_123)" }, { "name": "currency", "type": "string", "required": true, "description": "Currency code (only VND supported)" }, { "name": "order_amount", "type": "string", "required": true, "description": "Order amount (smallest unit)" }, { "name": "operation", "type": "string", "required": true, "description": "Transaction type (PURCHASE or VERIFY)" }, { "name": "order_description", "type": "string", "required": true, "description": "Order description" }, { "name": "order_invoice_number", "type": "string", "required": true, "description": "Invoice number (required for PURCHASE, example: INV_20231201_001)" }, { "name": "payment_method", "type": "string", "required": false, "description": "Payment method (CARD, BANK_TRANSFER, NAPAS_BANK_TRANSFER)" }, { "name": "customer_id", "type": "string", "required": false, "description": "Customer ID" }, { "name": "success_url", "type": "string", "required": false, "description": "Redirect URL on success (Example: https://yoursite.com/success)" }, { "name": "error_url", "type": "string", "required": false, "description": "Redirect URL on error (Example: https://yoursite.com/error)" }, { "name": "cancel_url", "type": "string", "required": false, "description": "Redirect URL on cancel (Example: https://yoursite.com/cancel)" }]} />

<Callout type="warn" title="Note">
The success_url, error_url, and cancel_url parameters 
only work when your application is running on a publicly accessible domain or IP
. If you are developing on 
localhost
, use tools to expose your local environment such as 
ngrok
, 
localtunnel
, or similar.
</Callout>

***

### Basic Order Creation Example

**Create HTML form**

<Callout type="danger" title="Important note about input order in HTML">
When building your own HTML form, keep the exact order of inputs as in the sample form below so the signing and processing on SePay side matches exactly; changing field positions may cause invalid signature.
</Callout>

<Html title="Payment form">
  {`<form action="https://pay-sandbox.sepay.vn/v1/checkout/init" method="POST">
    <input type="hidden" name="merchant" value="MERCHANT_123" />
    <input type="hidden" name="currency" value="VND" />
    <input type="hidden" name="order_amount" value="100000" />
    <input type="hidden" name="operation" value="PURCHASE" />
    <input type="hidden" name="order_description" value="Payment for order #12345" />
    <input type="hidden" name="order_invoice_number" value="INV_20231201_001" />
    <input type="hidden" name="customer_id" value="CUST_001" />
    <input type="hidden" name="success_url" value="https://yoursite.com/payment/success" />
    <input type="hidden" name="error_url" value="https://yoursite.com/payment/error" />
    <input type="hidden" name="cancel_url" value="https://yoursite.com/payment/cancel" />
    <input type="hidden" name="signature" value="a1b2c3d4e5f6..." />
    <button type="submit">Pay now</button>
    </form>`}
</Html>

**Response:**

After submitting the form, the system will redirect the user to SePay's payment page:

`https://pgapi-sandbox.sepay.vn?merchant=MERCHANT_123&currency=VND&order_amount=100000&operation=PURCHASE&order_description=Payment%20for%20order%20%2312345&order_invoice_number=INV_20231201_001&customer_id=CUST_001&success_url=https%3A%2F%2Fyoursite.com%2Fpayment%2Fsuccess&error_url=https%3A%2F%2Fyoursite.com%2Fpayment%2Ferror&cancel_url=https%3A%2F%2Fyoursite.com%2Fpayment%2Fcancel&signature=a1b2c3d4e5f6...`

<Callout type="warn" title="Note">
The payment page will display available payment methods based on your merchant configuration.
</Callout>

***

### Signature Verification

<Callout type="danger" title="Important note about fields when creating signature">
When creating signature, keep the exact order of fields in 
`signedFields`
 as in the sample code (do not reorder) so the signature string matches SePay's side.
</Callout>

**Signature is created from form parameters according to these rules:**

1. **Filter signing fields**: Only sign fields in the allowed list: `merchant, operation, payment_method, order_amount, currency, order_invoice_number, order_description, customer_id, success_url, error_url, cancel_url`
2. **Create signing string**: `field1=value1,field2=value2,field3=value3...`
3. **Encode**: `base64_encode(hash_hmac('sha256', $signedString, $secretKey, true))`

**Signature creation example:**

<Php title="PHP Data Signing Function">
```php
function signFields(array $fields, string $secretKey): string {
  $signed = [];
  $signedFields = array_values(array_filter(array_keys($fields), fn ($field) => in_array($field, [
      'merchant','operation','payment_method','order_amount','currency',
      'order_invoice_number','order_description','customer_id',
      'success_url','error_url','cancel_url'
  ])));

  foreach ($signedFields as $field) {
      if (! isset($fields[$field])) continue;
      $signed[] = $field . '=' . ($fields[$field] ?? '');
  }

  return base64_encode(hash_hmac('sha256', implode(',', $signed), $secretKey, true));
}
```
</Php>

**Example signature string:**

`merchant=MERCHANT_123,operation=PURCHASE,order_amount=100000,currency=VND,order_invoice_number=INV_20231201_001,order_description=Payment for order #12345,customer_id=CUST_001,success_url=https://yoursite.com/success,error_url=https://yoursite.com/error,cancel_url=https://yoursite.com/cancel`

***

<Callout type="warn" title="Important Notes">
Invoice number:
 
`order_invoice_number`
 must be unique and not duplicated. 2. 
Amount:
 Only VND supported, amount must be greater than 0 for 
`PURCHASE`
 transactions. 3. 
Callback URLs:
 Must be publicly accessible URLs from the internet. 4. 
Signature:
 Always verify signature to ensure data integrity. 5. 
Environment:
 Use sandbox for testing, production for real transactions.
</Callout>


# IPN

## IPN is an automatic payment notification mechanism that payment gateways (e.g., SePay, PayPal, Stripe...) send to your server when there is a change in transaction status — such as successful payment, failure, or cancellation.

---

**API Overview:**

SePay Payment Gateway API supports multiple payment methods including bank transfer via QR code, NAPAS QR, and international cards.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Authentication:** All APIs use Basic Authentication with `merchant_id` and `secret_key`.


---

### Configure IPN URL

IPN URL is configured on the merchant management page in SePay:

1. Log in to [SePay](https://sepay.vn)
2. Go to **Payment Gateway → Configuration → IPN**
3. Enter your endpoint URL to receive IPN
4. Save configuration

<Callout type="warn" title="Important Note">
IPN URL must be 
HTTPS
 and the endpoint must return HTTP status code 
200
 to confirm successful receipt.
</Callout>

***

### Request from SePay to Merchant

<Endpoint method="POST" path="https://your-url (url you configured in IPN)" />

**Headers:**

```http
X-Secret-Key: <secret_key>
Content-Type: application/json
```

<Callout type="tip" title="Note">
X-Secret-Key:
 Secret key for authentication (only available when merchant configures auth type = SECRET_KEY)
</Callout>

**Parameter list**

<ParamsTable rows={[{ "name": "timestamp", "type": "integer", "required": true, "description": "Unix timestamp when notification is sent" }, { "name": "notification_type", "type": "string", "required": true, "description": "Notification type: ORDER_PAID (successful payment), TRANSACTION_VOID (void transaction)" }, { "name": "order", "type": "object", "required": true, "description": "Order information", "children": [{ "name": "id", "type": "uuidv4", "required": true, "description": "SePay internal order ID" }, { "name": "order_id", "type": "string", "required": true, "description": "Unique order code" }, { "name": "order_status", "type": "string", "required": true, "description": "Status: CAPTURED (paid), CANCELLED (cancelled), AUTHENTICATION_NOT_NEEDED (awaiting payment)" }, { "name": "order_currency", "type": "string", "required": true, "description": "Currency code (VND)" }, { "name": "order_amount", "type": "string", "required": true, "description": "Order amount" }, { "name": "order_invoice_number", "type": "string", "required": true, "description": "Invoice number" }, { "name": "custom_data", "type": "array", "required": true, "description": "Custom data" }, { "name": "user_agent", "type": "string", "required": true, "description": "Customer's user agent" }, { "name": "ip_address", "type": "string", "required": true, "description": "Customer's IP address" }, { "name": "order_description", "type": "string", "required": true, "description": "Order description" }] }, { "name": "transaction", "type": "object", "required": true, "description": "Transaction information", "children": [{ "name": "id", "type": "uuidv4", "required": true, "description": "Internal transaction ID" }, { "name": "payment_method", "type": "string", "required": true, "description": "Payment method" }, { "name": "transaction_id", "type": "string", "required": true, "description": "Unique transaction code" }, { "name": "transaction_type", "type": "string", "required": true, "description": "Transaction type: PAYMENT, REFUND" }, { "name": "transaction_date", "type": "string", "required": true, "description": "Transaction date/time" }, { "name": "transaction_status", "type": "string", "required": true, "description": "Status: APPROVED, DECLINED" }, { "name": "transaction_amount", "type": "string", "required": true, "description": "Transaction amount" }, { "name": "transaction_currency", "type": "string", "required": true, "description": "Currency code" }] }, { "name": "customer", "type": "object", "required": true, "description": "Customer information", "children": [{ "name": "id", "type": "uuidv4", "required": true, "description": "Internal customer ID" }, { "name": "customer_id", "type": "string", "required": true, "description": "Merchant's customer ID" }] }]} />

**Example request body:**

<Response title="REQUEST">
```json
{
  "timestamp": 1757058220,
  "notification_type": "ORDER_PAID",
  "order": {
    "id": "e2c195be-c721-47eb-b323-99ab24e52d85",
    "order_id": "NPSETVI00101000042R",
    "order_status": "CAPTURED",
    "order_currency": "VND",
    "order_amount": "50000.00",
    "order_invoice_number": "SUB_202509_001",
    "custom_data": [],
    "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
    "ip_address": "14.xxx.xxx.xxx",
    "order_description": "Premium package recurring payment for September 2025"
  },
  "transaction": {
    "id": "384c66dd-41e6-4316-a544-b4141682595c",
    "payment_method": "CARD",
    "transaction_id": "68ba94ac80123",
    "transaction_type": "PAYMENT",
    "transaction_date": "2025-09-01 00:00:15",
    "transaction_status": "APPROVED",
    "transaction_amount": "50000",
    "transaction_currency": "VND",
    "authentication_status": "AUTHENTICATION_SUCCESSFUL",
    "card_number": "4111XXXXXXXX1111",
    "card_holder_name": "NGUYEN VAN A",
    "card_expiry": "12/26",
    "card_funding_method": "CREDIT",
    "card_brand": "VISA"
  },
  "customer": {
    "id": "bae12d2f-0580-4669-8841-cc35cf671613",
    "customer_id": "CUST_001"
  }
}
```
</Response>

**Handle IPN endpoint:**

<Php title="PHP">
```php
Route::post('/payment/ipn', function(Request $request) {
  // Verify secret key
  if ($request->header('X-Secret-Key') !== $secretKey) {
      return response()->json(['error' => 'Unauthorized'], 401);
  }

  $data = $request->json()->all();

  if ($data['notification_type'] === 'ORDER_PAID') {
      $order = Order::where('invoice_number', $data['order']['order_invoice_number'])->first();
      $order->status = 'paid';
      $order->save();
  }

  // Return 200 to acknowledge receipt
  return response()->json(['success' => true], 200);
});
```
</Php>

# PHP SDK

## Official PHP SDK for SePay Payment Gateway. Easily integrate payments, bank transfers, VietQR and recurring payments.

---

**API Overview:**

SePay Payment Gateway API supports multiple payment methods including bank transfer via QR code, NAPAS QR, and international cards.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Authentication:** All APIs use Basic Authentication with `merchant_id` and `secret_key`.


---

<Callout type="warn" title="Requirements">
PHP 7.4 or higher, ext-json, ext-curl, Guzzle HTTP client
</Callout>

#### Installation

<Node title="Installation">
```js
composer require sepay/sepay-pg
```
</Node>

***

#### Initialize Client

<Php title="Initialization">
```php
use SePay\\SePayClient;
use SePay\\Builders\\CheckoutBuilder;

// Initialize client
$sepay = new SePayClient(
  'SP-TEST-XXXXXXX',
  'spsk_live_xxxxxxxxxxxo99PoE7RsBpss3EFH5nV',
  SePayClient::ENVIRONMENT_SANDBOX, // or ENVIRONMENT_PRODUCTION
 []
);
```
</Php>

**Parameter explanation**

<ParamsTable rows={[{ "name": "SP-TEST-XXXXXXX", "type": "string", "required": true, "description": "Merchant unit code" }, { "name": "spsk_live_x...", "type": "string", "required": true, "description": "Merchant secret key" }, { "name": "SePayClient::ENVIRONMENT_...", "type": "string", "required": true, "description": "Environment variable (ENVIRONMENT_SANDBOX or ENVIRONMENT_PRODUCTION)" }, { "name": "[]", "type": "array", "required": false, "description": "Array of other configuration values" }]} />

**Example of other configurations:**

<Php title="PHP">
```php
[
  'timeout' => 60,           // Request timeout (in seconds)
  'retry_attempts' => 5,     // Number of retries when request fails
  'retry_delay' => 2000,     // Delay between retries (in milliseconds)
  'debug' => true,           // Enable debug mode (detailed logging)
  'user_agent' => 'MyApp/1.0 SePay-PHP-SDK/1.0.0', // Application identifier string sent in request
  'logger' => $customLogger, // PSR-3 compatible logger
];
```
</Php>

***

#### Initialize Payment Form Object (One-time Payment Order)

<Php title="Initialize one-time payment">
```php
$checkoutData = CheckoutBuilder::make()
  ->currency('VND')
  ->orderAmount(100000) // 100,000 VND
  ->operation('PURCHASE')
  ->orderDescription('Test payment')
  ->orderInvoiceNumber('INV_001')
  ->successUrl('https://yoursite.com/success')
  ->build();

// Create form fields with signature
$formFields = $sepay->checkout()->generateFormFields($checkoutData);
```
</Php>

**Attribute explanation**

<ParamsTable rows={[{ "name": "operation", "type": "string", "required": true, "description": "Transaction type, currently only supports: PURCHASE" }, { "name": "orderInvoiceNumber", "type": "string", "required": true, "description": "Order/invoice number (unique)" }, { "name": "orderAmount", "type": "string", "required": true, "description": "Transaction amount" }, { "name": "currency", "type": "string", "required": true, "description": "Currency unit (e.g.: VND, USD)" }, { "name": "paymentMethod", "type": "string", "required": false, "description": "Payment method: CARD, BANK_TRANSFER" }, { "name": "orderDescription", "type": "string", "required": false, "description": "Order description" }, { "name": "customerId", "type": "string", "required": false, "description": "Customer ID (if available)" }, { "name": "successUrl", "type": "string", "required": false, "description": "Callback URL on successful payment" }, { "name": "errorUrl", "type": "string", "required": false, "description": "Callback URL on error" }, { "name": "cancelUrl", "type": "string", "required": false, "description": "Callback URL when user cancels payment" }]} />

**Create payment processing form**

<Callout type="danger" title="Important note about creating HTML form and signature">
If you build your own HTML form and signature generation function not following the sample code, you must ensure the field order matches the parameter list above so the signing process matches exactly with SePay; swapping field positions may cause invalid signature and SePay will consider the request invalid
</Callout>

<Node title="Payment form">
```js
<form method="POST" action="https://pay.sepay.vn/checkout/init">
  <?php foreach ($formFields as $name => $value): ?>
      <input type="hidden" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars($value) ?>">
  <?php endforeach; ?>

  <button type="submit">Pay now</button>
</form>
```
</Node>

<Callout type="info" title="Note">
Enable debug mode: 
`$sepay->enableDebugMode();`
 - Configure retry behavior: 
`$sepay->setRetryAttempts(3)->setRetryDelay(1000);`
</Callout>

***

#### API

<Callout type="tip">
SDK provides methods to call Open API for SePay payment gateway.
</Callout>

<Php title="Query order list">
```php
$orders = $sepay->orders()->list([
  'per_page' => 10,
  'order_status' => 'CAPTURED',
  'from_created_at' => '2025-01-01',
  'to_created_at' => '2025-12-31',
]);
```
</Php>

<Php title="View order details">
```php
$order = $sepay->orders()->retrieve('ORDER_INVOICE_NUMBER');
```
</Php>

<Node title="Void order transaction">
```js
$result = $sepay->orders()->voidTransaction('ORDER_INVOICE_NUMBER');
```
</Node>

<Callout type="warning" title="Note">
Orders are created when customers complete payment, not directly through API.
</Callout>

***

#### Error Handling

<Callout type="tip">
The SDK has different exception types for different errors
</Callout>

<Php title="PHP">
```php
use SePay\\Exceptions\\AuthenticationException;
use SePay\\Exceptions\\ValidationException;
use SePay\\Exceptions\\NotFoundException;
use SePay\\Exceptions\\RateLimitException;
use SePay\\Exceptions\\ServerException;

try {
  $order = $sepay->orders()->retrieve('ORDER_INVOICE_NUMBER');
} catch (AuthenticationException $e) {
  // Invalid credentials or signature
  echo "Authentication failed: " . $e->getMessage();
} catch (ValidationException $e) {
  // Invalid request data
  echo "Validation error: " . $e->getMessage();

  // Get field-specific errors
  if ($e->hasFieldError('amount')) {
      $errors = $e->getFieldErrors('amount');
      echo "Amount errors: " . implode(', ', $errors);
  }
} catch (NotFoundException $e) {
  // Resource not found
  echo "Not found: " . $e->getMessage();
} catch (RateLimitException $e) {
  // Rate limit exceeded
  echo "Rate limited. Retry after: " . $e->getRetryAfter() . " seconds";
} catch (ServerException $e) {
  // Server error (5xx)
  echo "Server error: " . $e->getMessage();
}
```
</Php>

***

#### Testing

<TextBlock title="Test commands">
```text
# Run all tests
composer test

# Run tests with coverage report
composer test-coverage

# Static code analysis
composer phpstan

# Fix code style
composer cs-fix
```
</TextBlock>

***

<Callout type="tip">
See detailed installation and usage guide at 
GitHub Repository
</Callout>

# NodeJS SDK

## Official Node.js SDK for SePay payment gateway. Supports bank transfer QR code scanning VietQR, Napas QR code transfer, and international/domestic card payments via Visa/Master Card/JCB.

---

**API Overview:**

SePay Payment Gateway API supports multiple payment methods including bank transfer via QR code, NAPAS QR, and international cards.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Authentication:** All APIs use Basic Authentication with `merchant_id` and `secret_key`.


---

<Callout type="warn" title="Requirements">
Node 16 or higher
</Callout>

#### Installation

<Node title="Installation">
```js
npm i sepay-pg-node
```
</Node>

***

#### Initialize Client

<Node title="Initialize client">
```js
import { SePayPgClient } from 'sepay-pg-node';

const client = new SePayPgClient({
env: 'sandbox',
merchant_id: 'YOUR_MERCHANT_ID',
secret_key: 'YOUR_MERCHANT_SECRET_KEY'
});
```
</Node>

**Parameter explanation**

<ParamsTable rows={[{ "name": "env", "type": "string", "required": true, "description": "Current environment, supported values: sandbox, production" }, { "name": "merchant_id", "type": "string", "required": true, "description": "Merchant unit code" }, { "name": "secret_key", "type": "string", "required": true, "description": "Merchant secret key" }]} />

***

#### Initialize Payment Form Object (One-time Payment Order)

<Node title="Create payment URL">
```js
const checkoutURL = client.checkout.initCheckoutUrl();
```
</Node>

<Node title="Initialize one-time payment">
```js
const checkoutFormfields = client.checkout.initOneTimePaymentFields({
operation: 'PURCHASE',
payment_method: 'CARD' | 'BANK_TRANSFER' | 'NAPAS_BANK_TRANSFER',
order_invoice_number: string,
order_amount: number,
currency: string,
order_description?: string,
customer_id?: string,
success_url?: string,
error_url?: string,
cancel_url?: string,
custom_data?: string,
});
```
</Node>

**Parameter explanation**

<ParamsTable rows={[{ "name": "operation", "type": "string", "required": true, "description": "Transaction type, currently only supports: PURCHASE" }, { "name": "payment_method", "type": "string", "required": true, "description": "Payment method: CARD, BANK_TRANSFER, NAPAS_BANK_TRANSFER" }, { "name": "order_invoice_number", "type": "string", "required": true, "description": "Order/invoice number (unique)" }, { "name": "order_amount", "type": "string", "required": true, "description": "Transaction amount" }, { "name": "currency", "type": "string", "required": true, "description": "Currency unit (e.g.: VND, USD)" }, { "name": "order_description", "type": "string", "required": false, "description": "Order description" }, { "name": "customer_id", "type": "string", "required": false, "description": "Customer ID (if available)" }, { "name": "success_url", "type": "string", "required": false, "description": "Callback URL on successful payment" }, { "name": "error_url", "type": "string", "required": false, "description": "Callback URL on error" }, { "name": "cancel_url", "type": "string", "required": false, "description": "Callback URL when user cancels payment" }, { "name": "custom_data", "type": "string", "required": false, "description": "Custom data (merchant defined)" }]} />

<Response title="Returned JSON">
```json
{
  "merchant": "string",
  "operation": "string",
  "payment_method": "string",
  "order_invoice_number": "string",
  "order_amount": "string",
  "currency": "string",
  "order_description": "string",
  "customer_id": "string",
  "success_url": "string",
  "error_url": "string",
  "cancel_url": "string",
  "custom_data": "string",
  "signature": "string"
}
```
</Response>

**Create payment processing form**

<Callout type="danger" title="Important note about creating HTML form and signature">
If you build your own HTML form and signature generation function not following the sample code, you must ensure the field order matches the parameter list above so the signing process matches exactly with SePay; swapping field positions may cause invalid signature and SePay will consider the request invalid
</Callout>

<Node title="Payment form">
```js
return (
<form action={checkoutURL} method="POST">
  {Object.keys(checkoutFormfields).map(field => (
    <input type="hidden" name={field} value={checkoutFormfields[field]} />
  ))}
  <button type="submit">Pay now</button>
</form>
);
```
</Node>

***

#### API

<Callout type="tip">
SDK provides methods to call Open API for SePay payment gateway.
</Callout>

<Node title="Query order list">
```js
const fetchOrders = async () => {
try {
  const orders = await client.order.all({
    per_page: 20,
    q: 'search-keyword',
    order_status: 'COMPLETED',
    created_at: '2025-10-13',
    from_created_at: '2025-10-01',
    to_created_at: '2025-10-13',
    customer_id: null,
    sort: {
      created_at: 'desc'
    }
  });

  console.log('Orders:', orders.data);
} catch (error) {
  console.error('Error fetching orders:', error);
}
};
```
</Node>

<Node title="View order details">
```js
const fetchOrderDetail = async (orderInvoiceNumber) => {
try {
  const order = await client.order.retrieve(orderInvoiceNumber);
  console.log('Order detail:', order.data);
} catch (error) {
  console.error('Error fetching order detail:', error);
}
};
```
</Node>

<Node title="Void order transaction (for credit card payments)">
```js
const voidTransaction = async (orderInvoiceNumber) => {
try {
  const response = await client.order.voidTransaction(orderInvoiceNumber);
  console.log('Transaction voided successfully:', response.data);
} catch (error) {
  console.error('Error voiding transaction:', error);
}
};
```
</Node>

<Node title="Cancel order (for QR code payments)">
```js
const cancelOrder = async (orderInvoiceNumber) => {
try {
  const response = await client.order.cancel(orderInvoiceNumber);
  console.log('Order cancelled successfully:', response.data);
} catch (error) {
  console.error('Error cancelling order:', error);
}
};
```
</Node>

***

<Callout type="tip">
See detailed installation and usage guide at 
GitHub Repository
</Callout>