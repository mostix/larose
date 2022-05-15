<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
//  echo "<pre>";print_r($_POST);
//  echo "<pre>";print_r($_SESSION);echo "</pre>";

  if(isset($_GET['d_pid']) && is_numeric($_GET['d_pid'])) {
    // delete product from shopping cart
    $delete_product_id = $_GET['d_pid'];
  }
  else {
    $delete_product_id = 0;
  }
  
  $shopping_cart = array();
  
  if(isset($_SESSION['customer']['customer_id'])) {
    $customer_id = $_SESSION['customer']['customer_id'];
    $customer_is_loged = true;
  }
  else {
    $customer_is_loged = false;
  }
  
  if(isset($_SESSION['cart'])) {
    if(isset($_SESSION['cart'][$delete_product_id])) unset($_SESSION['cart'][$delete_product_id]);
    $shopping_cart = $_SESSION['cart'];
  }
  
  print_html_shopping_cart_progress();
  
  if(isset($customer_id) && $customer_id == 4 || $_SESSION['debug']) {
    //print_array_for_debug($_SESSION);
  }
  
  if(empty($shopping_cart)) {
    if(isset($_GET['session_ended'])) {
?>
  <h3 id="emptyCartWarning" class="alert alert-warning"><?=$languages[$current_lang]['header_shopping_cart_is_empty_on_session_end'];?></h3>
<?php    
    }
    else {
?>
  <p id="emptyCartWarning" class="alert alert-warning"><?=$languages[$current_lang]['header_shopping_cart_is_empty'];?></p>
<?php 
    }
  }
  else {
    //echo "<pre>";print_r($shopping_cart);
    $action = "/$current_lang/shopping-cart/shopping-cart-addresses";
    if(isset($customer_id) && $customer_id == 4 || $_SESSION['debug']) {
      //$action = "/$current_lang/shopping-cart/shopping-cart-addresses-test";
    }
?>
  <!--<form action="/<?=$current_lang;?>/shopping-cart/shopping-cart-addresses" method="post" id="shopping_cart" name="shopping_cart">-->
  <form action="<?=$action;?>" method="post" id="shopping_cart" name="shopping_cart">
    <div id="order-detail-content" class="table_block table-responsive">
      <table id="cart_summary" class="table table-bordered stock-management-on">
        <thead>
          <tr>
            <th class="item" width="10%"><?=$languages[$current_lang]['header_product'];?></th>
            <th class="item hidden-xs hidden-sm" width="45%"><?=$languages[$current_lang]['header_description'];?></th>
            <th class="item text-center" width="15%"><?=$languages[$current_lang]['header_quantity'];?></th>
            <th class="item" width="10%"><?=$languages[$current_lang]['header_delete'];?></th>
            <th class="item" width="10%"><?=$languages[$current_lang]['header_unit_price'];?></th>
            <th class="item" width="10%"><?=$languages[$current_lang]['header_total_price'];?></th>
          </tr>
        </thead>
        <tbody>
<?php
    $delivery_price = 0;
    $total_order_price = $delivery_price;
    foreach($shopping_cart as $products) {
      
      $product_id = $products['product_id'];
      $product_isbn = $products['product_isbn'];
      $product_price = $products['product_price'];
      $product_name = $products['product_name'];
      $product_url = $products['product_url'];
      $product_img_src = $products['product_img_src'];
      @$product_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$product_img_src);
      $product_image_dimensions = $product_image_params[3];
      $product_qty = $products['product_qty'];
      $product_price_total = number_format(($product_price*$product_qty),2,".",".");
      $total_order_price = $total_order_price+($product_price*$product_qty);
      $remove_product_from_cart_warning = $languages[$current_lang]['remove_product_from_cart_warning'];
?>
        <tr id="product_<?=$product_id;?>">
          <td class="cart_product">
            <a href="<?=$product_url;?>" target="_blank">
              <img src="<?=$product_img_src?>" class="img_table" <?=$product_image_dimensions?>>
            </a>
          </td>
          <td class="cart_description hidden-xs hidden-sm"><?=$product_name?></td>
          <td class="cart_quantity text-center">
            <input size="2" type="text" autocomplete="off"  name="product_qty" class="product_qty cart_quantity_input form-control grey" value="<?=$product_qty?>" />
            <div class="cart_quantity_button clearfix">
              <a href="javascript:;" rel="nofollow" data-id="<?=$product_id;?>" class="cart_quantity_down btn btn-outline button-minus btn-sm product_qty_minus" title="<?=$languages[$current_lang]['title_subtract'];?>">
                <span><i class="fa fa-minus"></i></span>
              </a>
              <a href="javascript:;" rel="nofollow" data-id="<?=$product_id;?>" class="cart_quantity_up btn btn-outline button-plus btn-sm product_qty_plus" title="<?=$languages[$current_lang]['title_add'];?>">
                <span><i class="fa fa-plus"></i></span>
              </a>
            </div>
          </td>
          <td class="cart_delete text-center">
            <a href="javascript:;" class="delete_product_btn" data-id="<?=$product_id?>">
              <i class="fa fa-trash-o fa-2x"></i>
            </a>
            <input type="hidden" class="input_product_id" name="products[<?=$product_id?>]['product_id']" value="<?=$product_id?>">
            <input type="hidden" class="input_product_qty" name="products[<?=$product_id?>]['product_qty']" value="<?=$product_qty?>">
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
    }
    $delivery_price = number_format($delivery_price,2,".","");
    $total_order_price = number_format($total_order_price,2,".","");
