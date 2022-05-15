<?php

use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

//require "{$_SERVER['DOCUMENT_ROOT']}/frontstore/config.php";
require 'bootstrap.php';

if(isset($_POST['customer_order']) || isset($_POST['quick_order'])) {
  $_SESSION['post'] = $_POST;
}
if (isset($_POST['current_lang'])) {
  $current_lang = $_POST['current_lang'];
  $_SESSION['current_lang'] = $current_lang;
}

$shopping_cart = array();
$has_products_in_cart = false;

if(isset($_SESSION['customer']['customer_id'])) {
  $customer_id = $_SESSION['customer']['customer_id'];
  $customer_is_loged = true;
} else {
  $customer_is_loged = false;
}

if(isset($_SESSION['cart'])) {
  $shopping_cart = $_SESSION['cart'];
  if(count($shopping_cart) > 0) $has_products_in_cart = true;
}

if(!$has_products_in_cart) {
  echo '<script> window.location.href="/'.$current_lang.'/shopping-cart/shopping-cart-overview?session_ended=1" </script>';
  exit("Вашата количка е празна, най-вероятно е изтекла вашата потребителска сесия. Моля добавете отново продуктите във Вашата количка и опитайте отново.");
}

if(isset($_POST)) {
  $store_id = 1;
  $delivery_price = $_POST['delivery_price'];

  if(isset($_POST['quick_order'])) {

    $customer_id = 0;
    $customer_group_id = 0;
    $customer_firstname = $_POST['customer_address_firstname'];
    $customer_lastname = $_POST['customer_address_lastname'];
    $customer_email = $_POST['customer_address_email'];
    $customer_phone = $_POST['customer_address_phone'];

  }

  if(isset($_POST['customer_order'])) {

    $customer_id = $_SESSION['customer']['customer_id'];
    $customer_group_id = $_SESSION['customer']['customer_group_id'];
    $customer_firstname = $_SESSION['customer']['customer_firstname'];
    $customer_lastname = $_SESSION['customer']['customer_lastname'];
    $customer_email = $_SESSION['customer']['customer_email'];
    $customer_phone = $_SESSION['customer']['customer_phone'];

  } //if(isset($_POST['place_order']))

  //shipping_address
  $shipping_firstname = $_POST['shipping_firstname'];
  $shipping_lastname = $_POST['shipping_lastname'];
  $shipping_phone = $_POST['shipping_phone'];
  $shipping_site_name = $_POST['shipping_site_name'];
  $shipping_address = str_replace('"', "", $_POST['shipping_address']).", $shipping_site_name";
  $shipping_interval = $_POST['shipping_interval'][0];
  switch($shipping_interval) {
    case "9":
      $shipping_interval_text = "9:00-12:00";
        break;
    case "12":
      $shipping_interval_text = "12:00-15:00";
        break;
    case "15":
      $shipping_interval_text = "15:00-17:00";
        break;
    case "17":
      $shipping_interval_text = "17:00-21:00";
        break;
    default: $shipping_interval_text = "";
        break;
  }

  $shipping_date = $_POST['shipping_date'];
  $shipping_cardtext = stripslashes($_POST['shipping_cardtext']);
  $shipping_cardtext_db = prepare_for_null_row($shipping_cardtext);
  $shipping_card_signature = stripslashes($_POST['shipping_card_signature']);
  $shipping_card_signature_db = prepare_for_null_row($shipping_card_signature);

  //invoice address
  $has_invoice_address = false;
  $order_invoice_id = 0;
  if(isset($_POST['has_invoice_address'])) {
    $has_invoice_address = true;
    $invoice_id = $_POST['invoice_id'];
    $invoice_firstname = $_POST['invoice_firstname'];
    $invoice_lastname = $_POST['invoice_lastname'];
    $invoice_company_name = $_POST['invoice_company_name'];
    $invoice_bulstat = $_POST['invoice_bulstat'];
    $invoice_accountable_person = $_POST['invoice_accountable_person'];
    $invoice_street = str_replace('"', "", $_POST['invoice_street']);
    $invoice_city = $_POST['invoice_city'];
    $invoice_postcode = $_POST['invoice_postcode'];
  }

  $order_comment = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['order_comment']));
  $order_total = str_replace(",", ".", $_POST['order_total']);
  $order_total_formatted = number_format($order_total,2,".","");
  $order_total_with_delivery = number_format($order_total+$delivery_price,2,".","");
  $order_status_id = 2; // В процес на обработване
  $currency_id = 4; // BGN
  $order_currency_code = "BGN";
  $order_currency_exchange_rate = "1.00000000";
  $order_ip = $_SERVER['REMOTE_ADDR'];
  if(isset($_POST['payment_method'])) {
    $payment_method =  $_POST['payment_method'];
    $payment_method_id = $_POST['payment'][$payment_method][0];
    $payment_method_translation = $_POST['payment'][$payment_method][1];
  }
  $order_products_quantity = 0;
}

