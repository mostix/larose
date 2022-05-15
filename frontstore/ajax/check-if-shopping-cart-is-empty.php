<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  
  if(isset($_SESSION['cart']) && (count($_SESSION['cart']) > 0)) {
    echo "not empty";
  }
  else echo "empty";