?>
        </tbody>
        
        <tfoot>
          <tr class="cart_total_price">
            <td colspan="2" id="cart_voucher" class="cart_voucher"></td>
            <td colspan="3" class="text-right"><?=$languages[$current_lang]['header_total_products_price'];?></td>
            <td class="price" id="total_price_container">
              <span id="total_order_price"><?=$total_order_price?></span><span class="currency">&nbsp;лв.</span></span>
            </td>
          </tr>
        </tfoot>
      </table>
      
      <p class="cart_navigation clearfix">
        <a href="/" class="button-exclusive btn btn-outline btn-sm" role="button"><?=$languages[$current_lang]['btn_continue_shopping'];?></a>
        <button type="submit" name="order_address" class="button btn btn-outline standard-checkout button-medium pull-right btn-sm">
          <?=$languages[$current_lang]['btn_save_and_continue'];?>
        </button>
      </p>

    </div> <!-- end order-detail-content -->
  </form>
<?php
    }
?>
  <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure'];?>">
    <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages[$current_lang]['remove_product_from_cart_warning'];?></p>
  </div>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".product_qty_minus").bind('click', function() {
        var product_id = $(this).attr("data-id");
        var product_qty_box = $("#product_"+product_id+" .product_qty");
        var product_qty_input = $("#product_"+product_id+" .input_product_qty");
        var product_price_box = $("#product_"+product_id+" .product_price");
        var product_price_total_box = $("#product_"+product_id+" .product_price_total");
        var product_qty = parseInt(product_qty_box.val())-1;
        var product_price = product_price_box.html();
        if(product_qty >= "1") {
          product_qty_box.val(product_qty);
          product_qty_input.html(product_qty);
          product_price_total_box.html((product_qty*parseFloat(product_price)).toFixed(2));
          var cart_product_qty = $(".layer_cart_cart #cart_product_qty").val();
          var cart_product_qty_new = parseInt(cart_product_qty)-1;
          //alert(cart_product_qty_new);
          $(".layer_cart_cart #cart_product_qty").val(cart_product_qty_new);
          
          CalculateTotalPrice();
          UpdateProductCount(product_id,'-');
        }
      });
      $(".product_qty_plus").bind('click', function() {
        var product_id = $(this).attr("data-id");
        var product_qty_box = $("#product_"+product_id+" .product_qty");
        var product_qty_input = $("#product_"+product_id+" .input_product_qty");
        var product_price_box = $("#product_"+product_id+" .product_price");
        var product_price_total_box = $("#product_"+product_id+" .product_price_total");
        var product_qty = parseInt(product_qty_box.val())+1;
        var product_price = product_price_box.html();
        product_qty_box.val(product_qty);
        product_qty_input.html(product_qty);
        product_price_total_box.html((product_qty*parseFloat(product_price)).toFixed(2));
        var cart_product_qty = $(".layer_cart_cart #cart_product_qty").val();
        var cart_product_qty_new = parseInt(cart_product_qty)+1;
        //alert(cart_product_qty_new);
        $(".layer_cart_cart #cart_product_qty").val(cart_product_qty_new);
        
        CalculateTotalPrice();
        UpdateProductCount(product_id,'+');
      });
      
      $("#modal_confirm").dialog({
        resizable: false,
        width: 400,
        height: 200,
        autoOpen: false,
        modal: true,
        draggable: false,
        closeOnEscape: true,
        dialogClass: "modal_confirm",
        buttons: {
          "<?=$languages[$current_lang]['btn_delete'];?>": function() {
            var product_id = $(".delete_product_btn.active").attr("data-id");
            window.location.href='/<?=$current_lang;?>/shopping-cart/shopping-cart-overview?d_pid='+product_id;
          },
          "<?=$languages[$current_lang]['btn_cancel'];?>": function() {
            $(".delete_product_btn").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_product_btn").bind('click', function() {
        $(".delete_product_btn").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
  </script>