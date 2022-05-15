<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

if (isset($_GET['pid'])) {
  $current_product_id = mysqli_real_escape_string($db_link, intval($_GET['pid'])); // current selected product
}
if (!isset($current_product_id) && isset($directory_id))
  $current_product_id = mysqli_real_escape_string($db_link, intval($directory_id));

if (isset($_GET['page']) && !empty($_GET['page'])) {
  //$current_category_path = $_GET['page'];

  $current_page_path_string = mysqli_real_escape_string($db_link, strip_tags($_GET['page']));
  //echo $current_page_path_string;
  $current_category_path = explode("/", $current_page_path_string);
  $count_category_path_elements = count($current_category_path) - 1;
  $current_category_pretty_url = $current_category_path[$count_category_path_elements];
  $current_lang = $current_category_path[0];
}

$query_current_params = "SELECT `languages`.`language_id`,`languages`.`language_root_content_id`,`contents`.`content_hierarchy_path` ,`contents`.`content_is_default`
                             FROM `languages` 
                       INNER JOIN `contents` ON `contents`.`content_id` = `languages`.`language_default_content_id`
                            WHERE `language_code` = '$current_lang'";
//echo $query_current_params;
$result_current_params = mysqli_query($db_link, $query_current_params);
if (!$result_current_params)
  echo mysqli_error($db_link);
if (mysqli_num_rows($result_current_params) > 0) {
  $row_current_params = mysqli_fetch_assoc($result_current_params);
  $current_language_id = $row_current_params['language_id'];
  $content_hierarchy_ids = $row_current_params['language_root_content_id'];
  $content_is_default = $row_current_params['content_is_default'];
  $home_page_url = ($content_is_default == 1) ? "" : $row_current_params['content_hierarchy_path'];
  
  mysqli_free_result($result_current_params);
}

$category_is_active = 0;
$cd_is_active = 0;
$query_category_params = "SELECT `categories`.`category_id`,`categories`.`category_hierarchy_ids`,`categories`.`category_is_active`,
                                 `category_descriptions`.`cd_name`,`category_descriptions`.`cd_is_active`
                            FROM `categories` 
                      INNER JOIN `category_descriptions` USING(`category_id`)
                      INNER JOIN `product_to_category` USING(`category_id`)
                           WHERE `product_to_category`.`product_id` = '$current_product_id' AND `category_descriptions`.`language_id` = '$current_language_id'";
//if($current_lang == "en") echo $query_category_params;
$result_category_params = mysqli_query($db_link, $query_category_params);
if (!$result_category_params)
  echo mysqli_error($db_link);
if (mysqli_num_rows($result_category_params) > 0) {
  $row_category_params = mysqli_fetch_assoc($result_category_params);
  $current_category_id = $row_category_params['category_id'];
  $cd_name = $row_category_params['cd_name'];
  $category_is_active = $row_category_params['category_is_active'];
  $cd_is_active = $row_category_params['cd_is_active'];
  $category_hierarchy_ids = $row_category_params['category_hierarchy_ids'];
  
  mysqli_free_result($result_category_params);
}
else {
  print_error_page();
}

$category_products_count = get_category_products_count($current_category_id);
$category_products_min_max_price = get_category_products_min_max_price($current_category_id);
$min_product_price = $category_products_min_max_price['min_product_price'];
$max_product_price = $category_products_min_max_price['max_product_price'];
//if($_SESSION['debug']) echo "$min_product_price = $max_product_price - $cd_name";

