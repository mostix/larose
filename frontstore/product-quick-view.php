<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  if(isset($_GET['product_id'])) {
    $current_product_id = $_GET['product_id']; // current selected product
  }
  
  // encrease product_viewed by one
  $query_update_product = "UPDATE `products` SET `product_viewed` = `product_viewed`+1 WHERE `product_id` = '$current_product_id'";
  $result_update_product = mysqli_query($db_link, $query_update_product);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
  }
  
  if(isset($_GET['page']) && !empty($_GET['page'])) {
    //$current_category_path = $_GET['page'];
    
    $current_page_path_string = $_GET['page'];
    //echo $current_page_path_string;
    $current_category_path = explode("/", $current_page_path_string);
    $count_category_path_elements = count($current_category_path)-1;
    $current_category_pretty_url = $current_category_path[$count_category_path_elements];
    $current_lang = $current_category_path[0];
    $current_cd_name = str_replace("-", " ", $current_category_path[$count_category_path_elements]);
  }
  
  $query_category_id = "SELECT `categories`.`category_hierarchy_ids` 
                        FROM `categories` 
                        INNER JOIN `product_to_category` USING(`category_id`)
                        WHERE `product_to_category`.`product_id` = '$current_product_id'";
  //echo $query_content;exit;
  $result_category_id = mysqli_query($db_link, $query_category_id);
  if(!$result_category_id) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_category_id) > 0) {
    $row_category_id = mysqli_fetch_assoc($result_category_id);
    $category_hierarchy_ids = $row_category_id['category_hierarchy_ids'];
  }
  
  $query_content_hierarchy_ids = "SELECT `language_root_content_id`,`language_id` FROM `languages` WHERE `language_code` = '$current_lang'";
  //echo $query_content;exit;
  $result_content_hierarchy_ids = mysqli_query($db_link, $query_content_hierarchy_ids);
  if(!$result_content_hierarchy_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_hierarchy_ids) > 0) {
    $row_content_hierarchy_ids = mysqli_fetch_assoc($result_content_hierarchy_ids);
    $current_language_id = $row_content_hierarchy_ids['language_id'];
    $content_hierarchy_ids = $row_content_hierarchy_ids['language_root_content_id'];
  }
  
  $query_product = "SELECT `weight_class_description`.`wcd_unit`,`length_class_description`.`lcd_unit`,`products`.`product_model`,`products`.`product_isbn`,
                          `products`.`product_quantity`,`products`.`product_price`,`products`.`product_weight`,`products`.`stock_status_id`, 
                          `products`.`product_length`,`products`.`product_width`,`products`.`product_height`,`products`.`product_subtract`,
                          `product_description`.`pd_name`,`product_description`.`pd_description`,`product_description`.`pd_meta_title`,
                          `product_description`.`pd_meta_description`,`product_description`.`pd_meta_keywords`,`product_discount`.`pd_price`
                    FROM `products`
                    INNER JOIN `product_description` USING(`product_id`)
                    LEFT JOIN `product_discount` USING(`product_id`)
                    INNER JOIN `weight_class_description` USING(`weight_class_id`)
                    INNER JOIN `length_class_description` USING(`length_class_id`)
                    WHERE `products`.`product_id` = '$current_product_id' AND `product_description`.`language_id` = '$current_language_id'
                      AND `weight_class_description`.`language_id` = '$current_language_id' AND `length_class_description`.`language_id` = '$current_language_id'";
  //echo $query_product;exit;
  $result_product = mysqli_query($db_link, $query_product);
  if(!$result_product) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_product) > 0) {
    $product_row = mysqli_fetch_assoc($result_product);
    
    $wcd_unit = $product_row['wcd_unit'];
    $lcd_unit = $product_row['lcd_unit'];
    $product_model = $product_row['product_model'];
    $product_isbn = $product_row['product_isbn'];
    $product_quantity = $product_row['product_quantity'];
    $product_price = $product_row['product_price'];
    $pd_price = (!empty($product_row['pd_price'])) ? $product_row['pd_price'] : "";
    $price_discount_class = (!empty($pd_price)) ? " class='line_through'" : "";
    $product_weight = $product_row['product_weight'];
    $stock_status_id = $product_row['stock_status_id'];
    $product_length = number_format($product_row['product_length'], 2,".",".");
    $product_width = number_format($product_row['product_width'], 2,".",".");
    $product_height = number_format($product_row['product_height'], 2,".",".");
    $product_subtract = $product_row['product_subtract'];
    $pd_name = stripslashes($product_row['pd_name']);
    $pd_description = stripslashes($product_row['pd_description']);
    $pd_meta_title = $product_row['pd_meta_title'];
    $pd_meta_description = $product_row['pd_meta_description'];
    $pd_meta_keywords = $product_row['pd_meta_keywords'];
  }
  //echo"<pre>";print_r($product_row);
  
  $pi_names_array = get_product_images($current_product_id);
  $pd_images_folder = "/frontstore/images/products/";
  if((isset($pi_names_array['default']['pi_name']) && !empty($pi_names_array['default']['pi_name']))) {

    $default_img_id = $pi_names_array['default']['product_image_id'];
    $default_img = $pi_names_array['default']['pi_name'];
    $default_img_exploded = explode(".", $default_img);
    $default_img_name = $default_img_exploded[0];
    $default_img_exstension = $default_img_exploded[1];

    $default_img_path_large = $pd_images_folder.$default_img;

    $default_img_thickbox_default = $pd_images_folder.$default_img_name."_thickbox_default.".$default_img_exstension;

    $default_img_cart_default = $pd_images_folder.$default_img_name."_cart_default.".$default_img_exstension;
    $gallery_img_cart = $pd_images_folder.$default_img_name."_small_default.".$default_img_exstension;
    $fb_image = $default_img_thickbox_default;
    @list($fb_width,$fb_height) = getimagesize($_SERVER['DOCUMENT_ROOT'].$fb_image);
    $full_path = $_SERVER['DOCUMENT_ROOT'].$pd_images_folder;

    $file = $full_path.$default_img;

    list($width,$height) = getimagesize($file);

    if($width > $height) {
      $default_img_style = "style='width:100%;height:auto;'";
    }
    else {
      $default_img_style = "style='height:100%;width:auto;'";
    }

    $default_img_cart_default_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$default_img_cart_default);
    //$default_img_cart_default_dimensions = $default_img_cart_default_params[3];
    $no_image = false;
  }
  else {
    $default_img_thickbox_default = "/frontstore/images/products/no_image_gal_zoom.jpg";
    $default_img_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$default_img_thickbox_default);
    $default_img_dimensions = $default_img_params[3];
    $no_image = true;
  }
  
  $pd_name_for_link = str_replace(array('\\','?','!','.',', ',',','(',')','%',' '), array('-','','','','-','-','-','-','-','-'), mb_convert_case(mb_strimwidth($pd_name, 0, 55,'', 'utf-8'), MB_CASE_LOWER, "UTF-8"));
  $product_link = "/$current_lang/pid-$current_product_id/$pd_name_for_link";
  
  $body_css = 'product';
