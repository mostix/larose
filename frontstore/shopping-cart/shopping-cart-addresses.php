<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//echo "<pre>";print_r($_POST);
//echo "<pre>";print_r($_SERVER);echo "</pre>";
//unset($_SESSION['cart']['order_login']);

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

$errors = array();

print_html_shopping_cart_progress();

if(!$has_products_in_cart) {
?>
<p id="emptyCartWarning" class="alert alert-warning"><?=$languages[$current_lang]['header_shopping_cart_is_empty'];?></p>
<?php
}
else {
  
  $url = "/$current_lang/shopping-cart/borica-request";
  
  if($customer_is_loged) {
    // customer = 4 Iliyan for testing purpose
    // customer = 29 Petko for testing purpose
?>
<form name="form_customer_order" id="form_customer_order" class="form form_place_order" action="<?=$url;?>" method="post">
  <p class="text-center mb-0"><?=$languages[$current_lang]['text_free_order_01'];?></p>
  <p class="text-center mb-0"><?=$languages[$current_lang]['text_free_order_02'];?></p>
  <p class="text-center mb-20"><?=$languages[$current_lang]['text_free_order_03'];?></p>
  <?php if($customer_id == 4 || $customer_id == 29) { ?>
  <h4 class="text-center mb-25">КАК ЖЕЛАЕТЕ ДА ПОЛУЧИТЕ ВАШАТА ПОРЪЧКА?</h4>
  <div class="row mb-25">
    <div class="col-sp-12 col-xs-12 col-sm-6 col-md-6 choose_order_type pr-0">
        <span class="link-arrow fa fa-angle-right"></span>
        <a href="javascript:;">
            Желая да бъде доставена
        </a>
    </div>
    <div class="col-sp-12 col-xs-12 col-sm-6 col-md-6 choose_order_type pr-0">
        <span class="link-arrow fa fa-angle-right"></span>
        <a href="javascript:;">
            Ще взема поръчката от магазина Ви, ще платя на място<br> и ЩЕ ПОЛУЧА 10% ОТСТЪПКА
        </a>
    </div>
  </div>
  <?php } ?>
  <div class="row">
    <div class="col-sp-12 col-xs-12 col-sm-6 col-md-6">
      <div class="form" style="padding-left: 0">
        <fieldset>
          <legend><?=$languages[$current_lang]['header_recipients_data'];?></legend>

          <div>
            <label for="shipping_firstname"><?=$languages[$current_lang]['header_firstname'];?><span class="red">*</span></label>
            <input type="text" id="shipping_firstname" class="fa" placeholder="&#xf007;" name="shipping_firstname" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="shipping_lastname"><?=$languages[$current_lang]['header_lastname'];?><span class="red">*</span></label>
            <input type="text" id="shipping_lastname" class="fa" placeholder="&#xf007;" name="shipping_lastname" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="shipping_phone"><?=$languages[$current_lang]['header_phone'];?><span class="red">*</span></label>
            <input type="text" id="shipping_phone" class="fa" placeholder="&#xf095;" name="shipping_phone" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="shipping_address"><?=$languages[$current_lang]['header_site_name'];?><span class="red">*</span></label>
            <input type="text" id="shipping_site" class="fa" placeholder="&#xf0d1;" name="shipping_site" />
            <input type="hidden" name="shipping_site_id" id="shipping_site_id" value="" />
            <input type="hidden" name="shipping_site_name" id="shipping_site_name" value="" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <p><i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['text_info_site_name'];?></i></p>
          <p class="red"><?=$languages[$current_lang]['text_info_site_name_delivery'];?></p>
          
          <div>
            <label for="shipping_address"><?=$languages[$current_lang]['header_delivery_address'];?><span class="red">*</span></label>
            <input type="text" id="shipping_address" class="fa" placeholder="&#xf0d1;" name="shipping_address" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <div class="clearfix"></div>
          <?php
            $today = date("Y-m-d");
            $tomorrow = date("Y-m-d", strtotime('+1 day', strtotime($today)));
            $current_hour = date('Hi', time());
            if($current_hour < 1600) {
              $shipping_date = $today;
              $delivery_price = 10.00;
            }
            else {
              $shipping_date = $tomorrow;
              $delivery_price = 5.00;
            }
          ?>
          <div>
            <label for="shipping_date"><?=$languages[$current_lang]['header_delivery_date'];?><span class="red">*</span></label>
            <input type="text" id="shipping_date" name="shipping_date" readonly class="datepicker fa" placeholder="&#xf073;" value="<?=$shipping_date;?>" onchange="GetTimeIntervals(this.value)" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <p class="date_info hidden"><i class="fa fa-info-circle"></i><i class="red"><?=$languages[$current_lang]['text_info_delivery_price'];?></i></p>
          <div class="clearfix"></div>
          <div id="time_interval">
            <?php
              print_html_shipping_time_interval_form($shipping_date);
            ?>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="shipping_card_signature"><?=$languages[$current_lang]['header_card_signature'];?><span class="red">*</span></label>
            <input type="text" id="shipping_card_signature" name="shipping_card_signature" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <p><i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['text_info_signature'];?></i></p>
          <div class="clearfix"></div>

          <div>
            <label for="shipping_cardtext"><?=$languages[$current_lang]['header_greeting_text'];?></label>
            <textarea id="shipping_cardtext" name="shipping_cardtext" class="fa" placeholder="&#xf003;"></textarea>
          </div>
          <div class="clearfix">&nbsp;</div>
        </fieldset>
      </div>
    </div>

    <!-- user registration and fields -->
    <div class="col-sp-12 col-xs-12 col-sm-6 col-md-6">

      <div class="form no_padding">
        <fieldset>
          <legend><?=$languages[$current_lang]['header_payment_method'];?></legend>
          <div style="margin-bottom: 10px;">
<?php
        $query_payment_methods = "SELECT `payment_methods`.`payment_method_id`,`payment_methods`.`payment_method`,`payment_methods`.`payment_method_is_default`,
                                         `payment_methods_translations`.`payment_method_translation`
                                    FROM `payment_methods`
                              INNER JOIN `payment_methods_translations` ON `payment_methods_translations`.`payment_method_id` = `payment_methods`.`payment_method_id`
                                   WHERE `payment_methods_translations`.`language_id` = '$current_language_id'
                                     AND `payment_methods`.`payment_method_is_active` = '1'";
        //echo $query_payment_methods."<br>";
        $result_payment_methods = mysqli_query($db_link, $query_payment_methods);
        if(!$result_payment_methods) echo mysqli_error($db_link);
        $count_payment_methods = mysqli_num_rows($result_payment_methods);
        if($count_payment_methods > 0) {
          while($row_payment_methods = mysqli_fetch_assoc($result_payment_methods)) {
            $payment_method_id = $row_payment_methods['payment_method_id'];
            $payment_method = $row_payment_methods['payment_method'];
            $payment_method_is_default = $row_payment_methods['payment_method_is_default'];
            $payment_method_translation = stripslashes($row_payment_methods['payment_method_translation']);
            $checked = ($payment_method_is_default == 1) ? "checked='checked'" : "";
?>
            <label for="payment_method_<?=$payment_method_id?>" style="display: inline-block">
              <input type='radio' name='payment_method' id="payment_method_<?=$payment_method_id?>" class='payment_method' value='<?=$payment_method?>' <?=$checked?>>
              <?=$payment_method_translation?>
            </label> &nbsp;
            <input type='hidden' name='payment[<?=$payment_method?>][0]' value='<?=$payment_method_id?>'>
            <input type='hidden' name='payment[<?=$payment_method?>][1]' value='<?=$payment_method_translation?>'>
<?php
          }
        }
?>
          </div>
          <?php print_html_payment_methods (); ?>
        </fieldset>
      </div>

    </div>

    <div class="form no_padding">
      <fieldset>
        <legend><?=$languages[$current_lang]['header_additional_orders_note'];?></legend>
        <textarea name="order_comment" id="order_comment"></textarea>
      </fieldset>
    </div>
    <p class="clearfix">&nbsp;</p>

    <table id="cart_summary" class="table table-bordered stock-management-on">
      <thead>
        <tr>
          <th class="item" width="10%"><?=$languages[$current_lang]['header_product'];?></th>
          <th class="item hidden-xs hidden-sm" width="45%"><?=$languages[$current_lang]['header_description'];?></th>
          <th class="item" width="15%" class="text-center"><?=$languages[$current_lang]['header_quantity'];?></th>
          <th class="item" width="15%"><?=$languages[$current_lang]['header_unit_price'];?></th>
          <th class="item" width="15%"><?=$languages[$current_lang]['header_total_price'];?></th>
        </tr>
      </thead>
      <tbody>
<?php
    $total_order_price = 0;
    $key = 0;
    $descr = "";

    foreach($shopping_cart as $products) {

      $product_id = $products['product_id'];
      $product_isbn = $products['product_isbn'];
      $product_price = $products['product_price'];
      $product_name = $products['product_name'];
      $descr .= ($key == 0) ? $product_name : ", $product_name";
      $product_url = $products['product_url'];
      $product_img_src = $products['product_img_src'];
      $product_image_params = @getimagesize($_SERVER['DOCUMENT_ROOT'].$product_img_src);
      $product_image_dimensions = $product_image_params[3];
      $product_qty = $products['product_qty'];
      $product_price_total = number_format(($product_price*$product_qty),2,".","");
      $total_order_price = $total_order_price+($product_price*$product_qty);
?>
        <tr id="product_<?=$product_id;?>">
          <td class="cart_product">
            <a href="<?=$product_url;?>" target="_blank">
              <img src="<?=$product_img_src?>" class="img_table" <?=$product_image_dimensions?>>
            </a>
          </td>
          <td class="cart_description hidden-xs hidden-sm"><?=$product_name?></td>
          <td>
            <div class="product_qty text-center"><?=$product_qty?></div>
          </td>
          <td class="cart_unit text-center">
            <ul class="price" id="product_price_6_31_0">
              <li class="price product_price"><?=$product_price?><span class="currency">&nbsp;лв.</span></li>
            </ul>
          </td>
          <td class="cart_total text-center price">
            <span class="product_price_total"><?=$product_price_total?></span><span class="currency">&nbsp;лв.</span>
          </td>
        </tr>
  <?php
      $key++;
    }
    $total_order_price_fomatted = number_format($total_order_price,2,".","");
    //echo $delivery_price;
    if($total_order_price > 50 && $shipping_date != $today) $delivery_price =  0.00; 
    $total_order_price_with_delivery = number_format($total_order_price+$delivery_price,2,".","");
  ?>
      </tbody>
      <tfoot>
        <tr class="cart_total_delivery">
          <td class="hidden-xs hidden-sm"></td>
          <td colspan="3" class="text-right"><?=$languages[$current_lang]['header_delivery'];?></td>
          <td class="price" id="total_shipping">
            <span class="price"><?=number_format($delivery_price,2,".","")?></span><span class="currency">&nbsp;лв.</span>
            <input type="hidden" id="product_descriptions" name="product_descriptions" value="<?=$descr?>" />
            <input type="hidden" id="delivery_price" name="delivery_price" value="<?=number_format($delivery_price,2,".","")?>" />
          </td>
        </tr>
        <tr class="cart_total_price">
          <td class="hidden-xs hidden-sm"></td>
          <td colspan="3" class="text-right"><?=$languages[$current_lang]['header_total_products_price'];?></td>
          <td class="price">
            <span><?=$total_order_price_fomatted?></span><span class="currency">&nbsp;лв.</span></span>
            <input type="hidden" id="order_total" name="order_total" value="<?=$total_order_price_fomatted?>">
          </td>
        </tr>
        <tr class="cart_total_price">
          <td class="hidden-xs hidden-sm"></td>
          <td colspan="3" class="text-right">
            <span><?=$languages[$current_lang]['header_total_price_with_delivery'];?></span>
          </td>
          <td class="price" id="total_price_container">
            <span id="total_order_price"><?=$total_order_price_with_delivery?></span><span class="currency">&nbsp;лв.</span></span>
          </td>
        </tr>
      </tfoot>
    </table>

    <div class="accept_terms_box">
      <input type="checkbox" name="accept_terms" id="accept_terms" /> 
      <label for="accept_terms" style="display: inline-block"><?=$languages[$current_lang]['header_accept_terms_and_conditions_01'];?></label>
      <a href="/<?=$current_lang;?>/<?php if($current_lang == "bg") echo "общи-условия";else echo "terms-and-conditions"; ?>#terms_cond" style="text-decoration: underline;" target="_blank">
        <?=$languages[$current_lang]['header_accept_terms_and_conditions_02'];?>
      </a>
    </div>
    <p class="clearfix">&nbsp;</p>

    <p class="cart_navigation clearfix">
      <a href="/<?=$current_lang; ?>/shopping-cart/shopping-cart-overview" class="btn btn-outline">
        <?=$languages[$current_lang]['btn_back']; ?>
      </a>
      <?php
      if ($customer_is_loged) {
        ?>
        <button type="submit" name="customer_order" class="btn btn-outline pull-right place_order">
          <?=$languages[$current_lang]['btn_order'];?>
        </button> 
        <?php
      }
      ?>
    </p>
  </div>
</form>
<?php
  } //if($customer_is_loged)
  else {
    //guest
?>
  <div class="row" style="text-align: center;">
    <div class="col-sp-12 col-xs-12 col-sm-4 col-md-4">
      <div class="option_box">
        <h3><?=$languages[$current_lang]['header_registered_cumstomer'];?></h3>
        <p><?=$languages[$current_lang]['text_registered_cumstomer'];?></p>
        <a href="javascript:;" class="login_btn button btn btn-outline"><?=$languages[$current_lang]['btn_login']; ?></a>
      </div>
    </div>

    <div class="col-sp-12 col-xs-12 col-sm-4 col-md-4">
      <div class="option_box">
        <h3><?=$languages[$current_lang]['header_new_cumstomer'];?></h3>
        <p><?=$languages[$current_lang]['text_new_cumstomer'];?></p>
        <a href="/<?=$current_lang; ?>/shopping-cart/shopping-cart-registration" class="button btn btn-outline">
          <?=$languages[$current_lang]['btn_sign_up']; ?>
        </a>
      </div>
    </div>

    <div class="col-sp-12 col-xs-12 col-sm-4 col-md-4">
      <div class="option_box">
        <h3><?=$languages[$current_lang]['header_quik_order'];?></h3>
        <p><?=$languages[$current_lang]['text_quik_order'];?></p>
        <a href="javascript:;" class="button btn btn-outline quik_order">
          <?=$languages[$current_lang]['btn_order'];?>
        </a>
      </div>
    </div>
  </div>
  <p class="clearfix">&nbsp;</p>

<form name="form_quick_order" id="form_quick_order" class="form form_place_order" action="<?=$url;?>" method="post">
  <p class="text-center"><?=$languages[$current_lang]['text_free_order_01'];?></p>
  <p class="text-center"><?=$languages[$current_lang]['text_free_order_02'];?></p>
  <p class="text-center"><?=$languages[$current_lang]['text_free_order_03'];?></p>
  <p>&nbsp;</p>
  
  <div class="row">
    <div class="col-sp-12 col-xs-12 col-sm-6 col-md-6">
      <div class="form" style="padding-left: 0">
        <fieldset>
          <legend><?=$languages[$current_lang]['header_recipients_data'];?></legend>

          <div>
            <label for="shipping_firstname"><?=$languages[$current_lang]['header_firstname'];?><span class="red">*</span></label>
            <input type="text" id="shipping_firstname" class="fa" placeholder="&#xf007;" name="shipping_firstname" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="shipping_lastname"><?=$languages[$current_lang]['header_lastname'];?><span class="red">*</span></label>
            <input type="text" id="shipping_lastname" class="fa" placeholder="&#xf007;" name="shipping_lastname" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="shipping_phone"><?=$languages[$current_lang]['header_phone'];?><span class="red">*</span></label>
            <input type="text" id="shipping_phone" class="fa" placeholder="&#xf095;" name="shipping_phone" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="shipping_address"><?=$languages[$current_lang]['header_site_name'];?><span class="red">*</span></label>
            <input type="text" id="shipping_site" class="fa" placeholder="&#xf0d1;" name="shipping_site" />
            <input type="hidden" name="shipping_site_id" id="shipping_site_id" value="" />
            <input type="hidden" name="shipping_site_name" id="shipping_site_name" value="" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <p><i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['text_info_site_name'];?></i></p>
          <p class="red"><?=$languages[$current_lang]['text_info_site_name_delivery'];?></p>
          
          <div>
            <label for="shipping_address"><?=$languages[$current_lang]['header_delivery_address'];?><span class="red">*</span></label>
            <input type="text" id="shipping_address" class="fa" placeholder="&#xf0d1;" name="shipping_address" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <!--<p><i class="fa fa-info-circle"></i><i>Само за гр. София</i></p>-->
          <div class="clearfix"></div>
          
          <?php
            $today = date("Y-m-d");
            $tomorrow = date("Y-m-d", strtotime('+1 day', strtotime($today)));
            $current_hour = date('Hi', time());
            if($current_hour < 1600) {
              $shipping_date = $today;
              $delivery_price = 10.00;
            }
            else {
              $shipping_date = $tomorrow;
              $delivery_price = 5.00;
            }
          ?>
          <div>
            <label for="shipping_date"><?=$languages[$current_lang]['header_delivery_date'];?><span class="red">*</span></label>
            <input type="text" id="shipping_date" name="shipping_date" readonly class="datepicker fa" placeholder="&#xf073;" value="<?=$shipping_date;?>" onchange="GetTimeIntervals(this.value)" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <p class="date_info hidden"><i class="fa fa-info-circle"></i><i class="red"><?=$languages[$current_lang]['text_info_delivery_price'];?></i></p>
          <div class="clearfix"></div>
          <div id="time_interval">
            <?php
              print_html_shipping_time_interval_form($shipping_date);
            ?>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="shipping_card_signature"><?=$languages[$current_lang]['header_card_signature'];?><span class="red">*</span></label>
            <input type="text" id="shipping_card_signature" name="shipping_card_signature" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <p><i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['text_info_signature'];?></i></p>
          <div class="clearfix"></div>

          <div>
            <label for="shipping_cardtext"><?=$languages[$current_lang]['header_greeting_text'];?></label>
            <textarea id="shipping_cardtext" name="shipping_cardtext" class="fa" placeholder="&#xf003;"></textarea>
          </div>
          <div class="clearfix">&nbsp;</div>
        </fieldset>
      </div>
    </div>

    <!-- user registration and fields -->
    <div class="col-sp-12 col-xs-12 col-sm-6 col-md-6">
      <div class="form no_padding">
        <fieldset>
          <legend><?=$languages[$current_lang]['header_senders_data'];?></legend>
          <div>
            <label for="customer_address_firstname"><?=$languages[$current_lang]['header_customer_firstname'];?><span class="red">*</span></label>
            <input type="text" name="customer_address_firstname" class="customer_address_firstname fa" placeholder="&#xf007;" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="customer_address_lastname"><?=$languages[$current_lang]['header_customer_lastname'];?><span class="red">*</span></label>
            <input type="text" name="customer_address_lastname" class="customer_address_lastname fa" placeholder="&#xf007;" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="customer_address_phone"><?=$languages[$current_lang]['header_customer_address_phone'];?><span class="red">*</span></label>
            <input type="text" name="customer_address_phone" class="customer_address_phone fa" placeholder="&#xf095;" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="customer_address_email">Email<span class="red">*</span></label>
            <input type="text" name="customer_address_email" class="customer_address_email fa" placeholder="&#xf0e0;" required="required" />
            <div class="alert alert-danger" style="display: none"><?=$languages[$current_lang]['required_field_error'];?>!</div>
          </div>
          <div class="clearfix"></div>
        </fieldset>
      </div>

      <div class="form no_padding">
        <fieldset>
          <legend><?=$languages[$current_lang]['header_additional_orders_note'];?></legend>
          <textarea name="order_comment" id="order_comment"></textarea>
        </fieldset>
      </div>
    
      <div class="form no_padding">
        <fieldset>
          <legend><?=$languages[$current_lang]['header_payment_method'];?></legend>
          <div style="margin-bottom: 10px;">
<?php
        $query_payment_methods = "SELECT `payment_methods`.`payment_method_id`,`payment_methods`.`payment_method`,`payment_methods`.`payment_method_is_default`,
                                         `payment_methods_translations`.`payment_method_translation`
                                    FROM `payment_methods`
                              INNER JOIN `payment_methods_translations` ON `payment_methods_translations`.`payment_method_id` = `payment_methods`.`payment_method_id`
                                   WHERE `payment_methods_translations`.`language_id` = '$current_language_id' 
                                     AND `payment_methods`.`payment_method_is_active` = '1'";
        //echo $query_payment_methods."<br>";
        $result_payment_methods = mysqli_query($db_link, $query_payment_methods);
        if(!$result_payment_methods) echo mysqli_error($db_link);
        $count_payment_methods = mysqli_num_rows($result_payment_methods);
        if($count_payment_methods > 0) {
          while($row_payment_methods = mysqli_fetch_assoc($result_payment_methods)) {
            $payment_method_id = $row_payment_methods['payment_method_id'];
            $payment_method = $row_payment_methods['payment_method'];
            $payment_method_is_default = $row_payment_methods['payment_method_is_default'];
            $payment_method_translation = stripslashes($row_payment_methods['payment_method_translation']);
            $checked = ($payment_method_is_default == 1) ? "checked='checked'" : "";
?>
            <label for="payment_method_<?=$payment_method_id?>" style="display: inline-block">
              <input type='radio' name='payment_method' id="payment_method_<?=$payment_method_id?>" class='payment_method' value='<?=$payment_method?>' <?=$checked?>>
              <?=$payment_method_translation?>
            </label> &nbsp;
            <input type='hidden' name='payment[<?=$payment_method?>][0]' value='<?=$payment_method_id?>'>
            <input type='hidden' name='payment[<?=$payment_method?>][1]' value='<?=$payment_method_translation?>'>
<?php
          }
        }
?>
          </div>
          
          <?php print_html_payment_methods (); ?>
        </fieldset>
      </div>
      
    </div>
    <p class="clearfix">&nbsp;</p>
  
    <table id="cart_summary" class="table table-bordered stock-management-on">
      <thead>
        <tr>
          <th class="item" width="10%"><?=$languages[$current_lang]['header_product'];?></th>
          <th class="item hidden-xs hidden-sm" width="45%"><?=$languages[$current_lang]['header_description'];?></th>
          <th class="item" width="15%" class="text-center"><?=$languages[$current_lang]['header_quantity'];?></th>
          <th class="item" width="15%"><?=$languages[$current_lang]['header_unit_price'];?></th>
          <th class="item" width="15%"><?=$languages[$current_lang]['header_total_price'];?></th>
        </tr>
      </thead>
      <tbody>
<?php
    $total_order_price = 0;
    $key = 0;
    $descr = "";
    
    foreach($shopping_cart as $products) {

      $product_id = $products['product_id'];
      $product_isbn = $products['product_isbn'];
      $product_price = $products['product_price'];
      $product_name = $products['product_name'];
      $descr .= ($key == 0) ? $product_name : ", $product_name";
      $product_url = $products['product_url'];
      $product_img_src = $products['product_img_src'];
      $product_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$product_img_src);
      $product_image_dimensions = $product_image_params[3];
      $product_qty = $products['product_qty'];
      $product_price_total = number_format(($product_price*$product_qty),2,".","");
      $total_order_price = $total_order_price+($product_price*$product_qty);
?>
        <tr id="product_<?=$product_id;?>">
          <td class="cart_product">
            <a href="<?=$product_url;?>" target="_blank">
              <img src="<?=$product_img_src?>" class="img_table" <?=$product_image_dimensions?>>
            </a>
          </td>
          <td class="cart_description hidden-xs hidden-sm"><?=$product_name?></td>
          <td>
            <div class="product_qty text-center"><?=$product_qty?></div>
          </td>
          <td class="cart_unit text-center">
            <ul class="price" id="product_price_6_31_0">
              <li class="price product_price"><?=$product_price?><span class="currency">&nbsp;лв.</span></li>
            </ul>
          </td>
          <td class="cart_total text-center price">
            <span class="product_price_total"><?=$product_price_total?></span><span class="currency">&nbsp;лв.</span>
          </td>
        </tr>
<?php
      $key++;
    }
    $total_order_price_fomatted = number_format($total_order_price,2,".","");
    if($total_order_price > 50 && $shipping_date != $today) $delivery_price =  0.00; 
    $total_order_price_with_delivery = number_format($total_order_price+$delivery_price,2,".","");
?>
      </tbody>
      <tfoot>
        <tr class="cart_total_delivery">
          <td class="hidden-xs hidden-sm"></td>
          <td colspan="3" class="text-right"><?=$languages[$current_lang]['header_delivery'];?></td>
          <td class="price" id="total_shipping">
            <span class="price"><?=number_format($delivery_price,2,".","")?></span><span class="currency">&nbsp;лв.</span>
            <input type="hidden" id="product_descriptions" name="product_descriptions" value="<?=$descr?>" />
            <input type="hidden" id="delivery_price" name="delivery_price" value="<?=number_format($delivery_price,2,".","")?>" />
          </td>
        </tr>
        <tr class="cart_total_price">
          <td class="hidden-xs hidden-sm"></td>
          <td colspan="3" class="text-right"><?=$languages[$current_lang]['header_total_products_price'];?></td>
          <td class="price">
            <span><?=$total_order_price_fomatted?></span><span class="currency">&nbsp;лв.</span></span>
            <input type="hidden" id="order_total" name="order_total" value="<?=$total_order_price_fomatted?>">
          </td>
        </tr>
        <tr class="cart_total_price">
          <td class="hidden-xs hidden-sm"></td>
          <td colspan="3" class="text-right">
            <span><?=$languages[$current_lang]['header_total_price_with_delivery'];?></span>
          </td>
          <td class="price" id="total_price_container">
            <span id="total_order_price"><?=$total_order_price_with_delivery?></span><span class="currency">&nbsp;лв.</span></span>
          </td>
        </tr>
      </tfoot>
    </table>

    <div class="accept_terms_box">
      <input type="checkbox" name="accept_terms" id="accept_terms" /> 
      <label for="accept_terms" style="display: inline-block"><?=$languages[$current_lang]['header_accept_terms_and_conditions_01'];?></label>
      <a href="/<?=$current_lang;?>/<?php if($current_lang == "bg") echo "общи-условия";else echo "terms-and-conditions"; ?>#terms_cond" style="text-decoration: underline;" target="_blank">
        <?=$languages[$current_lang]['header_accept_terms_and_conditions_02'];?>
      </a>
    </div>

    <p class="cart_navigation clearfix">
      <button type="submit" name="quick_order" class="button btn btn-outline standard-checkout button-medium pull-right btn-sm place_order">
        <?=$languages[$current_lang]['btn_order'];?>
      </button
    </p>
    
  </div>
  <!--<div class="row">-->
</form>

<?php 
      } //guest
