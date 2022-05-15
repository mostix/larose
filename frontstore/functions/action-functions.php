<?php

function is_active_page($page) {
  
  if(strstr($_GET['page'], $page)) {
    return true;
  }
  else {
    return false;
  }
}
  
function prepare_for_null_row($value) {

  if (empty($value) || is_null($value))
      $value = "NULL";
  else
      $value = "'$value'";

  return $value;
}

function print_array_for_debug($array_for_debug) {

  echo "<pre>";print_r($array_for_debug);echo "</pre>";
}

function print_object_for_debug($object_for_debug) {

  echo "<pre>"; var_dump($object_for_debug);echo "</pre>";
}

function multiexplode($delimiters,$string) {
  
  $ready = str_replace($delimiters, $delimiters[0], $string);
  $launch = explode($delimiters[0], $ready);
  return  $launch;
    
}

function get_easypay_idn($params) {

  // create a new cURL resource
  $ch = curl_init();

  // set URL and other appropriate options
  curl_setopt($ch, CURLOPT_URL, "https://www.epay.bg/ezp/reg_bill.cgi?$params");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, 0);

  // grab URL and pass it to the browser
  $easypay_idn_text = curl_exec($ch);
  $easypay_idn = substr($easypay_idn_text, -11);

  // close cURL resource, and free up system resources
  curl_close($ch);

  return $easypay_idn_text;
}

function start_page_build_time_measure() {
  
  $mtime = microtime(); 
  $mtime = explode(" ",$mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $starttime = $mtime;
}

function close_page_build_time_measure($print_time = false) {
  
  global $starttime;
  
  $mtime = microtime(); 
  $mtime = explode(" ",$mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $endtime = $mtime; 
  $totaltime = ($endtime - $starttime);
  if($print_time) echo "<br><p>This page was created in ".$totaltime." seconds</p>";
}

function generate_bcrypt_salt() {
  
  $rand_string = "";
  $charecters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./";
  for ($i = 0; $i < 22; $i++) {
      $randInt = mt_rand(0, 63);
      $rand_char = $charecters[$randInt];
      $rand_string .= $rand_char;
  }
  return $rand_string;
}

function generate_captcha() {
  
  global $db_link;

  unset($_SESSION['captcha123']);
  $_SESSION['captcha123'] = array();
  $rnd = rand(1,99);
  $query = "SELECT * FROM `captchas` LIMIT $rnd,1";
  //echo $query;
  $result = mysqli_query($db_link, $query);
  if (!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result)>0){

    $captcha = mysqli_fetch_assoc($result);
    $_SESSION['captcha123']['img'] = $captcha['captcha_image'];
    $_SESSION['captcha123']['code'] = $captcha['captcha_number'];
    setcookie("captcha_code", $captcha['captcha_number'], time() + (86400 * 30), "/"); // 86400 = 1 day

  }
}

function generate_strong_password($length = 8, $available_sets = 'luds') {
  
  $sets = array();
  if(strpos($available_sets, 'l') !== false)
          $sets[] = 'abcdefghjkmnpqrstuvwxyz';
  if(strpos($available_sets, 'u') !== false)
          $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
  if(strpos($available_sets, 'd') !== false)
          $sets[] = '23456789';
  if(strpos($available_sets, 's') !== false)
          $sets[] = '!@#$%&*?';

  $all = '';
  $password = '';
  foreach($sets as $set)
  {
          $password .= $set[array_rand(str_split($set))];
          $all .= $set;
  }

  $all = str_split($all);
  for($i = 0; $i < $length - count($sets); $i++)
          $password .= $all[array_rand($all)];

  $password = str_shuffle($password);

  return $password;
}

function check_if_users_passwords_match($user_password,$confirm_user_password) {
  global $languages;
  global $current_lang;
  if($user_password === $confirm_user_password) {
    return "";
  }
  else {
    return $languages[$current_lang]['customer_passwords_mismatch'];
  }
}

function check_if_user_email_is_valid($customer_email) {
  
  global $db_link;
  global $languages;
  global $current_lang;
  
  if(!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    return false;
  }
  else {
    if(!empty($customer_id)) $query = "SELECT `customer_id` FROM `customers` WHERE `customer_id` <> '$customer_id' AND `customer_email` = '$customer_email'";
    else $query = "SELECT `customer_id` FROM `customers` WHERE `customer_email` = '$customer_email'";
    //echo $query;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_num_rows($result) > 0) {
      return false;
    }
    else {
      return true;
    }
  }
}

