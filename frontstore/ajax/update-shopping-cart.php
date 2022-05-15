<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../languages/languages.php';
  include_once '../functions/include-functions.php';

  //echo "<pre>";print_r($_POST);
  
  if(isset($_POST['product_id'])) {
    // delete product from shopping cart
    $delete_product_id = $_POST['product_id'];
    if(isset($_SESSION['cart'][$delete_product_id])) {
      $_SESSION['total_products_qty'] -= $_SESSION['cart'][$delete_product_id]['product_qty'];
      $_SESSION['total_products_price'] -= $_SESSION['cart'][$delete_product_id]['product_price'];
      unset($_SESSION['cart'][$delete_product_id]);
    }
    $shopping_cart = $_SESSION['cart'];
  }
  
  print_shopping_cart();