$header_product = $languages[$current_lang]['header_product'];
$header_isbn = $languages[$current_lang]['header_isbn'];
$header_isbn_email = $languages[$current_lang]['header_isbn_email'];
$header_quantity = $languages[$current_lang]['header_quantity'];
$header_unit_price = $languages[$current_lang]['header_unit_price'];
$header_total_price = $languages[$current_lang]['header_total_price'];
//mysqli_query($db_link,"BEGIN");
$all_queries = "";

$query_insert_order = "INSERT INTO `orders`(`order_id`, 
                                            `store_id`, 
                                            `customer_id`, 
                                            `customer_group_id`, 
                                            `customer_firstname`, 
                                            `customer_lastname`, 
                                            `customer_email`, 
                                            `customer_phone`, 
                                            `shipping_firstname`, 
                                            `shipping_lastname`, 
                                            `shipping_address`, 
                                            `shipping_cardtext`, 
                                            `shipping_card_signature`, 
                                            `shipping_phone`, 
                                            `shipping_date`, 
                                            `shipping_time_interval`, 
                                            `payment_method_id`,   
                                            `order_comment`, 
                                            `order_products_quantity`, 
                                            `order_total`, 
                                            `order_delivery_price`, 
                                            `order_status_id`, 
                                            `currency_id`, 
                                            `order_currency_code`, 
                                            `order_currency_exchange_rate`, 
                                            `order_ip`, 
                                            `order_date_added`, 
                                            `order_date_modified`)
                                    VALUES (NULL,
                                            '$store_id',
                                            '$customer_id',
                                            '$customer_group_id',
                                            '$customer_firstname',
                                            '$customer_lastname',
                                            '$customer_email',
                                            '$customer_phone',
                                            '$shipping_firstname', 
                                            '$shipping_lastname', 
                                            '$shipping_address',
                                            $shipping_cardtext_db,
                                            $shipping_card_signature_db,
                                            '$shipping_phone',
                                            '$shipping_date',
                                            '$shipping_interval',
                                            '$payment_method_id',
                                            $order_comment,
                                            '$order_products_quantity',
                                            '$order_total',
                                            '$delivery_price',
                                            '$order_status_id',
                                            '$currency_id',
                                            '$order_currency_code',
                                            '$order_currency_exchange_rate',
                                            '$order_ip',
                                            NOW(),
                                            NOW())";
//echo $query_insert_order;
$all_queries = "<br>".$query_insert_order;
$result_insert_order = mysqli_query($db_link, $query_insert_order);
//if(mysqli_affected_rows($db_link) <= 0) {
//  echo $languages[$current_lang]['sql_error_insert']." - orders_test ".mysqli_error($db_link);
//  mysqli_query($db_link,"ROLLBACK");
//  exit;
//}

$order_id = mysqli_insert_id($db_link);
$_SESSION['order_id'] = $order_id;

$query_insert_device = "INSERT INTO `order_devices`(`order_device_id`, 
                                                    `order_id`, 
                                                    `order_device_info`)
                                            VALUES (NULL,
                                                    '$order_id',
                                                    '{$_SERVER['HTTP_USER_AGENT']}')";
//echo $query_insert_device;
$all_queries = "<br>".$query_insert_device;
$result_insert_device = mysqli_query($db_link, $query_insert_device);