function get_languages() {

  global $db_link;

  $languages_array = array();
  $query_languages = "SELECT `language_id`,`language_code`,`language_menu_name`,`language_name` FROM `languages` WHERE `language_is_active` = '1' ORDER BY `language_menu_order` ASC";
  $result_languages = mysqli_query($db_link, $query_languages);
  if (!$result_languages)
    echo mysqli_error($db_link);
  if (mysqli_num_rows($result_languages) > 0) {
    while ($row_languages = mysqli_fetch_assoc($result_languages)) {
      $language_id = $row_languages['language_id'];
      $languages_array[$language_id] = $row_languages;
    }
    mysqli_free_result($result_languages);
  }

  return $languages_array;
}

function get_currencies() {
  
  global $db_link;
  global $current_lang;
  $currencies_array = array();
  
  $query_currencies = "SELECT `currencies`.* FROM `currencies` WHERE `currency_is_active` = '1'";
  //echo $query_currencies;
  $result_currencies = mysqli_query($db_link, $query_currencies);
  if(!$result_currencies) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_currencies) > 0) {
    while($currencies_row = mysqli_fetch_assoc($result_currencies)) {
      $currency_id = $currencies_row['currency_id'];
      $currency_is_default = $currencies_row['currency_is_default'];
      if($currency_is_default == 1) {
        $currencies_array[$currency_id] = $currencies_row;
        $currencies_array['default'] = $currencies_row;
      }
      else $currencies_array[$currency_id] = $currencies_row;
    }
    mysqli_free_result($result_currencies);
  }
  
  return $currencies_array;
}

function beautify_name_for_url($pd_name) {
  
  return str_replace(array(' - ',' & ','\\','?','!','"','. ','.  ','.',', ',',','(',')','%',' '), 
                     array('-','-','-','','','','-','-','','-','-','-','-','-','-'), 
                    mb_convert_case(mb_strimwidth($pd_name, 0, 100,'', 'utf-8'), MB_CASE_LOWER, "UTF-8"));
}

function cyrialize_url($url) {
  return str_replace(array('а','б','в','г','д','е','ж', 'з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц', 'ч', 'ш', 'щ',  'ь','ъ','ю','я'), 
                        array('a','b','v','g','d','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','sht','y','a','yu','ya'), $url);
}

function get_default_lang_code() {
  
  global $db_link;

  $query_language = "SELECT `language_code`,`language_id` FROM `languages` WHERE `language_is_default_frontend` = '1'";
  //echo $query_language;
  $result_language = mysqli_query($db_link, $query_language);
  if(!$result_language) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_language) > 0) {
    $language_array = mysqli_fetch_assoc($result_language);
    $current_lang = stripslashes($language_array['language_code']);
  }
  return $language_array;
}

function get_all_lang_codes() {
  
  global $db_link;
  $codes_array = array();

  $query_language = "SELECT `language_code` FROM `languages`";
  //echo $query_language;
  $result_language = mysqli_query($db_link, $query_language);
  if(!$result_language) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_language) > 0) {
    while($code_row = mysqli_fetch_assoc($result_language)) {
      $language_code = $code_row['language_code'];
      $codes_array[] = $language_code;
    }
    mysqli_free_result($result_language);
  }
  
  return $codes_array;
}

function get_all_lang_ids() {
  
  global $db_link;
  $codes_array = array();

  $query_language = "SELECT `language_id` FROM `languages`";
  //echo $query_language;
  $result_language = mysqli_query($db_link, $query_language);
  if(!$result_language) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_language) > 0) {
    while($code_row = mysqli_fetch_assoc($result_language)) {
      $language_id = $code_row['language_id'];
      $ids_array[] = $language_id;
    }
    mysqli_free_result($result_language);
  }
  
  return $ids_array;
}

function check_url_status($url) {
  // Create a cURL handle
  $handle = curl_init($url);
  curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($handle, CURLOPT_NOBODY, true);

  /* Get the HTML or whatever is linked in $url. */
  $response = curl_exec($handle);

  /* Check for 404 (file not found). */
  $http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

  curl_close($handle);
  
  return $http_code;
}

