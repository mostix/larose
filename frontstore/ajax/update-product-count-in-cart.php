<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../languages/languages.php';
  include_once '../functions/include-functions.php';

  //echo "<pre>";print_r($_POST);
  if(isset($_POST['product_id'])) {
    $product_id =  $_POST['product_id'];
  }
  if(isset($_POST['operator'])) {
    $operator =  $_POST['operator'];
  }

  if(isset($_SESSION['cart'][$product_id])) {
    if($operator == "+") {
      $_SESSION['cart'][$product_id]['product_qty'] += 1;
      $_SESSION['total_products_qty'] += 1;
      $_SESSION['total_products_price'] += $_SESSION['cart'][$product_id]['product_price'];
    }
    else {
      $_SESSION['cart'][$product_id]['product_qty'] -= 1;
      $_SESSION['total_products_qty'] -= 1;
      $_SESSION['total_products_price'] -= $_SESSION['cart'][$product_id]['product_price'];
    }
  }
  
  print_shopping_cart();