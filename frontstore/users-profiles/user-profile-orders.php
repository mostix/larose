<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  $dirname = dirname(__FILE__);
  
  $customer_id = $_SESSION['customer']['customer_id'];
  $customer_fullname = $_SESSION['customer']['customer_firstname']." ".$_SESSION['customer']['customer_lastname'];

  if(isset($_GET['order_id'])) {
?>
  <div id="order_details" class="user_profile">
    <a href="/<?=$_GET['page'];?>" class="blue">&laquo; <?=$languages[$current_lang]['btn_back_to_orders_list'];?></a>
    <p></p>
<?php
    $order_id = $_GET['order_id'];
    $total_order_price = 0;
    
    $query_customer_order = "SELECT `orders`.`order_delivery_price`,`orders`.`order_total`,`orders`.`order_date_added`,`orders`.`order_comment`,`orders`.`shipping_firstname`,
                                  `orders`.`shipping_lastname`,`orders`.`shipping_address`,`orders`.`shipping_time_interval`,`orders`.`shipping_cardtext`,`orders`.`shipping_card_signature`,
                                  `orders`.`shipping_phone`,`orders`.`shipping_date`,`orders`.`payment_method_id`,`payment_methods_translations`.`payment_method_translation`,
                                  `currencies`.`currency_symbol_left`,`currencies`.`currency_symbol_right`,`order_statuses`.`order_status_name`
                          FROM `orders` 
                          INNER JOIN `order_statuses` ON `order_statuses`.`language_id` = '$current_language_id' AND `order_statuses`.`order_status_id` = `orders`.`order_status_id`
                          INNER JOIN `payment_methods_translations` ON `payment_methods_translations`.`language_id` = '$current_language_id' AND `payment_methods_translations`.`payment_method_id` = `orders`.`payment_method_id`
                          INNER JOIN `currencies` ON `currencies`.`currency_id` = `orders`.`currency_id`
                          WHERE `orders`.`order_id` = '$order_id'";
  //echo $query_customer_orders;
  $result_customer_order = mysqli_query($db_link, $query_customer_order);
  if(!$result_customer_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_customer_order) > 0) {
    $customer_order = mysqli_fetch_assoc($result_customer_order);

    $order_status_name = $customer_order['order_status_name'];
    $order_total_format = number_format($customer_order['order_total'],2,".","");
    $order_delivery_price = number_format($customer_order['order_delivery_price'],2,".","");
    $order_total_with_delivery = number_format($customer_order['order_total']+$customer_order['order_delivery_price'],2,".","");
    $order_date_added = date("d.m.Y H:i", strtotime($customer_order['order_date_added']));
    $order_comment = (!is_null($customer_order['order_comment'])) ? stripslashes($customer_order['order_comment']) : "";
    $shipping_firstname = $customer_order['shipping_firstname'];
    $shipping_lastname = $customer_order['shipping_lastname'];
    $shipping_address = $customer_order['shipping_address'];
    $shipping_interval = $customer_order['shipping_time_interval'];
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
      default: $shipping_interval_text = "";
          break;
    }
    $shipping_cardtext = $customer_order['shipping_cardtext'];
    $shipping_cardtext_text = (empty($shipping_cardtext)) ? "няма" : "$shipping_cardtext";
    $shipping_card_signature = $customer_order['shipping_card_signature'];
    $shipping_phone = $customer_order['shipping_phone'];
    $shipping_date = $customer_order['shipping_date'];
    $currency_symbol_left = $customer_order['currency_symbol_left'];
    $currency_symbol_right = $customer_order['currency_symbol_right'];
    $order_total = $currency_symbol_left.$order_total_format.$currency_symbol_right;
    $payment_method_id = $customer_order['payment_method_id'];
    $payment_method_translation = $customer_order['payment_method_translation'];
    if($payment_method_id == 2 || $payment_method_id == 4) {
      //$payment_method_id == 2 - payment method is with card, so we gonna query the payment_id of the transaction
      //$payment_method_id == 4 - payment method is easypay, so we gonna query the payment_id wich is the 10 digit payment code
      $query_payment_id = "SELECT `payment_id`,`order_payment_date`, `payment_result` FROM `order_payment` WHERE `order_id` = '$order_id'";
      $result_payment_id = mysqli_query($db_link, $query_payment_id);
      if(!$result_payment_id) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_payment_id) > 0) {
        $row_payment_id = mysqli_fetch_assoc($result_payment_id);
        $payment_id = $row_payment_id['payment_id'];
        $payment_result = $row_payment_id['payment_result'];
        $order_payment_date = date("d.m.Y H:m",strtotime("+1 day", strtotime($row_payment_id['order_payment_date']))); 
        
        switch ($payment_method_id) {
          case 2:
            $payment_method_translation .= ", Номер на трансакцията: $payment_id";
            break;
          case 4:
            switch ($payment_result) {
              case "PAID":
                $payment_result_text = "<span class='alert alert-success'>Платена</span>";
                break;
              case "PENDING":
                $payment_result_text = "<span class='alert alert-danger'>Трябва да платите до $order_payment_date</span>";
                break;
              case "EXPIRED":
                $payment_result_text = "<span class='alert alert-danger'>Изтекло време за плащане</span>";
                break;
              case "DENIED":
                $payment_result_text = "<span class='alert alert-danger'>Отказана</span>";
                break;
            }
            $payment_method_translation .= ", Код за плащане: $payment_id $payment_result_text";
            break;
          default:
            break;
        }
      }
    }
