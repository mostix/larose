<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../functions/include-functions.php';

//  echo "<pre>";print_r($_POST);exit;
  if(isset($_POST['product_id'])) {
    $product_id =  $_POST['product_id'];
  }
  if(isset($_POST['language_id'])) {
    $language_id =  $_POST['language_id'];
  }
  if(isset($_POST['product_isbn'])) {
    $product_isbn =  $_POST['product_isbn'];
  }
  if(isset($_POST['product_price'])) {
    $product_price =  $_POST['product_price'];
  }
  if(isset($_POST['pd_price'])) {
     if(!empty($_POST['pd_price'])) $product_price = $_POST['pd_price'];
  }
  if(isset($_POST['product_name'])) {
    $product_name =  $_POST['product_name'];
  }
  if(isset($_POST['product_url'])) {
    $product_url =  $_POST['product_url'];
  }
  if(isset($_POST['product_qty'])) {
    $product_qty =  $_POST['product_qty'];
  }
  if(isset($_POST['product_img_src'])) {
    $product_img_src =  $_POST['product_img_src'];
  }
  
  //unset($_SESSION['cart']);
  
  if(isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['product_qty'] += $product_qty;
  }
  else {
    $_SESSION['cart'][$product_id]['product_id'] = $product_id;
    $_SESSION['cart'][$product_id]['product_isbn'] = $product_isbn;
    $_SESSION['cart'][$product_id]['product_price'] = $product_price;
    $_SESSION['cart'][$product_id]['product_name'] = $product_name;
    $_SESSION['cart'][$product_id]['product_url'] = $product_url;
    $_SESSION['cart'][$product_id]['product_qty'] = $product_qty;
    $_SESSION['cart'][$product_id]['product_img_src'] = $product_img_src;
  }
  
  if(isset($_SESSION['total_products_qty'])) {
    $_SESSION['total_products_qty'] += $product_qty;
  }
  else {
    $_SESSION['total_products_qty'] = $product_qty;
  }
  
  if(isset($_SESSION['total_products_price'])) {
    $_SESSION['total_products_price'] += $product_price;
  }
  else {
    $_SESSION['total_products_price'] = $product_price;
  }
  
  //echo "<pre>";print_r($_SESSION);
  //echo json_encode($_SESSION['cart'][$product_id]);
?>