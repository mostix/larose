<?php
// Часова зона на сървъра
//date_default_timezone_set('UTC');

$order_id = $_COOKIE['order_id'];
if(!is_dir("{$_SERVER['DOCUMENT_ROOT']}/frontstore/orders/")) mkdir("{$_SERVER['DOCUMENT_ROOT']}/frontstore/orders/", 0777);
$session_file  = "{$_SERVER['DOCUMENT_ROOT']}/frontstore/orders/$order_id.txt";
$fp = fopen($session_file, "r"); 
$session_data = fread($fp, filesize($session_file)); 
fclose($fp); 
unlink($session_file);
$_SESSION = json_decode($session_data, true);
//print_array_for_debug($_SESSION);exit;

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
  if(count($shopping_cart) > 0)
    $has_products_in_cart = true;
}
  
// Проверка дали има обратно получената информация от БОРИКА?
if(empty($_REQUEST)) {
  echo "<h1>Пропадна връзката с Борика и не получихме отговор. Моля опитайте отново по-късно</h1>";
}

//if(!is_dir("{$_SERVER['DOCUMENT_ROOT']}/frontstore/orders/")) mkdir("{$_SERVER['DOCUMENT_ROOT']}/frontstore/orders/", 0777);
//file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/frontstore/orders/$order_id-response.txt", print_r($_REQUEST));

//print_array_for_debug($_REQUEST);

// Формиране на сигнатурата за проверка
// MAC_EXTENDED = ACTION, RC, APPROVAL, TERMINAL, TRTYPE, AMOUNT, CURRENCY, ORDER, RRN, INT_REF, PARES_STATUS, ECI, TIMESTAMP, NONCE
$ACTION = strlen($_REQUEST["ACTION"]) > 0 ? strlen($_REQUEST["ACTION"]).$_REQUEST["ACTION"] : "-";
$RC = strlen($_REQUEST["RC"]) > 0 ? strlen($_REQUEST["RC"]).$_REQUEST["RC"] : "-";
$APPROVAL = strlen($_REQUEST["APPROVAL"]) > 0 ? strlen($_REQUEST["APPROVAL"]).$_REQUEST["APPROVAL"] : "-";
$TERMINAL = strlen($_REQUEST["TERMINAL"]) > 0 ? strlen($_REQUEST["TERMINAL"]).$_REQUEST["TERMINAL"] : "-";
$TRTYPE = strlen($_REQUEST["TRTYPE"]) > 0 ? strlen($_REQUEST["TRTYPE"]).$_REQUEST["TRTYPE"] : "-";
$AMOUNT = strlen($_REQUEST["AMOUNT"]) > 0 ? strlen($_REQUEST["AMOUNT"]).$_REQUEST["AMOUNT"] : "-";
$CURRENCY = strlen($_REQUEST["CURRENCY"]) > 0 ? strlen($_REQUEST["CURRENCY"]).$_REQUEST["CURRENCY"] : "-";
$ORDER = strlen($_REQUEST["ORDER"]) > 0 ? strlen($_REQUEST["ORDER"]).$_REQUEST["ORDER"] : "-";
$RRN = strlen($_REQUEST["RRN"]) > 0 ? strlen($_REQUEST["RRN"]).$_REQUEST["RRN"] : "-";
$INT_REF = strlen($_REQUEST["INT_REF"]) > 0 ? strlen($_REQUEST["INT_REF"]).$_REQUEST["INT_REF"] : "-";
$PARES_STATUS = strlen($_REQUEST["PARES_STATUS"]) > 0 ? strlen($_REQUEST["PARES_STATUS"]).$_REQUEST["PARES_STATUS"] : "-";
$ECI = strlen($_REQUEST["ECI"]) > 0 ? strlen($_REQUEST["ECI"]).$_REQUEST["ECI"] : "-";
$TIMESTAMP = strlen($_REQUEST["TIMESTAMP"]) > 0 ? strlen($_REQUEST["TIMESTAMP"]).$_REQUEST["TIMESTAMP"] : "-";
$NONCE = strlen($_REQUEST["NONCE"]) > 0 ? strlen($_REQUEST["NONCE"]).$_REQUEST["NONCE"] : "-";
// data & p_sign
$DATA = $ACTION.$RC.$APPROVAL.$TERMINAL.$TRTYPE.$AMOUNT.$CURRENCY.$ORDER.$RRN.$INT_REF.$PARES_STATUS.$ECI.$TIMESTAMP.$NONCE;
$DATA = rtrim($DATA,"-"); // Fix FW: update Borica EMV 3DS ver 2.2 from 22.10.2020
//
// Сигнатура
$P_SIGN = hex2bin($_REQUEST["P_SIGN"]);