?>
<!--modal_confirm-->
<div style="display:none;" id="modal_confirm_terms" class="clearfix" title="<?=$languages[$current_lang]['text_warning'];?>">
  <p style="padding:0;margin:0;width:100%;float:left;line-height: 1.3em;"><?=$languages[$current_lang]['warning_accept_terms_first'];?></p>
</div>
<div style="display:none;" id="modal_first_choose_site" class="clearfix" title="<?=$languages[$current_lang]['text_warning'];?>">
  <p style="padding:0;margin:0;width:100%;float:left;line-height: 1.3em;"><?=$languages[$current_lang]['warning_first_choose_site'];?></p>
</div>
<div style="display:none;" id="modal_confirm_time_interval" class="clearfix" title="<?=$languages[$current_lang]['text_warning'];?>">
  <p style="padding:0;margin:0;width:100%;float:left;line-height: 1.3em;"><?=$languages[$current_lang]['warning_no_time_interval_selected'];?></p>
</div>
<div style="display:none;" id="modal_shipping_time_interval_not_available" class="clearfix" title="<?=$languages[$current_lang]['text_warning'];?>">
  <p style="padding:0;margin:0;width:100%;float:left;line-height: 1.3em;"><?=$languages[$current_lang]['warning_shipping_time_interval_not_available'];?></p>