?>
<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en-us"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="en-us"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="en-us"><![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9" lang="en-us"><![endif]-->
<html lang="en-us"  class="default">
  <head>
    <meta charset="utf-8" />
    <title><?=$languages[$current_lang]['e_shop_cms'];?> - <?=$pd_meta_title;?></title>
    <meta name="description" content="<?=$pd_meta_description;?>">
    <meta name="keywords" content="<?=$pd_meta_keywords;?>" />
    <meta name="robots" content="index,follow" />
    <meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="author" content="Eterrasystems Ltd.">
    <meta property="og:site_name" content="<?=$languages[$current_lang]['e_shop_cms'];?>">
    <meta property="og:locale" content="bg_BG">
    <meta property="fb:app_id" content="722916254478372">
    <meta property="og:url" content="<?="https://".urldecode($_SERVER['SERVER_NAME'].$product_link);?>" />
    <meta property="og:type" content="product" />
    <meta property="og:title" content="<?=strip_tags($pd_meta_title);?>" />
    <meta property="og:description" content="<?=strip_tags($pd_meta_description);?>" />
    <meta property="og:image" content="<?="https://".$_SERVER['SERVER_NAME'].$fb_image;?>" />
    <meta property="og:image:width" content="<?=$fb_width;?>" />
    <meta property="og:image:height" content="<?=$fb_height;?>" />
    <link rel="stylesheet" href="/frontstore/css/main.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/frontstore/css/product.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/frontstore/css/skin.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/frontstore/css/font-awesome.min.css" type="text/css"/>
    <script type="text/javascript" src="/frontstore/js/jquery/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="/frontstore/js/product.js"></script>
    <!--[if IE 8]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body id="product" class="product hide-left-column lang_<?=$current_lang;?> fullwidth double-menu">
    <!-- Load Facebook SDK for JavaScript -->
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '722916254478372',
          xfbml      : true,
          version    : 'v2.5'
        });
      };

      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
    </script>
    <script src="https://apis.google.com/js/platform.js" async defer>
      {lang: 'bg'}
    </script>
    <section id="columns" class="columns-container">
        <div class="container" style="width: 800px;">
          <div id="product_block_<?=$current_product_id;?>" class="row">
            <input type="hidden" name="product_isbn" class="product_id" value="<?=$current_product_id;?>">
            <input type="hidden" name="product_isbn" class="product_isbn" value="<?=$product_isbn;?>">
            <input type="hidden" name="product_price" class="product_price" value="<?=$product_price;?>">
            <input type="hidden" name="pd_price" class="pd_price" value="<?=$pd_price;?>">
            <input type="hidden" name="product_name" class="product_name" value="<?=$pd_name;?>">
            <input type="hidden" name="product_url" class="product_url" value="<?=$product_link;?>">

            <div class="primary_block row" itemscope itemtype="https://schema.org/Product">
              <div class="container">
                <div class="top-hr"></div>
              </div>
              <!-- left infos-->  
              <div class="pb-left-column col-xs-12 col-sm-12 col-md-6">
                <!-- product img-->       
