<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
//  echo "<pre>";print_r($_POST);
//  echo "<pre>";print_r($_SESSION);echo "</pre>";exit;

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
  
  if($has_products_in_cart) {
  //if(true) {
      
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
    
    //print_object_for_debug($_SESSION['post']['transactions']);
    
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
      }
?>
        <h1 class="alert-success alert" style="margin: 10px 0 20px;"><?=$languages[$current_lang]['text_order_completed_01'];?></h1>
        <p><?=$languages[$current_lang]['text_order_completed_11'];?></p>
        <p><?=$languages[$current_lang]['text_order_completed_02'].$languages[$current_lang]['text_order_completed_03'];?></p>
<?php
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
            <td colspan="3" style="color: #fff;background-color: #9e6c89;padding:10px;"><h3><?=$languages[$current_lang]['header_order'];?> &numero;<?=$_SESSION['order_id'];?></h3></td>
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
        </table>
        
      </div>
      
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
      ?><script> window.location.href="/" </script><?php
  }