if ($category_is_active == 0 || $cd_is_active == 0) {
  print_error_page();
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
                     WHERE `products`.`product_id` = '$current_product_id' AND `products`.`product_is_active` = '1'
                       AND `product_description`.`language_id` = '$current_language_id'
                       AND `weight_class_description`.`language_id` = '$current_language_id' AND `length_class_description`.`language_id` = '$current_language_id'";
//echo $query_product;
$result_product = mysqli_query($db_link, $query_product);
if (!$result_product)
  echo mysqli_error($db_link);
if (mysqli_num_rows($result_product) > 0) {
  $product_row = mysqli_fetch_assoc($result_product);

  $wcd_unit = $product_row['wcd_unit'];
  $lcd_unit = $product_row['lcd_unit'];
  $product_model = $product_row['product_model'];
  $product_isbn = $product_row['product_isbn'];
  $product_quantity = $product_row['product_quantity'];
  $product_subtract = $product_row['product_subtract'];
  $product_price = $product_row['product_price'];
  $pd_price = (!empty($product_row['pd_price'])) ? $product_row['pd_price'] : "";
  $price_discount_class = (!empty($pd_price)) ? " class='line_through'" : "";
  $product_weight = $product_row['product_weight'];
  $stock_status_id = $product_row['stock_status_id'];
  $product_length = number_format($product_row['product_length'], 2, ".", ".");
  $product_width = number_format($product_row['product_width'], 2, ".", ".");
  $product_height = number_format($product_row['product_height'], 2, ".", ".");
  $pd_name = stripslashes($product_row['pd_name']);
  $pd_name_for_link = beautify_name_for_url($pd_name);
  $cyr_url = cyrialize_url($pd_name_for_link);
  $pd_description = stripslashes($product_row['pd_description']);
  $meta_price = (!empty($pd_price)) ? $pd_price : $product_price;
  if($current_lang == "bg") {
    $pd_name_for_alt = "({$languages[$current_lang]['text_flowers_delivery']} $pd_name";
    $pd_name_for_title = "$pd_name с доставка на цветя от цветарски магазин LaRose";
    $pd_meta_title = "Купи за $meta_price лв. $pd_name с доставка на цветя LaRose";
    $pd_meta_description = "Поръчай ➤ $pd_name ➤ от цветарски магазин ЛаРоз сега на Супер цена $meta_price лв. ✈ Доставка на цветя и избор от над 
                          $category_products_count продукта $cd_name на цени започващи от $min_product_price лв. Повече онлайн или на ☎ 089 881 8116";
  }
  else {
    $pd_name_for_alt = "$pd_name";
    $pd_name_for_title = "$pd_name";
    $pd_meta_title = "Buy $pd_name with flower delivery Sofia LaRose";
    $pd_meta_description = "$pd_name ➤ from flower shop Sofia Bulgaria LaRose at Super price $meta_price BGN ✈ Flower delivery and choice from above 
                        $category_products_count products $cd_name at prices starting from $min_product_price BGN More online or ☎ 00359898818116";
  }
  $pd_meta_keywords = $product_row['pd_meta_keywords'];
  
  mysqli_free_result($result_product);
} else {
  print_error_page();
}
//if($_SESSION['debug']) echo "$current_page_pretty_url = $cyr_url";
if($cyr_url !== $current_page_pretty_url) {
  print_error_page();
}

//echo"<pre>";print_r($product_row);
// encrease product_viewed by one
$query_update_product = "UPDATE `products` SET `product_viewed` = `product_viewed`+1 WHERE `product_id` = '$current_product_id'";
$result_update_product = mysqli_query($db_link, $query_update_product);
if (mysqli_affected_rows($db_link) <= 0) {
  echo $languages[$current_lang]['sql_error_update'] . " - " . mysqli_error($db_link);
}

$pi_names_array = get_product_images($current_product_id);
$pd_images_folder = "/frontstore/images/products/";
if ((isset($pi_names_array['default']['pi_name']) && !empty($pi_names_array['default']['pi_name']))) {

  $default_img_id = $pi_names_array['default']['product_image_id'];
  $default_img = $pi_names_array['default']['pi_name'];
  $default_img_exploded = explode(".", $default_img);
  $default_img_name = $default_img_exploded[0];
  $default_img_exstension = $default_img_exploded[1];

  $default_img_path_large = $pd_images_folder . $default_img;

  $default_img_thickbox_default = $pd_images_folder . $default_img_name . "_thickbox_default." . $default_img_exstension;

  $default_img_cart_default = $pd_images_folder . $default_img_name . "_cart_default." . $default_img_exstension;
  $gallery_img_cart = $pd_images_folder . $default_img_name . "_small_default." . $default_img_exstension;
  $fb_image = $default_img_thickbox_default;
  @list($fb_width, $fb_height) = getimagesize($_SERVER['DOCUMENT_ROOT'] . $fb_image);
  $full_path = $_SERVER['DOCUMENT_ROOT'] . $pd_images_folder;

  $file = $full_path . $default_img;

  list($width, $height) = @getimagesize($file);

  if ($width > $height) {
    $default_img_style = "style='width:100%;height:auto;'";
  } else {
    $default_img_style = "style='height:100%;width:auto;'";
  }

  $default_img_cart_default_params = @getimagesize($_SERVER['DOCUMENT_ROOT'] . $default_img_cart_default);
  //$default_img_cart_default_dimensions = $default_img_cart_default_params[3];
  $no_image = false;
} else {
  $default_img_thickbox_default = "/frontstore/images/products/no_image_gal_zoom.jpg";
  @$default_img_params = getimagesize($_SERVER['DOCUMENT_ROOT'] . $default_img_thickbox_default);
  $default_img_dimensions = $default_img_params[3];
  $no_image = true;
}

//if($_SESSION['debug']) print_array_for_debug($_SERVER);
$use_hreflang = false;
$href_count = 0;
$hreflang = "";

foreach (get_languages() as $lang) {
  $lang_id = $lang['language_id'];
  $language_code = $lang['language_code']; 

  $query_product = "SELECT `product_description`.`pd_name`
                     FROM `products`
               INNER JOIN `product_to_category` USING(`product_id`)
               INNER JOIN `product_description` USING(`product_id`)
               INNER JOIN `categories` ON (`product_to_category`.`category_id` = `categories`.`category_id` AND `categories`.`category_is_active` = '1')
               INNER JOIN `category_descriptions` ON (`categories`.`category_id` = `category_descriptions`.`category_id` AND `category_descriptions`.`cd_is_active` = '1')
                    WHERE `products`.`product_id` = '$current_product_id' AND `products`.`product_is_active` = '1' 
                      AND `product_description`.`language_id` = '$lang_id' AND `products`.`stock_status_id` = '1'
                      AND `product_description`.`pd_is_active` = '1' AND `category_descriptions`.`language_id` = '$lang_id'";
  //echo "$query_product<br>";
  $result_product = mysqli_query($db_link, $query_product);
  if(!$result_product) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_product) > 0) {

    $hreflang_row = mysqli_fetch_assoc($result_product);

    $hreflang_name = $hreflang_row['pd_name'];
    $hreflang_name_for_link = beautify_name_for_url($hreflang_name);
    $hreflang_url = cyrialize_url($hreflang_name_for_link);
    $href = BASEPATHNOSLASH."/$language_code/pid-$current_product_id/$hreflang_url";

    $hreflang .= "<link rel='alternate' hreflang='$language_code' href='$href' />\n";
    if($language_code == 'bg') {
      $hreflang .= "<link rel='alternate' hreflang='x-default' href='$href' />\n";
    }

    $href_count++;

    mysqli_free_result($result_product);
  }
}
if($href_count > 1) $use_hreflang = true;

