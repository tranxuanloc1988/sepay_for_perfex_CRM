<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>

<head>
    <title>Redirecting to SePay...</title>
</head>

<body>
    <p>Redirecting to payment gateway...</p>
    <form id="sepay_form" action="<?php echo $url; ?>" method="POST">
        <input type="hidden" name="merchant" value="<?php echo $post_data['merchant']; ?>" />
        <input type="hidden" name="currency" value="<?php echo $post_data['currency']; ?>" />
        <input type="hidden" name="order_amount" value="<?php echo $post_data['order_amount']; ?>" />
        <input type="hidden" name="operation" value="<?php echo $post_data['operation']; ?>" />
        <input type="hidden" name="order_description" value="<?php echo $post_data['order_description']; ?>" />
        <input type="hidden" name="order_invoice_number" value="<?php echo $post_data['order_invoice_number']; ?>" />
        <input type="hidden" name="customer_id" value="<?php echo $post_data['customer_id']; ?>" />
        <input type="hidden" name="success_url" value="<?php echo $post_data['success_url']; ?>" />
        <input type="hidden" name="error_url" value="<?php echo $post_data['error_url']; ?>" />
        <input type="hidden" name="cancel_url" value="<?php echo $post_data['cancel_url']; ?>" />
        <input type="hidden" name="signature" value="<?php echo $signature; ?>" />
    </form>
    <script type="text/javascript">
        document.getElementById('sepay_form').submit();
    </script>
</body>

</html>