<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../functions/include-functions.php';
  
  //print_r($_GET);EXIT;
  
  if(isset($_GET['ENCODED'])) {
    $ENCODED =  $_GET['ENCODED'];
  }
  if(isset($_GET['CHECKSUM'])) {
    $CHECKSUM =  $_GET['CHECKSUM'];
  }
  if(isset($_GET['MIN'])) {
    $min =  $_GET['MIN'];
  }
  if(isset($_GET['INVOICE'])) {
    $invoice =  $_GET['INVOICE'];
  }
  if(isset($_GET['AMOUNT'])) {
    $sum =  $_GET['AMOUNT'];
  }
  if(isset($_GET['EXP_TIME'])) {
    $exp_date =  $_GET['EXP_TIME'];
  }
  
  $params = "ENCODED=$ENCODED&CHECKSUM=$CHECKSUM&MIN=$min&INVOICE=$invoice&AMOUNT=$sum&EXP_TIME=$exp_date";
  
  echo get_easypay_idn($params);