// Отваряне на файла съдържащ цифровия публичен ключ 
$public_key_file  = "/home/larovrcf/MPI_OW_APGW.cer";
$fp = fopen($public_key_file, "r"); 
$public_key = fread($fp, filesize($public_key_file)); 
fclose($fp); 

// Подписване на съобщението с цифров сертификат
$public_key_id = openssl_get_publickey($public_key); 
$ssl_verification = openssl_verify($DATA, $P_SIGN, $public_key_id, OPENSSL_ALGO_SHA256);  
openssl_free_key($public_key_id);   

// Проверка за валидност?
if($ssl_verification != 1) die("<h1>SSL Verification Error!</h1><p>".openssl_error_string()."</p>");

$payment_id = $_REQUEST["RRN"];
$reference = $_REQUEST["INT_REF"];
$auth_code = $_REQUEST["APPROVAL"];
$response_code = $_REQUEST["RC"];

// Проверка за успешна транзакция?
if($response_code == "00") {

  $payment_result = "CAPTURED";
  $trans_error = prepare_for_null_row("");
  
  $order_status_id = 2; // В процес на обработване
  $query_order = "UPDATE `orders` SET `order_status_id`='$order_status_id' WHERE `order_id` = '$order_id'";
  mysqli_query($db_link, $query_order);
}
else {

  $payment_result = "NOT CAPTURED";
  
  $ERR_GWP_CODES['bg'] = [
      -1 => 'В заявката не е попълнено задължително поле',
      -3 => "Aвторизационният хост не отговаря или форматът на отговора е неправилен",
      -4 => 'Няма връзка с авторизационния хост',
      -11 => 'Грешка в поле "Валута"  в заявката',
      -12 => 'Грешка в поле "Merchant ID / Идентификатор на търговец"',
      -15 => 'Грешка в поле "RRN" в заявката',
      -17 => 'Грешка при проверка на P_SIGN',
      -19 => 'Грешка в искането за автентификация или неуспешна автентификация',
      -20 => 'Разрешената  разлика между времето на сървъра на търговеца и e-Gateway сървъра е надвишена',
      -21 => 'Транзакцията вече е била изпълнена',
      -25 => 'Транзакцията е отказана (напр. от картодържателя)',
      -27 => 'Неправилно име на търговеца',
      -32 => 'Дублирана отказана транзакция',
  ];

  $ERR_GWP_CODES['en'] = [
      -1 => 'A mandatory request field is not filled in',
      -3 => "Acquirer host (NS) does not respond or wrong format of e-gateway response template file",
      -4 => 'No connection to the acquirer host (NS)',
      -11 => 'Error in the "Currency" request field',
      -12 => 'Error in the "Merchant ID" request field',
      -15 => 'Error in the "RRN" request field',
      -17 => 'Error in the validation of the P_SIGN',
      -19 => 'Error in the authentication information request or authentication failed',
      -20 => 'A permitted time interval (1 hour by default) between the transaction Time Stam prequest field and the e-Gateway time is exceeded',
      -21 => 'The transaction has already been executed',
      -25 => 'Transaction canceled (e.g. by user)',
      -27 => 'Invalid merchant name',
      -32 => 'Duplicate declined transaction',
  ];

  if(array_key_exists($response_code, $ERR_GWP_CODES[$current_lang])) {
    $trans_error = prepare_for_null_row($ERR_GWP_CODES[$current_lang][$response_code]);
  }
  else {
    $trans_error = prepare_for_null_row("Възникна грешка с код: $response_code");
  }

}
  
$query_order_trans = "INSERT INTO `order_transaction`(`order_transaction_id`, 
                                                        `order_id`, 
                                                        `payment_id`, 
                                                        `reference`, 
                                                        `trans_id`, 
                                                        `auth_code`, 
                                                        `response_code`, 
                                                        `result`, 
                                                        `error`) 
                                                  VALUES (NULL, 
                                                         '$order_id', 
                                                         '$payment_id', 
                                                         '$reference', 
                                                         '$payment_id', 
                                                         '$auth_code', 
                                                         '$response_code', 
                                                         '$payment_result',
                                                         $trans_error)";
