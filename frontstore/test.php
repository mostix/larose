<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  $dirname = dirname(__FILE__);

  phpinfo();
  exit;
  include_once 'config.php';
  include_once 'languages/languages.php';
  include_once 'functions/include-functions.php';
  
  $current_language_id = 1;
  $current_category_id = 20;
  $product_price = 25;
  $min_price = $product_price-10;
  $max_price = $product_price+10;
  $count = 2;
  
  $query_products = "SELECT `products`.`product_id`,`products`.`stock_status_id`,`products`.`product_isbn`,`products`.`product_quantity`,`products`.`product_price`,
                            `products`.`product_subtract`,`products`.`product_viewed`,`product_discount`.`pd_price`,
                            `product_description`.`pd_name`,`product_description`.`pd_description`
                       FROM `products`
                 INNER JOIN `product_to_category` USING(`product_id`)
                 INNER JOIN `product_description` USING(`product_id`)
                  LEFT JOIN `product_discount` USING(`product_id`)
                      WHERE `products`.`product_is_active` = '1' AND `products`.`stock_status_id` = '1'
                        AND `products`.`product_price` BETWEEN $min_price AND $max_price
                        AND `product_to_category`.`category_id` = '$current_category_id'
                        AND `product_description`.`language_id` = '$current_language_id'AND `product_description`.`pd_is_active` = '1'
                      LIMIT $count";
  $result_products = mysqli_query($db_link, $query_products);
  if(!$result_products) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_products) > 0) {
    while($product_row = mysqli_fetch_assoc($result_products)) {
      print_array_for_debug($product_row);
    }
  }
  exit;
  start_page_build_time_measure();
  
  print_newest_products(15);
  
  close_page_build_time_measure(1);