if($has_invoice_address) {
  $query_insert_invoice = "INSERT INTO `order_invoice`(`order_invoice_id`, 
                                                      `order_id`, 
                                                      `invoice_country_id`, 
                                                      `invoice_firstname`, 
                                                      `invoice_lastname`, 
                                                      `invoice_company_name`, 
                                                      `invoice_bulstat`, 
                                                      `invoice_accountable_person`, 
                                                      `invoice_street`, 
                                                      `invoice_city`,
                                                      `invoice_postcode`)
                                              VALUES (NULL,
                                                      '$order_id',
                                                      '$invoice_country_id',
                                                      '$invoice_firstname',
                                                      '$invoice_lastname',
                                                      '$invoice_company_name',
                                                      '$invoice_bulstat',
                                                      '$invoice_accountable_person',
                                                      '$invoice_street',
                                                      '$invoice_city',
                                                      '$invoice_postcode')";
  //echo $query_insert_invoice;
  $all_queries = "<br>".$query_insert_invoice;
  $result_insert_invoice = mysqli_query($db_link, $query_insert_invoice);
//  if(mysqli_affected_rows($db_link) <= 0) {
//    echo $languages[$current_lang]['sql_error_insert']." - order_invoice ".mysqli_error($db_link);
//    mysqli_query($db_link,"ROLLBACK");
//    exit;
//  }
}

//$to_merchant = "idimitrov@eterrasystems.com";
$to_merchant = "sales@larose.bg";
$to_customer = $customer_email;

$subject_merchant = "Онлайн поръчка (www.larose.bg)";
$text_email_order_01 = $languages[$current_lang]['text_email_order_01'];
$text_email_order_02 = $languages[$current_lang]['text_email_order_02'];
$text_email_order_03 = $languages[$current_lang]['text_email_order_03'];
$subject_customer = "$text_email_order_01 $order_id $text_email_order_02";

$message_merchant = "";
$message_customer = "";
$message_output = "";

$message_1= "<table cellpadding='10' cellspacing='2' border='0' width='1000' align='center' style='margin:0 auto;'>";
$message_merchant .= $message_1;
$message_customer .= $message_1;
$logo_image = "https://".$_SERVER['SERVER_NAME']."/frontstore/images/logo.png";
//$logo_image_params = getimagesize($logo_image);
//$logo_image_dimensions = $logo_image_params[3];

$message_customer .= "<tr>
                        <td colspan='6' height='180' align='center'>
                          <a href='https://".$_SERVER['SERVER_NAME']."' target='_blank'><img src='$logo_image'></a>
                        </td>
                      </tr>
                      <tr>
                        <td colspan='6' height='20' align='center'>
                          <font style='text-transform:uppercase;color:#333;font-size: 12pt'>
                            $text_email_order_03 $customer_firstname $customer_lastname
                          </font>
                        </td>
                      </tr>
                      <tr><td colspan='6' height='30'></td></tr>";

$header_order = $languages[$current_lang]['header_order'];
$header_product = $languages[$current_lang]['header_product'];
$header_isbn_email = $languages[$current_lang]['header_isbn_email'];
$header_quantity = $languages[$current_lang]['header_quantity'];
$header_unit_price = $languages[$current_lang]['header_unit_price'];
$header_total_price = $languages[$current_lang]['header_total_price'];

$message_2 = "<tr>
                <td colspan='6' height='20'>
                  <font style='font-weight: bold;text-transform:uppercase;color:#333;font-size: 12pt'>$header_order №$order_id</font>
                </td>
              </tr>
              <tr><td colspan='6' height='8'></td></tr>
              <tr>
                <td height='30' colspan='2' style='width:40%;background: #9e6c89;'><font style='color:#FFF;font-size: 12pt'> $header_product</font></td>
                <td height='30' align='center' style='width:10%;background: #9e6c89;'><font style='color:#FFF;font-size: 12pt'>$header_isbn_email</font></td>
                <td height='30' align='center' style='width:10%;background: #9e6c89;'><font style='color:#FFF;font-size: 12pt'>$header_quantity</font></td>
                <td height='30' align='center' style='width:20%;background: #9e6c89;'><font style='color:#FFF;font-size: 12pt'>$header_unit_price</font></td>
                <td height='30' align='center' style='width:20%;background: #9e6c89;'><font style='color:#FFF;font-size: 12pt'>$header_total_price</font></td>
              </tr>";