//echo $query_order_trans;
$all_queries = "<br>".$query_order_trans;
$result_order_trans = mysqli_query($db_link, $query_order_trans);
if(isset($_SESSION['post']['payment_method'])) {
  $payment_method =  $_SESSION['post']['payment_method'];
  $payment_method_id = $_SESSION['post']['payment'][$payment_method][0];
  $payment_method_translation = $_SESSION['post']['payment'][$payment_method][1];
}

$query_order_payment = "INSERT INTO `order_payment`(`order_payment_id`, 
                                                    `order_id`, 
                                                    `payment_method_id`, 
                                                    `payment_id`, 
                                                    `payment_result`, 
                                                    `order_payment_date`) 
                                             VALUES (NULL, 
                                                    '$order_id', 
                                                    '$payment_method_id', 
                                                    '$payment_id', 
                                                    '$payment_result',
                                                    NOW())";
$all_queries .= "<br>".$query_order_payment;
$result_order_payment = mysqli_query($db_link, $query_order_payment);

if(isset($has_products_in_cart) && $has_products_in_cart) {
  
  if(isset($_SESSION['post'])) {

    $store_id = 1;
    $delivery_price = $_SESSION['post']['delivery_price'];

    if(isset($_SESSION['post']['quick_order'])) {

      $customer_id = 0;
      $customer_group_id = 0;
      $customer_firstname = $_SESSION['post']['customer_address_firstname'];
      $customer_lastname = $_SESSION['post']['customer_address_lastname'];
      $customer_email = $_SESSION['post']['customer_address_email'];
      $customer_phone = $_SESSION['post']['customer_address_phone'];

    }

    if(isset($_SESSION['post']['customer_order'])) {

      $customer_id = $_SESSION['customer']['customer_id'];
      $customer_group_id = $_SESSION['customer']['customer_group_id'];
      $customer_firstname = $_SESSION['customer']['customer_firstname'];
      $customer_lastname = $_SESSION['customer']['customer_lastname'];
      $customer_email = $_SESSION['customer']['customer_email'];
      $customer_phone = $_SESSION['customer']['customer_phone'];

    } //if(isset($_POST['place_order']))

    //shipping_address
    $shipping_firstname = $_SESSION['post']['shipping_firstname'];
    $shipping_lastname = $_SESSION['post']['shipping_lastname'];
    $shipping_phone = $_SESSION['post']['shipping_phone'];
    $shipping_site_name = $_SESSION['post']['shipping_site_name'];
    $shipping_address = str_replace('"', "", $_SESSION['post']['shipping_address']).", $shipping_site_name";
    $shipping_interval = $_SESSION['post']['shipping_interval'][0];
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
      case "917":
        $shipping_interval_text = "9:00-17:00";
        break;
      default: $shipping_interval_text = "";
          break;
    }

    $shipping_date = $_SESSION['post']['shipping_date'];
    $shipping_cardtext = stripslashes($_SESSION['post']['shipping_cardtext']);
    $shipping_cardtext_db = prepare_for_null_row($shipping_cardtext);
    $shipping_card_signature = stripslashes($_SESSION['post']['shipping_card_signature']);
    $shipping_card_signature_db = prepare_for_null_row($shipping_card_signature);

    //invoice address
    $has_invoice_address = false;
    $order_invoice_id = 0;
    if(isset($_SESSION['post']['has_invoice_address'])) {
      $has_invoice_address = true;
      $invoice_id = $_SESSION['post']['invoice_id'];
      $invoice_firstname = $_SESSION['post']['invoice_firstname'];
      $invoice_lastname = $_SESSION['post']['invoice_lastname'];
      $invoice_company_name = $_SESSION['post']['invoice_company_name'];
      $invoice_bulstat = $_SESSION['post']['invoice_bulstat'];
      $invoice_accountable_person = $_SESSION['post']['invoice_accountable_person'];
      $invoice_street = str_replace('"', "", $_SESSION['post']['invoice_street']);
      $invoice_city = $_SESSION['post']['invoice_city'];
      $invoice_postcode = $_SESSION['post']['invoice_postcode'];
    }

    $order_comment = prepare_for_null_row(mysqli_real_escape_string($db_link,$_SESSION['post']['order_comment']));
    $order_total = str_replace(",", ".", $_SESSION['post']['order_total']);
    $order_total_formatted = number_format($order_total,2,".","");
    $order_total_with_delivery = number_format($order_total+$delivery_price,2,".","");
    $order_status_id = 2; // В процес на обработване
    $currency_id = 4; // BGN
    $order_currency_code = "BGN";
    $order_currency_exchange_rate = "1.00000000";
    $order_ip = $_SERVER['REMOTE_ADDR'];
    if(isset($_SESSION['post']['payment_method'])) {
      $payment_method =  $_SESSION['post']['payment_method'];
      $payment_method_id = $_SESSION['post']['payment'][$payment_method][0];
      $payment_method_translation = $_SESSION['post']['payment'][$payment_method][1];
    }
    $order_products_quantity = 0;
  }
    
  unset($_SESSION['customer']['has_invoice_address']);
  unset($_SESSION['customer']['shipping_address']);
  unset($_SESSION['customer']['invoice_address']);
  unset($_SESSION['total_products_qty']);
  unset($_SESSION['total_products_price']);
  unset($_SESSION['cart']);
  unset($_SESSION['post']);
  
  print_html_shopping_cart_progress();
    
  $header_product = $languages[$current_lang]['header_product'];
  $header_isbn = $languages[$current_lang]['header_isbn'];
  $header_isbn_email = $languages[$current_lang]['header_isbn_email'];
  $header_quantity = $languages[$current_lang]['header_quantity'];
  $header_unit_price = $languages[$current_lang]['header_unit_price'];
  $header_total_price = $languages[$current_lang]['header_total_price'];
