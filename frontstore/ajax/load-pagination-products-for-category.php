<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../languages/languages.php';
  include_once '../functions/include-functions.php';

  //echo "<pre>";print_r($_GET);
  if(isset($_POST['current_category_id'])) {
    $current_category_id =  $_POST['current_category_id'];
  }
  if(isset($_POST['cpid'])) {
    $current_category_parent_id = $_POST['cpid']; // category_parent_id
  }
  if(isset($_POST['cd_name'])) {
    $cd_name =  $_POST['cd_name'];
  }
  if(isset($_POST['language_id'])) {
    $current_language_id =  $_POST['language_id'];
  }
  if(isset($_POST['current_cat_href'])) {
    $current_cat_href =  $_POST['current_cat_href'];
  }
  $cd_pretty_url = "";
  if(isset($_POST['cd_pretty_url'])) {
    $cd_pretty_url =  $_POST['cd_pretty_url'];
  }
  if(isset($_POST['offset'])) {
    $offset = $_POST['offset'];
  }
  else $offset = 0;
  if(isset($_GET['pmin'])) {
    $price_min =  $_GET['pmin'];
  }
  if(isset($_GET['pmax'])) {
    $price_max =  $_GET['pmax'];
  }
  $products_count = false;
  if(isset($_POST['products_count'])) {
    $products_count =  $_POST['products_count'];
  }
  if(isset($_POST['order_by_price'])) {
    $order_by_price =  $_POST['order_by_price'];
  }

  $customer_id = isset($_SESSION['customer']['customer_id']) ? $_SESSION['customer']['customer_id'] : 0;
  $customer_wishlist = get_customer_wishlist($customer_id);
  
  list_products_by_category($current_category_id,$offset,$current_cat_href,$cd_pretty_url,$products_count);
?>
  <script>
    $(function() {
      bindGrid();
      $(".php_pagination a").bind('click', function() {
        var offset = $(this).attr("data");
        PushSortProductsState('pagination','<?=$current_language_id;?>',offset);
//        LoadPaginationProductsForCategory(offset);
      });
    });
  </script>