$message_merchant .= $message_2;
$message_customer .= $message_2;

foreach($shopping_cart as $products) {

  $product_id = $products['product_id'];
  $product_isbn = $products['product_isbn'];
  $product_price = $products['product_price'];
  $product_name = $products['product_name'];
  $product_name_db = mysqli_real_escape_string($db_link,$products['product_name']);
  $product_url = $products['product_url'];
  $product_img_src = "https://".$_SERVER['SERVER_NAME'].$products['product_img_src'];
  @$product_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$products['product_img_src']);
  $product_image_dimensions = $product_image_params[3];
  $product_qty = $products['product_qty'];
  $order_products_quantity += $product_qty;
  $product_price_total = number_format(($product_price*$product_qty),2,".","");

  $query_order_products = "INSERT INTO `order_details`(`order_product_id`, 
                                                        `order_id`, 
                                                        `product_id`, 
                                                        `product_name`, 
                                                        `product_isbn`, 
                                                        `product_quantity`, 
                                                        `product_price`) 
                                                  VALUES(NULL,
                                                        '$order_id',
                                                        '$product_id',
                                                        '$product_name_db',
                                                        '$product_isbn',
                                                        '$product_qty',
                                                        '$product_price')";
  $all_queries .= "<br>".$query_order_products;
  $result_order_products = mysqli_query($db_link, $query_order_products);

  $message_3 = "<tr>
                  <td height='110'><a href='".$_SERVER['SERVER_NAME']."$product_url' target='_blank'><img src='$product_img_src' $product_image_dimensions></a></td>
                  <td height='110'>$product_name</td>
                  <td height='110' align='center'><font style='font-weight:bold;font-size: 14px'>$product_isbn</font></td>
                  <td height='110' align='center'>$product_qty</td>
                  <td height='110' align='center'>$product_price лв.</td>
                  <td height='110' align='center'>$product_price_total лв.</td>
                </tr>";
  $message_merchant .= $message_3;
  $message_customer .= $message_3;
  $message_output .= "<tr>
                        <td>$product_isbn</td>
                        <td>$product_name</td>
                        <td>$product_qty</td>
                        <td>$product_price лв.</td>
                        <td>$product_price_total лв.</td>
                      </tr>";
} //foreach($shopping_cart)

$query_update_order = "UPDATE `orders` SET `order_products_quantity` = '$order_products_quantity' WHERE `order_id` = '$order_id'";
$all_queries .= "<br>\n".$query_update_order;
$result_update_order = mysqli_query($db_link, $query_update_order);
if(!$result_update_order) {
  echo $languages[$current_lang]['sql_error_update']." - `order_products_quantity` 2 ".mysqli_error($db_link);
}

$delivery_price_formatted = number_format($delivery_price,2,".","");
$header_payment_method = $languages[$current_lang]['header_payment_method'];
$header_customer_data = $languages[$current_lang]['header_customer_data'];
$header_delivery_price = $languages[$current_lang]['header_delivery_price'];
$header_summary = $languages[$current_lang]['header_summary'];
$header_customer_email = $languages[$current_lang]['header_customer_email'];
$header_name = $languages[$current_lang]['header_name'];
$phone_text = $languages[$current_lang]['header_customer_phone'];
$header_total_price_with_delivery = $languages[$current_lang]['header_total_price_with_delivery'];