function get_customer_wishlist($customer_id) {
  
  global $db_link;
  global $current_lang;
  $wishlist_array = array();
  
  $query_wishlist = "SELECT `product_id` FROM `customers_wishlists` WHERE `customer_id` = '$customer_id'";
  //echo $query_wishlist;
  $result_wishlist = mysqli_query($db_link, $query_wishlist);
  if(!$result_wishlist) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_wishlist) > 0) {
    while($currencies_row = mysqli_fetch_assoc($result_wishlist)) {
      $product_id = $currencies_row['product_id'];
      $wishlist_array[] = $product_id;
    }
    mysqli_free_result($result_wishlist);
  }
  
  return $wishlist_array;
}

function check_if_content_has_active_children($content_id) {
  
  global $db_link;
  
  $query_active_children = "SELECT `content_id` FROM `contents` WHERE `content_parent_id` = '$content_id' AND `content_show_in_menu` = '1' AND `content_is_active` = '1'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return true;
  }
  else return false;
}

function check_if_this_is_content_last_child($content_parent_id,$content_menu_order) {
  
  global $db_link;
  
  $query_active_children = "SELECT `content_id` FROM `contents` 
                            WHERE `content_parent_id` = '$content_parent_id' AND `content_menu_order` > '$content_menu_order'
                              AND `content_show_in_menu` = '1'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return false;
  }
  else return true;
}

function check_if_category_has_products($category_id) {
  
  global $db_link;
  global $current_language_id;
  
  $query_has_products = "SELECT `products`.`product_id`
                           FROM `products`
                     INNER JOIN `product_to_category` USING(`product_id`)
                     INNER JOIN `product_description` USING(`product_id`)
                          WHERE `products`.`product_is_active` = '1' AND `product_to_category`.`category_id` = '$category_id'
                            AND `product_description`.`language_id` = '$current_language_id' AND `product_description`.`pd_is_active` = '1'
                          LIMIT 1";
  $result_has_products = mysqli_query($db_link, $query_has_products);
  if(!$result_has_products) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_has_products) > 0) {
    return true;
  }
  return false;
}

function get_category_products_count($category_id) {
  
  global $db_link;
  global $current_language_id;
  
  $products_count = 0;
  $query_has_products = "SELECT `products`.`product_id`
                           FROM `products`
                     INNER JOIN `product_to_category` USING(`product_id`)
                     INNER JOIN `product_description` USING(`product_id`)
                          WHERE `products`.`product_is_active` = '1' AND `product_to_category`.`category_id` = '$category_id'
                            AND `product_description`.`language_id` = '$current_language_id' AND `product_description`.`pd_is_active` = '1'";
  $result_has_products = mysqli_query($db_link, $query_has_products);
  if(!$result_has_products) echo mysqli_error($db_link);
  $products_count = mysqli_num_rows($result_has_products);

  return $products_count;
}

function check_if_category_has_active_children($category_id) {
  
  global $db_link;
  
  $query_active_children = "SELECT `category_id` FROM `categories` 
                            WHERE `category_parent_id` = '$category_id' AND `category_show_in_menu` = '1' AND `category_is_active` = '1'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {
    return true;
  }
  return false;
}

function check_if_this_is_category_last_child($category_parent_id,$category_sort_order) {
  
  global $db_link;
  global $current_language_id;
  
  $query_active_children = "SELECT `category_id` 
                            FROM `categories` 
                            INNER JOIN `category_descriptions` USING(`category_id`)
                            WHERE `categories`.`category_parent_id` = '$category_parent_id' AND `categories`.`category_sort_order` > '$category_sort_order'
                              AND `categories`.`category_show_in_menu` = '1' AND `categories`.`category_is_active` = '1' 
                              AND `category_descriptions`.`language_id` = '$current_language_id' AND `category_descriptions`.`cd_is_active` = '1'";
  //if($current_language_id == 2 && $category_parent_id == 18) echo "$query_active_children<br>";
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return false;
  }
  else return true;
}