?>
    <div class="container marketing category cart" style="margin-top: 0;padding-top: 0;">
      <div class="row">
<?php
    if($payment_result == "CAPTURED") {
      if(isset($_SESSION['customer_email'])) {

        $to_customer = $_SESSION['customer_email']['to_customer'];
        $subject_customer = $_SESSION['customer_email']['subject_customer'];
        $message_customer = $_SESSION['customer_email']['message_customer'];
        $headers_customer = $_SESSION['customer_email']['headers_customer'];
        
        mail($to_customer, $subject_customer, $message_customer, $headers_customer,'-fsales@larose.bg');
        
        $query_review = "SELECT `customers_review_id` FROM `customers_reviews` WHERE `customer_email` = '$to_customer'";
        $result_review = mysqli_query($db_link, $query_review);
        //if(!$result_review) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_review) <= 0) {
          $query_insert = "INSERT INTO `customers_reviews`(`customers_review_id`, `customer_email`, `email_for_review_sended`)
                                                    VALUES (NULL,'$to_customer','0')";
          $result_insert = mysqli_query($db_link, $query_insert);
        }
        unset($_SESSION['customer_email']);
      }
?>
        <h1 class="alert-success alert" style="margin: 10px 0 20px;"><?=$languages[$current_lang]['text_order_completed_01'];?></h1>
        <p><?=$languages[$current_lang]['text_order_completed_11'];?></p>
        <p><?=$languages[$current_lang]['text_order_completed_02'].$languages[$current_lang]['text_order_completed_03'];?></p>