$message_4 = "<tr>
                <td colspan='4' height='30' style='background: #dadada;'></td>
                <td height='30' align='right' style='background: #dadada;'>$header_summary:</td>
                <td height='30' align='center' style='background: #dadada;font-weight:bold;'>$order_total_formatted лв.</td>
              </tr>
              <tr>
                <td colspan='4' height='30'></td>
                <td height='30' align='right' style='background: #dadada;'>$header_delivery_price:</td>
                <td height='30' align='center' style='background: #dadada;font-weight:bold;'>$delivery_price_formatted лв.</td>
              </tr>
              <tr>
                <td colspan='4' height='30'></td>
                <td height='30' align='right' style='background: #dadada;'>$header_total_price_with_delivery:</td>
                <td height='30' align='center' style='background: #dadada;font-weight:bold;'>$order_total_with_delivery лв.</td>
              </tr>
              <tr><td colspan='6' height='8'></td></tr>
              <tr>
                <td colspan='6' height='30' style='background: #9e6c89;'>
                  <font style='color:#FFF;font-size: 12pt'> $header_payment_method</font>
                </td>
              </tr>
              <tr>
                <td colspan='6' height='30'>
                  <br>$payment_method_translation<br>
                </td>
              </tr>
              <tr><td colspan='6' height='8'></td></tr>
              <tr>
                <td colspan='6' height='30' style='background: #9e6c89;'>
                  <font style='color:#FFF;font-size: 12pt'> $header_customer_data</font>
                </td>
              </tr>
              <tr>
                <td colspan='6' height='30'>
                  <br><font style='font-weight:bold;'>$header_name:</font> $customer_firstname $customer_lastname
                  <br><br><font style='font-weight:bold;'>$header_customer_email:</font> $customer_email
                  <br><br><font style='font-weight:bold;'>$phone_text:</font> $customer_phone
                  <br>
                </td>
              </tr>
              <tr><td colspan='6' height='8'></td></tr>";

$message_merchant .= $message_4;
$message_customer .= $message_4;

$header_delivery_address = $languages[$current_lang]['header_delivery_address'];
$header_invoice_address = $languages[$current_lang]['header_invoice_address'];
$shipping_cardtext_header = $languages[$current_lang]['header_shipping_cardtext'];
$text_no_cardtext = $languages[$current_lang]['text_no_cardtext'];
$shipping_cardtext_text = (empty($shipping_cardtext)) ? "$text_no_cardtext" : "$shipping_cardtext";
$shipping_card_signature_text = (empty($shipping_card_signature)) ? "$text_no_cardtext" : "$shipping_card_signature";
$shipping_address_text = str_replace('"', "", $shipping_address);
$shipping_date_formatted = date("d.m.Y",  strtotime($shipping_date));
$header_street = $languages[$current_lang]['header_street'];
$header_delivery_date = $languages[$current_lang]['header_delivery_date'];
$text_delivery_time_interval = $languages[$current_lang]['text_delivery_time_interval'];
$header_shipping_card_signature = $languages[$current_lang]['header_shipping_card_signature'];

$message_delivery_address = "<tr>
                              <td colspan='6' height='30' style='background: #9e6c89;'>
                                <font style='color:#FFF;font-size: 12pt'>$header_delivery_address</font>
                              </td>
                            </tr>
                            <tr>
                              <td colspan='6' height='100'>
                                <br><font style='font-weight:bold;'>$header_name:</font> $shipping_firstname $shipping_lastname
                                <br><br><font style='font-weight:bold;'>$header_street:</font> $shipping_address_text 
                                <br><br><font style='font-weight:bold;'>$header_delivery_date:</font> $shipping_date_formatted $text_delivery_time_interval $shipping_interval_text
                                <br><br><font style='font-weight:bold;'>$shipping_cardtext_header:</font> $shipping_cardtext_text
                                <br><br><font style='font-weight:bold;'>$header_shipping_card_signature:</font> $shipping_card_signature_text
                                <br><br><font style='font-weight:bold;'>$phone_text:</font> $shipping_phone
                                <br>
                              </td>
                            </tr>
                            <tr><td colspan='6' height='8'></td></tr>";

$message_merchant .= $message_delivery_address;
$message_customer .= $message_delivery_address;

if($has_invoice_address) {
$header_accountable_person = $languages[$current_lang]['header_accountable_person'];
$header_bulstat = $languages[$current_lang]['header_bulstat'];

$message_invoice = "<tr>
                      <td colspan='6' style='background: #9e6c89;'>
                        <font style='color:#FFF;font-size: 12pt'> $header_invoice_address</font>
                      </td>
                    </tr>
                    <tr>
                      <td colspan='6'>
                        $invoice_firstname $invoice_lastname<br>$invoice_company_name<br>$header_accountable_person $invoice_accountable_person
                        <br>$header_bulstat $invoice_bulstat<br>$invoice_street, $invoice_city, $invoice_postcode
                      </td>
                    </tr>";

$message_merchant .= $message_invoice;
$message_customer .= $message_invoice;
}

