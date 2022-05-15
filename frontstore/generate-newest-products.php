<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  $dirname = dirname(__FILE__);

  include_once 'config.php';
  include_once 'languages/languages.php';
  include_once 'functions/include-functions.php';

  $languages = get_all_lang_ids();
  
  $query_truncate = "TRUNCATE TABLE `newest_products`";
  mysqli_query($db_link, $query_truncate);
  
  foreach ($languages as $language_id) {
    
    $query_products = "SELECT `products`.`product_id`,`products`.`stock_status_id`,`products`.`product_isbn`,`products`.`product_quantity`,`products`.`product_price`,
                              `products`.`product_subtract`,`products`.`product_viewed`,`product_discount`.`pd_price`,
                              `product_description`.`pd_name`,`product_description`.`pd_description`, `category_descriptions`.`cd_name`
                         FROM `products`
                   INNER JOIN `product_to_category` USING(`product_id`)
                   INNER JOIN `product_description` USING(`product_id`)
                    LEFT JOIN `product_discount` USING(`product_id`)
                   INNER JOIN `categories` ON (`product_to_category`.`category_id` = `categories`.`category_id` AND `categories`.`category_is_active` = '1')
                   INNER JOIN `category_descriptions` ON (`categories`.`category_id` = `category_descriptions`.`category_id`)
                        WHERE `products`.`product_newest` = '1' AND `products`.`product_is_active` = '1' 
                          AND `product_description`.`language_id` = '$language_id' AND `products`.`stock_status_id` = '1'
                          AND `product_description`.`pd_is_active` = '1' AND `category_descriptions`.`language_id` = '$language_id'
                          AND `category_descriptions`.`cd_is_active` = '1'
                     GROUP BY `products`.`product_id`
                     ORDER BY `products`.`product_id` DESC 
                        LIMIT 15";
    //echo $query_products."<br>"; 
    $result_products = mysqli_query($db_link, $query_products);
    if(!$result_products) echo mysqli_error($db_link);
    $products_count = mysqli_num_rows($result_products);
    if($products_count > 0) {

      $insert_rows = "";
      $row = 0;
      while($product_row = mysqli_fetch_assoc($result_products)) {

        $cd_name = $product_row['cd_name'];
        $product_id = $product_row['product_id'];
        $pd_name = $product_row['pd_name'];
        $pd_description = stripslashes($product_row['pd_description']);
        $product_quantity = $product_row['product_quantity'];
        $product_subtract = $product_row['product_subtract'];
        $product_isbn = $product_row['product_isbn'];
        $product_viewed = $product_row['product_viewed'];
        $stock_status_id = $product_row['stock_status_id'];
        $product_price = $product_row['product_price'];
        $pd_price = (!empty($product_row['pd_price'])) ? $product_row['pd_price'] : "";

        $insert_rows .= ($row == $products_count-1) ? "('$product_id','$language_id','$cd_name','$pd_name','$pd_description','$product_quantity','$product_subtract',
                        '$product_isbn','$product_viewed','$stock_status_id','$product_price','$pd_price');" 
                                      : 
                        "('$product_id','$language_id','$cd_name','$pd_name','$pd_description','$product_quantity','$product_subtract',
                                '$product_isbn','$product_viewed','$stock_status_id','$product_price','$pd_price'),";
      
        $row++;
      }
    }
   
    $query_insert = "INSERT INTO `newest_products`(`product_id`, 
                                                  `language_id`, 
                                                  `cd_name`, 
                                                  `pd_name`, 
                                                  `pd_description`, 
                                                  `product_quantity`, 
                                                  `product_subtract`, 
                                                  `product_isbn`, 
                                                  `product_viewed`, 
                                                  `stock_status_id`, 
                                                  `product_price`, 
                                                  `pd_price`) 
                                            VALUES $insert_rows";
    //echo "$query_insert<br>";
    $result_insert = mysqli_query($db_link, $query_insert);
    if(mysqli_affected_rows($db_link) <= 0) {
      
    }
  }