?>
      <div class="details_box">
        <h2><?=$languages[$current_lang]['header_order'];?> &#35;<?=$order_id;?></h2>
        <p><b><?=$languages[$current_lang]['header_status'];?>:</b> <?=$order_status_name;?></p>
        <p><b><?=$languages[$current_lang]['header_date'];?>:</b> <?=$order_date_added;?></p>
      </div>
        
      <!--<div class="details_box">-->
      <div class="details_box">
        <h2><?=$languages[$current_lang]['header_order_products_info'];?></h2>
<?php
      $pd_images_folder = "/frontstore/images/products/";
      $query_order_details = "SELECT `order_details`.`order_product_id`,`order_details`.`product_id`,`order_details`.`product_name`,
                                      `order_details`.`product_isbn`,`order_details`.`product_quantity`,`order_details`.`product_price`
                              FROM `order_details` 
                              WHERE `order_details`.`order_id` = '$order_id'";
      //echo $query_order_detailss;
      $result_order_details = mysqli_query($db_link, $query_order_details);
      if(!$result_order_details) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_order_details) > 0) {
?>
        <table id="cart_summary" class="table table-bordered stock-management-on">
          <thead>
            <tr>
              <th class="item" width="10%"><?=$languages[$current_lang]['header_product'];?></th>
              <th class="item" width="40%"></th>
              <th class="item" width="15%"><?=$languages[$current_lang]['header_isbn'];?></th>
              <th class="item" width="15%" class="text-center"><?=$languages[$current_lang]['header_quantity'];?></th>
              <th class="item" width="10%"><?=$languages[$current_lang]['header_unit_price'];?></th>
              <th class="item" width="10%"><?=$languages[$current_lang]['header_total_price'];?></th>
            </tr>
          </thead>
          <tbody>
<?php
        while($order_details = mysqli_fetch_assoc($result_order_details)) {
        //echo "<pre>";print_r($order_details);

          $order_product_id = $order_details['order_product_id'];
          $product_id = $order_details['product_id'];
          $product_name = $order_details['product_name'];
          $product_isbn = $order_details['product_isbn'];
          $product_quantity = $order_details['product_quantity'];
          $product_price = $order_details['product_price'];
          $product_price_text = $currency_symbol_left.number_format($product_price,2,".","").$currency_symbol_right;
          $product_price_total = number_format(($product_price*$product_quantity),2,".","");
          $total_order_price = $total_order_price+($product_price*$product_quantity);

          $gallery_img = get_product_default_image($product_id);
          if(empty($gallery_img)) $gallery_img = "no_image.jpg";
          $gallery_img_exploded = explode(".", $gallery_img);
          $gallery_img_name = $gallery_img_exploded[0];
          $gallery_img_exstension = $gallery_img_exploded[1];
          $gallery_img_thumb = "/frontstore/images/products/".$gallery_img_name."_small_default.".$gallery_img_exstension;
          @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$gallery_img_thumb);
          $thumb_image_dimensions = $thumb_image_params[3];
?>
              <tr>
                <td class="cart_product">
                  <img src="<?=$gallery_img_thumb?>" class="img_table" <?=$thumb_image_dimensions?>>
                </td>
                <td class="cart_description"><?=$product_name?></td>
                <td><?=$product_isbn?></td>
                <td>
                  <div class="product_qty text-center"><?=$product_quantity?></div>
                </td>
                <td class="cart_quantity text-center"><?=$product_price_text?></td>
                <td class="cart_total text-center price">
                  <span class="product_price_total"><?=$product_price_total?></span><span class="currency">&nbsp;лв.</span>
                </td>
              </tr>
<?php
        } //while($order_details)
        $order_total_text = $currency_symbol_left.$order_total_format.$currency_symbol_right;
        $order_delivery_price_text = $currency_symbol_left.$order_delivery_price.$currency_symbol_right;
        $order_total_with_delivery_text = $currency_symbol_left.$order_total_with_delivery.$currency_symbol_right;