function get_product_default_image($product_id) {
  
  global $db_link;
  global $current_lang;
  global $current_cat_href;
  
  if(empty($current_lang)) {
    $current_lang_arr = explode ("/", $current_cat_href);
    $current_lang = $current_lang_arr[0];
  }
  
  $query_pi_name = "SELECT `product_image`.`pi_name` 
                    FROM `product_image` 
                    WHERE `product_image`.`product_id` = '$product_id'";
  //echo $query_pi_name;
  $result_pi_name = mysqli_query($db_link, $query_pi_name);
  if(!$result_pi_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_pi_name) > 0) {
    $row_pi_name = mysqli_fetch_assoc($result_pi_name);
    $pi_name = $row_pi_name['pi_name'];
    mysqli_free_result($result_pi_name);
  }
  else {
    $pi_name = ""; //default picture
  }
  
  return $pi_name;
}

function get_product_images($product_id) {
  
  global $db_link;
  global $current_lang;
  $pi_names_array = array();
  
  $query_pi_name = "SELECT `product_image_id`,`pi_name`,`pi_is_default` 
                    FROM `product_image` 
                    WHERE `product_image`.`product_id` = '$product_id'
                    ORDER BY  `product_image`.`pi_sort_order` ASC ";
  //echo $query_pi_name;
  $result_pi_name = mysqli_query($db_link, $query_pi_name);
  if(!$result_pi_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_pi_name) > 0) {
    while($pi_names_row = mysqli_fetch_assoc($result_pi_name)) {
      $pi_is_default = $pi_names_row['pi_is_default'];
      if($pi_is_default == 1) $pi_names_array['default'] = $pi_names_row;
      else $pi_names_array['gallery'][] = $pi_names_row;
    }
    mysqli_free_result($result_pi_name);
  }
  
  return $pi_names_array;
}

function get_category_products_min_max_price($current_category_id) {
  
  global $db_link;
  global $current_language_id;
  global $category_ids;
  
  $where_category = (isset($category_ids) && !empty($category_ids)) ? "`product_to_category`.`category_id` IN (".implode(",",$category_ids).")" : "`product_to_category`.`category_id` = '$current_category_id'";
  $query_min_max_price = "SELECT MIN(`product_price`) as `min_product_price`, MAX(`product_price`) as `max_product_price` 
                           FROM `products`
                     INNER JOIN `product_to_category` USING(`product_id`)
                     INNER JOIN `product_description` USING(`product_id`)
                          WHERE $where_category AND `products`.`product_is_active` = '1'
                            AND `product_description`.`pd_is_active` = '1' AND `product_description`.`language_id` = '$current_language_id'";
  //echo $query_min_max_price;
  $result_min_max_price = mysqli_query($db_link, $query_min_max_price);
  if(!$result_min_max_price) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_min_max_price) > 0) {
    $min_max_price_row = mysqli_fetch_assoc($result_min_max_price);
    mysqli_free_result($result_min_max_price);
  }
  
  return $min_max_price_row;
}

function get_lаst_auto_increment_id($table_name) {
  
  global $db_link;
  
  $query_lаst_auto_increment_id = "SELECT `auto_increment` FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '$table_name'";
  //echo $query_lаst_auto_increment_id;
  $result_lаst_auto_increment_id = mysqli_query($db_link, $query_lаst_auto_increment_id);
  if(!$result_lаst_auto_increment_id) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_lаst_auto_increment_id) > 0) {
    while($row_lаst_auto_increment_id = mysqli_fetch_assoc($result_lаst_auto_increment_id)) {
      $lаst_auto_increment_ids[] = $row_lаst_auto_increment_id['auto_increment'];
    }
    if(isset($lаst_auto_increment_ids[1])) {
      $lаst_auto_increment_id = $lаst_auto_increment_ids[1];
    }
    else {
      $lаst_auto_increment_id = $lаst_auto_increment_ids[0];
    }
    
    mysqli_free_result($result_lаst_auto_increment_id);
  }
  
  return $lаst_auto_increment_id;
}

