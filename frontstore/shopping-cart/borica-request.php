<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/frontstore/config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/frontstore/functions/include-functions.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/frontstore/languages/languages.php';

date_default_timezone_set("UTC");

if (isset($_POST['customer_order']) || isset($_POST['quick_order'])) {
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
  $cart_data = json_encode($_SESSION['cart']);
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
  // We gonna initiat the order status as failed, because there may be
  // there are cases that the user cancel the order before the bank return it
  // to the site again, so there is no way to change the status to failed
  // and if the payment is successfull we gonna change it in the response
  $order_status_id = 10; //Failed
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

mysqli_query($db_link,"BEGIN");
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

$order_id = mysqli_insert_id($db_link);
$_SESSION['post']['order_id'] = $order_id;

$query_insert_device = "INSERT INTO `order_devices`(`order_device_id`, 
                                                    `order_id`, 
                                                    `order_device_info`)
                                            VALUES (NULL,
                                                    '$order_id',
                                                    '{$_SERVER['HTTP_USER_AGENT']}')";
//echo $query_insert_device;
$all_queries .= "<br>".$query_insert_device;
$result_insert_device = mysqli_query($db_link, $query_insert_device);

//$to_merchant = "mony_koleg@abv.bg";
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
  $description = isset($description) ? "$description, $product_name" : $product_name;
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

if(mail($to_merchant, $subject_merchant, $message_merchant, $headers_merchant,'-fsales@larose.bg')) {
  //echo $all_queries;mysqli_query($db_link, "ROLLBACK");exit;
  
  mysqli_commit($db_link);
}
else {
//  print_r(error_get_last());
//  echo $languages[$current_lang]['error_registration_customer_send_email_fail'];
// mysqli_query($db_link, "ROLLBACK");
}

$customer_email_data = json_encode($_SESSION['customer_email']);
$session = json_encode($_SESSION);

setcookie('order_id', $order_id, time() + 360, '/; samesite=none', $_SERVER['HTTP_HOST'], true, true);

if(!is_dir("{$_SERVER['DOCUMENT_ROOT']}/frontstore/orders/")) mkdir("{$_SERVER['DOCUMENT_ROOT']}/frontstore/orders/", 0777);
file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/frontstore/orders/$order_id.txt", $session);

// Основни данни за извършване на електронното плащане: ORDER, AMOUNT, DESC
$ORDER = str_pad($order_id, 6, "0", STR_PAD_LEFT); // Номер поръчка, 6 знака с водещи нули 
$AD_CUST_BOR_ORDER_ID = $ORDER."@".$order_id;
$AMOUNT = number_format($order_total_with_delivery, 2, '.', ''); // Сума на плащането, Формат: xx.xx,  Пример: 12.34
$DESC = "Поръчка номер $ORDER: $description"; // Описание на плащането, Пример: "Тестова поръчка"
$TRTYPE = "1"; // Тип на транзацията
$CURRENCY = "BGN"; // Валута на плащането
$TERMINAL = "V2400238"; // Идентификатор на терминала получен от БОРИКА 
$MERCHANT = "2000000212"; // Идентификатор на търговеца получен от БОРИКА 
$TIMESTAMP = date("YmdHis"); // a) Формат: YYYYMMDDHHMMSS

// Формиране на сигнатура за подписване
$NONCE = strtoupper(bin2hex(openssl_random_pseudo_bytes(16))); 
// MAC_EXTENDED = TERMINAL, TRTYPE, AMOUNT, CURRENCY, ORDER, MERCHANT, TIMESTAMP, NONCE
$P_SIGN = 
strlen($TERMINAL).$TERMINAL.
strlen($TRTYPE).$TRTYPE.
strlen($AMOUNT).$AMOUNT.
strlen($CURRENCY).$CURRENCY.
strlen($ORDER).$ORDER.
strlen($MERCHANT).$MERCHANT.
strlen($TIMESTAMP).$TIMESTAMP.
strlen($NONCE).$NONCE;

// Отваряне на файла съдържащ цифровия частен ключ 
$private_key_file = "/home/larovrcf/larosersap.key";
$private_key_pass = '#T_Et<5HV)pt%ev;';
$fp = fopen($private_key_file, "r"); 
$private_key = fread($fp, filesize($private_key_file)); 
fclose($fp); 

// Подписване на съобщението с цифров сертификат
$private_key_id = openssl_get_privatekey($private_key, $private_key_pass); 
openssl_sign($P_SIGN, $signature, $private_key_id, OPENSSL_ALGO_SHA256);   
openssl_free_key($private_key_id);   

$P_SIGN = strtoupper(bin2hex($signature));

session_unset();
session_destroy();
?>
<html>
  <head>
    <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
  </head>
  <body OnLoad="OnLoadEvent();">
    
    <!--<form action="https://3dsgate-dev.borica.bg/cgi-bin/cgi_link" method="post" name="request_form">-->
    <form action="https://3dsgate.borica.bg/cgi-bin/cgi_link" method="post" name="request_form">
    
      <!-- Фиксирани -->
      <input type="hidden" name="TRTYPE" value="1" />
      <input type="hidden" name="COUNTRY" value="BG" />
      <input type="hidden" name="CURRENCY" value="BGN" />
      <input type="hidden" name="ADDENDUM" value="AD,TD" />
      <input type="hidden" name="MERCH_GMT" value="+03" />
       Основни 
      <input type="hidden" name="ORDER"  value="<?=$ORDER;?>" />
      <input type="hidden" name="AMOUNT" value="<?=$AMOUNT;?>" />
      <input type="hidden" name="DESC"  value="<?=$DESC;?>" />
      <input type="hidden" name="TIMESTAMP" value="<?=$TIMESTAMP;?>" />
       Допълнителни 
      <input type="hidden" name="TERMINAL" value="<?=$TERMINAL;?>" />
      <input type="hidden" name="MERCHANT" value="<?=$MERCHANT;?>" />
      <input type="hidden" name="MERCH_NAME" value="Larose 5 Ltd" />
      <input type="hidden" name="AD.CUST_BOR_ORDER_ID" value="<?=$AD_CUST_BOR_ORDER_ID;?>" />
       Сигнатури 
      <input type="hidden" name="NONCE" value="<?=$NONCE;?>" />
      <input type="hidden" name="P_SIGN" value="<?=$P_SIGN;?>" />
      
    </form>
    <script language="JavaScript">

      function OnLoadEvent() {
        document.request_form.submit();
        timVar = setTimeout("procTimeout()", 30000);
      }

      function procTimeout() {
        location = '/<?=$current_lang;?>/shopping-cart/borica-response';
      }

      //
      // disable page duplication -> CTRL-N key
      //
      if (document.all) {
        document.onkeydown = function () {
          if (event.ctrlKey && event.keyCode == 78) {
            return false;
          }
        }
      }
    </script>
  </body>
</html>
