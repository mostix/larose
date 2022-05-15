<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  $dirname = dirname(__FILE__);
  
  $customer_id = $_SESSION['customer']['customer_id'];
  $customer_fullname = $_SESSION['customer']['customer_firstname']." ".$_SESSION['customer']['customer_lastname'];
  
  if(isset($_GET['d_pid']) && is_numeric($_GET['d_pid'])) {
    // delete product from shopping cart
    $delete_product_id = $_GET['d_pid'];
    
    $query_delete_wp = "DELETE FROM `customers_wishlists` WHERE `customer_id` = '$customer_id' AND `product_id` = '$delete_product_id'";
    $result_delete_wp = mysqli_query($db_link, $query_delete_wp);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
    }
  }
  
  $query_customer_wishlist = "SELECT `product_id`, `product_name`, `product_price`, `product_url`, `product_image` 
                              FROM `customers_wishlists` 
                              WHERE `customer_id` = '$customer_id'";
  //echo $query_customer_wishlist;
  $result_customer_wishlist = mysqli_query($db_link, $query_customer_wishlist);
  if(!$result_customer_wishlist) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_customer_wishlist) > 0) {
?>
  <div id="order-detail-content" class="table_block table-responsive">
    <table id="cart_summary" class="table table-bordered stock-management-on">
      <thead>
        <tr>
          <th class="item" width="10%"><?=$languages[$current_lang]['header_product'];?></th>
          <th class="item" width="65%"><?=$languages[$current_lang]['header_description'];?></th>
          <th class="item" width="15%"><?=$languages[$current_lang]['header_unit_price'];?></th>
          <th class="item" width="10%"><?=$languages[$current_lang]['header_delete'];?></th>
        </tr>
      </thead>
      <tbody>
<?php
    while($wishlist_product = mysqli_fetch_assoc($result_customer_wishlist)) {

      $product_id = $wishlist_product['product_id'];
      $product_name = stripslashes($wishlist_product['product_name']);
      $product_price = $wishlist_product['product_price'];
      $product_url = stripslashes($wishlist_product['product_url']);
      $product_image = stripslashes($wishlist_product['product_image']);
      @$product_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$product_image);
      $product_image_dimensions = $product_image_params[3];
?>
        <tr id="wishlist_product_<?=$product_id;?>">
          <td class="cart_product">
            <a href="<?=$product_url;?>" target="_blank">
              <img src="<?=$product_image?>" class="img_table" <?=$product_image_dimensions?>>
            </a>
          </td>
          <td class="cart_description"><?=$product_name?></td>
          <td class="cart_unit text-center">
            <ul class="price" id="product_price_6_31_0">
              <li class="price product_price"><?=$product_price?><span class="currency">&nbsp;лв.</span></li>
            </ul>
          </td>
          <td class="cart_delete text-center">
            <a href="javascript:;" class="delete_product_btn" data-id="<?=$product_id?>">
              <i class="fa fa-trash-o fa-2x"></i>
            </a>
          </td>
        </tr>
<?php 
    }
?>
      </tbody>
    </table>
  </div>
<?php
  } //if(!empty($customer_wishlist))
  else {
    echo '<p class="alert alert-warning">'.$languages[$current_lang]['text_wishlist_is_empty'].'</p>';
  }
?>
  <p class="clearfix">&nbsp;</p>

  <!--modal_confirm-->
  <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure'];?>">
    <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages[$current_lang]['delete_product_from_wishlist'];?></p>
  </div>
  <script>
  $(function() {
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
          window.location.href='/<?=$current_lang;?>/user-profiles/user-profile-wishlist?d_pid='+product_id;
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