<?php
          if($no_image) {
            echo "<img src='$default_img_thickbox_default' alt='$pd_name' $default_img_dimensions>";
          }
          else {
?>
            <input type="hidden" name="product_img" class="product_img" value="<?=$gallery_img_cart;?>">
            <div id="image-block" class="clearfix">
              <div class="p-label">
              </div>
              <span id="view_full_size">
                <img id="bigpic" itemprop="image" src="<?=$default_img_thickbox_default;?>" title="<?=$pd_name;?>" alt="<?=$pd_name;?>"/>
                <span class="span_link no-print status-enable btn btn-outline"></span>
              </span>
            </div> <!-- end image-block -->
            <!-- thumbnails -->
            <div id="views_block" class="clearfix ">
              <span class="view_scroll_spacer">
                <a id="view_scroll_left" class="" title="Other views" href="javascript:{}">
                  <?=$languages[$current_lang]['text_previous'];?>
                </a>
              </span>
              <div id="thumbs_list">
                <ul id="thumbs_list_frame">
                  <li id="thumbnail_<?=$default_img_id;?>">
                    <a href="<?=$default_img_thickbox_default;?>" data-fancybox-group="other-views" class="fancybox shown" title="<?=$pd_name;?>">
                      <img class="img-responsive" id="thumb_<?=$default_img_id;?>" <?=$default_img_style;?> src="<?=$default_img_cart_default;?>" alt="<?=$pd_name;?>" title="<?=$pd_name;?>" itemprop="image" />
                    </a>
                  </li>
<?php
          if(isset($pi_names_array['gallery'])) {
            
            $images_count = count($pi_names_array['gallery']);
            
            foreach($pi_names_array['gallery'] as $img_key => $prod_gallery_image) {
              //echo"<pre>";print_r($prod_gallery_image);
              $gallery_img_id = $prod_gallery_image['product_image_id'];
              $gallery_img = $prod_gallery_image['pi_name'];
              $full_path = $_SERVER['DOCUMENT_ROOT'].$pd_images_folder;

              $file = $full_path.$gallery_img;

              list($width,$height) = getimagesize($file);
              
              if($width > $height) {
                $img_style = "style='width:100%;height:auto;'";
              }
              else {
                $img_style = "style='height:100%;width:auto;'";
              }
              
              $gallery_img_exploded = explode(".", $gallery_img);
              $gallery_img_name = $gallery_img_exploded[0];
              $gallery_img_exstension = $gallery_img_exploded[1];
              
              $gallery_img_path_large = $pd_images_folder.$gallery_img;
              
              $gallery_img_thickbox_default = $pd_images_folder.$gallery_img_name."_thickbox_default.".$gallery_img_exstension;
              
              $gallery_img_cart_default = $pd_images_folder.$gallery_img_name."_cart_default.".$gallery_img_exstension;
//              @$gallery_img_cart_default_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$gallery_img_cart_default);
//              $gallery_img_cart_default_dimensions = $gallery_img_cart_default_params[3];
              
              $class_last = ($img_key == $images_count-1) ? 'class="last"' : "";
?>
              <li id="thumbnail_<?=$gallery_img_id?>" <?=$class_last;?>>
                <a href="<?=$gallery_img_thickbox_default;?>"	data-fancybox-group="other-views" class="fancybox" title="<?=$pd_name;?>">
                  <img class="img-responsive" id="thumb_<?=$gallery_img_id?>" <?=$img_style;?> src="<?=$gallery_img_cart_default;?>" alt="<?=$pd_name;?>" title="<?=$pd_name;?>" itemprop="image" />
                </a>
              </li>
<?php
            }
          }
?>
                  
                </ul>
              </div> <!-- end thumbs_list -->
              <a id="view_scroll_right" title="Other views" href="javascript:{}">
                <?=$languages[$current_lang]['text_next'];?>
              </a>
            </div> <!-- end views-block -->
            <!-- end thumbnails -->
<?php
          }