if(!empty($_POST['order_comment'])) {
  $order_comment = $_POST['order_comment'];
  $header_order_comment = $languages[$current_lang]['header_order_comment'];
  $message_order_comment = "<tr>
                              <td colspan='6' height='30' style='background: #9e6c89;'>
                                <font style='color:#FFF;font-size: 12pt'> $header_order_comment</font>
                              </td>
                            </tr>
                            <tr><td colspan='6' height='30'><br>$order_comment<br></td></tr>";
  $message_merchant .= $message_order_comment;
  $message_customer .= $message_order_comment;
}
$text_email_marchent_crew = $languages[$current_lang]['text_email_marchent_crew'];

$message_customer .= "<tr><td colspan='6' height='40'></td></tr>
                      <tr>
                        <td colspan='6' height='30'>
                          <font style='font-weight: bold;text-transform:uppercase;color:#333;font-size: 12pt'>
                            $text_email_marchent_crew
                          </font>
                        </td>
                      </tr>";
$message_merchant .= "<tr><td colspan='6' height='80'></td></tr></table>";
$message_customer .= "<tr><td colspan='6' height='80'></td></tr></table>";

$headers_merchant = $languages[$current_lang]['email_headers_text'];
//$headers_merchant .= 'Cc: mony_koleg@abv.bg;' . "\r\n";
$headers_customer = $languages[$current_lang]['email_headers_text'];

$_SESSION['customer_email']['to_customer'] = $to_customer;
$_SESSION['customer_email']['subject_customer'] = $subject_customer;
$_SESSION['customer_email']['message_customer'] = $message_customer;
$_SESSION['customer_email']['headers_customer'] = $headers_customer;

$delivery_price = $_POST['delivery_price'];
$product_descriptions = $_POST['product_descriptions'];
$order_total = str_replace(",", ".", $_POST['order_total']);
$order_total_formatted = number_format($order_total,2,".","");
$order_total_with_delivery = number_format(($order_total+$delivery_price)*0.51129198,2,".","");
$currency_id = 3; // EUR
$order_currency_code = "EUR";
$order_products_quantity = 0;


$payer = new Payer();
$payer->setPaymentMethod('paypal');
// Set some example data for the payment.
$currency = $order_currency_code;
$amountPayable = $order_total_with_delivery;
$invoiceNumber = $order_id;
$amount = new Amount();
$amount->setCurrency($currency)
    ->setTotal($amountPayable);
$transaction = new Transaction();
$transaction->setAmount($amount)
    ->setDescription($product_descriptions)
    ->setInvoiceNumber($invoiceNumber);
//var_dump($transaction);
$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl($paypalConfig['return_url'])
    ->setCancelUrl($paypalConfig['cancel_url']);
$payment = new Payment();
$payment->setIntent('sale')
    ->setPayer($payer)
    ->setTransactions([$transaction])
    ->setRedirectUrls($redirectUrls);
try {
    $payment->create($apiContext);
} catch (PayPal\Exception\PayPalConnectionException $ex) {
    echo $ex->getCode(); // Prints the Error Code
    echo $ex->getData(); // Prints the detailed error message 
    die($ex);
} catch (Exception $ex) {
    die($ex);
}

if(mail($to_merchant, $subject_merchant, $message_merchant, $headers_merchant,'-fsales@larose.bg')) {
  //echo $all_queries;mysqli_query($db_link, "ROLLBACK");

  //mysqli_commit($db_link);
}
else {
//  print_r(error_get_last());
//  echo $languages[$current_lang]['error_registration_customer_send_email_fail'];
// mysqli_query($db_link, "ROLLBACK");
}

//echo '<script> window.location.href="/'.$payment->getApprovalLink().'" </script>';
header('location:' . $payment->getApprovalLink());
exit(1);