<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../languages/languages.php';
  include_once '../functions/include-functions.php';

//  echo "<pre>";print_r($_POST);exit;
  if(isset($_POST['product_id'])) {
    $product_id =  $_POST['product_id'];
  }
  $pd_price = 0;
  if(isset($_POST['pd_price'])) {
    $pd_price =  $_POST['pd_price'];
  }
  if(isset($_POST['product_price'])) {
    $product_price = (!empty($pd_price)) ? $pd_price : $_POST['product_price'];
  }
  if(isset($_POST['product_name'])) {
    $product_name = mysqli_real_escape_string($db_link, $_POST['product_name']);
  }
  if(isset($_POST['product_url'])) {
    $product_url = mysqli_real_escape_string($db_link, $_POST['product_url']);
  }
  if(isset($_POST['product_image'])) {
    $product_image = mysqli_real_escape_string($db_link, $_POST['product_image']);
  }
  if(isset($_POST['current_lang'])) {
    $current_lang =  $_POST['current_lang'];
  }
  $customer_id = $_SESSION['customer']['customer_id'];
  
  $query_insert_wishlist = "INSERT INTO `customers_wishlists`(`customer_id`,`product_id`,`product_name`,`product_price`,`product_url`,`product_image`) 
                                                    VALUES ('$customer_id','$product_id','$product_name','$product_price','$product_url','$product_image')";
  $result_insert_wishlist = mysqli_query($db_link, $query_insert_wishlist);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  else {
    $text_added_to_wishlist = $languages[$current_lang]['text_added_to_wishlist'];
    echo "<b>$product_name</b> $text_added_to_wishlist";
  }
?>