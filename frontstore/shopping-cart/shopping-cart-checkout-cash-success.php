<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
//  echo "<pre>";print_r($_POST);exit;
//  echo "<br><pre>";print_r($_SESSION);exit;
 
  $has_products_in_cart = false;
  $shopping_cart = array();
  if(isset($_SESSION['cart'])) {
    $shopping_cart = $_SESSION['cart'];
    $has_products_in_cart = true;
  }
    
  if($has_products_in_cart) {
    
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
    
    $store_id = 1;
    $delivery_price = $_POST['delivery_price'];
      
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
      case "917":
        $shipping_interval_text = "9:00-17:00";
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
    //$order_ip = $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']);
    if(isset($_POST['payment_method'])) {
      $payment_method =  $_POST['payment_method'];
      $payment_method_id = $_POST['payment'][$payment_method][0];
      $payment_method_translation = $_POST['payment'][$payment_method][1];
    }
    $order_products_quantity = 0;
    
//    mysqli_query($db_link,"BEGIN");
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
//  if(mysqli_affected_rows($db_link) <= 0) {
//    echo $languages[$current_lang]['sql_error_insert']." - orders ".mysqli_error($db_link);
//    mysqli_query($db_link,"ROLLBACK");
//    exit;
//  }

  $order_id = mysqli_insert_id($db_link);

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
//    if(mysqli_affected_rows($db_link) <= 0) {
//      echo $languages[$current_lang]['sql_error_insert']." - order_invoice ".mysqli_error($db_link);
//      mysqli_query($db_link,"ROLLBACK");
//      exit;
//    }
  }

//  $to_merchant = "mony_koleg@abv.bg";
//  $to_merchant = "idimitrov@eterrasystems.com";
//  $to_merchant = "petko_iordanov@abv.bg";
//  $to_customer = "laroz2@abv.bg";
//  $to_customer = "support@larose.bg";
  $to_merchant = "sales@larose.bg";
  $to_customer = $customer_email;

//  $subject_merchant = $languages[$current_lang]['email_order_subject_text'];
  $subject_merchant = "Онлайн поръчка (www.larose.bg)";
  $text_email_order_01 = $languages[$current_lang]['text_email_order_01'];
  $text_email_order_02 = $languages[$current_lang]['text_email_order_02'];
  $text_email_order_03 = $languages[$current_lang]['text_email_order_03'];
  
  $subject_customer = "$text_email_order_01 $order_id $text_email_order_02";

  $message_merchant = "";
  $message_customer = "";

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
                                                    VALUES('',
                                                          '$order_id',
                                                          '$product_id',
                                                          '$product_name_db',
                                                          '$product_isbn',
                                                          '$product_qty',
                                                          '$product_price')";
    $all_queries .= "<br>".$query_order_products;
    $result_order_products = mysqli_query($db_link, $query_order_products);
    
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
  } //foreach($shopping_cart)

  $query_update_order = "UPDATE `orders` SET `order_products_quantity` = '$order_products_quantity' WHERE `order_id` = '$order_id'";
  $all_queries .= "<br>\n".$query_update_order;
  $result_update_order = mysqli_query($db_link, $query_update_order);
  if(!$result_update_order) {
    echo $languages[$current_lang]['sql_error_update']." - `order_products_quantity` 2 ".mysqli_error($db_link);
  }
  
  $payment_id = 0; // no payment_id when payment type is on delivery, only when paying with card
    
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
                                                      NULL, 
                                                      NOW())";
  $all_queries .= "<br>".$query_order_payment;
  $result_order_payment = mysqli_query($db_link, $query_order_payment);
