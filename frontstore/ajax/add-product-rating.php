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
  if(isset($_POST['rating_stars_value'])) {
    $rating_stars_value =  $_POST['rating_stars_value'];
  }
  if(isset($_POST['current_lang'])) {
    $current_lang =  $_POST['current_lang'];
  }
  $customer_id = (isset($_SESSION['customer']['customer_id'])) ? $_SESSION['customer']['customer_id'] : 0;
  $customer_ip = $_SERVER['REMOTE_ADDR'];
  
  $query_product_rating = "INSERT INTO `product_rating`(`product_rating_id`, `product_id`, `customer_id`, `customer_ip`, `rating_value`)
                                                VALUES ('','$product_id','$customer_id','$customer_ip','$rating_stars_value')";
  $result_product_rating = mysqli_query($db_link, $query_product_rating);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  else {
    $product_rating_params = get_product_rating($product_id);
    
    $ratings_count = $product_rating_params['ratings_count'];
    $product_rating_imgs = $product_rating_params['rating_imgs'];
    
    $_SESSION['rating'][$product_id] = 1;
    
    $text_rate = $languages[$current_lang]['text_rate_vote'];
    $text_rates = $languages[$current_lang]['text_rate_votes'];
    $ratings_count_text = ($ratings_count == 1) ? $text_rate : $text_rates;
    
    echo "$product_rating_imgs <span class='text'>$ratings_count $ratings_count_text</span>";
  }
?>