<?php
    }
    else {
      //$payment_result == "CANCELED"
      $order_status_id = 7; //Canceled
?>
      <h1 class="error" style="margin: 10px 0 20px;padding: 0;">
        <?=$languages[$current_lang]['text_order_canceled_01'];?>
        <p><?=$trans_error;?></p>
        <p>Извиняваме се причиненото неудобство. <br> Моля свържете се с нас за да ни съобщите за възникналата грешката.</p>
      </h1>
<?php
    }
    $message_output = "";

    $header_order = $languages[$current_lang]['header_order'];
    $header_product = $languages[$current_lang]['header_product'];
    $header_isbn_email = $languages[$current_lang]['header_isbn_email'];
    $header_quantity = $languages[$current_lang]['header_quantity'];
    $header_unit_price = $languages[$current_lang]['header_unit_price'];
    $header_total_price = $languages[$current_lang]['header_total_price'];

    foreach($shopping_cart as $products) {

      $product_id = $products['product_id'];
      $product_isbn = $products['product_isbn'];
      $product_price = $products['product_price'];
      $product_name = $products['product_name'];
      $product_url = $products['product_url'];
      $product_img_src = "https://".$_SERVER['SERVER_NAME'].$products['product_img_src'];
      @$product_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$products['product_img_src']);
      $product_image_dimensions = $product_image_params[3];
      $product_qty = $products['product_qty'];
      $order_products_quantity += $product_qty;
      $product_price_total = number_format(($product_price*$product_qty),2,".","");

      if($payment_result == "CAPTURED") {
        // substract the product_quantity with the $product_qty the customer has ordered
        $query_product_qty = "SELECT `product_quantity`,`product_subtract` FROM `products` WHERE `product_id` = '$product_id'";
        $all_queries .= $query_product_qty."<br>";
        $result_product_qty = mysqli_query($db_link, $query_product_qty);
        if(!$result_product_qty) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_product_qty) > 0) {

          $product_qty_row = mysqli_fetch_assoc($result_product_qty);
          $product_subtract = $product_qty_row['product_subtract'];
          $product_quantity = $product_qty_row['product_quantity'];

          if($product_subtract == 1) {
            $product_qty_subtract = ($product_quantity < $product_qty) ? $product_quantity : $product_qty;
            $query_update_product = "UPDATE `products` SET `product_quantity` = `product_quantity`-$product_qty_subtract WHERE `product_id` = '$product_id'";
            $all_queries .= $query_update_product;
            $result_update_product = mysqli_query($db_link, $query_update_product);
          }

          mysqli_free_result($result_product_qty);
        }
      }
    
      $message_output .= "<tr>
                            <td>$product_isbn</td>
                            <td>$product_name</td>
                            <td>$product_qty</td>
                            <td>$product_price лв.</td>
                            <td>$product_price_total лв.</td>
                          </tr>";
    } //foreach($shopping_cart)
      
    $delivery_price_formatted = number_format($delivery_price,2,".","");
    $header_payment_method = $languages[$current_lang]['header_payment_method'];
    $header_customer_data = $languages[$current_lang]['header_customer_data'];
    $header_delivery_price = $languages[$current_lang]['header_delivery_price'];
    $header_summary = $languages[$current_lang]['header_summary'];
    $header_customer_email = $languages[$current_lang]['header_customer_email'];
    $header_name = $languages[$current_lang]['header_name'];
    $phone_text = $languages[$current_lang]['header_customer_phone'];
    $header_total_price_with_delivery = $languages[$current_lang]['header_total_price_with_delivery'];
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
    $text_email_marchent_crew = $languages[$current_lang]['text_email_marchent_crew'];
    $header_merchant = $languages[$current_lang]['merchant'];
