<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
//  echo "<pre>";print_r($_POST);
//  echo "<br><pre>";print_r($_SESSION);exit;
  
  $shopping_cart = array();
  $has_products_in_cart = false;

  $customer_id = 0;
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
  
  if(isset($_POST['customer_order']) || isset($_POST['quick_order'])) {
    $_SESSION['post'] = $_POST;
  }
  
  $error = "";
  
  if(isset($_GET['error']) && !empty($_GET['error'])) {
    $error = $_GET['error'];
  }
  else {
    if(isset($_REQUEST['error']) && !empty($_REQUEST['error'])) {
      $error = $_REQUEST['error'];
    }
  }
  
  //if($customer_is_loged && $has_products_in_cart) {
  if(true) {
    
    $order_id = $_SESSION['order_id'];
    $order_status_id = 10; //Failed
    $query_order = "UPDATE `orders` SET `order_status_id`='$order_status_id' WHERE `order_id` = '$order_id'";
    //echo $query_order;
    mysqli_query($db_link, $query_order);
    
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
    
    $store_id = 1;
    $delivery_price = $_SESSION['post']['delivery_price'];
      
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
    $order_status_id = 10; //10 Failed
    $currency_id = 4; // BGN
    $order_currency_code = "BGN";
    $order_currency_exchange_rate = "1.00000000";
    $order_ip = $_SERVER['REMOTE_ADDR'];
    //$order_ip = $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']);
    if(isset($_SESSION['post']['payment_method'])) {
      $payment_method =  $_SESSION['post']['payment_method'];
      $payment_method_id = $_SESSION['post']['payment'][$payment_method][0];
      $payment_method_translation = $_SESSION['post']['payment'][$payment_method][1];
    }
    $order_products_quantity = 0;
    
    print_html_shopping_cart_progress();
    
    $error_text_01 = $languages[$current_lang]['text_order_error'];
    $error_text_02 = (!empty($error)) ? '<p>'.$error_text_01.': <span class="error">'.$error.'</span></p>' : "";
?>
  <div class="container marketing category cart" style="margin-top: 0;padding-top: 0;">
    <div class="row">
      <p style="margin: 10px 0 20px;padding: 0;"><?=$languages[$current_lang]['text_order_canceled_01'];?></p>
      <?=$error_text_02;?>
      <p><?=$languages[$current_lang]['header_error_message'];?>: <span class="error"><?=$languages[$current_lang]['text_error_message_paypal'];?></span></p>
    </div>

    <table class="table_cart delivery_select">
      <tr>
        <td width="20%">
          <a href="/<?=$current_lang;?>/shopping-cart/shopping-cart-addresses" class="button-exclusive btn btn-outline btn-sm">
            <?=$languages[$current_lang]['btn_back'];?>
          </a>
        </td>
        <td width="60%"></td>
        <td width="20%">

        </td>
      </tr>
    </table>
  </div>
<?php

  }  //if($customer_is_loged && $has_products_in_cart)
  else {
?>
    <script> window.location.href="/" </script>
<?php
  }