?>
                  
                </div> <!-- end pb-left-column -->
                <!-- end left infos--> 
                <!-- center infos -->
                <div class="pb-center-column col-xs-12 col-sm-6 col-md-6">

                  <h1 itemprop="name"><?=$pd_name;?></h1>

                  <!--  /Module ProductComments -->
                  <p id="product_reference">
                    <label><?=$languages[$current_lang]['header_isbn'];?>: </label>
                    <span class="editable" itemprop="sku"><?=$product_isbn;?></span>
                  </p>
<!--                  <p id="product_condition">
                    New
                  </p>-->
                  
                  <div class="socialsharing_product list-inline no-print">
                    <div class="fb-like" data-href="<?=urldecode($_SERVER['SERVER_NAME'].$product_link);?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
                    <div class="google_plus" style="top:-16px">
                      <div class="g-plusone" data-href="<?=urldecode($_SERVER['SERVER_NAME'].$product_link);?>"></div>
                    </div>
                  </div>

                  <p id="availability_date" style="display: none;">
                    <span id="availability_date_label">Availability date:</span>
                    <span id="availability_date_value">0000-00-00</span>
                  </p>
                  <!-- Out of stock hook -->
                  <div id="oosHook" style="display: none;">

                  </div>

                  <!-- usefull links-->
                  <ul id="usefull_link_block" class="clearfix no-print">
                    <li class="no-print">
                      <?php
                        $onclick_wishlist_fn = (user_is_loged()) ? "AddProductToWishlist('$current_product_id','$pd_name')" : "OpenModalWindow(loggin_required_for_wishlist)";
                      ?>
                      <a class="btn-tooltip addToWishlist no_padding" href="javascript:;" onclick="<?=$onclick_wishlist_fn;?>" data-toggle="tooltip" title="<?=$languages[$current_lang]['title_add_to_wishlist'];?>">
                        <i class="fa fa-heart fa-lg" style="margin-right: 7px;"></i> <?=$languages[$current_lang]['title_add_to_wishlist'];?>
                      </a>
                    </li>
                  </ul>
                  
                  <!-- add to cart form-->
                  <div id="buy_block">
                    <div class="box-info-product">
                      <div class="content_prices clearfix">
                        <!-- prices -->
                        <div class="price">
                          <p class="our_price_display content_price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                            <input type="hidden" class="basic_price" value="<?php if(!empty($pd_price)) echo $pd_price; else echo $product_price?>" />
                            <span id="our_price_display" class="price" itemprop="price" content="<?php if(!empty($pd_price)) echo $pd_price; else echo $product_price?>">
                              <?php if(!empty($pd_price)) echo $pd_price; else echo $product_price?><span class="currency">&nbsp;лв.</span>
                            </span>
                            <meta itemprop="priceCurrency" content="BGN" />
                          </p>
                          <?php
                            if(!empty($pd_price)) {
                              $pd_percent_reduction = 100-(ceil(($pd_price / $product_price) * 100));
                          ?>
                          <p id="reduction_percent">
                            <span id="reduction_percent_display"><?=$pd_percent_reduction;?> %</span>
                          </p>
                          <p id="old_price">
                            <span id="old_price_display"><?=$product_price;?><span class="currency">&nbsp;лв.</span></span>
                            <!--  -->
                          </p>
                          <?php } ?>
                        </div> <!-- end prices -->

                        <div class="clear"></div>
                      </div> <!-- end content_prices -->
                      <div class="product_attributes clearfix">
                        <!-- quantity wanted -->
                        <p id="quantity_wanted_p">
                          <a href="javascript:;" class="product_qty_minus cart_quantity_down btn btn-outline button-minus btn-sm" title="<?=$languages[$current_lang]['title_subtract'];?>" rel="nofollow">
                            <span><i class="fa fa-minus"></i></span>
                          </a>
                          <input type="text" name="product_qty" class="product_qty input_product_qty text form-control" value="1" />
                          <a href="javascript:;" class="product_qty_plus cart_quantity_up btn btn-outline button-plus btn-sm" title="<?=$languages[$current_lang]['title_add'];?>" rel="nofollow">
                            <span><i class="fa fa-plus"></i></span>
                          </a>
                          <span class="clearfix"></span>
                        </p>
                        <div class="box-cart-bottom">
                          <div>
                            <?php
                              if($stock_status_id == 3) {
                                //not in stock
                            ?>
                              <p class="out-of-stock no-print">
                                <?=$languages[$current_lang]['text_out_in_stock'];?>
                              </p>
                            <?php
                              }
                              else {
                            ?>
                              <p id="add_to_cart" class="buttons_bottom_block no-print">
                                <a href="javascript:;" onclick="AddProductToCart('<?=$current_product_id;?>','<?=$current_language_id;?>')" class="button add_to_cart btn btn-outline" rel="nofollow" title="<?=$languages[$current_lang]['btn_add_to_shopping_cart'];?>">
                                  <span><?=$languages[$current_lang]['btn_add_to_shopping_cart'];?></span>
                                </a>
                              </p>
                            <?php
                              }
                            ?>
                          </div>
                        </div> <!-- end box-cart-bottom -->
                        <label><?=$languages[$current_lang]['header_rating'];?></label>
                        <div id="rating_stars" class="clearfix">