?>
            </tbody>
            <tfoot>
              <tr class="cart_total_delivery">
                <td colspan="2"></td>
                <td colspan="3" class="text-right"><?=$languages[$current_lang]['header_delivery'];?></td>
                <td class="price" id="total_shipping">
                  <span class="price"><?=$order_total_text;?></span>
                </td>
              </tr>
              <tr class="cart_total_price">
                <td colspan="2"></td>
                <td colspan="3" class="text-right"><?=$languages[$current_lang]['header_total_products_price'];?></td>
                <td class="price">
                  <span><?=$order_delivery_price_text?></span>
                </td>
              </tr>
              <tr class="cart_total_price">
                <td colspan="2"></td>
                <td colspan="3" class="text-right">
                  <span><?=$languages[$current_lang]['header_total_price_with_delivery'];?></span>
                </td>
                <td class="price" id="total_price_container">
                  <span id="total_order_price"><?=$order_total_with_delivery_text?></span>
                </td>
              </tr>
            </tfoot>
          </table>
<?php
      } //if(mysqli_num_rows($result_order_details) > 0)
    } //if(mysqli_num_rows($result_customer_order)
?>
      </div>
      <!--<div class="details_box">-->
      
      <div class="details_box">
        <h2><?=$languages[$current_lang]['header_payment_method'];?></h2>
        <p><?=$payment_method_translation;?></p>
      </div>
      
      <div class="details_box">
        <h2><?=$languages[$current_lang]['header_delivery_address'];?></h2>
<?php
        $phone_text = $languages[$current_lang]['header_phone'];
        $shipping_cardtext_header = $languages[$current_lang]['header_shipping_cardtext'];
        $shipping_date_formatted = date("d.m.Y",  strtotime($shipping_date));
        $shipping_address_text = str_replace('"', "", $shipping_address);
        $shipping_card_signature_text = (empty($shipping_card_signature)) ? "няма" : "$shipping_card_signature";
        echo "<font style='font-weight:bold;'>Име:</font> $shipping_firstname $shipping_lastname
              <br><font style='font-weight:bold;'>Адрес:</font> $shipping_address_text 
              <br><font style='font-weight:bold;'>Дата на доставка:</font> $shipping_date_formatted във времеви интервал $shipping_interval_text часа
              <br><font style='font-weight:bold;'>$shipping_cardtext_header:</font> $shipping_cardtext_text
              <br><font style='font-weight:bold;'>Подпис на картичката:</font> $shipping_card_signature_text
              <br><font style='font-weight:bold;'>$phone_text:</font> $shipping_phone";
 
?>
      </div>
      <!--<div class="details_box">-->
<?php
  $query_order_invoice = "SELECT `order_invoice`.`invoice_firstname`,`order_invoice`.`invoice_lastname`,`order_invoice`.`invoice_company_name`,
                                `order_invoice`.`invoice_bulstat`,`order_invoice`.`invoice_accountable_person`,`order_invoice`.`invoice_street`,
                                `order_invoice`.`invoice_city`
                          FROM `order_invoice`
                          WHERE `order_id` = '$order_id'";
  //echo $query_order_invoices;
  $result_order_invoice = mysqli_query($db_link, $query_order_invoice);
  if(!$result_order_invoice) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_order_invoice) > 0) {
?>
    <div class="details_box">
      <h2><?=$languages[$current_lang]['header_invoice_address'];?></h2>
<?php

    $order_invoice_details = mysqli_fetch_assoc($result_order_invoice);
    
    $invoice_firstname = $order_invoice_details['invoice_firstname'];
    $invoice_lastname = $order_invoice_details['invoice_lastname'];
    $invoice_company_name = $order_invoice_details['invoice_company_name'];
    $invoice_bulstat = $order_invoice_details['invoice_bulstat'];
    $invoice_accountable_person = $order_invoice_details['invoice_accountable_person'];
    $invoice_street = $order_invoice_details['invoice_street'];
    $invoice_city = $order_invoice_details['invoice_city'];
    
    $header_accountable_person = $languages[$current_lang]['header_accountable_person'];
    $header_bulstat = $languages[$current_lang]['header_bulstat'];
      
    echo "<p>$invoice_firstname $invoice_lastname<br>$invoice_company_name<br>$header_accountable_person $invoice_accountable_person
          <br>$header_bulstat $invoice_bulstat<br>$invoice_street, $invoice_city</p>";
?>
    </div>
    <!--<div class="details_box">-->
<?php
  }
  
    if(!empty($order_comment)) {
?>
      <div class="details_box">
        <h2><?=$languages[$current_lang]['header_order_comment'];?></h2>
        <p><?=$order_comment;?></p>
      </div>
<?php
    }