function get_gallery_images($gallery_id,$count = false) {
  
  global $db_link;
  global $current_language_id;
  $gi_names_array = array();
  
  $limit = ($count) ? "LIMIT $count" : "";
  $query_gi_name = "SELECT `gallery_images`.`gallery_image_id`,`gallery_images`.`name`,`gallery_images`.`is_album_cover`,`gallery_images`.`is_active`,
                            `gallery_images`.`sort_order`,`gid`.`gallery_image_id`,`gid`.`gallery_image_title`,`gid`.`gallery_image_comment`
                    FROM `gallery_images` 
                    INNER JOIN `gallery_images_descriptions` as `gid` USING(`gallery_image_id`)
                    WHERE `gallery_images`.`gallery_id` = '$gallery_id' AND `is_active`='1' 
                      AND `gid`.`language_id` = '$current_language_id'
                    ORDER BY `gallery_images`.`gallery_image_id` DESC $limit";
  //if($current_language_id == 2) echo $query_gi_name;
  $result_gi_name = mysqli_query($db_link, $query_gi_name);
  if(!$result_gi_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_gi_name) > 0) {
    while($gi_names_row = mysqli_fetch_assoc($result_gi_name)) {
      $gi_names_array[] = $gi_names_row;
    }
    mysqli_free_result($result_gi_name);
  }
  
  return $gi_names_array;
}

function get_gallery_text($gallery_id) {
  
  global $db_link;
  global $current_language_id;
  
  $query_gi_text = "SELECT `gallery_text`
                      FROM `gallery_descriptions`
                     WHERE `gallery_id` = '$gallery_id' AND `language_id` = '$current_language_id'";
  //if($current_language_id == 2) echo $query_gi_text;
  $result_gi_text = mysqli_query($db_link, $query_gi_text);
  if(!$result_gi_text) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_gi_text) > 0) {
    $gi_texts_row = mysqli_fetch_assoc($result_gi_text);
    $gallery_text = $gi_texts_row['gallery_text'];
 
    mysqli_free_result($result_gi_text);
  }
  
  return $gallery_text;
}

function get_galleries($count) {
  
  global $db_link;
  global $current_language_id;
  
  $galleries_array = array();
  $limit = ($count == 0) ? "" : "LIMIT $count";
  
  $query_galleries = "SELECT `galleries`.`gallery_id`,`gallery_descriptions`.`gallery_name`,`gallery_images`.`name` as `album_cover`
                      FROM `galleries` 
                      INNER JOIN `gallery_descriptions` USING(`gallery_id`)
                      INNER JOIN `gallery_images` USING(`gallery_id`)
                      WHERE `galleries`.`is_active` = '1' AND `gallery_descriptions`.`language_id` = '$current_language_id'
                        AND `gallery_images`.`is_album_cover` = '1'
                      ORDER BY `galleries`.`sort_order` ASC $limit";
  //echo $query_galleries;exit;
  $result_galleries = mysqli_query($db_link, $query_galleries);
  if(!$result_galleries) echo mysqli_error($db_link);
  $galleries_count = mysqli_num_rows($result_galleries);
  if($galleries_count > 0) {
    
    while($gallery_row = mysqli_fetch_assoc($result_galleries)) {

      $galleries_array[] = $gallery_row;
    }
    mysqli_free_result($result_galleries);

  }
  
  return $galleries_array;
}