<?php
  //unset($_SESSION['rating'][$current_product_id]);
                          if(isset($_SESSION['rating'][$current_product_id])) {
                            $product_rating_params = get_product_rating($current_product_id);
    
                            $ratings_count = $product_rating_params['ratings_count'];
                            $product_rating_imgs = $product_rating_params['rating_imgs'];
                            $ratings_count_text = ($ratings_count == 1) ? "глас" : "гласа";

                            echo "$product_rating_imgs <span class='text'>$ratings_count $ratings_count_text</span>";
                          }
                          else {
                            $customer_has_rated_already = check_if_customer_has_rated($current_product_id);
                            
                            if($customer_has_rated_already) {
                              $product_rating_params = get_product_rating($current_product_id);
    
                              $ratings_count = $product_rating_params['ratings_count'];
                              $product_rating_imgs = $product_rating_params['rating_imgs'];
                              $ratings_count_text = ($ratings_count == 1) ? "глас" : "гласа";

                              echo "$product_rating_imgs <span class='text'>$ratings_count $ratings_count_text</span>";
                            }
                            else {
                              //print rating form
?>
                            <fieldset data="not_set" style="margin-bottom:6px;">
                              <img src="/frontstore/images/star-empty.png" id="rating_star1" class="rating_star" data="1" alt="rating star" width="17" height="16" />
                              <img src="/frontstore/images/star-empty.png" id="rating_star2" class="rating_star" data="2" alt="rating star" width="17" height="16" />
                              <img src="/frontstore/images/star-empty.png" id="rating_star3" class="rating_star" data="3" alt="rating star" width="17" height="16" />
                              <img src="/frontstore/images/star-empty.png" id="rating_star4" class="rating_star" data="4" alt="rating star" width="17" height="16" />
                              <img src="/frontstore/images/star-empty.png" id="rating_star5" class="rating_star" data="5" alt="rating star" width="17" height="16" />
                            </fieldset>
                            <p class="rating_hint"></p>
                            <a href="javascript:;" onClick="AddProductRating('<?=$current_product_id;?>')" id="btn_add_rating" class="button btn btn-outline" style="display: none;">
                              <?=$languages[$current_lang]['btn_add_rating'];?>
                            </a>
                            <input type="hidden" name="stars_opacity" id="stars_opacity" value="1" />
                            <input type="hidden" name="rating_hint" id="rating_hint" value="" />
                            <input type="hidden" name="rating_stars_value" id="rating_stars_value" value="" />
<?php
                            }
                          }
