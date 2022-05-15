<?php

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

require 'bootstrap.php';

if (empty($_GET['paymentId']) || empty($_GET['PayerID'])) {
    throw new Exception('The response is missing the paymentId and PayerID');
}
if(isset($_SESSION['post'])) {
  
}
$paymentId = $_GET['paymentId'];
$payment = Payment::get($paymentId, $apiContext);
$execution = new PaymentExecution();
$execution->setPayerId($_GET['PayerID']);
try {
    // Take the payment
    $payment->execute($execution, $apiContext);
    try {
        $payment = Payment::get($paymentId, $apiContext);
        $data = [
            'transaction_id' => $payment->getId(),
            'payment_amount' => $payment->transactions[0]->amount->total,
            'payment_status' => $payment->getState(),
            'invoice_id' => $payment->transactions[0]->invoice_number
        ];
        $data['invoice_id'];
        //$_SESSION['post']['transactions'] = $payment->transactions[0];
        $payment_method_id = 3;
        $query_order_payment = "INSERT INTO `order_payment`(`order_payment_id`, 
                                                            `order_id`, 
                                                            `payment_method_id`, 
                                                            `payment_id`, 
                                                            `payment_result`, 
                                                            `order_payment_date`) 
                                                     VALUES (NULL, 
                                                            '{$data['invoice_id']}', 
                                                            '$payment_method_id', 
                                                            '$paymentId', 
                                                            '{$data['payment_status']}',
                                                            NOW())";
        $result_order_payment = mysqli_query($db_link, $query_order_payment);
        if(mysqli_affected_rows($db_link) > 0 && $data['payment_status'] === 'approved') {
          // Payment successfully added, redirect to the payment complete page.
          header('location:https://www.larose.bg/bg/shopping-cart/shopping-cart-checkout-paypal-success');
          exit(1);
        } else {
          header('location:https://www.larose.bg/bg/shopping-cart/shopping-cart-checkout-paypal-failure');
          exit(1);
        }

    } catch (Exception $e) {
        // Failed to retrieve payment from PayPal
    }
} catch (Exception $e) {
    // Failed to take payment
}
/**
 * Add payment to database
 *
 * @param array $data Payment data
 * @return int|bool ID of new payment or false if failed
 */
function addPayment($data)
{
    global $db_link;
    if (is_array($data)) {
        $stmt = $db_link->prepare('INSERT INTO `order_payment` (transaction_id, payment_amount, payment_status, invoice_id, createdtime) VALUES(?, ?, ?, ?, ?)');
        $stmt->bind_param(
            'sdsss',
            $data['transaction_id'],
            $data['payment_amount'],
            $data['payment_status'],
            $data['invoice_id'],
            date('Y-m-d H:i:s')
        );
        $stmt->execute();
        $stmt->close();
        return $db_link->insert_id;
    }
    return false;
}