function get_product_rating($product_id) {
  
  global $db_link;
  
  $query_product_rating = "SELECT `rating_value` FROM `product_rating` WHERE `product_id` = '$product_id'";
  //echo $query_product_rating;exit;
  $result_product_rating = mysqli_query($db_link, $query_product_rating);
  if(!$result_product_rating) echo mysqli_error($db_link);
  $ratings_count = mysqli_num_rows($result_product_rating);
  if($ratings_count > 0) {
    
    $rating_star_1 = 0;
    $rating_star_2 = 0;
    $rating_star_3 = 0;
    $rating_star_4 = 0;
    $rating_star_5 = 0;
    
    while($rating_row = mysqli_fetch_assoc($result_product_rating)) {

      switch ($rating_row['rating_value']) {
        case 1:
          $rating_star_1++;
          break;
        case 2:
          $rating_star_2++;
          break;
        case 3:
          $rating_star_3++;
          break;
        case 4:
          $rating_star_4++;
          break;
        case 5:
          $rating_star_5++;
          break;
      }
    }
    
    $ratings_value = ($rating_star_1*1)+($rating_star_2*2)+($rating_star_3*3)+($rating_star_4*4)+($rating_star_5*5);
    $product_rating = round($ratings_value/$ratings_count, 1);
    
    $product_rating_imgs = "";

    switch ($product_rating) {
      case 0:
        $product_rating_imgs .= '<img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/>';
        break;
      case ($product_rating>0 && $product_rating<1):
        $product_rating_imgs .= '<img src="/frontstore/images/star-half2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/>';
        break;
      case 1:
        $product_rating_imgs .= '<img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/>';
        break;
      case ($product_rating>1 && $product_rating<2):
        $product_rating_imgs .= '<img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-half2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/>';
        break;
      case 2:
        $product_rating_imgs .= '<img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/>';
        break;
      case ($product_rating>2 && $product_rating<3):
        $product_rating_imgs .= '<img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-half2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/>';
        break;
      case 3:
        $product_rating_imgs .= '<img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/>';
        break;
      case ($product_rating>3 && $product_rating<4):
        $product_rating_imgs .= '<img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-half2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/>';
        break;
      case 4:
        $product_rating_imgs .= '<img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/>';
        break;
      case ($product_rating>4 && $product_rating<5):
        $product_rating_imgs .= '<img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-half2.png" width="17" height="16" alt="звезда"/>';
        break;
      case 5:
        $product_rating_imgs .= '<img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-full2.png" width="17" height="16" alt="звезда"/>';
        break;
    }
  
    $product_rating_params['product_rating'] = $product_rating;
    $product_rating_params['ratings_count'] = $ratings_count;
    $product_rating_params['rating_imgs'] = $product_rating_imgs;
    
    mysqli_free_result($result_product_rating);

  }
  else {
    $product_rating_params['product_rating'] = 0;
    $product_rating_params['ratings_count'] = 0;
    $product_rating_params['rating_imgs'] = '<img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/><img src="/frontstore/images/star-empty.png" width="17" height="16" alt="звезда"/>';
  }
  
  return $product_rating_params;
}

function check_if_customer_has_rated($product_id) {
  
  global $db_link;
  
  $customer_id = (isset($_SESSION['customer']['customer_id'])) ? $_SESSION['customer']['customer_id'] : 0;
  $customer_ip = $_SERVER['REMOTE_ADDR'];
  
  if($customer_id == 0) {
    $query_customer_has_rated = "SELECT `product_rating_id` FROM `product_rating` 
                                WHERE `product_id` = '$product_id' AND `customer_ip` = '$customer_ip'";
  }
  else {
    $query_customer_has_rated = "SELECT `product_rating_id` FROM `product_rating` 
                                WHERE `product_id` = '$product_id' AND `customer_id` = '$customer_id'";
  }
  //if($customer_id == 4) echo $query_customer_has_rated;exit;
  $result_customer_has_rated = mysqli_query($db_link, $query_customer_has_rated);
  if(!$result_customer_has_rated) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_customer_has_rated) > 0) {
    return true;
  }
  else {
    return false;
  }
  mysqli_free_result($result_customer_has_rated);
}

function get_random_product_ids_list($count) {
  
  global $db_link;
  global $current_language_id;
  
  $random_prod_ids_list = "";
  $query_random_prod_ids = "SELECT `product_id` 
                            FROM `products` 
                            INNER JOIN `product_description` USING(`product_id`)
                            WHERE `product_is_active` = '1' AND `product_description`.`pd_is_active` = '1'
                              AND `product_description`.`language_id` = '$current_language_id'
                          ORDER BY RAND() LIMIT $count";
  //echo $query_random_prod_ids."<br>";
  $result_random_prod_ids = mysqli_query($db_link, $query_random_prod_ids);
  if(!$result_random_prod_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_random_prod_ids) > 0) {
    $key = 0;
    while($random_prod_ids_row = mysqli_fetch_assoc($result_random_prod_ids)) {

      $random_prod_ids_list .= ($key == 0) ? $random_prod_ids_row['product_id'] : ",".$random_prod_ids_row['product_id'];

      $key++;
    }
  }
  return $random_prod_ids_list;
}

//epay function
function hmac($algo,$data,$passwd){
  /* md5 and sha1 only */
  $algo=strtolower($algo);
  $p=array('md5'=>'H32','sha1'=>'H40');
  if(strlen($passwd)>64) $passwd=pack($p[$algo],$algo($passwd));
  if(strlen($passwd)<64) $passwd=str_pad($passwd,64,chr(0));

  $ipad=substr($passwd,0,64) ^ str_repeat(chr(0x36),64);
  $opad=substr($passwd,0,64) ^ str_repeat(chr(0x5C),64);

  return($algo($opad.pack($p[$algo],$algo($ipad.$data))));
}
?>