//  if(mysqli_affected_rows($db_link) <= 0) {
//    echo $languages[$current_lang]['sql_error_insert']." - `order_payment` ".mysqli_error($db_link);
//    mysqli_query($db_link,"ROLLBACK");
//    exit;
//  }

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
                                  <font style='color:#FFF;font-size: 12pt'> $header_delivery_address</font>
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
  
  if(mail($to_merchant, $subject_merchant, $message_merchant, $headers_merchant) && mail($to_customer, $subject_customer, $message_customer, $headers_customer,'-fsales@larose.bg')) {
    //echo $all_queries;mysqli_query($db_link, "ROLLBACK");

    //mysqli_query($db_link, "ROLLBACK");
    //mysqli_commit($db_link);
    
    $query_review = "SELECT `customers_review_id` FROM `customers_reviews` WHERE `customer_email` = '$to_customer'";
    $result_review = mysqli_query($db_link, $query_review);
    //if(!$result_review) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_review) <= 0) {
      $query_insert = "INSERT INTO `customers_reviews`(`customers_review_id`, `customer_email`, `email_for_review_sended`)
                                                VALUES (NULL,'$to_customer','0')";
      $result_insert = mysqli_query($db_link, $query_insert);
    }
    
    $order_was_successfull = true;
    unset($_SESSION['customer']['has_invoice_address']);
    unset($_SESSION['customer']['shipping_address']);
    unset($_SESSION['customer']['invoice_address']);
    unset($_SESSION['cart']);
  }
  else {
    //print_r(error_get_last());
    echo $languages[$current_lang]['error_order_customer_send_email_fail'];
    $order_was_successfull = true;
  }
  
  print_html_shopping_cart_progress();

  if(isset($order_was_successfull)) {
    $total_order_price = "";
    $order_total_with_delivery = $delivery_price;
?>
    <h1 class="alert-success alert" style="margin: 10px 0 20px;"><?=$languages[$current_lang]['text_order_completed_01'];?></h1>
    <p><?=$languages[$current_lang]['text_order_completed_11'];?></p>
    <p><?=$languages[$current_lang]['text_order_completed_02'].$languages[$current_lang]['text_order_completed_03'];?></p>
    <h2><?=$languages[$current_lang]['text_order_completed_04'];?>:</h2>
    <!--<h2><?=$languages[$current_lang]['text_order_completed_06'];?>:</h2>-->

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
      $product_price_total = number_format(($product_price*$product_qty),2,".",".");
      $total_order_price = $total_order_price+($product_price*$product_qty);

      $message_3 = "<tr>
                      <td><a href='$product_url' target='_blank'><img src='$product_img_src' $product_image_dimensions></a></td>
                      <td>$product_name</td>
                      <td><font style='font-weight:bold;font-size: 14px'>$product_isbn</font></td>
                      <td>$product_qty</td>
                      <td>$product_price лв.</td>
                      <td>$product_price_total лв.</td>
                    </tr>";
      echo $message_3;
    } //foreach($shopping_cart as $products)
    $order_total_with_delivery += $total_order_price; 
    $total_order_price = number_format($total_order_price,2,".",".");
    $order_total_with_delivery = number_format($order_total_with_delivery,2,".",".");
?>
    </tbody>
    <tfoot>
      <tr class="cart_total_delivery">
        <td colspan="2"></td>
        <td colspan="3" class="text-right"><?=$languages[$current_lang]['header_delivery'];?></td>
        <td class="price" id="total_shipping">
          <span class="price"><?=$delivery_price_formatted?></span><span class="currency">&nbsp;лв.</span>
        </td>
      </tr>
      <tr class="cart_total_price">
        <td colspan="2"></td>
        <td colspan="3" class="text-right"><?=$languages[$current_lang]['header_total_products_price'];?></td>
        <td class="price">
          <span><?=$total_order_price?></span><span class="currency">&nbsp;лв.</span></span>
        </td>
      </tr>
      <tr class="cart_total_price">
        <td colspan="2"></td>
        <td colspan="3" class="text-right">
          <span><?=$languages[$current_lang]['header_total_price_with_delivery'];?></span>
        </td>
        <td class="price" id="total_price_container">
          <span id="total_order_price"><?=$order_total_with_delivery?></span><span class="currency">&nbsp;лв.</span></span>
        </td>
      </tr>
    </tfoot>
  </table>
<?php
  } //if(isset($order_was_successfull))
  else {
?>
    <h2 class="warning"><?=$languages[$current_lang]['text_order_was_not_successfully'];?></h2>
<?php
  }

  }  //if($has_products_in_cart)
  else {
?>
    <p class="alert alert-warning"><?=$languages[$current_lang]['text_shopping_cart_inactive_session'];?></p>
    <!--<script> window.location.href="/" </script>-->
<?php
  }