?>
      <div id="obb_receipt" style="border: 1px solid #9e6c89;padding: 10px;">

        <table width="100%" cellspacing="0" cellpadding="2" border="0">
          <tr>
            <td width="30%"><img src="/frontstore/images/logo-larose.png" alt="logo" style="width:auto; height:auto; z-index:1000;"></td>
            <td width="25%">
              <b><?=$languages[$current_lang]['header_merchant_name']."</b>: $header_merchant";?>
              <br>
              <b><?=$languages[$current_lang]['header_website'];?></b>: www.larose.bg
            </td>
            <td width="45%">
              <b><?=$languages[$current_lang]['header_customer_name'];?>:</b> <?="$customer_firstname $customer_lastname";?>
              <br>
              <b><?=$languages[$current_lang]['header_delivery_address'];?></b>: <br><b><?=$header_name;?>:</b> <?="$shipping_firstname $shipping_lastname";?> 
                                                                                  <br><b><?=$header_street;?>:</b> <?=$shipping_address_text;?> 
                                                                                  <br><b><?=$header_delivery_date;?>:</b> <?=$shipping_date_formatted;?> <?=$text_delivery_time_interval;?> <?=$shipping_interval_text;?>
                                                                                  <br><b><?=$shipping_cardtext_header;?>:</b> <?=$shipping_cardtext_text;?>
                                                                                  <br><b><?=$header_shipping_card_signature;?>:</b> <?=$shipping_card_signature_text;?>
                                                                                  <br><b><?=$phone_text;?>:</b> <?=$shipping_phone;?>
            </td>
          </tr>
          <tr><td colspan="3">&nbsp;</td></tr>
          <tr>
            <td colspan="3" style="color: #fff;background-color: #9e6c89;padding:10px;"><h3><?=$languages[$current_lang]['text_order_completed_04'];?></h3></td>
          </tr>
          <tr>
            <td colspan="3" style="padding:10px;border: 1px solid #9e6c89;">
              <table width="100%" class="obb_receipt" style="margin-bottom: 10px;">
                <thead>
                  <tr>
                    <td>Track ID</td>
                    <td>Order ID</td>
                    <td>Reference &num;</td>
                    <td>Post Date</td>
                    <td>Transaction ID</td>
                    <td>Auth Code &num;</td>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><?=$payment_id;?></td>
                    <td><?=$order_id;?></td>
                    <td><?=$reference;?></td>
                    <td><?=date("md");?></td>
                    <td><?=$payment_id;?></td>
                    <td><?=$auth_code;?></td>
                  </tr>
                </tbody>
              </table>

              <table width="100%" class="obb_receipt">
                <thead>
                  <tr>
                    <td><?=$header_payment_method?></td>
                    <td><?=$languages[$current_lang]['header_transaction_type']?></td>
                    <td><?=$languages[$current_lang]['header_transaction_amount']?></td>
                    <td><?=$languages[$current_lang]['header_currency']?></td>
                    <td><?=$languages[$current_lang]['header_transaction_date']?></td>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><?=$payment_method_translation;?></td>
                    <td><?=$languages[$current_lang]['header_transaction_type_text']?></td>
                    <td><?=$order_total_with_delivery?> лв.</td>
                    <td>Български лев</td>
                    <td><?=date("d.m.Y");?></td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
          <tr><td colspan="3">&nbsp;</td></tr>
          <tr>
            <td colspan="3" style="color: #fff;background-color: #9e6c89;padding:10px;"><h3><?=$languages[$current_lang]['header_order'];?> &numero;<?=$order_id;?></h3></td>
          </tr>
          <tr>
            <td colspan="3" style="padding:10px;border: 1px solid #9e6c89;">
              <table width="100%" class="obb_receipt">
                <thead>
                  <tr>
                    <td><?=$header_isbn;?></td>
                    <td><?=$header_product;?></td>
                    <td><?=$header_quantity;?></td>
                    <td><?=$header_unit_price;?></td>
                    <td><?=$header_total_price;?></td>
                  </tr>
                </thead>
                <tbody>
                  <?=$message_output;?>
                  <tr>
                    <td colspan="4" class="text-right"><?=$languages[$current_lang]['header_total_products_price'];?>:</td>
                    <td class="text-right"><?=$order_total_formatted;?> лв.</td>
                  </tr>
                  <tr>
                    <td colspan="4" class="text-right"><?=$languages[$current_lang]['header_delivery'];?>:</td>
                    <td class="text-right"><?=$delivery_price?> лв.</td>
                  </tr>
                  <tr>
                    <td colspan="4" class="text-right"><?=$languages[$current_lang]['header_total_price_with_delivery'];?>:</td>
                    <td class="text-right"><?=$order_total_with_delivery?> лв.</td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
          <tr><td colspan="3">&nbsp;</td></tr>
          <tr>
            <td colspan="3" style="color: #fff;background-color: #9e6c89;padding:10px;"><h3>Transmission Information</h3></td>
          </tr>
          <tr>
            <td colspan="3" style="padding:10px;border: 1px solid #9e6c89;">
              <table width="100%" class="obb_receipt">
                <tbody>
                  <tr>
                    <td width="20%" class="text-right"><?=$languages[$current_lang]['header_result_code'];?>:</td>
                    <td width="30%"><?=$languages[$current_lang]['header_result_code_'.$payment_result];?></td>
                    <td width="20%" class="text-right">Response Code:</td>
                    <td width="30%"><?=$response_code;?></td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
          <tr><td colspan="3">&nbsp;</td></tr>
        </table>
        
      </div>
        
      <p>&nbsp;</p>
      
      <table width="100%">
        <tr>
          <td width="60%"><?=$languages[$current_lang]['text_print_bank_receipt'];?></td>
          <td width="40%">
            <a href="javascript:;" onClick="PrintAreaById('obb_receipt')" style="float: right" class="btn btn-primary button outline-outward">
              <?=$languages[$current_lang]['btn_print'];?>
            </a>
          </td>
        </tr>
      </table>
      
      <p>&nbsp;</p>
      <a href="<?=$current_lang;?>/<?php if($current_lang == "bg") echo "общи-условия#terms_cond";else echo "terms-and-conditions#terms_cond"; ?>" style="color: #9e6c89;font-weight: bold;" target="_blank"><?=$languages[$current_lang]['text_return_policy'];?></a>
      
      <p>&nbsp;</p>
      <p></p>
      
      <a href="/" style="display:inline;" class="btn btn-primary button outline-outward">
        <?=$languages[$current_lang]['btn_back_to_index'];?>
      </a>
    </div>
  </div>
<?php
} //if($customer_is_loged && $has_products_in_cart)
else {
    ?>
      <!--<script> window.location.href="/" </script>-->
  <?php
}