?> 
                        </div>
                        <script type="text/javascript">
                          $(document).ready(function() {
                            $(".product_qty_minus").bind('click', function() {
                              var product_qty_input = $(".input_product_qty");
                              var product_qty = parseInt(product_qty_input.val())-1;
                              if(product_qty >= "1") {
                                product_qty_input.val(product_qty);
                              }
                            });
                            $(".product_qty_plus").bind('click', function() {
                              var product_qty_input = $(".input_product_qty");
                              var product_qty = parseInt(product_qty_input.val())+1;
                              product_qty_input.val(product_qty);
                            });
                            $(".rating_star").mouseenter(function() {
                              $(".rating_star").attr("src","/frontstore/images/star-empty.png");
                              var number = $(this).attr("data");
                              var i=0;
                              switch(number) {
                                case "1":
                                  $(this).attr("src","/frontstore/images/star-full2.png");
                                  $("#rating_stars img").css("opacity","0.4");
                                  $(".rating_hint").html("<?=$languages[$current_lang]['text_rating_2'];?>");
                                  break;
                                case "2":
                                  for(i; i<=number ;i++) {
                                    $("#rating_star"+i).attr("src","/frontstore/images/star-full2.png");
                                  }
                                  $("#rating_stars img").css("opacity","0.6");
                                  $(".rating_hint").html("<?=$languages[$current_lang]['text_rating_3'];?>");
                                  break;
                                case "3":
                                  for(i; i<=number ;i++) {
                                    $("#rating_star"+i).attr("src","/frontstore/images/star-full2.png");
                                  }
                                  $("#rating_stars img").css("opacity","0.7");
                                  $(".rating_hint").html("<?=$languages[$current_lang]['text_rating_4'];?>");
                                  break;
                                case "4":
                                  for(i; i<=number ;i++) {
                                    $("#rating_star"+i).attr("src","/frontstore/images/star-full2.png");
                                  }
                                  $("#rating_stars img").css("opacity","0.8");
                                  $(".rating_hint").html("<?=$languages[$current_lang]['text_rating_5'];?>");
                                  break;
                                case "5":
                                  for(i; i<=number ;i++) {
                                    $("#rating_star"+i).attr("src","/frontstore/images/star-full2.png");
                                  }
                                  $("#rating_stars img").css("opacity","1");
                                  $(".rating_hint").html("<?=$languages[$current_lang]['text_rating_6'];?>");
                                  break;
                              }
                            });
                            $(".rating_star").click(function() {
                              var number = $(this).attr("data");
                              var i=0;
                              $(".rating_star").attr("src","/frontstore/images/star-empty.png");
                              $(".rating_star").removeClass("set");
                              $("#rating_stars fieldset").attr("data","set");
                              $("#rating_stars_value").val(number);
                              $("#btn_add_rating").show();
                              switch(number) {
                                case "1":
                                  $(this).attr("src","/frontstore/images/star-full2.png");
                                  $(this).addClass("set");
                                  $("#stars_opacity").val("0.4");
                                  $("#rating_hint").val("<?=$languages[$current_lang]['text_rating_2'];?>");
                                  break;
                                case "2":
                                  for(i; i<=number ;i++) {
                                    $("#rating_star"+i).attr("src","/frontstore/images/star-full2.png");
                                    $("#rating_star"+i).addClass("set");
                                  }
                                  $("#stars_opacity").val("0.6");
                                  $("#rating_hint").val("<?=$languages[$current_lang]['text_rating_3'];?>");
                                  break;
                                case "3":
                                  for(i; i<=number ;i++) {
                                    $("#rating_star"+i).attr("src","/frontstore/images/star-full2.png");
                                    $("#rating_star"+i).addClass("set");
                                  }
                                  $("#stars_opacity").val("0.7");
                                  $("#rating_hint").val("<?=$languages[$current_lang]['text_rating_4'];?>");
                                  break;
                                case "4":
                                  for(i; i<=number ;i++) {
                                    $("#rating_star"+i).attr("src","/frontstore/images/star-full2.png");
                                    $("#rating_star"+i).addClass("set");
                                  }
                                  $("#stars_opacity").val("0.8");
                                  $("#rating_hint").val("<?=$languages[$current_lang]['text_rating_5'];?>");
                                  break;
                                case "5":
                                  for(i; i<=number ;i++) {
                                    $("#rating_star"+i).attr("src","/frontstore/images/star-full2.png");
                                    $("#rating_star"+i).addClass("set");
                                  }
                                  $("#stars_opacity").val("1");
                                  $("#rating_hint").val("<?=$languages[$current_lang]['text_rating_6'];?>");
                                  break;
                              }
                            })
                            $(".rating_star").mouseleave(function() {
                              $(this).attr("src","/frontstore/images/star-empty.png");
                            });
                            $("#rating_stars fieldset").mouseleave(function() {
                              var fieldsetData = $("#rating_stars fieldset").attr("data");
                              var starsOpacity = $("#stars_opacity").val();
                              var ratingHint = $("#rating_hint").val();
                              if(fieldsetData == "not_set") {
                                $(".rating_star").attr("src","/frontstore/images/star-empty.png");
                                $(".rating_hint").html("");
                                $("#rating_stars img").css("opacity","1");
                                $("#btn_add_rating").hide();
                              }
                              else {
                                $(".rating_star").attr("src","/frontstore/images/star-empty.png");
                                $(".rating_star.set").attr("src","/frontstore/images/star-full2.png");
                                $(".rating_hint").html(ratingHint);
                                $("#rating_stars img").css("opacity",starsOpacity);
                                $("#btn_add_rating").show();
                              }
                            })
                          });
                        </script>
                      </div> <!-- end product_attributes -->
                      <div class="box-cart-bottom">
                        
                      </div> <!-- end box-cart-bottom -->
                    </div> <!-- end box-info-product -->
                  </div>
                </div> <!-- end pb-right-column-->
              </div> <!-- end primary_block -->
          </div>
        </div>
      </section>
  </body>