</div>
<script type="text/javascript">
  $(document).ready(function () {
    var today = new Date();
    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    start_date = (<?=$current_hour;?> > 1600) ? tomorrow : today;
    $(".datepicker").datepicker({ dateFormat: "yy-mm-dd", minDate:  start_date});
    
    $(".quik_order").bind('click', function () {
      var quik_order_view = $("#form_quick_order").css("display");
      //alert(quik_order_view);
      if (quik_order_view == "block") {
        $("#form_quick_order").hide();
      }
      else {
        $("#form_quick_order").show();
      }
    });
    
    //autocomplete sites
    $("#shipping_site").autocomplete({
      source: "/frontstore/ajax/get-sites-autocomplete.php",
      minLength: 2,
      select: function( event, ui ) {
        //alert(ui.item.site_name);
        $('#shipping_site_id').val(ui.item.site_id);
        $('#shipping_site_name').val(ui.item.site_type+" "+ui.item.site_name);
      },
      close: function( event, ui ) {
        $('#shipping_site').val($('#shipping_site_name').val());
        GetTimeIntervals($("#shipping_date").val());
        if($("#shipping_date").val() == "2020-03-09") {
          $("p.date_info").addClass("hidden");
          //console.log("why?");
        }
      }
    });
      
    $("#time_interval .time_interval").bind('click', function () {
      var link = $(this);
      if(link.hasClass("hint_not_aval")) {
        $("#modal_shipping_time_interval_not_available").dialog("open");
        return;
      }
      $(".time_interval input").attr("disabled",true);
      $(".time_interval").removeClass("active");
      link.addClass("active");
      link.children("input").attr("disabled",false);
    });
    
    $("#has_invoice_address").bind('click', function () {
      var checkbox_1 = document.getElementById("has_invoice_address");
      var checkbox_state_1 = checkbox_1.checked;
      //alert(checkbox_state_1);
      if (checkbox_state_1) {
        checkbox_1.checked = true;
        $("#has_invoice_address_box").show();
      }
      else {
        checkbox_1.checked = false;
        $("#has_invoice_address_box").hide();
      }
    });
    
    $("#input_invoice_address").bind('click', function () {
      var checkbox = document.getElementById("input_invoice_address");
      var checkbox_state = checkbox.checked;
      if (checkbox_state) {
        checkbox.checked = true;
        $("#customer_invoice_addresses").hide();
      }
      else {
        checkbox.checked = false;
        $("#customer_invoice_addresses").show();
      }
    });
    
    $("#modal_first_choose_site").dialog({
      resizable: false,
      width: 400,
      height: 200,
      autoOpen: false,
      modal: true,
      draggable: false,
      closeOnEscape: true,
      dialogClass: "modal_confirm",
      buttons: {
        "<?=$languages[$current_lang]['btn_close'];?>": function() {
          $(this).dialog("close");
        }
      }
    });
    
    $("#modal_shipping_time_interval_not_available").dialog({
      resizable: false,
      width: 400,
      height: 200,
      autoOpen: false,
      modal: true,
      draggable: false,
      closeOnEscape: true,
      dialogClass: "modal_confirm",
      buttons: {
        "<?=$languages[$current_lang]['btn_close'];?>": function() {
          $(this).dialog("close");
        }
      }
    });
    
    $("#modal_confirm_time_interval").dialog({
      resizable: false,
      width: 400,
      height: 200,
      autoOpen: false,
      modal: true,
      draggable: false,
      closeOnEscape: true,
      dialogClass: "modal_confirm",
      buttons: {
        "<?=$languages[$current_lang]['btn_close'];?>": function() {
          $(this).dialog("close");
        }
      }
    });
    
    $("#modal_confirm_terms").dialog({
      resizable: false,
      width: 400,
      height: 200,
      autoOpen: false,
      modal: true,
      draggable: false,
      closeOnEscape: true,
      dialogClass: "modal_confirm",
      buttons: {
        "<?=$languages[$current_lang]['btn_close'];?>": function() {
          $(".accept_terms_box").css({color:"red"});
          $(".accept_terms_box a").css({color:"red"});
          $(this).dialog("close");
          setTimeout(function () { 
            $(".accept_terms_box").css({color:"#5a5a5a"}); 
            $(".accept_terms_box a").css({color:"#3881C2"}); 
          }, 4000);
        }
      }
    });
    
    $(".place_order").click(function(event) {
      
      var required_fields_ok = true;
      $(".form input[required='required']").each(function(){
        if($(this).val() == "") {
          $(this).next(".alert").show();
          required_fields_ok = false;
        }
        else {
          $(this).next(".alert").hide();
        }
      });
      if(!required_fields_ok) {
        $('body,html').animate({scrollTop: 230}, 600);
        event.preventDefault();
        return;
      }
      if(!$(".time_interval.active").length) {
        $("#modal_confirm_time_interval").dialog("open");
        event.preventDefault();
        return;
      }
      else {
        var state_sterms = ($("#accept_terms").is(':checked') ? "1" : "0");
        if(state_sterms == "0") {
          $("#modal_confirm_terms").dialog("open");
          event.preventDefault();
          return;
        }
      }
    });
    
    $.each($(".payment_method"), function(){
      if($(this).is(":checked")) {
        $("#"+$(this).val()).show();
      }
    })
    //$(".payment_method_img:first").show();
      
    $(".payment_method").click(function() {
      var payment_method = $(this).val();
      $(".payment_method_img").hide();
      $("#"+payment_method).show();
      if(payment_method == "paypal") {
        $(".form_place_order").attr("action","/<?=$current_lang;?>/shopping-cart/paypal-request");
      }
      else if(payment_method == "bank_transfer") {
        $(".form_place_order").attr("action","/<?=$current_lang;?>/shopping-cart/shopping-cart-checkout-bank-transfer-success");
      }
      else if(payment_method == "delivery") {
        $(".form_place_order").attr("action","/<?=$current_lang;?>/shopping-cart/shopping-cart-checkout-cash-success");
      }
      else if(payment_method == "epay") {
        $(".form_place_order").attr("action","/<?=$current_lang;?>/shopping-cart/shopping-cart-checkout-easypay-success");
      }
      else {
        $(".form_place_order").attr("action","/<?= $current_lang; ?>/shopping-cart/borica-request");
      }
    });
  });
</script>
<?php
} //if($has_products_in_cart)