?>
    </div>
    <!--<div id="orders_list"> -->
<?php
    } //if(isset($_GET['order_id']))
    else {
?>
    <div id="orders_list" class="form">
<?php
      $query_customer_orders = "SELECT `orders`.`order_id`,`orders`.`order_total`,`orders`.`order_status_id`,`orders`.`order_date_added`,
                                        `currencies`.`currency_symbol_left`,`currencies`.`currency_symbol_right`,`order_statuses`.`order_status_name`,
                                        `payment_methods_translations`.`payment_method_translation`  
                                FROM `orders` 
                                INNER JOIN `payment_methods_translations` ON `payment_methods_translations`.`language_id` = '$current_language_id' AND `payment_methods_translations`.`payment_method_id` = `orders`.`payment_method_id`
                                INNER JOIN `order_statuses` ON `order_statuses`.`language_id` = '$current_language_id' AND `order_statuses`.`order_status_id` = `orders`.`order_status_id`
                                INNER JOIN `currencies` ON `currencies`.`currency_id` = `orders`.`currency_id`
                                WHERE `customer_id` = '$customer_id'
                                ORDER BY `orders`.`order_id` DESC";
      //echo $query_customer_orders;
      $result_customer_orders = mysqli_query($db_link, $query_customer_orders);
      if(!$result_customer_orders) echo mysqli_error($db_link);
      $customer_orders_count = mysqli_num_rows($result_customer_orders);
      if($customer_orders_count > 0) {
?>
        <table>
          <thead>
            <tr>
              <th>&numero;</th>
              <th><?=$languages[$current_lang]['header_date'];?></th>
              <th><?=$languages[$current_lang]['header_status'];?></th>
              <th><?=$languages[$current_lang]['header_summary'];?></th>
              <th><?=$languages[$current_lang]['header_payment_method'];?></th>
              <th><?=$languages[$current_lang]['header_view_details'];?></th>
            </tr>
          </thead>
          <tbody>
<?php
        while($customer_order = mysqli_fetch_assoc($result_customer_orders)) {

          $order_id = $customer_order['order_id'];
          $order_status_id = $customer_order['order_status_id'];
          $status_class = ($order_status_id == 5) ? ' class="complete"' : "";
          $order_status_name = $customer_order['order_status_name'];
          $order_total_format = number_format($customer_order['order_total'],2,".",".");
          $order_date_added = date("d.m.Y",strtotime($customer_order['order_date_added']));
          $payment_method_translation = $customer_order['payment_method_translation'];
          $currency_symbol_left = $customer_order['currency_symbol_left'];
          $currency_symbol_right = $customer_order['currency_symbol_right'];
          $order_total = $currency_symbol_left.$order_total_format.$currency_symbol_right;
?>
          <tr>
            <td><?=$order_id;?></td>
            <td><?=$order_date_added;?></td>
            <td<?=$status_class;?>><?=$order_status_name;?></td>
            <td><?=$order_total;?></td>
            <td><?=$payment_method_translation;?></td>
            <td><a href="/<?=$_GET['page']."?order_id=".$order_id;?>" class="blue"><?=$languages[$current_lang]['btn_view_details'];?></a></td>
          </tr>
<?php
      }
?>
          </tbody>
        </table>
<?php
      } //if($customer_orders_count > 0)
      else {
        echo '<p class="alert alert-warning">'.$languages[$current_lang]['text_no_orders_yet'].'</p>';
      }
?>
    </div>
    <!--col-lg-9 -->
<?php
    }
?> 