$additional_css = '<link rel="stylesheet" href="/frontstore/css/product.css" type="text/css" media="all" />';
$additional_js = '<script type="text/javascript" src="/frontstore/js/product.js"></script>';
$body_css = 'product';

print_html_header($pd_meta_title, $pd_meta_description, $pd_meta_keywords, $additional_css = false, $additional_js, $body_css);
//echo "<pre>";print_r($_SERVER);
//echo "<pre>";print_r($_SESSION);
?>
<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function () {
    FB.init({
      appId: '722916254478372',
      xfbml: true,
      version: 'v2.5'
    });
  };

  (function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
      return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>

<div id="columns" class="columns-container">
  <div class="container">
    <div id="product_block_<?= $current_product_id; ?>" class="row">
      <input type="hidden" name="product_id" class="product_id" value="<?= $current_product_id; ?>">
      <input type="hidden" name="product_isbn" class="product_isbn" value="<?= $product_isbn; ?>">
      <input type="hidden" name="product_price" class="product_price" value="<?= $product_price; ?>">
      <input type="hidden" name="pd_price" class="pd_price" value="<?= $pd_price; ?>">
      <input type="hidden" name="product_name" class="product_name" value="<?= $pd_name; ?>">
      <input type="hidden" name="product_url" class="product_url" value="<?= urldecode($_SERVER['REQUEST_URI']); ?>">

<?php print_left_column(); ?>

      <!-- Center -->
      <div id="center_column" class="col-sp-12 col-xs-12 col-sm-12 col-md-9 col-lg-9">
        <div id="breadcrumb" class="clearfix">			

          <!-- Breadcrumb -->
          <div class="breadcrumb clearfix">
<?php print_categories_breadcrumbs($category_hierarchy_ids, $pd_name); ?>
          </div>
          <!-- /Breadcrumb --> 
        </div>

        <div class="primary_block row" itemscope itemtype="https://schema.org/Product">

          <!-- left infos-->  
          <div class="pb-left-column col-xs-12 col-sm-12 col-md-4 col-lg-5">
            <!-- product img-->       
            <?php
            if ($no_image) {
              echo "<img src='$default_img_thickbox_default' alt='$pd_name' $default_img_dimensions>";
            } else {
              $pd_images_watermark = "/frontstore/images/watermark-340-500.png";
              ?>
              <input type="hidden" name="product_img_big" class="product_img_big" value="<?= $default_img_thickbox_default; ?>">
              <input type="hidden" name="product_img" class="product_img" value="<?= $gallery_img_cart; ?>">
              <div id="image-block" class="clearfix">
                <div class="p-label">
                </div>
                <span id="view_full_size">
                  <!--<img src="<?= $pd_images_watermark; ?>" class="watermark" style="left: 0;" alt="<?= $pd_name_for_alt; ?>" title="<?= $pd_name_for_title; ?>" width="340" height="500" >-->
                  <img id="bigpic" itemprop="image" src="<?= $default_img_thickbox_default; ?>" title="<?= $pd_name_for_title; ?>" alt="<?= $pd_name; ?>"/>
                  <span class="span_link no-print status-enable btn btn-outline"></span>
                </span>
              </div> <!-- end image-block -->
              <!-- thumbnails -->
              <div id="views_block" class="clearfix ">
                <span class="view_scroll_spacer">
                  <a id="view_scroll_left" class="" title="Other views" href="javascript:{}">

                  </a>
                </span>
                <div id="thumbs_list">
                  <ul id="thumbs_list_frame">
                    <li id="thumbnail_<?= $default_img_id; ?>">
                      <a href="<?= $default_img_thickbox_default; ?>" data-fancybox-group="other-views" class="fancybox shown" title="<?= $pd_name; ?>">
                        <img class="img-responsive" id="thumb_<?= $default_img_id; ?>" <?= $default_img_style; ?> src="<?= $default_img_cart_default; ?>" alt="<?= $pd_name; ?>" title="<?= $pd_name; ?>" itemprop="image" />
                      </a>
                    </li>
                    <?php
                    if (isset($pi_names_array['gallery'])) {

                      $images_count = count($pi_names_array['gallery']);

                      foreach ($pi_names_array['gallery'] as $img_key => $prod_gallery_image) {
                        //echo"<pre>";print_r($prod_gallery_image);
                        $gallery_img_id = $prod_gallery_image['product_image_id'];
                        $gallery_img = $prod_gallery_image['pi_name'];
                        $full_path = $_SERVER['DOCUMENT_ROOT'] . $pd_images_folder;

                        $file = $full_path . $gallery_img;

                        list($width, $height) = getimagesize($file);

                        if ($width > $height) {
                          $img_style = "style='width:100%;height:auto;'";
                        } else {
                          $img_style = "style='height:100%;width:auto;'";
                        }

                        $gallery_img_exploded = explode(".", $gallery_img);
                        $gallery_img_name = $gallery_img_exploded[0];
                        $gallery_img_exstension = $gallery_img_exploded[1];

                        $gallery_img_path_large = $pd_images_folder . $gallery_img;

                        $gallery_img_thickbox_default = $pd_images_folder . $gallery_img_name . "_thickbox_default." . $gallery_img_exstension;

                        $gallery_img_cart_default = $pd_images_folder . $gallery_img_name . "_cart_default." . $gallery_img_exstension;

                        $class_last = ($img_key == $images_count - 1) ? 'class="last"' : "";
                        ?>
                        <li id="thumbnail_<?= $gallery_img_id ?>" <?= $class_last; ?>>
                          <a href="<?= $gallery_img_thickbox_default; ?>"	data-fancybox-group="other-views" class="fancybox" title="<?= $pd_name; ?>">
                            <img class="img-responsive" id="thumb_<?= $gallery_img_id ?>" <?= $img_style; ?> src="<?= $gallery_img_cart_default; ?>" alt="<?= $pd_name; ?>" title="<?= $pd_name; ?>" itemprop="image" />
                          </a>
                        </li>
                        <?php
                      }
                    }
                    ?>
                  </ul>
                </div> <!-- end thumbs_list -->
                <a id="view_scroll_right" title="Other views" href="javascript:{}">

                </a>
              </div> <!-- end views-block -->
              <!-- end thumbnails -->
  <?php
}
?>
          </div> <!-- end pb-left-column -->
          <!-- end left infos--> 
          <!-- center infos -->
          <div class="pb-center-column col-xs-12 col-sm-6 col-md-4 col-lg-4">

            <h1 itemprop="name"><?= $pd_name; ?></h1>

            <!--  /Module ProductComments -->
            <p id="product_reference">
              <label><?= $languages[$current_lang]['header_isbn']; ?>: </label>
              <span class="editable" itemprop="sku"><?= $product_isbn; ?></span>
            </p>
            <div id="short_description_block">
              <div id="short_description_content" class="rte align_justify" itemprop="description">
                <p><?= $pd_description; ?></p>
              </div>
            </div> <!-- end short_description_block -->

            <div class="socialsharing_product list-inline no-print">
              <div class="fb-like" data-href="<?= $_SERVER['SERVER_NAME'] . urldecode($_SERVER['REQUEST_URI']); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
            </div>

            <!-- usefull links-->
            <ul id="usefull_link_block" class="clearfix no-print">
              <li class="no-print">
<?php
$onclick_wishlist_fn = (user_is_loged()) ? "AddProductToWishlist('$current_product_id','$pd_name')" : "OpenModalWindow(loggin_required_for_wishlist)";
?>
                <a class="btn-tooltip addToWishlist no_padding" href="javascript:;" onclick="<?= $onclick_wishlist_fn; ?>" data-toggle="tooltip" title="<?= $languages[$current_lang]['title_add_to_wishlist']; ?>">
                  <i class="fa fa-heart fa-lg" style="margin-right: 7px;"></i> <?= $languages[$current_lang]['title_add_to_wishlist']; ?>
                </a>
              </li>

              <li class="sendtofriend">
                <a id="send_friend_button" href="#send_friend_form">
<?= $languages[$current_lang]['text_send_to_friend']; ?>
                </a>
                <div style="display: none;">
                  <div id="send_friend_form">
                    <p  class="page-subheading">
<?= $languages[$current_lang]['text_send_to_friend']; ?>
                    </p>
                    <div class="row">
                      <div class="product clearfix col-xs-12 col-sm-6">
                        <img src="<?= $default_img_thickbox_default; ?>" alt="<?= $pd_name; ?>" />
                        <div class="product_desc">
                          <p class="product_name">
                            <strong><?= $pd_name; ?></strong>
                          </p>
                          <p><?= $pd_description; ?></p>
                        </div>
                      </div><!-- .product -->
                      <div class="send_friend_form_content col-xs-12 col-sm-6" id="send_friend_form_content">
                        <div id="send_friend_form_error" class="error"></div>
                        <div id="send_friend_form_success" class="success"></div>
                        <div class="form_container">
                          <p class="intro_form"><?= $languages[$current_lang]['text_recipient']; ?>:</p>
                          <p class="text">
                            <label for="friend_name">
<?= $languages[$current_lang]['text_friends_name']; ?> <sup class="required">*</sup> :
                            </label>
                            <input id="friend_name" name="friend_name" type="text" value="" class="form-control" style="width: 100%;"/>
                          </p>
                          <p class="text">
                            <label for="friend_email">
<?= $languages[$current_lang]['text_friends_email']; ?> <sup class="required">*</sup> :
                            </label>
                            <input id="friend_email" name="friend_email" type="text" value="" class="form-control" style="width: 100%;"/>
                          </p>
                          <p class="txt_required">
                            <sup class="required">*</sup> <?= $languages[$current_lang]['text_required_fields']; ?>
                          </p>
                        </div>
                        <p class="submit">
                          <button id="sendEmail" onClick="SendToAFriend()" class="btn button button-small btn-sm" name="sendEmail" type="submit">
                            <span><?= $languages[$current_lang]['btn_send']; ?></span>
                          </button>&nbsp;
<?= $languages[$current_lang]['text_or']; ?>&nbsp;
                          <a class="closefb" href="#">
<?= $languages[$current_lang]['btn_cancel']; ?>
                          </a>
                        </p>
                      </div> <!-- .send_friend_form_content -->
                    </div>
                  </div>
                </div>
              </li>

              <li class="print">
                <a href="javascript:print();">
<?= $languages[$current_lang]['btn_print']; ?>
                </a>
              </li>
            </ul>
          </div>
          <!-- end center infos-->
          <!-- pb-right-column-->
          <div class="pb-right-column col-xs-12 col-sm-6 col-md-4 col-lg-3">
            <!-- add to cart form-->
            <div id="buy_block">
              <div class="box-info-product">
                <div class="content_prices clearfix">
                  <!-- prices -->
                  <div class="price">
                    <meta itemprop="manufacturer" content="<?= $languages[$current_lang]['merchant']; ?>">
                    <meta itemprop="category" content="<?= $languages[$current_lang]['merchant_logo_text_title']; ?>" />  
                    <p class="our_price_display content_price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                      <input type="hidden" class="basic_price" value="<?php if (!empty($pd_price)) echo $pd_price; else echo $product_price ?>" />
                      <span id="our_price_display" class="price" itemprop="price" content="<?php if (!empty($pd_price)) echo $pd_price; else echo $product_price ?>">
                    <?php if (!empty($pd_price)) echo $pd_price; else echo $product_price ?><span class="currency">&nbsp;лв.</span>
                      </span>
                      <meta itemprop="priceCurrency" content="BGN" />
                      <link itemprop="availability" href="https://schema.org/InStock" /> 
                    </p>
                    <?php if (!empty($pd_price)) {
                      $pd_percent_reduction = 100 - (ceil(($pd_price / $product_price) * 100));
                    ?>
                      <p id="reduction_percent">
                        <span id="reduction_percent_display"><?= $pd_percent_reduction; ?> %</span>
                      </p>
                      <p id="old_price">
                        <span id="old_price_display"><?= $product_price; ?><span class="currency">&nbsp;лв.</span></span>
                        <!--  -->
                      </p>
                    <?php } ?>
                  </div> <!-- end prices -->

                  <div class="clear"></div>
                </div> <!-- end content_prices -->
                <div class="product_attributes clearfix">
                  <!-- quantity wanted -->
                  <p id="quantity_wanted_p">
                    <a href="javascript:;" class="product_qty_minus cart_quantity_down btn btn-outline button-minus btn-sm" title="<?= $languages[$current_lang]['title_subtract']; ?>" rel="nofollow">
                      <span><i class="fa fa-minus"></i></span>
                    </a>
                    <input type="text" name="product_qty" class="product_qty input_product_qty text form-control" value="1" />
                    <a href="javascript:;" class="product_qty_plus cart_quantity_up btn btn-outline button-plus btn-sm" title="<?= $languages[$current_lang]['title_add']; ?>" rel="nofollow">
                      <span><i class="fa fa-plus"></i></span>
                    </a>
                    <span class="clearfix"></span>
                  </p>
                  <div class="box-cart-bottom">
                    <div>
                      <?php
                        $no_stock = false;
                        if ($stock_status_id == 2) {
                          $no_stock = true;
                        //upon request
                      ?>
                        <p class="upon_request no-print">
                        <?= $languages[$current_lang]['text_upon_request']; ?>
                        </p>
                      <?php
                        } elseif ($stock_status_id == 3 || ($product_subtract == 1 && $product_quantity == 0)) {
                          $no_stock = true;
                          //not in stock
                      ?>
                        <p class="out-of-stock no-print">
                        <?= $languages[$current_lang]['text_out_in_stock']; ?>
                        </p>
                      <?php
                        } else {
                      ?>
                        <p id="add_to_cart" class="buttons_bottom_block no-print">
                          <a href="javascript:;" onclick="AddProductToCart('<?= $current_product_id; ?>', '<?= $current_language_id; ?>')" class="button add_to_cart btn btn-outline" rel="nofollow" title="<?= $languages[$current_lang]['btn_add_to_shopping_cart']; ?>">
                            <span><?= $languages[$current_lang]['btn_add_to_shopping_cart']; ?></span>
                          </a>
                        </p>
                      <?php
                        }
                      ?>
                    </div>
                  </div><!-- end box-cart-bottom -->
                  <label class="no_margin"><?= $languages[$current_lang]['header_rating']; ?></label>
                  <div id="rating_stars" class="clearfix">
                    <?php
                    $product_rating_params = get_product_rating($current_product_id);

                    $ratings_count = $product_rating_params['ratings_count'];
                    $product_rating_imgs = $product_rating_params['rating_imgs'];
                    $text_rate = $languages[$current_lang]['text_rate_vote'];
                    $text_rates = $languages[$current_lang]['text_rate_votes'];
                    $ratings_count_text = ($ratings_count == 1) ? $text_rate : $text_rates;

                    if ($ratings_count != 0) {
                      echo "<div class='clearfix'>$product_rating_imgs <span class='text'>$ratings_count $ratings_count_text</span></div><br>";
                    } else {
                      $text_no_rating = $languages[$current_lang]['text_no_rating'];
                      echo "<p>$text_no_rating</p>";
                    }

                    //unset($_SESSION['rating'][$current_product_id]);
                    if (isset($_SESSION['rating'][$current_product_id])) {
                      //do nothing
                    } else {
                      $customer_has_rated_already = check_if_customer_has_rated($current_product_id);

                      if ($customer_has_rated_already) {
                        //do nothing
                      } else {
                        //print rating form
                        ?>
                        <label class="no_margin"><?= $languages[$current_lang]['header_customer_rating']; ?></label>
                        <fieldset data="not_set" style="margin-bottom:6px;">
                          <img src="/frontstore/images/star-empty.png" id="rating_star1" class="rating_star" data="1" alt="rating star" width="17" height="16" /><img src="/frontstore/images/star-empty.png" id="rating_star2" class="rating_star" data="2" alt="rating star" width="17" height="16" /><img src="/frontstore/images/star-empty.png" id="rating_star3" class="rating_star" data="3" alt="rating star" width="17" height="16" /><img src="/frontstore/images/star-empty.png" id="rating_star4" class="rating_star" data="4" alt="rating star" width="17" height="16" /><img src="/frontstore/images/star-empty.png" id="rating_star5" class="rating_star" data="5" alt="rating star" width="17" height="16" />
                        </fieldset>
                        <p class="rating_hint"></p>
                        <a href="javascript:;" onClick="AddProductRating('<?= $current_product_id; ?>')" id="btn_add_rating" class="button btn btn-outline" style="display: none;">
    <?= $languages[$current_lang]['btn_add_rating']; ?>
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
                    $(document).ready(function () {
                      $(".product_qty_minus").bind('click', function () {
                        var product_qty_input = $(".input_product_qty");
                        var product_qty = parseInt(product_qty_input.val()) - 1;
                        if (product_qty >= "1") {
                          product_qty_input.val(product_qty);
                        }
                      });
                      $(".product_qty_plus").bind('click', function () {
                        var product_qty_input = $(".input_product_qty");
                        var product_qty = parseInt(product_qty_input.val()) + 1;
                        product_qty_input.val(product_qty);
                      });
                      $(".rating_star").mouseenter(function () {
                        $(".rating_star").attr("src", "/frontstore/images/star-empty.png");
                        var number = $(this).attr("data");
                        var i = 0;
                        switch (number) {
                          case "1":
                            $(this).attr("src", "/frontstore/images/star-full2.png");
                            $("#rating_stars img").css("opacity", "0.4");
                            $(".rating_hint").html("<?= $languages[$current_lang]['text_rating_2']; ?>");
                            break;
                          case "2":
                            for (i; i <= number; i++) {
                              $("#rating_star" + i).attr("src", "/frontstore/images/star-full2.png");
                            }
                            $("#rating_stars img").css("opacity", "0.6");
                            $(".rating_hint").html("<?= $languages[$current_lang]['text_rating_3']; ?>");
                            break;
                          case "3":
                            for (i; i <= number; i++) {
                              $("#rating_star" + i).attr("src", "/frontstore/images/star-full2.png");
                            }
                            $("#rating_stars img").css("opacity", "0.7");
                            $(".rating_hint").html("<?= $languages[$current_lang]['text_rating_4']; ?>");
                            break;
                          case "4":
                            for (i; i <= number; i++) {
                              $("#rating_star" + i).attr("src", "/frontstore/images/star-full2.png");
                            }
                            $("#rating_stars img").css("opacity", "0.8");
                            $(".rating_hint").html("<?= $languages[$current_lang]['text_rating_5']; ?>");
                            break;
                          case "5":
                            for (i; i <= number; i++) {
                              $("#rating_star" + i).attr("src", "/frontstore/images/star-full2.png");
                            }
                            $("#rating_stars img").css("opacity", "1");
                            $(".rating_hint").html("<?= $languages[$current_lang]['text_rating_6']; ?>");
                            break;
                        }
                      });
                      $(".rating_star").click(function () {
                        var number = $(this).attr("data");
                        var i = 0;
                        $(".rating_star").attr("src", "/frontstore/images/star-empty.png");
                        $(".rating_star").removeClass("set");
                        $("#rating_stars fieldset").attr("data", "set");
                        $("#rating_stars_value").val(number);
                        $("#btn_add_rating").show();
                        switch (number) {
                          case "1":
                            $(this).attr("src", "/frontstore/images/star-full2.png");
                            $(this).addClass("set");
                            $("#stars_opacity").val("0.4");
                            $("#rating_hint").val("<?= $languages[$current_lang]['text_rating_2']; ?>");
                            break;
                          case "2":
                            for (i; i <= number; i++) {
                              $("#rating_star" + i).attr("src", "/frontstore/images/star-full2.png");
                              $("#rating_star" + i).addClass("set");
                            }
                            $("#stars_opacity").val("0.6");
                            $("#rating_hint").val("<?= $languages[$current_lang]['text_rating_3']; ?>");
                            break;
                          case "3":
                            for (i; i <= number; i++) {
                              $("#rating_star" + i).attr("src", "/frontstore/images/star-full2.png");
                              $("#rating_star" + i).addClass("set");
                            }
                            $("#stars_opacity").val("0.7");
                            $("#rating_hint").val("<?= $languages[$current_lang]['text_rating_4']; ?>");
                            break;
                          case "4":
                            for (i; i <= number; i++) {
                              $("#rating_star" + i).attr("src", "/frontstore/images/star-full2.png");
                              $("#rating_star" + i).addClass("set");
                            }
                            $("#stars_opacity").val("0.8");
                            $("#rating_hint").val("<?= $languages[$current_lang]['text_rating_5']; ?>");
                            break;
                          case "5":
                            for (i; i <= number; i++) {
                              $("#rating_star" + i).attr("src", "/frontstore/images/star-full2.png");
                              $("#rating_star" + i).addClass("set");
                            }
                            $("#stars_opacity").val("1");
                            $("#rating_hint").val("<?= $languages[$current_lang]['text_rating_6']; ?>");
                            break;
                        }
                      })
                      $(".rating_star").mouseleave(function () {
                        $(this).attr("src", "/frontstore/images/star-empty.png");
                      });
                      $("#rating_stars fieldset").mouseleave(function () {
                        var fieldsetData = $("#rating_stars fieldset").attr("data");
                        var starsOpacity = $("#stars_opacity").val();
                        var ratingHint = $("#rating_hint").val();
                        if (fieldsetData == "not_set") {
                          $(".rating_star").attr("src", "/frontstore/images/star-empty.png");
                          $(".rating_hint").html("");
                          $("#rating_stars img").css("opacity", "1");
                          $("#btn_add_rating").hide();
                        } else {
                          $(".rating_star").attr("src", "/frontstore/images/star-empty.png");
                          $(".rating_star.set").attr("src", "/frontstore/images/star-full2.png");
                          $(".rating_hint").html(ratingHint);
                          $("#rating_stars img").css("opacity", starsOpacity);
                          $("#btn_add_rating").show();
                        }
                      })
                    });
                  </script>
                </div> <!-- end product_attributes -->
              </div> <!-- end box-info-product -->
            </div>
          </div> <!-- end pb-right-column-->
        </div> <!-- end primary_block -->
        <?php
          if($no_stock) {
            print_similar_products($product_price, $current_category_id);
          }
        ?>
      </div>
    </div>
  </div>
</div>
<?php
print_html_footer();
//echo "<pre>";print_r($_SERVER);
?>
