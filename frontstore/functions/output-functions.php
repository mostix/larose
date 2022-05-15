<?php
 
function print_html_header($meta_title,$meta_description,$meta_keywords,$additional_css = false,$additional_js = false,$body_css = false) {

  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  global $use_hreflang;
  global $hreflang;
  global $canonical_link;
  global $meta_links;
  global $home_page_url;
  global $content_is_home_page;
  global $current_page_path_string;
  global $fb_image;
  global $fb_width;
  global $fb_height;
  global $robots_meta;
  global $content_hierarchy_ids;

  //unset($_SESSION['currencies']);
  if(!isset($_SESSION['currencies'])) {
    $_SESSION['currencies'] = get_currencies();
    $_COOKIE['currency'] = $_SESSION['currencies']['default']['currency_id'];
    $currency_symbol_left = $_SESSION['currencies']['default']['currency_symbol_left'];
    $currency_symbol_right = $_SESSION['currencies']['default']['currency_symbol_right'];
    $currency_symbol_left_span = "";
    $currency_symbol_right_span = "";
    if(!empty($currency_symbol_left)) $currency_symbol_left_span = "<span class='currency'>$currency_symbol_left</span>";
    if(!empty($currency_symbol_right)) $currency_symbol_right_span = "<span class='currency'>$currency_symbol_right</span>";
  }
  else {
    @$current_currency_id = $_COOKIE['currency'];
    @$currency_symbol_left = $_SESSION['currencies'][$current_currency_id]['currency_symbol_left'];
    @$currency_symbol_right = $_SESSION['currencies'][$current_currency_id]['currency_symbol_right'];
    $currency_symbol_left_span = "";
    $currency_symbol_right_span = "";
    if(!empty($currency_symbol_left)) $currency_symbol_left_span = "<span class='currency'>$currency_symbol_left</span>";
    if(!empty($currency_symbol_right)) $currency_symbol_right_span = "<span class='currency'>$currency_symbol_right</span>";
  }

  $customer_ip = $_SERVER['REMOTE_ADDR']; //77.238.81.170
  $allowed_ips = array("87.227.189.87s");

  $meta_title = (!empty($meta_title)) ? $meta_title : $languages[$current_lang]['default_meta_title'];
  $meta_description = (!empty($meta_description)) ? $meta_description : "";
  $meta_keywords = (!empty($meta_keywords)) ? $meta_keywords : "cvetarski magazin, цветарски магазин, цветарски магазин софия, цветарски магазин студентски град, цветя онлайн, cvetia online, buketi online, cvetarski magazin sofia, доставка на цветя, dostavka cvetq, cvetia dostavka";
  if(!isset($fb_image)) {
    $fb_image = "/frontstore/images/logo-larose.png";
    @list($fb_width,$fb_height) = getimagesize($_SERVER['DOCUMENT_ROOT']."/frontstore/images/logo-larose.png");
  }

//    unset($_SESSION['cart']);
//    session_destroy();

  if(!isset($_SESSION['captcha123'])) {
    $_SESSION['captcha123'] = array();
    $_SESSION['captcha_error']['count'] = 0;
    $rnd = rand(1, 99);
    $query = "SELECT `captchas`.* FROM `captchas` LIMIT $rnd,1";
    //echo $query;
    $result = mysqli_query($db_link, $query);
    if (!$result)
      echo mysqli_error($db_link);
    if (mysqli_num_rows($result) > 0) {

      $captcha = mysqli_fetch_assoc($result);
      $_SESSION['captcha123']['img'] = $captcha['captcha_image'];
      $_SESSION['captcha123']['code'] = $captcha['captcha_number'];
      setcookie("captcha_code", $captcha['captcha_number'], time() + 3600, "/"); // 1 hour
    }
  }

  if(!$body_css) $body_css = "index";
  if(!$robots_meta) $robots_meta = "index,follow";
?>
<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="bg"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="bg"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="bg"><![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9" lang="bg"><![endif]-->
<html lang="<?=$current_lang;?>"  class="default" >
<head>
<meta charset="utf-8" />
<title><?=strip_tags($meta_title);?></title>
<meta name="description" content="<?=strip_tags($meta_description);?>">
<meta name="keywords" content="<?=strip_tags($meta_keywords);?>" >
<meta name="robots" content="<?=$robots_meta;?>" />
<meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="author" content="Eterrasystems Ltd.">
<meta property="og:site_name" content="<?=$languages[$current_lang]['e_shop_cms'];?>">
<meta property="og:locale" content="bg_BG">
<meta property="fb:app_id" content="722916254478372">
<meta property="og:url" content="<?="https://".urldecode($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);?>" />
<meta property="og:type" content="product" />
<meta property="og:title" content="<?=strip_tags($meta_title);?>" />
<meta property="og:description" content="<?=strip_tags($meta_description);?>" />
<meta property="og:image" content="<?="https://".$_SERVER['SERVER_NAME'].$fb_image;?>" />
<meta property="og:image:width" content="<?=$fb_width;?>" />
<meta property="og:image:height" content="<?=$fb_height;?>" />
<link href="/favicon.png" rel="shortcut icon" />
<?php if(!isset($canonical_link)) echo "<link rel=\"canonical\" href=\"".urldecode($_SERVER['SCRIPT_URI'])."\" /> \n"; else echo "$canonical_link\n";?>
<?php if($use_hreflang) echo $hreflang;?>
<link rel="stylesheet" href="/frontstore/css/all-style.min.css" type="text/css" media="all" />
<?php if($additional_css) echo "$additional_css \n"; ?>
<link rel="stylesheet" href="/frontstore/js/jquery/ui/jquery-ui.min.css" type="text/css" media="all" />
<link rel="stylesheet" href="/frontstore/css/styles.css" type="text/css" media="all" />
<?php if(isset($meta_links) && !empty($meta_links)) { echo "$meta_links \n"; } ?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MDC5RZR');</script>
<script type="text/javascript">
var currencies = new Array();
var CUSTOMIZE_TEXTFIELD = 1;
var current_lang = '<?=$current_lang;?>';
var FancyboxI18nClose = '<?=$languages[$current_lang]['btn_close'];?>';
var FancyboxI18nNext = '<?=$languages[$current_lang]['btn_next'];?>';
var FancyboxI18nPrev = '<?=$languages[$current_lang]['btn_privious'];?>';
var baseDir = '/';
var baseUri = '/';
var contentOnly = false;
var displayList = true;
var homeslider_loop = 1;
var homeslider_pause = 3000;
var homeslider_speed = 500;
var homeslider_width = 779;
var img_dir = '/frontstore/img/';
var loggin_required_for_wishlist = '<?=$languages[$current_lang]['text_loggin_required_for_wishlist'];?>!';
var product_already_in_wishlist = '<?=$languages[$current_lang]['text_product_already_in_wishlist'];?>!';
var page_name = 'index';
var placeholder_blocknewsletter = '<?=$languages[$current_lang]['text_enter_your_email'];?>';
var quickView = true;
var stf_msg_error = '<?=$languages[$current_lang]['text_email_send_error'];?>';
var stf_msg_required = '<?=$languages[$current_lang]['header_required_fields'];?>';
var stf_msg_success = '<?=$languages[$current_lang]['text_email_send_successfully'];?>';
var stf_msg_title = '<?=$languages[$current_lang]['text_send_to_friend'];?>';
</script>
<script type="text/javascript" src="/frontstore/js/js-all.min.js"></script>
<?php if($additional_js) echo "$additional_js \n"; ?>
<!--[if IE 8]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
</head>
<body id="<?=$body_css;?>" class="<?=$body_css;?> hide-left-column lang_<?=$current_lang;?> fullwidth double-menu">
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MDC5RZR" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php
if(!isset($_COOKIE['cookie_policy'])) {
?>
<div id="cookies_policy">
  <a href="javascript:;" onclick="ConfirmCookiesPolicy()" class="pull-right btn btn-warning"><?=$languages[$current_lang]['btn_accept_cookie_policy'];?></a>
  <p class="no_margin"><?=$languages[$current_lang]['text_cookie_policy'];?>

    <a href="/<?=$current_lang;?>/<?php if($current_lang == "bg") echo "политика-за-бисквитките";else echo "cookie-policy"; ?>" target="_blank"><?=$languages[$current_lang]['link_cookie_policy'];?></a>
  </p>
</div>
<?php } ?>
<?php
  //unset($_COOKIE['pop_up_window']);
  if(!isset($_COOKIE['pop_up_window'])) { 
    // content_type_id = 9 is pop-up window
    $query_content = "SELECT `content_id`,`content_name`,`content_menu_text`,`content_text`
                         FROM `contents`
                        WHERE `content_type_id` = '9' AND `content_is_active` = '1'
                          AND `content_parent_id` = (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
     //echo $query_content."<br>";
     $result_content = mysqli_query($db_link, $query_content);
     if(!$result_content) echo mysqli_error($db_link);
     if(mysqli_num_rows($result_content) > 0) {
       $content_array = mysqli_fetch_assoc($result_content);
       $current_content_id = $content_array['content_id'];
       $content_name = stripslashes($content_array['content_name']);
       $content_menu_text = stripslashes($content_array['content_menu_text']);
       $content_text = stripslashes($content_array['content_text']);
?>
<div id="pop_up_window_holder">
  <div id="modal_window_backgr"></div>
  <div id="pop_up_window"> <span class="cross" onclick="ClosePopUpWindow()" title="Затвори"></span>
    <h2><?=$content_name;?></h2>
    <div class="mb-20"><?=$content_text;?></div>
    <p>&nbsp;</p>
    <a class="btn btn-outline" onclick="ClosePopUpWindow()">OK</a>
  </div>
</div>
<?php
     }
  }
?>
<noscript><?=$languages[$current_lang]['text_noscript'];?></noscript>
<section id="page" data-column="col-xs-12 col-sm-6 col-md-4" data-type="grid"> 
<!-- Header -->
<header id="header">
  <section class="header-container">
    <div id="topbar">
      <div class="banner">
        <div class="container">
          <div class="row"> </div>
        </div>
      </div>
      <div class="nav">
        <div class="container">
          <div class="inner">
            <nav>
              <div class="btn-group phones col-xs-12 col-sm-12 col-md-4 col-lg-3">
                <!-- Block phones -->
                  <i class="fa fa-phone fa-2x"></i>
                  <a href="tel:+359898818116"><span>089 881 8116</span></a> 
                  <span>&nbsp;/&nbsp;</span> 
                  <a href="tel:+359878818116"><span>087 881 8116</span></a>
   
                <!-- Block phones -->
              </div>
              <p class="delivery_text col-xs-12 col-sm-12 col-md-4 col-lg-6">
                <?=$languages[$current_lang]['text_free_delivery_alt'];?>
              </p>
              <!-- Block user information module NAV  -->
              <div class="text-right col-xs-12 col-sm-12 col-md-4 col-lg-3">
                <ul class="links col-xs-11 col-sm-11 col-md-11 col-lg-11">
<?php 
                if(isset($_SESSION['customer'])) {
?>
                  <li>
                    <a href="/<?=$current_lang;?>/user-profiles/user-profile-data">
                      <i class="fa fa-user"></i><?=$languages[$current_lang]['customer_profile'];?>
                    </a>
                  </li>
                  <li>
                    <a href="/<?=$current_lang;?>/user-profiles/user-profile-wishlist" >
                      <i class="fa fa-star"></i><?=$languages[$current_lang]['customer_wishlist'];?>
                    </a>
                  </li>
                  <li>
                    <a href="/<?=$current_lang;?>/logout">
                      <i class="fa fa-sign-out"></i><?=$languages[$current_lang]['logout'];?>
                    </a>
                  </li>
<?php
                }
                else {
?>
                  <li>
                    <span class="login login_btn clever_link" data-target="_self">
                      <i class="fa fa-sign-in"></i><?=$languages[$current_lang]['login_sign_in'];?>
                    </span>
                  </li>
                  <li>
                    <span class="clever_link registration" data-link="/<?=$current_lang;?>/registration" data-target="_self">
                      <i class="fa fa-user"></i> <?=$languages[$current_lang]['login_sign_up'];?>
                    </span>
                  </li>
<?php
                }
?>
                  <li>
                    <a href="<?php if($current_lang == "bg") echo "/bg/как-да-поръчам"; else echo "/en/how-to-order"?>">
                      <i class="fa fa-truck"></i><?=$languages[$current_lang]['text_how_to_order'];?>
                    </a>
                  </li>
                </ul>
                <div id="choose_language" class="kt-language col-xs-1 col-sm-1 col-md-1 col-lg-1">
                  <a href="javascript:;" class="choose_language toggle-dropdown">

                  </a>
                  <ul class="dropdown-menu" id="languages">
                    <?php print_header_language_menu(); ?>
                  </ul>
                </div>

<?php
            if($_SESSION['debug']) {
              $customer_id = "";
              @$customer_id = $_SESSION['customer']['customer_id'];
              if($customer_id == 4 || $customer_id == 7 || $customer_id == 8 || (in_array($customer_ip, $allowed_ips))) {
?>
                <div id="choose_currency" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                  <a href="javascript:;" class="choose_currency toggle-dropdown">

                  </a>
                  <ul class="dropdown-menu" id="currencies">
<?php
                foreach($_SESSION['currencies'] as $key => $currency) {

                  if($key != "default") {

                    $currency_id = $currency['currency_id'];
                    $currency_title = $currency['currency_title'];
                    $currency_is_default = $currency['currency_is_default'];
                    $currency_code = $currency['currency_code'];
                    $currency_symbol_left = $currency['currency_symbol_left'];
                    $currency_symbol_right = $currency['currency_symbol_right'];
                    $currency_symbol = "";
                    if(!empty($currency_symbol_left)) $currency_symbol = $currency_symbol_left;
                    if(!empty($currency_symbol_right)) $currency_symbol = $currency_symbol_right;
                    $currency_decimal_place = $currency['currency_decimal_place'];
                    $currency_exchange_rate = $currency['currency_exchange_rate'];
                    if(isset($_COOKIE['currency'])) {
                      $class_active = ($_COOKIE['currency'] == $currency_id) ? " class='active'" : "";
                    }
                    else {
                      $class_active = ($currency_is_default == 1) ? " class='active'" : "";
                    }

?>
                    <li<?= $class_active; ?>>
                      <a href="javascript:;" onclick="createCookie('currency','<?=$currency_id;?>');location.reload(true);">
                        <span class="currency_symbol"><?= "$currency_symbol"; ?></span>
                      </a>
                    </li>
<?php
                  } //if($key != "default")
                }
?>
                  </ul>
                </div>
<?php
              }
            }
?>
              </div>
            </nav>
          </div>
        </div>
      </div>
    </div>
    <div id="header-main">
      <div class="container">
        <div class="inner">
          <div class="row">
            <div id="header_logo" class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
              <a href="/<?= $home_page_url ?>">
                <img src="/frontstore/images/logo-700.png" width="700" height="137" title="<?=$languages[$current_lang]['merchant_logo_text_title'];?>" alt="<?=$languages[$current_lang]['merchant_logo_text_alt'];?>">
              </a>
            </div>
            <div class="hidden-sp hidden-xs hidden-sm col-md-1 col-lg-1"> 
              <p>&nbsp;</p>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3" style="padding-top: 3%;"> 
              <!-- MODULE Block cart -->
              <?php //echo "<pre>";print_r($_SESSION);echo "</pre>"; ?>
              <div class="blockcart_top clearfix">
                <div class="shopping_cart">
                  <div class="media heading">
                    <div class="title-cart pull-right"> <span class="icon-cart"></span></div>
                    <div class="cart-inner media-body hidden-xs">
                      <div class="title_block">
                        <span class="clever_link color" data-link="/<?=$current_lang;?>/shopping-cart/shopping-cart-overview" data-target="_self">
                          <?=$languages[$current_lang]['header_shopping_cart'];?>
                        </span>
                      </div>
                      <span class="ajax_cart_total unvisible"><?=$currency_symbol_left_span;?><span class="price_text">0.00</span><?=$currency_symbol_right_span;?></span> 
                        - 
                      <span class="ajax_cart_quantity unvisible">0</span> 
                      <span class="ajax_cart_product_txt cart_product_txt unvisible"><?=$languages[$current_lang]['text_cart_product'];?></span> 
                      <span class="ajax_cart_product_txt_s cart_product_txt unvisible"><?=$languages[$current_lang]['text_cart_products'];?></span> 
                      <span class="ajax_cart_no_product">(<?=$languages[$current_lang]['text_empty_cart'];?>)</span>
                    </div>
                  </div>
                  <div class="cart_block block exclusive">
                    <div class="block_content"> 
                      <!-- block list of products -->
                      <div class="cart_block_list">
                        <?php print_shopping_cart() ?>
                      </div>
                    </div>
                  </div>
                  <!-- .cart_block --> 
                </div>
              </div>

              <div id="layer_cart">
                <div class="layer_cart_product col-xs-12 col-md-6"> <span class="cross" title="Close window"></span>
                  <p class="h2"><i class="fa fa-ok"></i><?=$languages[$current_lang]['header_product_added_to_cart'];?></p>
                  <div class="product-image-container layer_cart_img"></div>
                  <div class="layer_cart_product_info">
                    <span id="layer_cart_product_title" class="product-name"></span>
                    <span id="layer_cart_product_attributes"></span>
                    <div> <strong class="dark"><?=$languages[$current_lang]['header_quantity'];?>:</strong> <span id="layer_cart_product_quantity"></span> </div>
                    <div>
                      <strong class="dark"><?=$languages[$current_lang]['header_total'];?>:</strong> 
                      <span id="layer_cart_product_price"></span><span class="currency">&nbsp;лв.</span> 
                    </div>
                  </div>
                </div>
                <div class="layer_cart_cart col-xs-12 col-md-6">
                  <p class="h2 hidden-xs"> 
                  <?php
                    //unset($_SESSION['total_products_qty']);unset($_SESSION['total_products_price']);
                    if(isset($_SESSION['total_products_qty']) && $_SESSION['total_products_qty'] == 1) {
                  ?>
                    <!-- Singular Case [both cases are needed because page may be updated in Javascript] --> 
                    <span class="ajax_cart_product_txt"> <?=$languages[$current_lang]['text_one_product_in_cart'];?>. </span> 
                    <span class="ajax_cart_product_txt_s unvisible"> <?=$languages[$current_lang]['text_you_have'];?> <span class="ajax_cart_quantity"></span> <?=$languages[$current_lang]['text_products_in_cart'];?>. </span>
                    <input type="hidden" id="cart_product_qty" value="1" >
                  <?php
                    }
                    else {
                  ?>
                    <!-- Plural Case [both cases are needed because page may be updated in Javascript] --> 
                    <span class="ajax_cart_product_txt unvisible"> <?=$languages[$current_lang]['text_one_product_in_cart'];?>. </span> 
                    <span class="ajax_cart_product_txt_s"> <?=$languages[$current_lang]['text_you_have'];?> <span class="ajax_cart_quantity"><?php if(isset($_SESSION['total_products_qty'])) { echo $_SESSION['total_products_qty'];} else echo "0" ?></span> <?=$languages[$current_lang]['text_products_in_cart'];?>. </span>
                    <input type="hidden" id="cart_product_qty" value="<?php if(isset($_SESSION['total_products_qty'])) { echo $_SESSION['total_products_qty'];} else echo "0" ?>" >
                  <?php 
                    }
                  ?>
                  </p>
                  <div class="clearfix hidden-xs"></div>
                  <div class="layer_cart_row hidden-xs"> 
                    <strong class="dark"> <?=$languages[$current_lang]['header_total_products_price'];?> </strong> 
                    <span class="ajax_block_products_total"> <span class="price_text"><?php if(isset($_SESSION['total_products_price'])) { echo $_SESSION['total_products_price'];} else echo "0.00" ?></span><span class="currency">лв.</span> </span> 
                  </div>
                  <div class="clearfix"></div>
                  <div class="button-container"> 
                    <span class="continue btn btn-outline button exclusive-medium" title="<?=$languages[$current_lang]['btn_continue_shopping'];?>"> 
                      <span> <?=$languages[$current_lang]['btn_continue_shopping'];?> </span> 
                    </span>
                    <span class="clever_link btn btn-warning button pull-right" data-link="/<?=$current_lang;?>/shopping-cart/shopping-cart-overview" data-target="_self">
                      <?=$languages[$current_lang]['btn_proceed_to_checkout'];?>
                    </span>
                  </div>
                </div>
                <div class="crossseling"></div>
              </div>
              <!-- #layer_cart -->
              <div class="layer_cart_overlay"></div>

              <!-- /MODULE Block cart -->
              <div class="hidden">
                <div class="widget col-lg-8 col-md-8 col-sm-8 col-xs-8 col-sp-12 hidden-sp hidden-xs hidden-sm text-center">
                  <div class="widget-html block">
                    <div class="block_content">
                      <p><img src="/frontstore/images/delivery-top.png" title="<?=$languages[$current_lang]['text_free_delivery_title'];?>" alt="<?=$languages[$current_lang]['text_free_delivery_alt'];?>" width="380" height="52" /></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <input type="hidden" name="current_page_path_string" id="current_page_path_string" value="<?=$_SERVER['REQUEST_URI'];?>">
  <input type="hidden" name="current_lang" id="current_lang" value="<?=$current_lang;?>">

</header>
<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/frontstore/main-nav.php';
} //print_html_header
  
function print_shopping_cart() {
  
    global $languages;
    global $current_lang;
    
    $total_order_price = 0.00;
    $total_products_qty = 0;
      
    if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
?>
      <dl class="products">
<?php
      foreach($_SESSION['cart'] as $key => $products) {

        $product_id = $products['product_id'];
        $product_isbn = $products['product_isbn'];
        $product_price = $products['product_price'];
        if(isset($_SESSION['currencies']['default']['currency_exchange_rate'])) {
          $product_price = $product_price*$_SESSION['currencies']['default']['currency_exchange_rate'];
        }
        $product_name = $products['product_name'];
        $product_url = $products['product_url'];
        $product_qty = $products['product_qty'];
        $total_products_qty += $product_qty;
        $total_products_qty_class = ($total_products_qty > 1) ? "_s" : "";
        $product_img_src = $products['product_img_src'];
        $product_total_price = number_format(($product_price*$product_qty),2,".",".");
        $total_order_price = number_format(($total_order_price+($product_price*$product_qty)),2,".",".");
        $class_first_item = ($key == 0) ? ' class="first_item"' : "";
?>
        <dt id="cart_block_product_<?=$product_id?>"<?=$class_first_item?>>
          <a class="cart-images" href="<?=$product_url?>" title=""><img src="<?=$product_img_src;?>" alt="<?=$product_name?>" /></a>
          <div class="cart-info">
            <div class="product-name">
              <a class="cart_block_product_name" href="<?=$product_url?>" title="<?=$product_name?>"></a>
              <span class="quantity-formated"> x <span class="quantity"><?=$product_qty?></span></span> 
              <span class="price"> <?=$product_total_price?><span class="currency">&nbsp;лв.</span> </span>
            </div>
            <div class="product-atributes">
              <?=$product_isbn?>
            </div>
          </div>
          <span class="remove_link">
            <a class="ajax_cart_block_remove_link" href="javascript:;" onclick="UpdateShoppingCart('<?=$product_id?>')" rel="nofollow" title="">
              <i class="fa fa-trash-o"></i>
            </a>
          </span>
        </dt>
<?php
      } //foreach($_SESSION['cart'])
      $total_order_price_formatted = number_format(($total_order_price),2,".",".");
?>
      </dl>
      <div class="cart-prices">
        <div class="cart-prices-line last-line">
          <span class="price cart_block_total ajax_block_cart_total"><?=$total_order_price_formatted?><span class="currency">&nbsp;лв.</span></span>
          <span><?=$languages[$current_lang]['header_summary'];?> </span>
        </div>
      </div>
      <p class="cart-buttons clearfix">
        <span class="clever_link btn btn-warning button-medium button button-small pull-right" data-target="_self" data-link="/<?=$current_lang;?>/shopping-cart/shopping-cart-overview" title="<?=$languages[$current_lang]['btn_checkout'];?>">
          <?=$languages[$current_lang]['btn_checkout'];?>
        </span>
      </p>
      <script type="text/javascript">
        $('.clever_link').click(function(){
          var target = $(this).data('target');
          //console.log(target);
          window.open($(this).data('link'), target);
          return false;
        });
        $(".blockcart_top .ajax_cart_total").removeClass("unvisible");
        $(".blockcart_top .ajax_cart_total .price_text").html("<?=$total_order_price;?>");
        $(".blockcart_top .ajax_cart_quantity").html("<?=$total_products_qty;?>").removeClass("unvisible");
        $(".blockcart_top .cart_product_txt").addClass("unvisible");
        $(".blockcart_top .ajax_cart_product_txt<?=$total_products_qty_class;?>").removeClass("unvisible");
        $(".blockcart_top .ajax_cart_no_product").addClass("unvisible");
        $(".layer_cart_cart #cart_product_qty").val("<?=$total_products_qty;?>");
        $(".layer_cart_cart .price_text").html("<?=$total_order_price;?>");
      </script>
<?php
    } //if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0)
    else {
?>
      <p class="cart_block_no_products">
        <?=$languages[$current_lang]['header_shopping_cart_is_empty'];?>
      </p>
      <script type="text/javascript">
        $(".blockcart_top .ajax_cart_total").addClass("unvisible");
        $(".blockcart_top .ajax_cart_total .price").html("0.00");
        $(".blockcart_top .ajax_cart_quantity").html("0").addClass("unvisible");
        $(".blockcart_top .cart_product_txt").addClass("unvisible");
        $(".blockcart_top .cart_product_txt_s").removeClass("unvisible");
        $(".blockcart_top .ajax_cart_no_product").removeClass("unvisible");
        $(".layer_cart_cart #cart_product_qty").val("0");
        $(".layer_cart_cart .price_text").html("0.00");
      </script>
<?php
    }
  
  }
  
  function print_header_language_menu() {

  global $db_link;
  global $current_language_id;
  global $current_page_pretty_url;
  global $content_hierarchy_ids; //coming from frontstore/index.php

  $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  $content_parent_id = $content_hierarchy_ids_array[0];

  $query_languages = "SELECT `languages`.`language_id`, `languages`.`language_code`, `languages`.`language_root_content_id`,
                             `languages`.`language_menu_name`, `contents`.`content_hierarchy_path`, `contents`.`content_is_default` 
                        FROM `languages`
                  INNER JOIN `contents` ON `contents`.`content_id` = `languages`.`language_default_content_id`
                       WHERE `languages`.`language_is_active` = '1'
                    ORDER BY `languages`.`language_menu_order` ASC";
  //echo $query_content;exit;
  $result_languages = mysqli_query($db_link, $query_languages);
  if (!$result_languages)
    echo mysqli_error($db_link);
  $language_count = mysqli_num_rows($result_languages);
  if ($language_count > 1) {
    while ($language_row = mysqli_fetch_assoc($result_languages)) {

      $language_id = $language_row['language_id'];
      $language_code = $language_row['language_code'];
      $content_is_default = $language_row['content_is_default'];
      $language_root_content_id = $language_row['language_root_content_id'];
      $content_hierarchy_path = "/" . $language_row['content_hierarchy_path'];
      if($content_is_default == 1) $content_hierarchy_path = str_replace ("/$language_code", "", $content_hierarchy_path);
      $language_menu_name = stripslashes($language_row['language_menu_name']);
      $class_active = ($language_root_content_id == $content_parent_id) ? " class='active'" : "";
      ?>
      <li<?= $class_active; ?>>
        <a href="<?= $content_hierarchy_path; ?>" onclick="createCookie('language', '<?= $language_code; ?>')">
          <img src="/frontstore/images/flags/<?= $language_code; ?>.png" alt="bulgarian flag" width="16" height="11" />
          <!--<span class="lang_name"><?= $language_menu_name; ?></span>-->
        </a>
      </li>
      <?php
    }
    mysqli_free_result($result_languages);
  }
}
  
function print_header_menu($content_parent_id, $content_hierarchy_level_start , $number_of_hierarchy_levels) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;
  global $content_hierarchy_ids; //coming from frontstore/index.php or frontstore/categories.php

  if($content_parent_id == 0) {
    if(strstr($content_hierarchy_ids, ".")) {
      $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
      $content_parent_id = $content_hierarchy_ids_array[0];
    }
    else {
      $content_parent_id = $content_hierarchy_ids;
    }
  }

  $content_hierarchy_level_in_query = "";

  if($content_hierarchy_level_start == 0) {
    if($number_of_hierarchy_levels != 0) $content_hierarchy_level_in_query = " AND `content_hierarchy_level` <= '$number_of_hierarchy_levels'";
  }
  else {
    $content_hierarchy_level_in_query = " AND `content_hierarchy_level` = '$content_hierarchy_level_start'";
  }

  $query_content = "SELECT `content_id`,`content_type_id`,`content_is_default`,`content_menu_text`,`content_hierarchy_path`,
                           `content_has_children`,`content_hierarchy_level`,`content_text`,`content_menu_order`,`content_pretty_url`,`content_target`
                      FROM `contents`
                     WHERE `content_id` NOT IN(2,4,15,16) AND `content_parent_id` = '$content_parent_id' $content_hierarchy_level_in_query
                       AND `content_show_in_menu` = '1' AND `content_is_active` = '1'
                  ORDER BY `content_menu_order` ASC";
  //echo $query_content;exit;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {

    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_type_id = $content_row['content_type_id'];
      $content_is_default = $content_row['content_is_default'];
      $content_menu_text = stripslashes($content_row['content_menu_text']);
      $content_hierarchy_path = ($content_type_id == 2) ? "javascript:;" : "/".$content_row['content_hierarchy_path'];
      $content_has_children = $content_row['content_has_children'];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_text = $content_row['content_text'];
      $content_pretty_url = $content_row['content_pretty_url'];
      $content_menu_order = $content_row['content_menu_order'];
      $content_target = (is_null($content_row['content_target'])) ? "" : "target='".$content_row['content_target']."'";
      switch($content_type_id) {
        case 1:
          $content_hierarchy_path = "/".$content_row['content_hierarchy_path']; //content
          break;
        case 2:
          $content_hierarchy_path = "javascript:;"; //categories
          break;
        case 4:
          $content_hierarchy_path = $content_text; //redirecting link
          break;
        default: $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
      }
      $class_active = "";
      //echo "$current_page_path_string - $content_pretty_url<br>";
      if(isset($current_page_path_string) && !empty($current_page_path_string)) {
        if(strstr($current_page_path_string, $content_pretty_url)) $class_active = ' active';
      }
      else {
        if($content_is_default == 1) $class_active = ' class="active"';
      }

      if($content_is_default == 1) $content_hierarchy_path = "";
      $content_has_active_children = check_if_content_has_active_children($content_id);
      $content_is_last_child = check_if_this_is_content_last_child($content_parent_id,$content_menu_order);

      if($content_has_children == 1 && $content_hierarchy_level < $number_of_hierarchy_levels && $content_has_active_children) {
?>
      <li class="parent dropdown<?=$class_active;?>">
        <a class="dropdown-toggle has-category" data-toggle="dropdown" href="<?=$content_hierarchy_path;?>">
          <span class="menu-title"><?=$content_menu_text;?> <b class="caret"></b></span>
        </a>
        <div class="dropdown-menu level_<?=$content_hierarchy_level;?>">
          <ul>
<?php
          print_header_menu($content_id, $content_hierarchy_level_start, $number_of_hierarchy_levels);
      }
      else {
        if($content_type_id == 2) { // categories
          echo "<li class='$class_active'><a href='$content_text' $content_target>$content_menu_text</a></li>\n";
        }
        elseif($content_type_id == 4) { // redirecting link
          echo "<li class='$class_active'><a href='$content_text' $content_target>$content_menu_text</a></li>\n";
        }
        else echo "<li class='$class_active'><a href='$content_hierarchy_path' $content_target>$content_menu_text</a></li>\n";
      }

      if($content_hierarchy_level > 2 && $content_is_last_child) {
?>
          </ul>
        </div>
      </li>
<?php
      }
    }
  }
}
  
function print_header_categories_menu($category_parent_id,$number_of_hierarchy_levels) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;

  $current_category_id = 0;
  $current_category_parent_id = 0;
  if(isset($_GET['cid'])) {
    $current_category_id = $_GET['cid']; // current selected category_id
  }
  if(isset($_GET['cpid'])) {
    $current_category_parent_id = $_GET['cpid']; // category_parent_id
  }
  if(isset($_SESSION['ccid'])) {
    $current_category_id = $_SESSION['ccid']; // current selected category_id
  }
  if(isset($_SESSION['ccpid'])) {
    $current_category_parent_id = $_SESSION['ccpid']; // category_parent_id
  }

  $query_categories = "SELECT `categories`.`category_id`,`categories`.`category_hierarchy_level`,`categories`.`category_sort_order`,`categories`.`category_has_children`,
                              `categories`.`category_parent_id`,`categories`.`category_image_path`,`categories`.`category_is_section_header`,`categories`.`category_attribute_1`,
                              `category_descriptions`.`cd_pretty_url`,`category_descriptions`.`cd_name`, `category_descriptions`.`cd_hierarchy_path` 
                         FROM `categories`
                   INNER JOIN `category_descriptions` USING(`category_id`)
                        WHERE `categories`.`category_parent_id` = '$category_parent_id' AND `categories`.`category_is_active` = '1'
                          AND `categories`.`category_hierarchy_level` <= '$number_of_hierarchy_levels' AND `category_descriptions`.`cd_is_active` = '1'
                          AND `categories`.`category_show_in_menu` = '1' AND `category_descriptions`.`language_id` = '$current_language_id'
                     ORDER BY `categories`.`category_sort_order` ASC";
  //if($current_language_id == 2 && $category_parent_id == 18) echo $query_categories;
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  $category_count = mysqli_num_rows($result_categories);
  if($category_count > 0) {

    while($category_row = mysqli_fetch_assoc($result_categories)) {

      $category_id = $category_row['category_id']; 
      $category_hierarchy_level = $category_row['category_hierarchy_level'];
      $category_sort_order = $category_row['category_sort_order'];
      $category_has_children = $category_row['category_has_children'];
      $category_image_name = $category_row['category_image_path'];
      $category_image_path = "";
      if(!is_null($category_image_name)) {
        $category_image_name_exploded = explode(".", $category_image_name);
        $image_name = $category_image_name_exploded[0];
        $image_exstension = mb_convert_case($category_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
        $image_path = "/frontstore/images/category-thumbs/";
        $category_image_path = $image_path.$image_name."_cat_thumb.".$image_exstension;
      }
      $category_is_section_header = $category_row['category_is_section_header'];
      $category_attribute_1 = $category_row['category_attribute_1'];
      $cd_pretty_url = $category_row['cd_pretty_url'];
      $cd_name = $category_row['cd_name'];
      $cd_hierarchy_path = $category_row['cd_hierarchy_path'];
      $cd_url = str_replace(array('(',')'), array('',''), mb_convert_case(mb_strimwidth($cd_hierarchy_path, 0, 95,'', 'utf-8'), MB_CASE_LOWER, "UTF-8"));
      $cyr_url = cyrialize_url($cd_url);
      if($category_is_section_header == 0) {
        $class_section_header = "";
        $href = "/$current_lang/cid-$category_id/$cyr_url";
//        $href = "/$current_lang/$cd_hierarchy_path?cid=$category_id";
//        $href .= ($category_has_children == 1) ? "cpid=all" : "cpid=$category_parent_id";
      }
      else {
        $class_section_header = "section_header";
        $href =  "javascript:;";
      }

      $class_active = "";
      $category_has_active_children = check_if_category_has_active_children($category_id);
      $dropdown_arrow = ($category_hierarchy_level == 1 && $category_has_active_children) ? "<b class='caret'></b>" : "";
      $category_is_last_child = check_if_this_is_category_last_child($category_parent_id,$category_sort_order);
      if($current_category_id == $category_id || $current_category_parent_id == $category_id) $class_active = ' active';

      if($category_has_children == 1 && $category_hierarchy_level < $number_of_hierarchy_levels && $category_has_active_children) {
?>
        <li class="parent dropdown<?=$class_active;?> <?=$class_section_header;?> <?=$category_attribute_1;?>">
          <a class="dropdown-toggle has-category" data-toggle="dropdown" href="<?=urldecode($href);?>">
            <span class="menu-title"><?=$cd_name;?> <?=$dropdown_arrow;?></span>
          </a>
          <div class="dropdown-menu level_<?=$category_hierarchy_level;?>">
            <ul>
<?php
            print_header_categories_menu($category_id,$number_of_hierarchy_levels);
      }
      else {
?>
        <li class='<?="$class_section_header $category_attribute_1 $class_active";?>'><a href='<?=urldecode($href);?>' class='<?=$class_section_header;?>'><span class='menu-title'><?="$cd_name $dropdown_arrow";?></span></a></li>
<?php
      }

      if($category_hierarchy_level > 1 && $category_is_last_child) {
?>
            </ul>
          </div>
        </li>
<?php
      }
    }
  }
}
  
function print_sitemap_content_menu($content_hierarchy_level_start,$number_of_hierarchy_levels,$offset) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;
  global $content_hierarchy_ids; //coming from frontstore/index.php or frontstore/categories.php

  $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  if(strstr($content_hierarchy_ids, ".")) {
    $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
    $content_parent_id = $content_hierarchy_ids_array[0];
  }
  else {
    $content_parent_id = $content_hierarchy_ids;
  }

  $content_hierarchy_level_in_query = "";
  $limit = 10;

  if($content_hierarchy_level_start == 0) {
    if($number_of_hierarchy_levels != 0) $content_hierarchy_level_in_query = " AND `content_hierarchy_level` <= '$number_of_hierarchy_levels'";
  }
  else {
    $content_hierarchy_level_in_query = " AND `content_hierarchy_level` = '$content_hierarchy_level_start'";
  }

  $query_content = "SELECT `content_id`,`content_type_id`,`content_menu_text`,`content_hierarchy_path`,`content_has_children`,`content_is_default`,
                           `content_hierarchy_level`,`content_text`,`content_menu_order`,`content_target`,`content_attribute_1` 
                      FROM `contents`
                     WHERE `content_parent_id` = '$content_parent_id' $content_hierarchy_level_in_query
                       AND `content_is_active` = '1' AND `content_id` NOT IN(12,22,23)
                  ORDER BY `content_menu_order` ASC
                     LIMIT $offset, $limit";
  //echo $query_content;exit;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {
    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_type_id = $content_row['content_type_id'];
      $content_menu_text = stripslashes($content_row['content_menu_text']);
      $content_has_children = $content_row['content_has_children'];
      $content_is_default = $content_row['content_is_default'];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_text = $content_row['content_text'];
      $content_menu_order = $content_row['content_menu_order'];
      $content_target = (is_null($content_row['content_target'])) ? "" : "target='".$content_row['content_target']."'";
      switch($content_type_id) {
        case 1:
          $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
        case 2:
          $content_hierarchy_path = "javascript:;";
          break;
        case 4:
          $content_hierarchy_path = $content_text;
          break;
        default: $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
      }
      $class_active = "";
      if($content_is_default == 1) $content_hierarchy_path = "";

      if(in_array($content_id,$content_hierarchy_ids_array)) $class_active = ' current_page_item';

      echo "<li class='$class_active'><a href='$content_hierarchy_path' $content_target>$content_menu_text</a></li>\n";

    }
  }
}

function print_sitemap_categories_menu($category_parent_id,$number_of_hierarchy_levels) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;

  $current_category_id = 0;
  $current_category_parent_id = 0;
  if(isset($_GET['cid'])) {
    $current_category_id = $_GET['cid']; // current selected category_id
  }
  if(isset($_GET['cpid'])) {
    $current_category_parent_id = $_GET['cpid']; // category_parent_id
  }

  $query_categories = "SELECT `categories`.`category_id`,`categories`.`category_hierarchy_level`,`categories`.`category_sort_order`,`categories`.`category_has_children`,
                              `categories`.`category_image_path`,`categories`.`category_is_section_header`,`categories`.`category_attribute_1`,
                              `category_descriptions`.`cd_pretty_url`,`category_descriptions`.`cd_name`, `category_descriptions`.`cd_hierarchy_path` 
                         FROM `categories`
                   INNER JOIN `category_descriptions` USING(`category_id`)
                        WHERE `categories`.`category_parent_id` = '$category_parent_id' AND `categories`.`category_is_active` = '1'
                          AND `categories`.`category_hierarchy_level` <= '$number_of_hierarchy_levels' AND `category_descriptions`.`cd_is_active` = '1'
                          AND `categories`.`category_show_in_menu` = '1' AND `category_descriptions`.`language_id` = '$current_language_id'
                     ORDER BY `categories`.`category_sort_order` ASC";
  //if($current_language_id == 2 && $category_parent_id == 18) echo $query_categories;
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  $category_count = mysqli_num_rows($result_categories);
  if($category_count > 0) {

    while($category_row = mysqli_fetch_assoc($result_categories)) {

      $category_id = $category_row['category_id'];
      $category_hierarchy_level = $category_row['category_hierarchy_level'];
      $category_sort_order = $category_row['category_sort_order'];
      $category_has_children = $category_row['category_has_children'];
      $category_image_name = $category_row['category_image_path'];
      $category_image_path = "";
      if(!is_null($category_image_name)) {
        $category_image_name_exploded = explode(".", $category_image_name);
        $image_name = $category_image_name_exploded[0];
        $image_exstension = mb_convert_case($category_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
        $image_path = "/frontstore/images/category-thumbs/";
        $category_image_path = $image_path.$image_name."_cat_thumb.".$image_exstension;
      }
      $category_is_section_header = $category_row['category_is_section_header'];
      $category_attribute_1 = $category_row['category_attribute_1'];
      $cd_pretty_url = $category_row['cd_pretty_url'];
      $cd_name = $category_row['cd_name'];
      $cd_hierarchy_path = $category_row['cd_hierarchy_path'];
      $cd_url = str_replace(array('(',')'), array('',''), mb_convert_case(mb_strimwidth($cd_hierarchy_path, 0, 95,'', 'utf-8'), MB_CASE_LOWER, "UTF-8"));
      $cyr_url = cyrialize_url($cd_url);
      if($category_is_section_header == 0) {
        $class_section_header = "";
        $href = "/$current_lang/cid-$category_id/$cyr_url";
//        $href = "/$current_lang/$cd_hierarchy_path?cid=$category_id";
//        $href .= ($category_has_children == 1) ? "cpid=all" : "cpid=$category_parent_id";
      }
      else {
        $class_section_header = "section_header";
        $href =  "javascript:;";

      }

      $class_active = "";
      $dropdown_arrow = ($category_hierarchy_level == 1 && $category_is_section_header == 1) ? "<b class='caret'></b>" : "";
      $category_has_active_children = check_if_category_has_active_children($category_id);
      $category_is_last_child = check_if_this_is_category_last_child($category_parent_id,$category_sort_order);
      if($current_category_id == $category_id || $current_category_parent_id == $category_id) $class_active = ' active';

      if($category_has_children == 1 && $category_hierarchy_level < $number_of_hierarchy_levels && $category_has_active_children) {
?>
        <li>
          <a href="<?=urldecode($href);?>"><?=$cd_name;?></a>
          <ul>
<?php
          print_sitemap_categories_menu($category_id,$number_of_hierarchy_levels);
    }
    else {
?>
      <li><a href='<?=urldecode($href);?>'><?=$cd_name;?></a></li>
<?php
    }

    if($category_hierarchy_level > 1 && $category_is_last_child) {
?>
          </ul>
        </li>
<?php
      }
    }
  }
}

function print_footer_menu($content_hierarchy_level_start,$number_of_hierarchy_levels,$offset) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;
  global $content_hierarchy_ids; //coming from frontstore/index.php or frontstore/categories.php

  $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  if(strstr($content_hierarchy_ids, ".")) {
    $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
    $content_parent_id = $content_hierarchy_ids_array[0];
  }
  else {
    $content_parent_id = $content_hierarchy_ids;
  }

  $content_hierarchy_level_in_query = "";
  $limit = 10;

  if($content_hierarchy_level_start == 0) {
    if($number_of_hierarchy_levels != 0) $content_hierarchy_level_in_query = " AND `content_hierarchy_level` <= '$number_of_hierarchy_levels'";
  }
  else {
    $content_hierarchy_level_in_query = " AND `content_hierarchy_level` = '$content_hierarchy_level_start'";
  }

  $query_content = "SELECT `content_id`,`content_type_id`,`content_menu_text`,`content_hierarchy_path`,`content_has_children`,`content_is_default`,
                           `content_hierarchy_level`,`content_text`,`content_menu_order`,`content_target`,`content_attribute_1` 
                      FROM `contents`
                     WHERE `content_parent_id` = '$content_parent_id' $content_hierarchy_level_in_query
                       AND `content_show_in_footer` = '1'  AND `content_is_active` = '1'
                  ORDER BY `content_menu_order` ASC
                     LIMIT $offset, $limit";
  //echo $query_content;exit;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {
    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_type_id = $content_row['content_type_id'];
      $content_menu_text = stripslashes($content_row['content_menu_text']);
      $content_has_children = $content_row['content_has_children'];
      $content_is_default = $content_row['content_is_default'];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_text = $content_row['content_text'];
      $content_menu_order = $content_row['content_menu_order'];
      $content_target = (is_null($content_row['content_target'])) ? "" : "target='".$content_row['content_target']."'";
      switch($content_type_id) {
        case 1:
          $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
        case 2:
          $content_hierarchy_path = "javascript:;";
          break;
        case 4:
          $content_hierarchy_path = $content_text;
          break;
        default: $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
      }
      $class_active = "";
      if($content_is_default == 1) $content_hierarchy_path = "";

      if(in_array($content_id,$content_hierarchy_ids_array)) $class_active = ' current_page_item';

      echo "<li class='$class_active'><a href='$content_hierarchy_path' $content_target>$content_menu_text</a></li>\n";

    }
  }
}
  
function print_category_name($category_parent_id) {
  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;

  $query_category_name = "SELECT `category_descriptions`.`cd_name` 
                          FROM `categories`
                          INNER JOIN `category_descriptions` USING(`category_id`)
                          WHERE `categories`.`category_id` = '$category_parent_id' AND `category_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_category_name;exit;
  $result_category_name = mysqli_query($db_link, $query_category_name);
  if(!$result_category_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_category_name)) {
    $category_row = mysqli_fetch_assoc($result_category_name);
    $cd_name = $category_row['cd_name'];
    echo $cd_name;
  }
}
  
function print_gallery_in_footer($gallery_id) {
    
  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;

  $count = 24;
  $gi_names_array = get_gallery_images($gallery_id,$count);
  $gallery_images_folder = "/frontstore/images/galleries/";

  if(count($gi_names_array) > 0) {

    foreach($gi_names_array as $key => $prod_gallery_image) {
      //echo"<pre>";print_r($prod_gallery_image);
      $gallery_image_id = $prod_gallery_image['gallery_image_id'];
      $is_album_cover = $prod_gallery_image['is_album_cover'];
      $is_active = $prod_gallery_image['is_active'];
      $set_active_inactive = ($is_active == 1) ? 0 : 1;
      $sort_order = $prod_gallery_image['sort_order'];
      $gallery_image_title = stripslashes($prod_gallery_image['gallery_image_title']);
      $gallery_image_comment = stripslashes($prod_gallery_image['gallery_image_comment']);
      $gallery_image = $prod_gallery_image['name'];
      $gallery_image_orig = $gallery_images_folder.$gallery_image;
      $gallery_image_exploded = explode(".", $gallery_image);
      $gallery_img_name = $gallery_image_exploded[0];
      $gallery_img_exstension = $gallery_image_exploded[1];
      $gallery_img_path_small = $gallery_images_folder.$gallery_img_name."_small_thumb.".$gallery_img_exstension;
      @$gallery_img_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$gallery_img_path_small);
      $gallery_img_dimensions = $gallery_img_params[3];
?>
      <div class="image-item  col-md-1 col-xs-2">
        <a class="fancybox" rel="gallery" href= "<?=$gallery_image_orig;?>" title="<?=$gallery_image_title;?>">
          <img class="replace-2x img-responsive" src="<?=$gallery_img_path_small?>" alt="<?=$gallery_image_title;?>" <?=$gallery_img_dimensions?> />
        </a>
      </div>
<?php
    }

  }
}
  
function print_galleries($galleries_array) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;

  $galleries_count = count($galleries_array);

  if($galleries_count > 0) {

    $gallery_cover_images_folder = "/frontstore/images/galleries/";
    $blocks_counter = 1;

    foreach($galleries_array as $key => $gallery) {
      //echo"<pre>";print_r($gallery);
      $gallery_id = $gallery['gallery_id'];
      $gallery_name = stripslashes($gallery['gallery_name']);
      $gallery_name_escaped = str_replace(array('\\','?','!','.','(',')','%'), array('','','','','','',''), $gallery_name);
      $gallery_name_url = str_replace(" ", "-", mb_convert_case($gallery_name_escaped, MB_CASE_LOWER, "UTF-8"));
      $gallery_details_link = "/$current_lang/gid-$gallery_id/$gallery_name_url";
      $gallery_cover_img = $gallery['album_cover'];
      $gallery_cover_img_orig = $gallery_cover_images_folder.$gallery_cover_img;
      $gallery_cover_img_exploded = explode(".", $gallery_cover_img);
      $gallery_cover_img_name = $gallery_cover_img_exploded[0];
      $gallery_cover_img_exstension = $gallery_cover_img_exploded[1];
      $gallery_cover_img_path_small = $gallery_cover_images_folder.$gallery_cover_img_name."_big_thumb.".$gallery_cover_img_exstension;
      @$gallery_cover_img_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$gallery_cover_img_path_small);
      $gallery_cover_img_dimensions = $gallery_cover_img_params[3];

      $gallery_cover_img_file = $_SERVER['DOCUMENT_ROOT'].$gallery_cover_images_folder.$gallery_cover_img;
      list($width,$height) = getimagesize($gallery_cover_img_file);

      if($width > $height) {
        $default_img_style = "style='width:98%;height:auto;'";
      }
      else {
        $default_img_style = "style='height:98%;width:auto;margin:1% auto;'";
      }

      $query_images_count = "SELECT `gallery_images`.`gallery_image_id`
                              FROM `gallery_images` 
                              INNER JOIN `gallery_images_descriptions` as `gid` USING(`gallery_image_id`)
                              WHERE `gallery_images`.`gallery_id` = '$gallery_id' AND `is_active`='1' 
                                AND `gid`.`language_id` = '$current_language_id'";
      //echo $query_images_count;
      $result_images_count = mysqli_query($db_link, $query_images_count);
      if(!$result_images_count) echo mysqli_error($db_link);
      $images_count = mysqli_num_rows($result_images_count);

      if($blocks_counter == 1) {
?>
      <div class="row">
<?php
      }
?>
        <div class="col-sp-12 col-xs-12 col-sm-6 col-md-3 gallery_album_block">
          <h3><a href="<?=$gallery_details_link;?>"><?=$gallery_name;?></a></h3>
          <div class="image">
            <a href="<?=$gallery_details_link;?>"><img src="<?=$gallery_cover_img_path_small?>" alt="<?=$gallery_name;?>" <?=$default_img_style;?> /></a>
          </div>
          <p class="text-center"><i class="fa fa-camera fa-lg"></i> <?=$images_count;?> снимки</p>
        </div>
<?php
      if($galleries_count < 4) {
        if($blocks_counter == $galleries_count) {
?>
      </div>
      <div class="clearfix"></div>
<?php
        }
      }
      if($blocks_counter == 4) {
?>
      </div>
      <div class="clearfix"></div>
<?php
        $blocks_counter = 0;
      }

      $blocks_counter++;
    } //foreach($galleries_array)
  } //if(count($galleries_array) > 0)
}
  
function list_news($offset = false,$news_count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;

  $content_type_id = 7; // news
  
  $page_offset = 6;
  $offset = ($offset) ? $offset : 0;
  
  if(!$news_count) {
    $query_news = "SELECT `news`.`news_id`
                        FROM `news`
                        WHERE `news`.`news_is_active` = '1'";
    //echo $query_news."<br>";
    $result_news = mysqli_query($db_link, $query_news);
    if(!$result_news) echo mysqli_error($db_link);
    $news_count = mysqli_num_rows($result_news);
  }
  
  $query_news = "SELECT `news`.`news_id`,`news`.`news_post_date`,`news`.`news_start_time`,`news`.`news_end_time`,
                        `news`.`news_is_active`,`news`.`news_image`,`news_descriptions`.`news_title`,`news_descriptions`.`news_summary` 
                    FROM `news` 
                    INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                    WHERE `news`.`news_is_active` = '1' AND `news_descriptions`.`language_id` = '$current_language_id'
                    ORDER BY `news`.`news_created_date` DESC
                    LIMIT $offset,$page_offset";
  //echo $query_news;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news) > 0) {
    
    $query_content = "SELECT `content_pretty_url`
                      FROM `contents`
                      WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
    //echo $query_content."<br><br>";
    $result_content = mysqli_query($db_link, $query_content);
    if(!$result_content) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_content) > 0) {
      $content_array = mysqli_fetch_assoc($result_content);
      $content_pretty_url = $content_array['content_pretty_url'];
    }
    
    // if the results are more then $page_offset
    // making a pagination, finding how many pages will be needed
    $current_page = ($offset/$page_offset)+1;

    if($news_count > $page_offset) {
      $page_count = ceil($news_count/$page_offset);
    }
    
    $block = 1;
    
    while($news_row = mysqli_fetch_assoc($result_news)) {

      $news_id = $news_row['news_id'];
      $news_title = stripslashes($news_row['news_title']);
      $news_title_escaped = str_replace(array('\\','?','!','.','(',')','%'), array('','','','','','',''), $news_title);
      $news_title_url = str_replace(" ", "-", mb_convert_case($news_title_escaped, MB_CASE_LOWER, "UTF-8"));
      //$news_summary = truncate($news_row['news_summary']);
      $news_summary = mb_strimwidth(stripslashes($news_row['news_summary']), 0, 280, "...");
      $news_post_date_day = date("d", strtotime($news_row['news_post_date']));
      $news_post_date_month_text = "text_date_month_".date("m", strtotime($news_row['news_post_date']));
      $news_post_date_month = $languages[$current_lang][$news_post_date_month_text];
      $news_post_date_year = date("Y", strtotime($news_row['news_post_date']));
      $news_start_time = $news_row['news_start_time'];
      $news_end_time = $news_row['news_end_time'];
      $news_images_folder = "/frontstore/images/news/";
      $news_image = $news_images_folder.$news_row['news_image'];
      $news_image_exploded = explode(".", $news_image);
      $current_news_image_name = $news_image_exploded[0];
      $current_news_image_exstension = $news_image_exploded[1];
      $image_thumb_name = $current_news_image_name."_thumb.".$current_news_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$image_thumb_name);
      $thumb_image_dimensions = $thumb_image_params[3];
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $news_details_link = "/$current_lang/$content_pretty_url/$news_title_url?nid=$news_id";
      
      if($block == 4) $block = 1;
      $class_first = ($block == 1) ? " first" : "";
?>
      <div class="dt-sc-one-third column<?=$class_first;?>">
        <article class="blog-entry format-quote">
          <div class="blog-entry-inner">
            <div class="entry-title">
                <h4><a href="<?=$news_details_link;?>"><?=$news_title;?></a></h4>
              </div>
              <div class="entry-thumb">
                  <a href="<?=$news_details_link;?>">
                      <img title="<?=$news_title;?>" alt="<?=$news_title;?>" src="<?=$image_thumb_name;?>" <?=$thumb_image_dimensions;?>>
                      <div class="blog-overlay"><span class="image-overlay-inside"></span></div>
                  </a>                                                
                  <div class="entry-meta">
                      <div class="date">
                          <span><?=$news_post_date_day;?></span>
                          <?=$news_post_date_month;?><br>
                          <?=$news_post_date_year;?>
                      </div>
                  </div>
              </div>
              <div class="entry-body">
                  <p><?=$news_summary;?></p>
                  <a href="<?=$news_details_link;?>"><?=$languages[$current_lang]['btn_read_more'];?> <i class="fa fa-angle-double-right"></i></a>
              </div>
          </div>
        </article>
      </div>
<?php
      $block++;
    }
    mysqli_free_result($result_news);
    
    if(isset($page_count)) {
?>
    <div class="clear"></div>
    <div class="col-lg-12">
      <div class="pagination">
        <ul>
<?php
        $pages = 1;
        $current_offset = $offset;
        $offset = 0;

        if($current_page == 1) {
          echo '<li class="disabled btn_prev_page"><a href="javascript:;" data="">&laquo; </a></li>';
        }
        else {
          $prev_offset = $current_offset - $page_offset;
          echo '<li class="btn_prev_page"><a href="#news_anchor" data="'.$prev_offset.'">&laquo; </a></li>';
        }

        while($pages <= $page_count) {

          if($current_page == $pages) {
            echo "<li id='pag_$pages' class='active-page'>$pages</li>";
          }
          else {
            echo "<li id='pag_$pages'><a href='#news_anchor' class='inactive' data=\"$offset\">$pages</a></li>";
          }

          $pages++;
          $offset += $page_offset;
        }
        if($current_page == $page_count) {
          echo '<li class="disabled btn_next_page"><a href="javascript:;" data=""> &raquo;</a></li>';
        }
        else {
          $next_offset = $current_offset + $page_offset;
          echo '<li class="btn_next_page"><a href="#news_anchor" data="'.$next_offset.'">&raquo; </a></li>';
        }
?>
        </ul>
        <input type="hidden" class="news_count" value="<?=$news_count;?>" >
        <input type="hidden" class="language_id" value="<?=$current_language_id;?>" >
        <input type="hidden" class="current_lang" value="<?=$current_lang;?>" >
      </div>
    </div>
    <script>
      $(function() {
        $(".pagination a").bind('click', function() {
          var offset = $(this).attr("data");
          LoadPaginationNews(offset);
        });
      });
    </script>
<?php
    }
  }
}

function print_category_submenu($current_category_id,$category_parent_id,$number_of_hierarchy_levels) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;

  if($category_parent_id == "all") {
    $category_parent_id = 17;
  }
  $query_categories = "SELECT `categories`.`category_id`,`categories`.`category_hierarchy_level`,`categories`.`category_sort_order`,`categories`.`category_has_children`,
                              `categories`.`category_image_path`,`categories`.`category_is_section_header`,`categories`.`category_attribute_1`,
                              `category_descriptions`.`cd_pretty_url`,`category_descriptions`.`cd_name`, `category_descriptions`.`cd_hierarchy_path` 
                         FROM `categories`
                   INNER JOIN `category_descriptions` USING(`category_id`)
                        WHERE `categories`.`category_parent_id` = '$category_parent_id' AND `categories`.`category_is_active` = '1'
                          AND `category_hierarchy_level` <= '$number_of_hierarchy_levels' AND `category_descriptions`.`cd_is_active` = '1'
                          AND `category_descriptions`.`language_id` = '$current_language_id'
                     ORDER BY `categories`.`category_sort_order` ASC";
  //echo $query_categories;
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  $category_count = mysqli_num_rows($result_categories);
  if($category_count > 1) {
?>
    <!-- Block category submenu -->
    <div id="categories_block_left" class="hidden-xs block block-primary">
      <div class="title_block"><?php print_category_name($category_parent_id) ?></div>
      <div class="block_content">
        <ul class="list-block list-group bullet tree dhtml">
<?php
    while($category_row = mysqli_fetch_assoc($result_categories)) {

      $category_id = $category_row['category_id'];
      $category_hierarchy_level = $category_row['category_hierarchy_level'];
      $category_sort_order = $category_row['category_sort_order'];
      $category_has_children = $category_row['category_has_children'];
      $category_image_name = $category_row['category_image_path'];
      $category_image_path = "";
      if(!is_null($category_image_name)) {
        $category_image_name_exploded = explode(".", $category_image_name);
        $image_name = $category_image_name_exploded[0];
        $image_exstension = mb_convert_case($category_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
        $image_path = "/frontstore/images/category-thumbs/";
        $category_image_path = $image_path.$image_name."_cat_thumb.".$image_exstension;
      }
      $category_is_section_header = $category_row['category_is_section_header'];
      $category_attribute_1 = $category_row['category_attribute_1'];
      $cd_pretty_url = $category_row['cd_pretty_url'];
      $cd_name = $category_row['cd_name'];
      $cd_hierarchy_path = $category_row['cd_hierarchy_path'];
      $cd_url = str_replace(array('(',')'), array('',''), mb_convert_case(mb_strimwidth($cd_hierarchy_path, 0, 95,'', 'utf-8'), MB_CASE_LOWER, "UTF-8"));
      $cyr_url = cyrialize_url($cd_url);
      if($category_is_section_header == 0) {
        $class_section_header = "";
        $href = "/$current_lang/cid-$category_id/$cyr_url";
//        $href = "/$current_lang/$cd_hierarchy_path?cid=$category_id";
      }
      else {
        $class_section_header = "section_header";
        $href =  "javascript:;";
      }

      $class_active = "";
      $dropdown_arrow = ($category_hierarchy_level == 1 && $category_is_section_header == 1) ? "<b class='caret'></b>" : "";
      $category_has_active_children = check_if_category_has_active_children($category_id);
      $category_is_last_child = check_if_this_is_category_last_child($category_parent_id,$category_sort_order);
      if($current_category_id == $category_id || $category_parent_id == $category_id) $class_active = 'active';

      if($category_has_children == 1 && $category_hierarchy_level < $number_of_hierarchy_levels && $category_has_active_children) {
        //if(strstr($current_page_path_string, $cd_pretty_url)) $class_active = ' active';
        $menu_backgr = (!is_null($category_image_path) && $category_hierarchy_level > 1) ? " style=\"padding-left: 40px;background: url('".$category_image_path."') no-repeat 10px 14px;\"" : "";
?>
        <li class="<?=$class_active;?>">
          <a href="<?=$href?>" title="<?=$cd_name?>">
            <?=$cd_name?>
            <span id="leo-cat-3" style="display:none" class="leo-qty badge pull-right"></span>
          </a>
          <ul>
<?php
          print_category_submenu($category_id,$number_of_hierarchy_levels);
      }
      else {
?>
          <li class="<?=$class_active;?>">
            <a href="<?=$href?>" title="<?=$cd_name?>">
              <?=$cd_name?>
              <span id="leo-cat-3" style="display:none" class="leo-qty badge pull-right"></span>
            </a>
          </li>
<?php
      }

      if($category_hierarchy_level > 1 && $category_is_last_child) {
?>
          </ul>
        </li>
<?php
      }
    }
?>

        </ul>
      </div>
    </div>
    <!-- /Block category submenu -->
<?php
  } // if($category_count > 0) {
}
  
function print_content_breadcrumbs($content_hierarchy_ids,$current_content_name) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;

  $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  //echo "<pre>";print_r($content_hierarchy_ids_array);echo "</pre><br>";
  $ids_count = count($content_hierarchy_ids_array);

  if($ids_count > 2) {
?>
    <a class="home" href="/" title="Доставка на цветя"><img src="/frontstore/images/home.png" width="12" height="12" alt="Доставка на цветя"></a>
    <span class="navigation-pipe">&gt;</span>
<?php
    foreach($content_hierarchy_ids_array as $key => $content_id) {

      if($key != 0 && $key != ($ids_count-1)) {
        $query_content = "SELECT `content_type_id`,`content_menu_text`,`content_hierarchy_path`
                          FROM `contents`
                          WHERE `content_id` = '$content_id' AND `content_show_in_menu` = '1'";
        //echo "<br>$query_content<br>";
        $result_content = mysqli_query($db_link, $query_content);
        if(!$result_content) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_content) > 0) {
          $content_row = mysqli_fetch_assoc($result_content);

          $content_type_id = $content_row['content_type_id'];
          $content_menu_text = stripslashes($content_row['content_menu_text']);

          switch($content_type_id) {
            case 1:
              $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
              break;
            case 2:
              $content_hierarchy_path = "javascript:;";
              break;
            case 4:
              $content_hierarchy_path = $content_text;
              break;
            default: $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
              break;
          }
?>
          <span class="navigation_page">
            <span itemscope itemtype="https://data-vocabulary.org/Breadcrumb">
            <?php if($category_is_section_header == 0) { ?>
              <a itemprop="url" href="<?=$content_hierarchy_path?>" title="<?=$content_menu_text;?>" >
                <span itemprop="title"><?=$content_menu_text;?></span>
              </a>
            <?php 
              } else {
                echo $content_menu_text;
              } 
            ?>
            </span>
          </span>
          <span class="navigation-pipe" >&gt;</span>
<?php
        }
      } //if($key != 0 || $key != $ids_count-1)
    } //foreach($content_hierarchy_ids_array
?>
    <span class="navigation-pipe">&gt;</span>
    <?=$current_content_name;?>
<?php
  }
  else {
?>
    <a class="home" href="/" title="<?=$languages[$current_lang]['title_goto_homepage'];?>"><i class="fa fa-home"></i></a>
    <span class="navigation-pipe">&gt;</span>
    <?=$current_content_name;?>
<?php
  }
}
  
function print_categories_breadcrumbs($category_hierarchy_ids, $current_cd_name, $is_category_page = false) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;

  //echo $category_hierarchy_ids;
  $category_hierarchy_ids_array = explode(".", $category_hierarchy_ids);
  //if(isset($_SESSION['debug'])) echo "<pre>";print_r($_SERVER);echo "</pre><br>";
  $ids_count = count($category_hierarchy_ids_array);
  //echo $ids_count;
?>
  <ol itemscope itemtype="https://schema.org/BreadcrumbList">
    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
      <a class="home" itemprop="item" href="/" title="Доставка на цветя">
        <span itemprop="name">Начало</span>
        <img src="/frontstore/images/home.png" width="12" height="12" alt="Доставка на цветя">
      </a>
      <meta itemprop="position" content="1" />
      <span class="navigation-pipe">&gt;</span>
    </li>
<?php
  $breadcrumb_key = 2;
  foreach($category_hierarchy_ids_array as $key => $current_category_id) {

    $prev_key = $key-1;

    if($key == 0 || !$is_category_page) {
      $query_categories = "SELECT `categories`.`category_id`,`categories`.`category_is_section_header`,`category_descriptions`.`cd_pretty_url`,
                                  `category_descriptions`.`cd_name`, `category_descriptions`.`cd_hierarchy_path`
                          FROM `categories`
                          INNER JOIN `category_descriptions` ON `category_descriptions`.`category_id` = `categories`.`category_id`
                          WHERE `categories`.`category_id` = '$current_category_id' AND `category_descriptions`.`language_id` = '$current_language_id'";
    }
    else {
      $query_categories = "SELECT `cat_1`.`category_id`,`cat_1`.`category_is_section_header`,`category_descriptions`.`cd_pretty_url`,`category_descriptions`.`cd_name`, 
                                  `category_descriptions`.`cd_hierarchy_path`
                          FROM `categories` as `cat_1`
                          INNER JOIN `categories` as `cat_2` ON `cat_1`.`category_id` = `cat_2`.`category_parent_id`
                          INNER JOIN `category_descriptions` ON `category_descriptions`.`category_id` = `cat_1`.`category_id`
                          WHERE `cat_2`.`category_id` = '$current_category_id' AND `category_descriptions`.`language_id` = '$current_language_id'";
    }
    //echo "<br>$query_categories<br>";
    if($key != ($ids_count-1) && $is_category_page) {

    }
    else {
      
      if(($ids_count == 1 && !$is_category_page) || $ids_count > 1) {
        
        $result_categories = mysqli_query($db_link, $query_categories);
        if(!$result_categories) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_categories) > 0) {
          $category_row = mysqli_fetch_assoc($result_categories);

          $category_id = $category_row['category_id'];
          $category_is_section_header = $category_row['category_is_section_header'];
          $cd_name = stripslashes($category_row['cd_name']);
          $cd_hierarchy_path = stripslashes($category_row['cd_hierarchy_path']);
          $cd_url = str_replace(array('(',')'), array('',''), mb_convert_case(mb_strimwidth($cd_hierarchy_path, 0, 95,'', 'utf-8'), MB_CASE_LOWER, "UTF-8"));
          $cyr_url = cyrialize_url($cd_url);

          $parent_id_url = (isset($category_hierarchy_ids_array[$prev_key])) ? "&cpid=$category_hierarchy_ids_array[$prev_key]" : "";
          if($category_id == 17) $parent_id_url = "&cpid=all";

          if($category_is_section_header == 0) { ?>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a itemprop="item" href="/<?=$current_lang;?>/cid-<?=$category_id?>/<?=$cyr_url;?>" title="<?=$cd_name;?>" >
                <span itemprop="name"><?=$cd_name;?></span>
              </a>
              <meta itemprop="position" content="<?=$breadcrumb_key;?>" />
              <span class="navigation-pipe">&gt;</span>
            </li>
          <?php 
            } else {
          ?>
            <!--<span itemprop="item"><span itemprop="name"><?=$cd_name;?></span></span>-->
          <?php
            }
        }
      }
      $breadcrumb_key++;
    } 
  } //foreach($category_hierarchy_ids_array as $key => $current_category_id)
?>
    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <span itemprop="name"><?=$current_cd_name;?></span>
    </li>
  </ol>
<?php
}
  
function print_left_column() {
  global $languages;
  global $current_lang;
?>
      <section id="right_column" class="column sidebar col-sp-12 col-xs-12 col-sm-12 col-md-3 col-lg-3">

        <div class="shopping_cart hidden-xs">
          <div class="media heading">
            <div class="title-cart pull-right">
              <span class="icon-cart"></span>
            </div>
            <div class="cart-inner media-body">
              <div class="title_block">
                <div class="clever_link no_hover" data-link="/<?=$current_lang;?>/shopping-cart/shopping-cart-overview" data-target="_self">
                  <?=$languages[$current_lang]['header_shopping_cart'];?>
                </div>
              </div>
              <span class="ajax_cart_total unvisible">
                <span class="price">0.00</span><span class="currency">лв.</span>
              </span>
              <span class="ajax_cart_quantity unvisible">0</span>
              <span class="ajax_cart_product_txt unvisible">item</span>
              <span class="ajax_cart_product_txt_s unvisible">item(s)</span>
              <span class="ajax_cart_no_product">(empty)</span>
              <span class="block_cart_expand unvisible">&nbsp;</span>
              <span class="block_cart_collapse">&nbsp;</span>
            </div>	
          </div>

          <div class="cart_block block exclusive">
            <div class="block_content">
              <div class="cart_block_list expanded">
                <?php print_shopping_cart() ?>
              </div>
            </div>
          </div><!-- .cart_block -->
        </div>

        <div id="random_products_right" class="block products_block hidden-xs hidden-sm">
          <div class="title_block">
            <span><?=$languages[$current_lang]['header_random_products'];?></span>
          </div>
          <div class="block_content products-block">
            <ul class="products products-block">
              <?php print_random_products('12')?>
            </ul>
          </div>
        </div>

      </section>
<?php
}
  
function print_html_user_profile_menu() {
  global $languages;
  global $current_lang;
?>
    <ul class="myaccount-link-list">
      <li<?php if(is_active_page("user-profile-data")) echo ' class="active"';?>>
        <a href="/<?=$current_lang;?>/user-profiles/user-profile-data">
          <i class="fa fa-user"></i>
          <span><?=$languages[$current_lang]['header_user_data'];?></span>
        </a>
      </li>
      <li<?php if(is_active_page("user-profile-orders")) echo ' class="active"';?>>
        <a href="/<?=$current_lang;?>/user-profiles/user-profile-orders">
          <i class="fa fa-list-ol"></i>
          <span><?=$languages[$current_lang]['header_orders'];?></span>
        </a>
      </li>
      <li<?php if(is_active_page("user-profile-wishlist")) echo ' class="active"';?>>
        <a href="/<?=$current_lang;?>/user-profiles/user-profile-wishlist">
          <i class="fa fa-heart"></i>
          <span><?=$languages[$current_lang]['header_wishlist'];?></span>
        </a>
      </li>
    </ul>
<?php  
}
  
function print_index_sliders($count) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $content_hierarchy_ids; //coming from frontstore/index.php or frontstore/categories.php

  $limit = ($count == 0) ? "" : "LIMIT $count";

  $query_sliders = "SELECT `sliders`.`slider_id`,`sliders`.`slider_sort_order`,`sliders_descriptions`.`slider_header`,
                           `sliders_descriptions`.`slider_text` ,`sliders_descriptions`.`slider_link`, `slider_img_alt`, `slider_img_title`
                    FROM `sliders`
                    INNER JOIN `sliders_descriptions` ON `sliders_descriptions`.`slider_id` = `sliders`.`slider_id`
                    WHERE `sliders`.`slider_is_active` = '1' AND `sliders_descriptions`.`language_id` = '$current_language_id'
                    ORDER BY `slider_sort_order` ASC $limit";
  //echo $query_sliders;exit;
  $result_sliders = mysqli_query($db_link, $query_sliders);
  if(!$result_sliders) echo mysqli_error($db_link);
  $sliders_count = mysqli_num_rows($result_sliders);
  if($sliders_count > 0) {
    $key = 0;
?>
<div id="slideshow" class="clearfix">
  <div class="bannercontainer banner-fullwidth" style="padding: 0;margin: 0px;background-color:#d9d9d9">
    <div id="slider" class="rev_slider fullwidthbanner" style="width:100%;height:450px;">
      <ul>
<?php
    while($slider_row = mysqli_fetch_assoc($result_sliders)) {

      $slider_id = $slider_row['slider_id'];
      $slider_header = $slider_row['slider_header'];
      $slider_text = $slider_row['slider_text'];
      $slider_link = $slider_row['slider_link'];
      $slider_img_alt = (!empty($slider_row['slider_img_alt'])) ? $slider_row['slider_img_alt'] : $slider_header;
      $slider_img_title = (!empty($slider_row['slider_img_title'])) ? $slider_row['slider_img_title'] : $slider_header;

      $query_images = "SELECT `name`, `is_background` FROM `slider_images` WHERE `slider_id` = '$slider_id'";
      $result_images = mysqli_query($db_link, $query_images);
      if(!$result_images) echo mysqli_error($db_link);
      $sliders_count = mysqli_num_rows($result_images);
      if($sliders_count > 0) {
        $slider_forground_image = "";
        while($image_row = mysqli_fetch_assoc($result_images)) {
          $is_background = $image_row['is_background'];
          $slider_image = $image_row['name'];
          if($is_background == 1) {
            $slider_background_image_name_exploded = explode(".", $slider_image);
            $slider_background_image_name = $slider_background_image_name_exploded[0];
            $slider_background_image_exstension = mb_convert_case($slider_background_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
            $slider_background_image_name_orig = "/frontstore/images/slider/".$slider_background_image_name."_frontstore.".$slider_background_image_exstension;
          }
          else {
            $slider_forground_image_name_exploded = explode(".", $slider_image);
            $slider_forground_image_name = $slider_forground_image_name_exploded[0];
            $slider_forground_image_exstension = mb_convert_case($slider_forground_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
            $slider_forground_image_name_orig = "/frontstore/images/slider/".$slider_forground_image_name."_frontstore.".$slider_forground_image_exstension;
            $slider_forground_image = "<img src='$slider_forground_image_name_orig' width='233' height='350' alt='$slider_header'>";
          }
        }

      }
//        if(($key % 2 == 0)) {
      if(true) {
?>
        <li data-masterspeed="300"  data-transition="random" data-slotamount="7" data-thumb="<?=$slider_background_image_name_orig?>">
          <img src="<?=$slider_background_image_name_orig?>" title="<?=$slider_img_title;?>" alt="<?=$slider_img_alt;?>"/>
          <div class="caption title_slier lfb easeInElastic ltt"
               data-x="539"
               data-y="122"
               data-speed="300"
               data-start="1200"
               data-easing="easeOutExpo" 
               onclick="window.open('<?=$slider_link;?>', '_self');" style="font-size:43px;background-color:transparent;color:#4da9c7;z-index: 62;">
            <?=$slider_header;?>
          </div>
          <div class="caption slider_dec lfr easeOutBack stl"
               data-x="540"
               data-y="206"
               data-speed="300"
               data-start="1600"
               data-easing="easeOutExpo" 
               onclick="window.open('<?=$slider_link;?>', '_self');" style="font-size:14px;color:#ffffff;z-index: 63;">
            <?=$slider_text;?>
          </div>
          <div class="caption btn-links sfr easeOutBack ltb"
               data-x="540"
               data-y="208"
               data-speed="300"
               data-start="2000"
               data-easing="easeOutExpo" 
               onclick="window.open('<?=$slider_link;?>', '_self');" style="z-index: 64;">
            <?=$languages[$current_lang]['btn_slider_see_more'];?>
          </div>
          <div class="caption lfb easeInElastic"
               data-x="24"
               data-y="12"
               data-speed="300"
               data-start="2400"
               data-easing="easeOutExpo">
            <?=$slider_forground_image;?>
          </div>
        </li>
<?php 
    }
    else {
?>
        <li data-masterspeed="300"  data-transition="random" data-slotamount="7" data-thumb="<?=$slider_background_image_name_orig?>">
          <img src="<?=$slider_background_image_name_orig?>" title="<?=$slider_img_title;?>" alt="<?=$slider_img_alt;?>"/>
        <div class="caption  title_slier lfb easeInElastic ltt"
            data-x="70"
            data-y="122"
            data-speed="300"
            data-start="1200"
            data-easing="easeOutExpo"
            onclick="window.open('<?=$slider_link;?>', '_self');"
            style="font-size:43px;background-color:transparent;color:#ffffff;z-index: 70;">
          <?=$slider_header;?>
        </div>
        <div class="caption slider_dec lfr easeOutBack stl"
            data-x="70"
            data-y="206"
            data-speed="300"
            data-start="1600"
            data-easing="easeOutExpo" 
            onclick="window.open('<?=$slider_link;?>', '_self');" 
            style="font-size:14px;color:#ffffff;z-index: 71;">
          <?=$slider_text;?>
        </div>
        <div class="caption btn-links sfr easeOutBack ltb"
            data-x="72"
            data-y="298"
            data-speed="300"
            data-start="2000"
            data-easing="easeOutExpo" 
            onclick="window.open('<?=$slider_link;?>', '_self');" 
            style="z-index: 72;">
          <?=$languages[$current_lang]['btn_slider_see_more'];?>
        </div>
        <div class="caption sft easeInQuart stb"
          data-x="774"
          data-y="0"
          data-speed="300"
          data-start="2000"
          data-easing="easeOutExpo">
          <img src="<?=$slider_forground_image_name_orig;?>" alt="<?=$slider_header;?>"/>
        </div>
      </li>
<?php
    }
    $key++;
  } //while($slider_row)
?>
      </ul>
      <div class="tp-bannertimer tp-top"></div>
    </div>
  </div>

  <script type="text/javascript">
    var tpj = jQuery;
    if (tpj.fn.cssOriginal != undefined) tpj.fn.css = tpj.fn.cssOriginal;

    tpj("#slider").revolution({
      delay: 9000,
      startheight: 450,
      startwidth: 1170,
      hideThumbs: 200,
      thumbWidth: 100,
      thumbHeight: 50,
      thumbAmount: 5,
      navigationType: "bullet",
      navigationArrows: "none",
      navigationStyle: "round",
      navOffsetHorizontal: 0,
      navOffsetVertical: 20,
      touchenabled: "on",
      onHoverStop: "on",
      shuffle: "off",
      stopAtSlide: -1,
      stopAfterLoops: -1,
      hideCaptionAtLimit: 0,
      hideAllCaptionAtLilmit: 0,
      hideSliderAtLimit: 0,
      fullWidth: "on",
      shadow: 0,
      startWithSlide: 0
    });
    $(document).ready(function () {
      $('.caption', $('#slider')).click(function () {
        if ($(this).data('link') != undefined && $(this).data('link') != '')
          location.href = $(this).data('link');
      });
    });
  </script>
</div>      
<?php
  } //if(mysqli_num_rows($result_sliders) > 0)
}
  
function print_index_banners($count) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $content_hierarchy_ids; //coming from frontstore/index.php or frontstore/categories.php

  $limit = ($count == 0) ? "" : "LIMIT $count";

  $query_banners = "SELECT `banners`.`banner_id`,`banners`.`banner_image`,`banners_links`.* 
                    FROM `banners` 
                    INNER JOIN `banners_links` ON `banners_links`.`banner_id` = `banners`.`banner_id`
                    WHERE `banners`.`banner_is_active` = '1' AND `banners_links`.`language_id` = '$current_language_id'
                    ORDER BY `banner_sort_order` ASC $limit";
  //echo $query_banners;exit;
  $result_banners = mysqli_query($db_link, $query_banners);
  if(!$result_banners) echo mysqli_error($db_link);
  $banners_count = mysqli_num_rows($result_banners);
  if($banners_count > 0) {
    $key = 0;

    while($banner_row = mysqli_fetch_assoc($result_banners)) {

      $banner_id = $banner_row['banner_id'];
      $banner_name = $banner_row['banner_name'];
      $banner_image = $banner_row['banner_image'];
      $banner_link = $banner_row['banner_link'];
      $banner_img_alt = (!empty($banner_row['banner_img_alt'])) ? $banner_row['banner_img_alt'] : $banner_name;
      $banner_img_title = (!empty($banner_row['banner_img_title'])) ? $banner_row['banner_img_title'] : $banner_name;
?>
      <div class="static_banner">
        <a href="<?=$banner_link;?>">
          <img src="/frontstore/images/banners/<?=$banner_image;?>" width="293" height="111" title="<?=$banner_img_title;?>" alt="<?=$banner_img_alt;?>" >
        </a>
      </div>
<?php
      $key++;
    } //while($banner_row)

  } //if(mysqli_num_rows($result_banners) > 0)
}
  
function print_html_shipping_time_interval_form($shipping_date = false) {
  
  global $db_link;
  global $languages;
  global $current_hour;
  global $current_lang;

  $interval_1_visib = "";
  $interval_2_visib = "";
  $interval_3_visib = "";
  $interval_4_visib = "";
  $interval_5_visib = "";
  $today = date("Y-m-d");
  $tomorrow = date("Y-m-d", strtotime('+1 day', strtotime($today)));
  if(!isset($current_hour)) $current_hour = date('Hi', time());
  //echo $current_hour;
  
  $non_working_day = false;
  $query = "SELECT `day_id` FROM `non_working_days` WHERE `day` = '$shipping_date'";
  //echo $query;
  $result = mysqli_query($db_link, $query);
  if(!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result) > 0) {
    $non_working_day = true;
  }
  
  $query = "SELECT `shipping_interval_limit` FROM `order_settings`";
  $result = mysqli_query($db_link, $query);
  if(!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $shipping_interval_limit = $row['shipping_interval_limit'];
  }
?>
    <label for="shipping_time_interval"><?=$languages[$current_lang]['header_delivery_time_interval'];?><span class="red">*</span></label>
<?php
  if($shipping_date != $today) {
    /*
     * we have 5 intervals
     * 1st one is 9:30-12:00, which in database is 9 (from the first interval)
     * 2nd one is 12:00-15:00, which in database is 12 (from the first interval)
     * 3rd one is 15:00-17:00, which in database is 15 (from the first interval)
     * 4th one is 17:00-21:00, which in database is 17 (from the first interval)
     * 5th one is 9:00-17:00 (the whole working day), which in database is 917 (from the first interval + second)
     */
    $interval_4_disabled = "";
    if($non_working_day) {
      $interval_1_visib = " hint_not_aval";
      $interval_2_visib = " hint_not_aval";
      $interval_3_visib = " hint_not_aval";
      $interval_4_visib = " hint_not_aval";
      $interval_5_visib = " hint_not_aval";
    }
    else {
      /*
       * if the selected date is tomorrow and the current hour is past 20:00 o'clock 
       * the fisrt interval will be unavailable
       */
      if($shipping_date == $tomorrow && $current_hour > 2000) {
        $interval_1_visib = " hidden";
        $query_check_int_2_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '12'";
        //echo $query_check_int_2_availability."<br>";
        $result_check_int_2_availability = mysqli_query($db_link, $query_check_int_2_availability);
        if(!$result_check_int_2_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_2_availability) > 0) {
          $row_int_2_availability = mysqli_fetch_assoc($result_check_int_2_availability);
          $orders_count = $row_int_2_availability['orders_count'];
          $interval_2_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_3_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '15'";
        //echo $query_check_int_3_availability."<br>";
        $result_check_int_3_availability = mysqli_query($db_link, $query_check_int_3_availability);
        if(!$result_check_int_3_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_3_availability) > 0) {
          $row_int_3_availability = mysqli_fetch_assoc($result_check_int_3_availability);
          $orders_count = $row_int_3_availability['orders_count'];
          $interval_3_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_4_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '17'";
        //echo $query_check_int_4_availability."<br>";
        $result_check_int_4_availability = mysqli_query($db_link, $query_check_int_4_availability);
        if(!$result_check_int_4_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_4_availability) > 0) {
          $row_int_4_availability = mysqli_fetch_assoc($result_check_int_4_availability);
          $orders_count = $row_int_4_availability['orders_count'];
          $interval_4_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_5_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '917'";
        //echo $query_check_int_5_availability."<br>";
        $result_check_int_5_availability = mysqli_query($db_link, $query_check_int_5_availability);
        if(!$result_check_int_5_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_5_availability) > 0) {
          $row_int_5_availability = mysqli_fetch_assoc($result_check_int_5_availability);
          $orders_count = $row_int_5_availability['orders_count'];
          $interval_5_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
      }
      else {
        /*
         * in all other cases if the selected date is not today all the intervals are available
         * of course if there are less then 4 orders (or whatever is choosen for maximum orders for a day from the admin in products-shipping-interval.php) 
         */
        $query_check_int_1_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '9'";
        //echo $query_check_int_1_availability."<br>";
        $result_check_int_1_availability = mysqli_query($db_link, $query_check_int_1_availability);
        if(!$result_check_int_1_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_1_availability) > 0) {
          $row_int_1_availability = mysqli_fetch_assoc($result_check_int_1_availability);
          $orders_count = $row_int_1_availability['orders_count'];
          $interval_1_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_2_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '12'";
        //echo $query_check_int_2_availability."<br>";
        $result_check_int_2_availability = mysqli_query($db_link, $query_check_int_2_availability);
        if(!$result_check_int_2_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_2_availability) > 0) {
          $row_int_2_availability = mysqli_fetch_assoc($result_check_int_2_availability);
          $orders_count = $row_int_2_availability['orders_count'];
          $interval_2_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_3_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '15'";
        //echo $query_check_int_3_availability."<br>";
        $result_check_int_3_availability = mysqli_query($db_link, $query_check_int_3_availability);
        if(!$result_check_int_3_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_3_availability) > 0) {
          $row_int_3_availability = mysqli_fetch_assoc($result_check_int_3_availability);
          $orders_count = $row_int_3_availability['orders_count'];
          $interval_3_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_4_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '17'";
        //echo $query_check_int_4_availability."<br>";
        $result_check_int_4_availability = mysqli_query($db_link, $query_check_int_4_availability);
        if(!$result_check_int_4_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_4_availability) > 0) {
          $row_int_4_availability = mysqli_fetch_assoc($result_check_int_4_availability);
          $orders_count = $row_int_4_availability['orders_count'];
          //$orders_count = 4;
          $interval_4_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_5_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '917'";
        //echo $query_check_int_5_availability."<br>";
        $result_check_int_5_availability = mysqli_query($db_link, $query_check_int_5_availability);
        if(!$result_check_int_5_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_5_availability) > 0) {
          $row_int_5_availability = mysqli_fetch_assoc($result_check_int_5_availability);
          $orders_count = $row_int_5_availability['orders_count'];
          //$orders_count = 4;
          $interval_5_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
      }
    }
  }
  else {
    //today
    
    /*
     * $interval_4_disabled will be used for automatically selecting the last interval when $current_hour is between 1400 and 1600
     * because in this time of the day all other intervals are not selectable
     */
    $interval_4_disabled = 'disabled="disabled"';
    $interval_1_visib = " hidden";
    /*
     * we will not be using interval 5, which is the whole working day from 9:00 till 17:00, when the choosen date is today
     */
    $interval_5_visib = " hidden";
    if($current_hour >= 1100 && $current_hour < 1400) {
      $interval_1_visib = " hidden";
      $interval_2_visib = " hidden";
      if($non_working_day) {
        $interval_3_visib = " hint_not_aval";
        $interval_4_visib = " hint_not_aval";
        $interval_5_visib = " hint_not_aval";
      }
      else {
        $query_check_int_3_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '15'";
        //echo $query_check_int_3_availability."<br>";
        $result_check_int_3_availability = mysqli_query($db_link, $query_check_int_3_availability);
        if(!$result_check_int_3_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_3_availability) > 0) {
          $row_int_3_availability = mysqli_fetch_assoc($result_check_int_3_availability);
          $orders_count = $row_int_3_availability['orders_count'];
          $interval_3_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_4_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '17'";
        //echo $query_check_int_4_availability."<br>";
        $result_check_int_4_availability = mysqli_query($db_link, $query_check_int_4_availability);
        if(!$result_check_int_4_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_4_availability) > 0) {
          $row_int_4_availability = mysqli_fetch_assoc($result_check_int_4_availability);
          $orders_count = $row_int_4_availability['orders_count'];
          $interval_4_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
      }
    }
    elseif($current_hour >= 1400 && $current_hour < 1600) {
      $interval_1_visib = " hidden";
      $interval_2_visib = " hidden";
      $interval_3_visib = " hidden";
      $interval_4_visib = " ";
      $interval_4_disabled = ""; //in this interval the last time period is automatically choosen
      if($non_working_day) {
        $interval_4_visib = " hint_not_aval";
        $interval_5_visib = " hint_not_aval";
      }
      else {
        $query_check_int_4_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '17'";
        //echo $query_check_int_4_availability."<br>";
        $result_check_int_4_availability = mysqli_query($db_link, $query_check_int_4_availability);
        if(!$result_check_int_4_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_4_availability) > 0) {
          $row_int_4_availability = mysqli_fetch_assoc($result_check_int_4_availability);
          $orders_count = $row_int_4_availability['orders_count'];
          $interval_4_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
      }
    }
    else {
      //all intervals are available
      if($non_working_day) {
        $interval_1_visib = " hint_not_aval";
        $interval_2_visib = " hint_not_aval";
        $interval_3_visib = " hint_not_aval";
        $interval_4_visib = " hint_not_aval";
        $interval_5_visib = " hint_not_aval";
      }
      else {
        $query_check_int_1_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '9'";
        //echo $query_check_int_1_availability."<br>";
        $result_check_int_1_availability = mysqli_query($db_link, $query_check_int_1_availability);
        if(!$result_check_int_1_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_1_availability) > 0) {
          $row_int_1_availability = mysqli_fetch_assoc($result_check_int_1_availability);
          $orders_count = $row_int_1_availability['orders_count'];
          $interval_1_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_2_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '12'";
        //echo $query_check_int_2_availability."<br>";
        $result_check_int_2_availability = mysqli_query($db_link, $query_check_int_2_availability);
        if(!$result_check_int_2_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_2_availability) > 0) {
          $row_int_2_availability = mysqli_fetch_assoc($result_check_int_2_availability);
          $orders_count = $row_int_2_availability['orders_count'];
          $interval_2_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_3_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '15'";
        //echo $query_check_int_3_availability."<br>";
        $result_check_int_3_availability = mysqli_query($db_link, $query_check_int_3_availability);
        if(!$result_check_int_3_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_3_availability) > 0) {
          $row_int_3_availability = mysqli_fetch_assoc($result_check_int_3_availability);
          $orders_count = $row_int_3_availability['orders_count'];
          $interval_3_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_4_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '17'";
        //echo $query_check_int_4_availability."<br>";
        $result_check_int_4_availability = mysqli_query($db_link, $query_check_int_4_availability);
        if(!$result_check_int_4_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_4_availability) > 0) {
          $row_int_4_availability = mysqli_fetch_assoc($result_check_int_4_availability);
          $orders_count = $row_int_4_availability['orders_count'];
          $interval_4_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
        $query_check_int_5_availability = "SELECT COUNT(`order_id`) as `orders_count` FROM `orders` 
                                            WHERE `shipping_date` = '$shipping_date' AND `order_status_id` IN(2,15) AND `shipping_time_interval` = '917'";
        //echo $query_check_int_5_availability."<br>";
        $result_check_int_5_availability = mysqli_query($db_link, $query_check_int_5_availability);
        if(!$result_check_int_5_availability) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_int_5_availability) > 0) {
          $row_int_5_availability = mysqli_fetch_assoc($result_check_int_5_availability);
          $orders_count = $row_int_5_availability['orders_count'];
          //$orders_count = 4;
          $interval_5_visib .= ($orders_count < $shipping_interval_limit) ? "" : " hint_not_aval";
        }
      }
    }
  }
?>
  <a href="javascript:;" class="time_interval interval_1<?=$interval_1_visib?>">
    9:30-12:00<input type="hidden" name="shipping_interval[]" value="9" disabled="disabled"/>
  </a>
  <a href="javascript:;" class="time_interval interval_2<?=$interval_2_visib?>">
    12:00-15:00<input type="hidden" name="shipping_interval[]" value="12" disabled="disabled"/>
  </a>
  <a href="javascript:;" class="time_interval interval_3<?=$interval_3_visib?>">
    15:00-17:00<input type="hidden" name="shipping_interval[]" value="15" disabled="disabled"/>
  </a>
  <a href="javascript:;" class="time_interval interval_4<?=$interval_4_visib?>">
    17:00-21:00<input type="hidden" name="shipping_interval[]" value="17" <?=$interval_4_disabled;?>/>
  </a>
  <a href="javascript:;" class="time_interval interval_5<?=$interval_5_visib?>">
    9:00-17:00<input type="hidden" name="shipping_interval[]" value="917"/> (целия работен ден)
  </a>
<?php  
}
  
function print_html_shopping_cart_progress() {
  global $languages;
  global $current_lang;
?>
  <!-- Steps -->
  <ul class="step clearfix" id="order_step">
    <li class="col-md-4 col-xs-12 <?php if(is_active_page("shopping-cart-overview")) echo 'step_current';else echo "step_todo"?> first">
      <span><em>01.</em> <?=$languages[$current_lang]['header_shopping_cart_progress_overview'];?></span>
    </li>
    <li class="col-md-4 col-xs-12 <?php if(is_active_page("shopping-cart-addresses")) echo 'step_current';else echo "step_todo"?> second">
      <span><em>02.</em> <?=$languages[$current_lang]['header_shopping_cart_progress_address'];?></span>
    </li>
    <li class="col-md-4 col-xs-12 <?php if(is_active_page("shopping-cart-checkout-cash-success") || is_active_page("shopping-cart-checkout-card-success")) echo 'step_current';else echo "step_todo"?> last">
      <span><em>03.</em> <?=$languages[$current_lang]['header_shopping_cart_progress_place_order'];?></span>
    </li>
  </ul>
  <!-- /Steps -->
<?php  
}
  
function print_html_footer() {
  global $languages;
  global $current_lang;
?>
<!--footer-->
<footer id="footer" class="footer-container">
  <div id="leo-footer-top" class="footer-top">
    <div class="container">
      <div class="inner">
        <div class="row">
          <div>
            <div class="widget col-lg-8 col-md-8 col-sm-12 col-xs-12 col-sp-12">
              <div class="widget-html block footer-block block nopadding">
                <div class="title_block"><?=$languages[$current_lang]['header_payment_method'];?></div>
                <div id="card" class="block_content toggle-footer">
                  <div class="single_card">
                    <img src="/frontstore/images/visa_79x50.png" class="img-responsive" alt="Visa card -" width="79" height="50">
                  </div>
                  <div class="single_card">
                    <img src="/frontstore/images/visa_electron_79x50.png" class="img-responsive" alt="Visa Electron card logo" width="79" height="50">
                  </div>
                  <div class="single_card">
                    <img src="/frontstore/images/v_pay_46x50.png" class="img-responsive" alt="V-pay card logo" width="46" height="50">
                  </div>
                  <div class="single_card">
                    <span class="clever_link" data-link="https://www.mastercard.com/index.html" data-target="_blank">
                      <img src="/frontstore/images/mc_accpt_80x50.gif" class="img-responsive" alt="MasterCard logo" width="80" height="50">
                    </span>
                  </div>
                  <div class="single_card">
                    <span class="clever_link" data-link="https://www.mastercard.com/index.html" data-target="_blank">
                      <img src="/frontstore/images/me_accpt_80x50.gif" class="img-responsive" alt="MasterCard Electron logo" width="80" height="50">
                    </span>
                  </div>
                  <div class="single_card more_margin">
                    <img src="/frontstore/images/ms_accpt_80x50.gif" class="img-responsive" alt="Maestro card logo" width="80" height="50">
                  </div>
                  <div class="single_card more_margin">
                    <img src="/frontstore/images/vbv_98x50.gif" class="img-responsive" alt="Verified by Visa logo" width="98" height="50">
                  </div>
                  <div class="single_card">
                    <img src="/frontstore/images/sclogo_92x50.gif" class="img-responsive" alt="MasterCard SecureCode logo" width="92" height="50">
                  </div>
                  <div class="clear"></div>
                </div>
              </div>
            </div>
            <div class="widget col-lg-4 col-md-4 col-sm-12 col-xs-12 col-sp-12">
              <div id="social_block" class="block clearfix">
                <div class="title_block"><?=$languages[$current_lang]['text_social'];?></div>
                <div class="block_content">
                  <ul>
                    <li class="facebook"> 
                      <a target="_blank" href="https://www.facebook.com/CvetarskiMagazinLaRose">
                        <img src="/frontstore/images/facebook.png" width="300" height="300" alt="Цветарски магазин LaRose в facebook" title="Facebook и доставка на цветя от LaRose" style="display:none;">
                        <span>Facebook</span> 
                      </a> 
                    </li>
                    <li class="twitter"> 
                      <a target="_blank" href="https://twitter.com/Cvetia_online"> 
                        <img src="/frontstore/images/twitter.jpg" width="360" height="360" alt="Цветарски магазин LaRose в Twitter" title="Twitter и доставка на цветя от LaRose" style="display:none;">
                        <span>Twitter</span> </a> 
                    </li>
                    <li class="pinterest"> 
                      <a target="_blank" href="https://www.pinterest.com/cvetarskiLaRose/"> 
                        <img src="/frontstore/images/pinterest.png" width="512" height="512" alt="Цветарски магазин LaRose в Pinterest" title="Pinterest и доставка на цветя от LaRose" style="display:none;">
                        <span>Pinterest</span> 
                      </a> 
                    </li>
                    <li class="instagram"> 
                      <a target="_blank" href="https://www.instagram.com/cvetarski_magazin_larose/"> 
                        <img src="/frontstore/images/instagram.jpg" width="360" height="360" alt="Цветарски магазин LaRose в Instagram" title="Instagram и доставка на цветя от LaRose" style="display:none;">
                        <span>Instagram</span> </a> 
                    </li>
                  </ul>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- #footertop -->
  <div id="leo-footer-center" class="footer-center">
    <div class="container">
      <div class="inner">
        <div class="row">
          <div class="widget col-lg-3 col-md-3 col-sm-12 col-xs-12 col-sp-12">
            <div class="widget-links block block nopadding footer-block">
              <div class="title_block"> <?=$languages[$current_lang]['header_menu'];?> </div>
              <div class="block_content toggle-footer">
                <div id="tabs932030802" class="panel-group">
                  <ul class="nav-links">
                    <?php print_footer_menu($content_hierarchy_level_start = 2,$number_of_hierarchy_levels = 1,$offset = 0); ?>
                    <li>
                      <?php if($current_lang == "bg") { ?>
                      <a href="/bg/pid-497/produkt-s-tsena-po-izbor">Продукт с цена по избор</a>
                      <?php } else { ?>
                      <a href="/en/pid-497/product-with-price-by-choice">Product with price by choice</a>
                      <?php } ?>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="widget col-lg-3 col-md-3 col-sm-12 col-xs-12 col-sp-12 no_padding">
            <div class="widget-html block footer-block block nopadding">
              <div class="title_block"><?=$languages[$current_lang]['header_name'];?></div>
              <div class="block_content toggle-footer">
                <?=$languages[$current_lang]['text_shop_name'];?>
              </div>
              <div class="title_block"><?=$languages[$current_lang]['text_bank'];?></div>
              <div class="block_content toggle-footer">
                Обединена Българска Банка<br>
                Ла Роза 5 ЕООД<br>
                BGN BG37UBBS80021041700440<br>
                EUR BG76UBBS80021454005610
              </div>
              <div class="title_block"> <?=$languages[$current_lang]['header_street'];?> </div>
              <div class="block_content toggle-footer">
                <p><?=$languages[$current_lang]['text_address_01'];?></p>
                <!--<span><?=$languages[$current_lang]['text_address_02'];?></span>-->
              </div>
              <div class="title_block"> <?=$languages[$current_lang]['header_phone'];?> </div>
              <div class="block_content toggle-footer">
                <span>089 881 8116  /  087 881 8116</span>
              </div>
            </div>
          </div>
          <div class="widget col-lg-3 col-md-3 col-sm-12 col-xs-12 col-sp-12 hidden">
            <div class="widget-links block block nopadding footer-block">
              <div class="title_block"> Assistance </div>
              <div class="block_content toggle-footer">
                <div id="tabs580088991" class="panel-group">
                  <ul class="nav-links">
                    <?php print_footer_menu($content_hierarchy_level_start = 2,$number_of_hierarchy_levels = 1,$offset = 4); ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="widget col-lg-6 col-md-6 col-sm-12 col-xs-12 col-sp-12"> 
            <!-- cart_default	 -->
            <div class="widget-images block">
              <div class="title_block"> <?=$languages[$current_lang]['text_photo_gallery'];?> </div>
              <div class="block_content clearfix">
                <div class="images-list clearfix">
                  <div class="row footer_gallery">
                    <?php print_gallery_in_footer($gallery_id = 1) ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- #footercenter -->
  <div id="footernav" class="footer-nav">
    <div class="container">
      <div class="inner">
        <div id="powered"> &COPY; <?=date("Y");?> <?=$languages[$current_lang]['text_all_rights_reserved'];?>. <br/>
          <?=$languages[$current_lang]['text_developed_by'];?> <span>Eterrasystems</span>
        </div>
      </div>
    </div>
  </div>
</footer>

<?php
  if(!isset($_SESSION['customer'])) {
?>
  <form name="loginform" action="javascript:;" method="post" id="loginform" style="display:none;">
    <a href="javascript:;" class="close_btn"></a>
    <div>
      <table>
        <tr>
          <td>
            <label for="login_customer_email"><?=$languages[$current_lang]['login_customer_email'];?>:</label>
            <input type="text" autofocus name="login_customer_email" id="login_customer_email" class="form-control" required="required" />
          </td>
        </tr>
        <tr>
          <td>
            <label for="login_customer_password"><?=$languages[$current_lang]['login_customer_password'];?>:</label>
            <input type="password" name="login_customer_password" id="login_customer_password" class="form-control" required="required" />
          </td>
        </tr>
        <tr>
          <td>
            <label class="captcha" for="login_customer_captcha"><?=$languages[$current_lang]['login_customer_captcha'];?></label>
            <img src="/captchas/<?=$_SESSION['captcha123']['img'];?>" class="float_left" alt="<?=$languages[$current_lang]['alt_login_captcha'];?>" style="margin-right:10px;"/>
            <input type="text" name="login_customer_captcha" id="login_customer_captcha" class="form-control float_left" maxlength="6" required="required" />
          </td>
        </tr>
        <tr>
          <td>
            <span class="clever_link f_pass" data-link="/<?=$current_lang;?>/user-profiles/user-profile-change-password" data-target="_self">
              <?=$languages[$current_lang]['link_forgotten_password'];?>
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <button type="submit" name="login" class="button btn btn-outline" onClick="LogInUser('<?=$current_lang;?>');">
            <?=$languages[$current_lang]['btn_login'];?>
            </button>
          </td>
        </tr>
      </table>
    </div>
  </form>
<?php
  }
?>
<div id="modal_window_backgr"></div>
<div id="modal_window"></div>
<div id="ajax_loader_backgr"></div>
<div id="ajax_loader">
  <div class="sk-cube-grid">
    <div class="sk-cube sk-cube1"></div>
    <div class="sk-cube sk-cube2"></div>
    <div class="sk-cube sk-cube3"></div>
    <div class="sk-cube sk-cube4"></div>
    <div class="sk-cube sk-cube5"></div>
    <div class="sk-cube sk-cube6"></div>
    <div class="sk-cube sk-cube7"></div>
    <div class="sk-cube sk-cube8"></div>
    <div class="sk-cube sk-cube9"></div>
  </div>
</div>
<div id="ajax_notification">
  <span class="close_warning">Close</span>
  <p class="ajaxmessage"></p>
</div>
<script type="text/javascript">
 
  function ClosePopUpWindow() {
    createCookie('pop_up_window','1',24);
    $("#pop_up_window_holder").remove();
  }

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-71617868-1', 'auto');
  ga('send', 'pageview');
  
//  var leoOption = {
//    productNumber:0,
//    productInfo:0,
//    productTran:1,
//    productCdown: 0,
//    productColor: 0
//  }
  
  $(document).ready( function(){
    $(".blockcart_top").bind('mouseenter', function() {
      //e.preventDefault();
      $("#header-main .cart_block.exclusive").slideDown();
    });
    $(".blockcart_top").bind('mouseleave', function() {
      $("#header-main .cart_block.exclusive").slideUp();
    });
    $("nav .section_header").bind('click', function() {
      var ul = $(this).find("ul.dropdown_menu");
      if($(ul).css("display") == "block") {
        $("ul.dropdown_menu").slideUp();
        $(".section_header").removeClass("active");
        $(ul).slideUp();
      }
      else {
        $("ul.dropdown_menu").slideUp();
        $(".section_header").removeClass("active");
        $(ul).slideDown();
        $(this).addClass("active");
      }
    });
    
    //languages
    //$(".choose_language").html($("#languages li.active a").html() + " <i class='fa fa-angle-down'></i>");
    $(".choose_language").html($("#languages li.active a").html());
    $("#languages li.active").hide();
    $("#choose_language .choose_language").bind('click', function () {
      if ($("#languages").css("display") == "block") {
        $("#languages").slideUp();
        $("#choose_language").removeClass("active");
      }
      else {
        $("#languages").slideDown();
        $("#choose_language").addClass("active");
      }
    });
    
    //currencies
    $(".choose_currency").html($("#currencies li.active a").html());
    $("#currencies li.active").hide();
    $("#choose_currency .choose_currency").bind('click', function () {
      if ($("#currencies").css("display") == "block") {
        $("#currencies").slideUp();
        $("#choose_currency").removeClass("active");
      }
      else {
        $("#currencies").slideDown();
        $("#choose_currency").addClass("active");
      }
    });
  
    $(".login_btn").bind('click', function() {
      if($("#loginform").css("display") == "block") {
        $("#loginform").hide();
        $("#modal_window_backgr").hide();
        $(".warning_field").remove();
      }
      else {
        $("#loginform").show();
        $("#modal_window_backgr").show();
      }
    });
    $("#loginform .close_btn").bind('click', function() {
      $("#loginform").hide();
      $("#modal_window_backgr").hide();
      $(".warning_field").remove();
    });
    
    $(".fancybox").fancybox({
      openEffect: 'none',
      closeEffect: 'none'
    });
    $('.clever_link').click(function(){
      var target = $(this).data('target');
      //console.log(target);
      window.open($(this).data('link'), target);
      return false;
    });
    jQuery("#cavas_menu").OffCavasmenu();
    $('#cavas_menu .navbar-toggle').click(function() {
        $('body,html').animate({
            scrollTop: 0
        }, 0);
        return false;
    });
  });
  
  if(!$("li.active").length) {
    $("li.home").addClass("active");
  }
  
  (function($) {
    $.fn.OffCavasmenu = function(opts) {
        // default configuration
        var config = $.extend({}, {
            opt1: null,
            text_warning_select: "Please select One to remove?",
            text_confirm_remove: "Are you sure to remove footer row?",
            JSON: null
        }, opts);
        // main function
        // initialize every element
        this.each(function() {
            var $btn = $('#cavas_menu .navbar-toggle');
            var $nav = null;
            if (!$btn.length)
                return;
            var $nav = $('<section id="off-canvas-nav" class="leo-megamenu"><nav class="offcanvas-mainnav" ><div id="off-canvas-button"><span class="off-canvas-nav"></span>Close</div></nav></sections>');
            var $menucontent = $($btn.data('target')).find('.megamenu').clone();
            $("body").append($nav);
            $("#off-canvas-nav .offcanvas-mainnav").append($menucontent);
            $("#off-canvas-nav .offcanvas-mainnav").css('min-height',$(window).height()+30+"px");
            $("html").addClass ("off-canvas");
            $("#off-canvas-button").click( function(){
                    $btn.click();	
            } );
            $btn.toggle(function() {
                $("body").removeClass("off-canvas-inactive").addClass("off-canvas-active");
            }, function() {
                $("body").removeClass("off-canvas-active").addClass("off-canvas-inactive");
            });
        });
        return this;
    }
  })(jQuery);

  $(document.body).on('click', '[data-toggle="dropdown"]' ,function(){
      if(!$(this).parent().hasClass('open') && this.href && this.href != '#'){
          window.location.href = this.href;
      }
  });

</script>
<!--footer-->
<?php
}
  
function print_newest_products($count,$category_id = false) {

  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  global $cd_pretty_url;
  global $current_page_path_string;
  global $customer_wishlist;

  $where_category = ($category_id) ? " AND `categories`.`category_id` = '$category_id'" : "";

  /*
   * old query
   */
  $query_products = "SELECT `products`.`product_id`,`products`.`stock_status_id`,`products`.`product_isbn`,`products`.`product_quantity`,`products`.`product_price`,
                            `products`.`product_subtract`,`products`.`product_viewed`,`product_discount`.`pd_price`,
                            `product_description`.`pd_name`,`product_description`.`pd_description`, `category_descriptions`.`cd_name`
                       FROM `products`
                 INNER JOIN `product_to_category` USING(`product_id`)
                 INNER JOIN `product_description` USING(`product_id`)
                  LEFT JOIN `product_discount` USING(`product_id`)
                 INNER JOIN `categories` ON (`product_to_category`.`category_id` = `categories`.`category_id` AND `categories`.`category_is_active` = '1')
                 INNER JOIN `category_descriptions` ON (`categories`.`category_id` = `category_descriptions`.`category_id` AND `category_descriptions`.`cd_is_active` = '1')
                      WHERE `products`.`product_newest` = '1' AND `products`.`product_is_active` = '1' $where_category 
                        AND `product_description`.`language_id` = '$current_language_id' AND `products`.`stock_status_id` = '1'
                        AND `product_description`.`pd_is_active` = '1' AND `category_descriptions`.`language_id` = '$current_language_id'
                   GROUP BY `products`.`product_id`
                   ORDER BY `products`.`product_id` DESC 
                      LIMIT $count";
  
  /*
   * new query, there is a cron job using the above query and inserting once a day newest products
   */
  $query_products = "SELECT `newest_products`.* FROM `newest_products` WHERE `language_id` = '$current_language_id'";
  //if($_SESSION['debug']) echo $query_products."<br>"; 
  $result_products = mysqli_query($db_link, $query_products);
  if(!$result_products) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_products) > 0) {

    $blocks_on_row = 1;

    while($product_row = mysqli_fetch_assoc($result_products)) {

      //if($_SESSION['debug'])    print_array_for_debug($product_row); 
      if($blocks_on_row == 4) $blocks_on_row = 1;

      $cd_name = $product_row['cd_name'];
      $product_id = $product_row['product_id'];
      $pd_name = $product_row['pd_name'];
      $pd_name_for_alt = ($current_lang == "bg") ? "Доставка на цветя $pd_name" : "";
      $pd_name_for_title = ($current_lang == "bg") ? "$pd_name с доставка на цветя от цветарски магазин LaRose" : "";
      $pd_description = stripslashes($product_row['pd_description']);
      $product_quantity = $product_row['product_quantity'];
      $product_subtract = $product_row['product_subtract'];
      $product_isbn = $product_row['product_isbn'];
      $product_viewed = $product_row['product_viewed'];
      $stock_status_id = $product_row['stock_status_id'];

      $pd_images_folder = "/frontstore/images/products/";
      $pd_images_watermark = "/frontstore/images/watermark-170-250.png";
      $default_img = get_product_default_image($product_id);

      $pi_names_array = get_product_images($product_id);
      if((isset($pi_names_array['default']['pi_name']) && !empty($pi_names_array['default']['pi_name']))) {
        $default_img = $pi_names_array['default']['pi_name'];
        $default_img_exploded = explode(".", $default_img);
        $default_img_name = $default_img_exploded[0];
        $default_img_exstension = $default_img_exploded[1];
        $gallery_img_home_default = $pd_images_folder.$default_img_name."_home_default.".$default_img_exstension;
        $gallery_img_cart = $pd_images_folder.$default_img_name."_small_default.".$default_img_exstension;
        $full_path = $_SERVER['DOCUMENT_ROOT'].$gallery_img_home_default;

        $file = $full_path;

        list($width,$height) = getimagesize($file);
        $default_img_dimensions = "width='$width' height='$height'";
                
        if($width > $height) {
          $default_img_style = "";
        }
        else {
          $default_img_style = "style='height:100%;width:auto;'";
        }
      }
      else $gallery_img_path_large = $pd_images_folder."no_image.jpg";
      $additional_img = "";
      if(isset($pi_names_array['gallery'])) {
        //echo"<pre>";print_r($pi_names_array['gallery']);
        $gallery_img = $pi_names_array['gallery'][0]['pi_name'];
        $gallery_img_exploded = explode(".", $gallery_img);
        $gallery_img_name = $gallery_img_exploded[0];
        $gallery_img_exstension = $gallery_img_exploded[1];
        $gallery_additional_img = $pd_images_folder.$gallery_img_name."_home_default.".$gallery_img_exstension;
        $full_path = $_SERVER['DOCUMENT_ROOT'].$gallery_additional_img;

        $file = $full_path;

        list($width,$height) = @getimagesize($file);

        if($width > $height) {
          $img_style = "";
        }
        else {
          $img_style = "style='height:100%;width:auto;'";
        }

        $additional_img = "<img src='$gallery_additional_img' width='$width' height='$height' title='$pd_name_for_title' alt='$pd_name_for_alt' class='img-responsive' $img_style>";
      }

      $product_price = $product_row['product_price'];
      $pd_price = (!empty($product_row['pd_price']) && $product_row['pd_price'] != 0.00) ? $product_row['pd_price'] : "";
      $price_discount_class = (!empty($pd_price)) ? " class='line_through'" : "";
      $class_first = ($blocks_on_row == 1) ? " first_item" : "";
      $pd_name_for_link = beautify_name_for_url($pd_name);
      $cyr_url = cyrialize_url($pd_name_for_link);
      $product_link = "/$current_lang/pid-$product_id/$cyr_url";
      $quick_view_link = "/$current_lang/product-quick-view?product_id=$product_id";
      $onclick_wishlist_fn = (user_is_loged()) ? (in_array($product_id, $customer_wishlist)) ? "OpenModalWindow(product_already_in_wishlist)" : "AddProductToWishlist('$product_id','$pd_name')" : "OpenModalWindow(loggin_required_for_wishlist)";
?>
      <div class="ajax_block_product col-xs-12 col-sm-6 col-md-4<?=$class_first;?>">
        <div id="product_block_<?=$product_id;?>" class="product-container product-block" itemscope itemtype="https://schema.org/Product">
          <div class="left-block">
            <input type="hidden" name="product_isbn" class="product_isbn" value="<?=$product_isbn;?>">
            <input type="hidden" name="product_price" class="product_price" value="<?=$product_price;?>">
            <input type="hidden" name="pd_price" class="pd_price" value="<?=$pd_price;?>">
            <input type="hidden" name="product_name" class="product_name" value="<?=$pd_name;?>">
            <input type="hidden" name="product_url" class="product_url" value="<?=urldecode($product_link);?>">
            <input type="hidden" name="product_qty" class="product_qty" value="1">
            <input type="hidden" name="product_img" class="product_img" value="<?=$gallery_img_cart;?>">
            <div class="product-image-container image">
              <a class="product_img_link" href="<?=urldecode($product_link);?>" itemprop="url">
                <?php if($stock_status_id == 3 || ($product_subtract == 1 && $product_quantity == 0)) { ?>
                <span class="leo-more-info" data-idproduct="2"><span class="text">Продуктът е изчерпан</span></span>
                <?php } ?>
                <!--<img src="<?=$pd_images_watermark;?>" class="watermark" alt="<?=$pd_name_for_alt;?>" title="<?=$pd_name_for_title;?>" width="170" height="250" >-->
                <img class="replace-2x img-responsive" src="<?=$gallery_img_home_default;?>" <?="$default_img_dimensions $default_img_style";?> alt="<?=$pd_name_for_alt;?>" title="<?=$pd_name_for_title;?>" itemprop="image" />
                <span class="product-additional" data-idproduct="<?=$product_id;?>">
                  <!--<img src="<?=$pd_images_watermark;?>" class="watermark" alt="<?=$pd_name_for_alt;?>" title="<?=$pd_name_for_title;?>" width="170" height="250" >-->
                  <?=$additional_img;?>
                </span>
              </a>
            </div>
            <div class="functional-buttons-detail">
              <div class="action-button  clearfix">
                <div class="wishlist">
                  <a class="btn-tooltip addToWishlist" href="javascript:;" onclick="<?=$onclick_wishlist_fn;?>" data-toggle="tooltip" title="<?=$languages[$current_lang]['title_add_to_wishlist'];?>">
                    <i class="fa fa-heart"></i>
                  </a>
                </div>
                <div class="button-quick-view">
                  <a class="quick-view btn btn-tooltip fancybox fancybox.ajax" href="<?=$quick_view_link;?>" rel="nofollow" title="<?=$languages[$current_lang]['title_quick_view'];?>" >
                    <i class="fa fa-search-plus"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="right-block">
            <div class="product-meta">
              <div itemprop="name" class="name"> <a class="product-name" href="<?=urldecode($product_link);?>" title="<?=$pd_name;?>"> <?=$pd_name;?> </a> </div>
              <div class="comments_note product-rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                <div class="star_content">
                  <?php
                    $product_rating_params = get_product_rating($product_id);

                    $product_rating = $product_rating_params['product_rating'];
                    $ratings_count = $product_rating_params['ratings_count'];
                    $product_rating_imgs = $product_rating_params['rating_imgs'];
                    $text_rate = $languages[$current_lang]['text_rate_vote'];
                    $text_rates = $languages[$current_lang]['text_rate_votes'];
                    $ratings_count_text = ($ratings_count == 1) ? $text_rate : $text_rates;

                    echo "$product_rating_imgs <span class='text'>$ratings_count $ratings_count_text</span>";
                  ?>
                  <meta itemprop="worstRating" content = "0" />
                  <meta itemprop="ratingValue" content = "<?=$product_rating?>" />
                  <meta itemprop="bestRating" content = "5" />
                </div>
                <span class="nb-comments"><span itemprop="reviewCount"><?=$product_viewed?></span> Прегледа</span>
              </div>
<?php
              $current_currency_id = isset($_COOKIE['currency']) ? $_COOKIE['currency'] : "default";
              $current_currency_decimal_place = $_SESSION['currencies'][$current_currency_id]['currency_decimal_place'];
              $current_currency_exchange_rate = $_SESSION['currencies'][$current_currency_id]['currency_exchange_rate'];
              $customer_ip = $_SERVER['REMOTE_ADDR']; //77.238.81.170
              $product_price_formatted = (empty($pd_price)) ? number_format(($product_price*$current_currency_exchange_rate),$current_currency_decimal_place,".",".") : $pd_price;
?>
              <meta itemprop="manufacturer" content="<?=$languages[$current_lang]['merchant'];?>">
              <meta itemprop="category" content="<?=$cd_name;?>" />
              <div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="content_price">
                <input type="hidden" class="basic_price" value="<?php if(!empty($pd_price)) echo $pd_price; else echo $product_price?>" />
                <span itemprop="price" class="price product-price" content="<?=$product_price_formatted?>"> 
                  <span class="currency"><?=$_SESSION['currencies'][$current_currency_id]['currency_symbol_left'];?></span>
                  <?=$product_price_formatted?>
                  <span class="currency"><?=$_SESSION['currencies'][$current_currency_id]['currency_symbol_right'];?></span>
                </span>
                <meta itemprop="priceCurrency" content="BGN" />
                <?php
                  if(!empty($pd_price)) {
                    $pd_percent_reduction = 100-(ceil(($pd_price / $product_price) * 100));
                    echo '<span class="old-price product-price"> '.$product_price.'<span class="currency">&nbsp;лв.</span></span>';
                    echo '<span class="price-percent-reduction"> '.$pd_percent_reduction.'% </span>';
                  }
               
                  if($stock_status_id == 2) {
                    //upon request
                  ?>
                    <span class="upon_request red pull-right">
                      <link itemprop="availability" href="https://schema.org/OutOfStock" /> <?=$languages[$current_lang]['text_upon_request'];?> 
                    </span>
                  <?php
                  }
                  elseif($stock_status_id == 3 || ($product_subtract == 1 && $product_quantity == 0)) {
                    //not in stock
                ?>
                  <span class="out-of-stock pull-right">
                    <link itemprop="availability" href="https://schema.org/OutOfStock" /> <?=$languages[$current_lang]['text_out_in_stock'];?> 
                  </span>
                <?php
                  }
                  else {
                ?>
                  <span class="available-now pull-right hidden">
                    <link itemprop="availability" href="https://schema.org/InStock" /> <?=$languages[$current_lang]['text_in_stock']." ".$product_quantity." бр.";?> 
                  </span> 
                <?php
                  }
                ?>
              </div>
              <div class="functional-buttons clearfix">
                <?php
                  if($stock_status_id != 2 && $stock_status_id != 3) {
                    //not in stock
                    if($product_subtract == 1 && $product_quantity == 0) {
                      
                    }
                    else {
                ?>
                <div class="cart">
                  <span onclick="AddProductToCart('<?=$product_id;?>','<?=$current_language_id;?>')" class="button add_to_cart btn btn-outline" rel="nofollow" title="<?=$languages[$current_lang]['btn_add_to_shopping_cart'];?>">
                    <span><?=$languages[$current_lang]['btn_add_to_shopping_cart'];?></span>
                  </span>
                </div>
                <?php
                    }
                  }
                ?>
              </div>
            </div>
          </div>
        </div>

      </div>
<?php
    $blocks_on_row++;
    } //while($product_row)

    mysqli_free_result($result_products);
  }
}
  
function print_top_sellers_products($count, $category_id = false) {
  
  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  global $cd_pretty_url;
  global $current_page_path_string;
  global $customer_wishlist;

  $where_category = ($category_id) ? " AND `categories`.`category_id` = '$category_id'" : "";
  
  $query_top_sellers = "SELECT `order_details`.`product_id`,count(*) as `orders_count`, SUM(`order_details`.`product_quantity`) as `product_sales`
                          FROM `order_details` 
                         WHERE `order_details`.`product_id` <> '497'
                      GROUP BY `order_details`.`product_id` 
                      ORDER BY `product_sales` DESC, `orders_count` DESC 
                        LIMIT $count";
  //echo $query_top_sellers;
  $result_top_sellers = mysqli_query($db_link, $query_top_sellers);
  if(!$result_top_sellers) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_top_sellers) > 0) {
    
    $blocks_on_row = 1;
        
    while($top_sellers_row = mysqli_fetch_assoc($result_top_sellers)) {
      
      $product_id = $top_sellers_row['product_id'];
      $orders_count = $top_sellers_row['orders_count'];
      $product_sales = $top_sellers_row['product_sales'];
      
      $query_products = "SELECT `products`.`product_id`,`products`.`product_isbn`,`products`.`stock_status_id`,`products`.`product_price`,`products`.`product_quantity`,
                                `products`.`product_subtract`,`products`.`product_viewed`,`product_discount`.`pd_price`,`product_description`.`pd_name`,
                                `product_description`.`pd_description`, `category_descriptions`.`cd_name`
                           FROM `products`
                     INNER JOIN `product_to_category` USING(`product_id`)
                     INNER JOIN `product_description` USING(`product_id`)
                      LEFT JOIN `product_discount` USING(`product_id`)
                     INNER JOIN `categories` ON (`product_to_category`.`category_id` = `categories`.`category_id` AND `categories`.`category_is_active` = '1')
                     INNER JOIN `category_descriptions` ON (`categories`.`category_id` = `category_descriptions`.`category_id`)
                          WHERE `products`.`product_id` = '$product_id' AND `products`.`product_is_active` = '1' $where_category 
                            AND `product_description`.`language_id` = '$current_language_id' AND `products`.`stock_status_id` = '1'
                            AND `product_description`.`pd_is_active` = '1' AND `category_descriptions`.`language_id` = '$current_language_id'
                            AND `category_descriptions`.`cd_is_active` = '1'";
      //echo $query_products."<br>";
      $result_products = mysqli_query($db_link, $query_products);
      if(!$result_products) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_products) > 0) {


        $product_row = mysqli_fetch_assoc($result_products);

        if($blocks_on_row == 4) $blocks_on_row = 1;

        $cd_name = $product_row['cd_name'];
        $pd_name = $product_row['pd_name'];
        $pd_name_for_alt = ($current_lang == "bg") ? "Доставка на цветя $pd_name" : "";
        $pd_name_for_title = ($current_lang == "bg") ? "$pd_name с доставка на цветя от цветарски магазин LaRose" : "";
        $pd_description = stripslashes($product_row['pd_description']);
        $product_quantity = $product_row['product_quantity'];
        $product_subtract = $product_row['product_subtract'];
        $product_isbn = $product_row['product_isbn'];
        $product_viewed = $product_row['product_viewed'];
        $stock_status_id = $product_row['stock_status_id'];

        $pd_images_folder = "/frontstore/images/products/";
        $pd_images_watermark = "/frontstore/images/watermark-170-250.png";
        $default_img = get_product_default_image($product_id);

        $pi_names_array = get_product_images($product_id);
        if((isset($pi_names_array['default']['pi_name']) && !empty($pi_names_array['default']['pi_name']))) {
          $default_img = $pi_names_array['default']['pi_name'];
          $default_img_exploded = explode(".", $default_img);
          $default_img_name = $default_img_exploded[0];
          $default_img_exstension = $default_img_exploded[1];
          $gallery_img_home_default = $pd_images_folder.$default_img_name."_home_default.".$default_img_exstension;
          $gallery_img_cart = $pd_images_folder.$default_img_name."_small_default.".$default_img_exstension;
          $full_path = $_SERVER['DOCUMENT_ROOT'].$gallery_img_home_default;

          $file = $full_path;

          list($width,$height) = getimagesize($file);
          $default_img_dimensions = "width='$width' height='$height'";

          if($width > $height) {
            $default_img_style = "";
          }
          else {
            $default_img_style = "style='height:100%;width:auto;'";
          }
        }
        else $gallery_img_path_large = $pd_images_folder."no_image.jpg";
        $additional_img = "";
        if(isset($pi_names_array['gallery'])) {
          //echo"<pre>";print_r($pi_names_array['gallery']);
          $gallery_img = $pi_names_array['gallery'][0]['pi_name'];
          $gallery_img_exploded = explode(".", $gallery_img);
          $gallery_img_name = $gallery_img_exploded[0];
          $gallery_img_exstension = $gallery_img_exploded[1];
          $gallery_additional_img = $pd_images_folder.$gallery_img_name."_home_default.".$gallery_img_exstension;
          $full_path = $_SERVER['DOCUMENT_ROOT'].$gallery_additional_img;

          $file = $full_path;

          list($width,$height) = getimagesize($file);

          if($width > $height) {
            $img_style = "";
          }
          else {
            $img_style = "style='height:100%;width:auto;'";
          }

          $additional_img = "<img src='$gallery_additional_img' width='$width' height='$height' title='$pd_name_for_title' alt='$pd_name_for_alt' class='img-responsive' $img_style>";
        }

        $product_price = $product_row['product_price'];
        $pd_price = (!empty($product_row['pd_price'])) ? $product_row['pd_price'] : "";
        $price_discount_class = (!empty($pd_price)) ? " class='line_through'" : "";
        $class_first = ($blocks_on_row == 1) ? " first_item" : "";
        $pd_name_for_link = beautify_name_for_url($pd_name);
        $cyr_url = cyrialize_url($pd_name_for_link);
        $product_link = "/$current_lang/pid-$product_id/$cyr_url";
        $quick_view_link = "/$current_lang/product-quick-view?product_id=$product_id";
        $onclick_wishlist_fn = (user_is_loged()) ? (in_array($product_id, $customer_wishlist)) ? "OpenModalWindow(product_already_in_wishlist)" : "AddProductToWishlist('$product_id','$pd_name')" : "OpenModalWindow(loggin_required_for_wishlist)";
?>
        <div class="ajax_block_product col-xs-12 col-sm-6 col-md-4<?=$class_first;?>">
          <div id="product_block_<?=$product_id;?>" class="product-container product-block" itemscope itemtype="https://schema.org/Product">
            <div class="left-block">
              <input type="hidden" name="product_isbn" class="product_isbn" value="<?=$product_isbn;?>">
              <input type="hidden" name="product_price" class="product_price" value="<?=$product_price;?>">
              <input type="hidden" name="pd_price" class="pd_price" value="<?=$pd_price;?>">
              <input type="hidden" name="product_name" class="product_name" value="<?=$pd_name;?>">
              <input type="hidden" name="product_url" class="product_url" value="<?=urldecode($product_link);?>">
              <input type="hidden" name="product_qty" class="product_qty" value="1">
              <input type="hidden" name="product_img" class="product_img" value="<?=$gallery_img_cart;?>">
              <div class="product-image-container image">
                <a class="product_img_link" href="<?=urldecode($product_link);?>" itemprop="url">
                  <?php if($stock_status_id == 3 || ($product_subtract == 1 && $product_quantity == 0)) { ?>
                  <span class="leo-more-info" data-idproduct="2"><span class="text">Продуктът е изчерпан</span></span>
                  <?php } ?>
                  <img class="replace-2x img-responsive" src="<?=$gallery_img_home_default;?>" <?="$default_img_dimensions $default_img_style";?> alt="<?=$pd_name_for_alt;?>" title="<?=$pd_name_for_title;?>" itemprop="image" />
                  <span class="product-additional" data-idproduct="<?=$product_id;?>">
                    <!--<img src="<?=$pd_images_watermark;?>" class="watermark" alt="<?=$pd_name_for_alt;?>" title="<?=$pd_name_for_title;?>" width="170" height="250" >-->
                    <?=$additional_img;?>
                  </span>
                </a>
              </div>
              <div class="functional-buttons-detail">
                <div class="action-button  clearfix">
                  <div class="wishlist">
                    <a class="btn-tooltip addToWishlist" href="javascript:;" onclick="<?=$onclick_wishlist_fn;?>" data-toggle="tooltip" title="<?=$languages[$current_lang]['title_add_to_wishlist'];?>">
                      <i class="fa fa-heart"></i>
                    </a>
                  </div>
                  <div class="button-quick-view">
                    <a class="quick-view btn btn-tooltip fancybox fancybox.ajax" href="<?=$quick_view_link;?>" rel="nofollow" title="<?=$languages[$current_lang]['title_quick_view'];?>" >
                      <i class="fa fa-search-plus"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="right-block">
              <div class="product-meta">
                <div itemprop="name" class="name"> <a class="product-name" href="<?=urldecode($product_link);?>" title="<?=$pd_name;?>"> <?=$pd_name;?> </a> </div>
                <div class="comments_note product-rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                  <div class="star_content">
                    <?php
                      $product_rating_params = get_product_rating($product_id);

                      $product_rating = $product_rating_params['product_rating'];
                      $ratings_count = $product_rating_params['ratings_count'];
                      $product_rating_imgs = $product_rating_params['rating_imgs'];
                      $text_rate = $languages[$current_lang]['text_rate_vote'];
                      $text_rates = $languages[$current_lang]['text_rate_votes'];
                      $ratings_count_text = ($ratings_count == 1) ? $text_rate : $text_rates;

                      echo "$product_rating_imgs <span class='text'>$ratings_count $ratings_count_text</span>";
                    ?>
                    <meta itemprop="worstRating" content = "0" />
                    <meta itemprop="ratingValue" content = "<?=$product_rating?>" />
                    <meta itemprop="bestRating" content = "5" />
                  </div>
                  <span class="nb-comments"><span itemprop="reviewCount"><?=$product_viewed?></span> Прегледа</span>
                </div>
                <meta itemprop="manufacturer" content="<?=$languages[$current_lang]['merchant'];?>">
                <meta itemprop="category" content="<?=$cd_name;?>" />
                <div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="content_price">
                  <input type="hidden" class="basic_price" value="<?php if(!empty($pd_price)) echo $pd_price; else echo $product_price?>" />
                  <span itemprop="price" class="price product-price"> <?php if(!empty($pd_price)) echo $pd_price; else echo $product_price?><span class="currency">&nbsp;лв.</span> </span>
                  <meta itemprop="priceCurrency" content="BGN" />
                  <?php
                    if(!empty($pd_price)) {
                      $pd_percent_reduction = 100-(ceil(($pd_price / $product_price) * 100));
                      echo '<span class="old-price product-price"> '.$product_price.'<span class="currency">&nbsp;лв.</span></span>';
                      echo '<span class="price-percent-reduction"> '.$pd_percent_reduction.'% </span>';
                    }
                    
                    if($stock_status_id == 2) {
                    //upon request
                  ?>
                    <span class="upon_request red pull-right">
                      <link itemprop="availability" href="https://schema.org/OutOfStock" /> <?=$languages[$current_lang]['text_upon_request'];?> 
                    </span>
                  <?php
                    }
                    elseif($stock_status_id == 3 || ($product_subtract == 1 && $product_quantity == 0)) {
                      //not in stock
                  ?>
                    <span class="out-of-stock pull-right">
                      <link itemprop="availability" href="https://schema.org/OutOfStock" /> <?=$languages[$current_lang]['text_out_in_stock'];?> 
                    </span>
                  <?php
                    }
                    else {
                  ?>
                    <span class="available-now pull-right hidden">
                      <link itemprop="availability" href="https://schema.org/InStock" /> <?=$languages[$current_lang]['text_in_stock']." ".$product_quantity." бр.";?> 
                    </span> 
                  <?php
                    }
                  ?>
                </div>
                <div class="functional-buttons clearfix">
                  <?php
                    if($stock_status_id != 2 && $stock_status_id != 3) {
                    //not in stock
                    if($product_subtract == 1 && $product_quantity == 0) {
                      
                    }
                    else {
                ?>
                <div class="cart">
                  <span onclick="AddProductToCart('<?=$product_id;?>','<?=$current_language_id;?>')" class="button add_to_cart btn btn-outline" rel="nofollow" title="<?=$languages[$current_lang]['btn_add_to_shopping_cart'];?>">
                    <span><?=$languages[$current_lang]['btn_add_to_shopping_cart'];?></span>
                  </span>
                </div>
                <?php
                    }
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>

        </div>
<?php

        mysqli_free_result($result_products);
      }
       
      $blocks_on_row++;
    } //while($top_sellers_row)
  } //if(mysqli_num_rows($result_top_sellers) > 0)
}

function print_most_viewed_products($count, $category_id = false) {

  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  global $cd_pretty_url;
  global $current_page_path_string;
  global $customer_wishlist;

  $where_category = ($category_id) ? " AND `categories`.`category_id` = '$category_id'" : "";

  $query_products = "SELECT `products`.`product_id`,`products`.`product_isbn`,`products`.`stock_status_id`,`products`.`product_price`,`products`.`product_quantity`,
                            `products`.`product_subtract`,`products`.`product_viewed`,`product_discount`.`pd_price`,`product_description`.`pd_name`,
                            `product_description`.`pd_description`, `category_descriptions`.`cd_name`
                      FROM `products`
                      INNER JOIN `product_to_category` USING(`product_id`)
                      INNER JOIN `product_description` USING(`product_id`)
                      LEFT JOIN `product_discount` USING(`product_id`)
                      INNER JOIN `categories` ON (`product_to_category`.`category_id` = `categories`.`category_id` AND `categories`.`category_is_active` = '1')
                      INNER JOIN `category_descriptions` ON (`categories`.`category_id` = `category_descriptions`.`category_id`)
                      WHERE `products`.`product_most_viewed` = '1' AND `products`.`product_is_active` = '1' $where_category 
                        AND `product_description`.`language_id` = '$current_language_id' AND `products`.`stock_status_id` = '1'
                        AND `product_description`.`pd_is_active` = '1' AND `category_descriptions`.`language_id` = '$current_language_id'
                        AND `category_descriptions`.`cd_is_active` = '1' AND `products`.`product_id` <> '497'
                      GROUP BY `products`.`product_id`
                      ORDER BY `products`.`product_viewed` DESC 
                      LIMIT $count";
  /*
   * new query, there is a cron job using the above query and inserting once a day newest products
   */
  $query_products = "SELECT `most_viewed_products`.* FROM `most_viewed_products` WHERE `language_id` = '$current_language_id'";
  //echo $query_products."<br>"; 
  $result_products = mysqli_query($db_link, $query_products);
  if(!$result_products) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_products) > 0) {

    $blocks_on_row = 1;

    while($product_row = mysqli_fetch_assoc($result_products)) {

      if($blocks_on_row == 4) $blocks_on_row = 1;

      $cd_name = $product_row['cd_name'];
      $product_id = $product_row['product_id'];
      $pd_name = $product_row['pd_name'];
      $pd_name_for_alt = ($current_lang == "bg") ? "Доставка на цветя $pd_name" : "";
      $pd_name_for_title = ($current_lang == "bg") ? "$pd_name с доставка на цветя от цветарски магазин LaRose" : "";
      $pd_description = stripslashes($product_row['pd_description']);
      $product_quantity = $product_row['product_quantity'];
      $product_subtract = $product_row['product_subtract'];
      $product_isbn = $product_row['product_isbn'];
      $product_viewed = $product_row['product_viewed'];
      $stock_status_id = $product_row['stock_status_id'];

      $pd_images_folder = "/frontstore/images/products/";
      $pd_images_watermark = "/frontstore/images/watermark-170-250.png";
      $default_img = get_product_default_image($product_id);

      $pi_names_array = get_product_images($product_id);
      if((isset($pi_names_array['default']['pi_name']) && !empty($pi_names_array['default']['pi_name']))) {
        $default_img = $pi_names_array['default']['pi_name'];
        $default_img_exploded = explode(".", $default_img);
        $default_img_name = $default_img_exploded[0];
        $default_img_exstension = $default_img_exploded[1];
        $gallery_img_home_default = $pd_images_folder.$default_img_name."_home_default.".$default_img_exstension;
        $gallery_img_cart = $pd_images_folder.$default_img_name."_small_default.".$default_img_exstension;
        $full_path = $_SERVER['DOCUMENT_ROOT'].$gallery_img_home_default;

        $file = $full_path;

        list($width,$height) = getimagesize($file);
        $default_img_dimensions = "width='$width' height='$height'";
                
        if($width > $height) {
          $default_img_style = "";
        }
        else {
          $default_img_style = "style='height:100%;width:auto;'";
        }
      }
      else $gallery_img_path_large = $pd_images_folder."no_image.jpg";
      $additional_img = "";
      if(isset($pi_names_array['gallery'])) {
        //echo"<pre>";print_r($pi_names_array['gallery']);
        $gallery_img = $pi_names_array['gallery'][0]['pi_name'];
        $gallery_img_exploded = explode(".", $gallery_img);
        $gallery_img_name = $gallery_img_exploded[0];
        $gallery_img_exstension = $gallery_img_exploded[1];
        $gallery_additional_img = $pd_images_folder.$gallery_img_name."_home_default.".$gallery_img_exstension;
        $full_path = $_SERVER['DOCUMENT_ROOT'].$gallery_additional_img;

        $file = $full_path;

        list($width,$height) = getimagesize($file);

        if($width > $height) {
          $img_style = "";
        }
        else {
          $img_style = "style='height:100%;width:auto;'";
        }

        $additional_img = "<img src='$gallery_additional_img' width='$width' height='$height' title='$pd_name_for_title' alt='$pd_name_for_alt' class='img-responsive' $img_style>";
      }

      $product_price = $product_row['product_price'];
      $pd_price = (!empty($product_row['pd_price']) && $product_row['pd_price'] != 0.00) ? $product_row['pd_price'] : "";
      $price_discount_class = (!empty($pd_price)) ? " class='line_through'" : "";
      $class_first = ($blocks_on_row == 1) ? " first_item" : "";
      $pd_name_for_link = beautify_name_for_url($pd_name);
      $cyr_url = cyrialize_url($pd_name_for_link);
      $product_link = "/$current_lang/pid-$product_id/$cyr_url";
      $quick_view_link = "/$current_lang/product-quick-view?product_id=$product_id";
      $onclick_wishlist_fn = (user_is_loged()) ? (in_array($product_id, $customer_wishlist)) ? "OpenModalWindow(product_already_in_wishlist)" : "AddProductToWishlist('$product_id','$pd_name')" : "OpenModalWindow(loggin_required_for_wishlist)";
?>
      <div class="ajax_block_product col-xs-12 col-sm-6 col-md-4<?=$class_first;?>">
        <div id="product_block_<?=$product_id;?>" class="product-container product-block" itemscope itemtype="https://schema.org/Product">
          <div class="left-block">
            <input type="hidden" name="product_isbn" class="product_isbn" value="<?=$product_isbn;?>">
            <input type="hidden" name="product_price" class="product_price" value="<?=$product_price;?>">
            <input type="hidden" name="pd_price" class="pd_price" value="<?=$pd_price;?>">
            <input type="hidden" name="product_name" class="product_name" value="<?=$pd_name;?>">
            <input type="hidden" name="product_url" class="product_url" value="<?=urldecode($product_link);?>">
            <input type="hidden" name="product_qty" class="product_qty" value="1">
            <input type="hidden" name="product_img" class="product_img" value="<?=$gallery_img_cart;?>">
            <div class="product-image-container image">
              <a class="product_img_link" href="<?=urldecode($product_link);?>" itemprop="url">
                <?php if($stock_status_id == 3 || ($product_subtract == 1 && $product_quantity == 0)) { ?>
                <span class="leo-more-info" data-idproduct="2"><span class="text">Продуктът е изчерпан</span></span>
                <?php } ?>
                <!--<img src="<?=$pd_images_watermark;?>" class="watermark" alt="<?=$pd_name_for_alt;?>" title="<?=$pd_name_for_title;?>" width="170" height="250" >-->
                <img class="replace-2x img-responsive" src="<?=$gallery_img_home_default;?>" <?="$default_img_dimensions $default_img_style";?> alt="<?=$pd_name_for_alt;?>" title="<?=$pd_name_for_title;?>" itemprop="image" />
                <span class="product-additional" data-idproduct="<?=$product_id;?>">
                  <!--<img src="<?=$pd_images_watermark;?>" class="watermark" alt="<?=$pd_name_for_alt;?>" title="<?=$pd_name_for_title;?>" width="170" height="250" >-->
                  <?=$additional_img;?>
                </span>
              </a>
            </div>
            <div class="functional-buttons-detail">
              <div class="action-button  clearfix">
                <div class="wishlist">
                  <a class="btn-tooltip addToWishlist" href="javascript:;" onclick="<?=$onclick_wishlist_fn;?>" data-toggle="tooltip" title="<?=$languages[$current_lang]['title_add_to_wishlist'];?>">
                    <i class="fa fa-heart"></i>
                  </a>
                </div>
                <div class="button-quick-view">
                  <a class="quick-view btn btn-tooltip fancybox fancybox.ajax" href="<?=$quick_view_link;?>" rel="nofollow" title="<?=$languages[$current_lang]['title_quick_view'];?>" >
                    <i class="fa fa-search-plus"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="right-block">
            <div class="product-meta">
              <div itemprop="name" class="name"> <a class="product-name" href="<?=urldecode($product_link);?>" title="<?=$pd_name;?>"> <?=$pd_name;?> </a> </div>
              <div class="comments_note product-rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                <div class="star_content">
                  <?php
                    $product_rating_params = get_product_rating($product_id);

                    $product_rating = $product_rating_params['product_rating'];
                    $ratings_count = $product_rating_params['ratings_count'];
                    $product_rating_imgs = $product_rating_params['rating_imgs'];
                    $text_rate = $languages[$current_lang]['text_rate_vote'];
                    $text_rates = $languages[$current_lang]['text_rate_votes'];
                    $ratings_count_text = ($ratings_count == 1) ? $text_rate : $text_rates;

                    echo "$product_rating_imgs <span class='text'>$ratings_count $ratings_count_text</span>";
                  ?>
                  <meta itemprop="worstRating" content = "0" />
                  <meta itemprop="ratingValue" content = "<?=$product_rating?>" />
                  <meta itemprop="bestRating" content = "5" />
                </div>
                <span class="nb-comments"><span itemprop="reviewCount"><?=$product_viewed?></span> Прегледа</span>
              </div>
              <meta itemprop="manufacturer" content="<?=$languages[$current_lang]['merchant'];?>">
              <meta itemprop="category" content="<?=$cd_name;?>" />
              <div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="content_price">
                <input type="hidden" class="basic_price" value="<?php if(!empty($pd_price)) echo $pd_price; else echo $product_price?>" />
                <span itemprop="price" class="price product-price"> <?php if(!empty($pd_price)) echo $pd_price; else echo $product_price?><span class="currency">&nbsp;лв.</span> </span>
                <meta itemprop="priceCurrency" content="BGN" />
                <?php
                  if(!empty($pd_price)) {
                    $pd_percent_reduction = 100-(ceil(($pd_price / $product_price) * 100));
                    echo '<span class="old-price product-price"> '.$product_price.'<span class="currency">&nbsp;лв.</span></span>';
                    echo '<span class="price-percent-reduction"> '.$pd_percent_reduction.'% </span>';
                  }
               
                  if($stock_status_id == 2) {
                    //upon request
                  ?>
                    <span class="upon_request red pull-right">
                      <link itemprop="availability" href="https://schema.org/OutOfStock" /> <?=$languages[$current_lang]['text_upon_request'];?> 
                    </span>
                  <?php
                  }
                  elseif($stock_status_id == 3 || ($product_subtract == 1 && $product_quantity == 0)) {
                    //not in stock
                ?>
                  <span class="out-of-stock pull-right">
                    <link itemprop="availability" href="https://schema.org/OutOfStock" /> <?=$languages[$current_lang]['text_out_in_stock'];?> 
                  </span>
                <?php
                  }
                  else {
                ?>
                  <span class="available-now pull-right hidden">
                    <link itemprop="availability" href="https://schema.org/InStock" /> <?=$languages[$current_lang]['text_in_stock']." ".$product_quantity." бр.";?> 
                  </span> 
                <?php
                  }
                ?>
              </div>
              <div class="functional-buttons clearfix">
                <?php
                  if($stock_status_id != 2 && $stock_status_id != 3) {
                    //not in stock
                    if($product_subtract == 1 && $product_quantity == 0) {
                      
                    }
                    else {
                ?>
                <div class="cart">
                  <span onclick="AddProductToCart('<?=$product_id;?>','<?=$current_language_id;?>')" class="button add_to_cart btn btn-outline" rel="nofollow" title="<?=$languages[$current_lang]['btn_add_to_shopping_cart'];?>">
                    <span><?=$languages[$current_lang]['btn_add_to_shopping_cart'];?></span>
                  </span>
                </div>
                <?php
                    }
                  }
                ?>
              </div>
            </div>
          </div>
        </div>

      </div>
<?php
    $blocks_on_row++;
    } //while($product_row)

    mysqli_free_result($result_products);
  }
}
  
function print_random_products($count = false,$category_id = false) {

  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  global $cd_pretty_url;
  global $current_page_path_string;

  $where_category = ($category_id) ? " AND `product_to_category`.`category_id` = '$category_id'" : "";

  $query_random_products = "SELECT `products`.`product_id`,`products`.`product_isbn`,`products`.`product_price`,`products`.`product_viewed`,`product_discount`.`pd_price`,
                                   `products`.`product_quantity`,`products`.`product_subtract`,`product_description`.`pd_name`,`product_description`.`pd_description`,
                                   `category_descriptions`.`cd_name`
                              FROM `products`
                        INNER JOIN `product_to_category` USING(`product_id`)
                        INNER JOIN `product_description` USING(`product_id`)
                         LEFT JOIN `product_discount` USING(`product_id`)
                        INNER JOIN `categories` ON (`product_to_category`.`category_id` = `categories`.`category_id` AND `categories`.`category_is_active` = '1')
                        INNER JOIN `category_descriptions` ON (`categories`.`category_id` = `category_descriptions`.`category_id` AND `category_descriptions`.`cd_is_active` = '1')
                             WHERE `products`.`product_id` IN(".get_random_product_ids_list($count).") $where_category  AND `products`.`product_id` <> '497'
                               AND `product_description`.`language_id` = '$current_language_id' AND `products`.`stock_status_id` = '1'
                               AND `category_descriptions`.`language_id` = '$current_language_id'
                          GROUP BY `products`.`product_id`";
  //if($current_language_id == 2) echo $query_random_products."<br>";
  $result_products = mysqli_query($db_link, $query_random_products);
  if(!$result_products) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_products) > 0) {

    $blocks_on_row = 1;

    while($product_row = mysqli_fetch_assoc($result_products)) {

      if($blocks_on_row == 5) $blocks_on_row = 1;

      $cd_name = $product_row['cd_name'];
      $product_id = $product_row['product_id'];
      $pd_name = $product_row['pd_name'];
      $pd_name_for_alt = ($current_lang == "bg") ? "Доставка на цветя $pd_name" : "";
      $pd_name_for_title = ($current_lang == "bg") ? "$pd_name с доставка на цветя от цветарски магазин LaRose" : "";
      $pd_description = stripslashes($product_row['pd_description']);
      $product_quantity = $product_row['product_quantity'];
      $product_subtract = $product_row['product_subtract'];
      $product_isbn = $product_row['product_isbn'];
      $product_viewed = $product_row['product_viewed'];

      $pd_images_folder = "/frontstore/images/products/";
      $pd_images_watermark = "/frontstore/images/watermark-170-250.png";
      $default_img = get_product_default_image($product_id);

      $pi_names_array = get_product_images($product_id);
      if((isset($pi_names_array['default']['pi_name']) && !empty($pi_names_array['default']['pi_name']))) {
        $default_img = $pi_names_array['default']['pi_name'];
        $default_img_exploded = explode(".", $default_img);
        $default_img_name = $default_img_exploded[0];
        $default_img_exstension = $default_img_exploded[1];
        $gallery_img_home_default = $pd_images_folder.$default_img_name."_home_default.".$default_img_exstension;
        $gallery_img_cart = $pd_images_folder.$default_img_name."_small_default.".$default_img_exstension;
        $full_path = $_SERVER['DOCUMENT_ROOT'].$gallery_img_home_default;

        $file = $full_path;

        list($width,$height) = @getimagesize($file);
        $default_img_dimensions = "width='$width' height='$height'";

        if($width > $height) {
          $default_img_style = "";
        }
        else {
          $default_img_style = "style='height:100%;width:auto;'";
        }
      }
      else $gallery_img_path_large = $pd_images_folder."no_image.jpg";

      $product_price = $product_row['product_price'];
      $pd_price = (!empty($product_row['pd_price'])) ? $product_row['pd_price'] : "";
      $price_discount_class = (!empty($pd_price)) ? " class='line_through'" : "";
      $class_first = ($blocks_on_row == 1) ? " first_item" : "";
      $pd_name_for_link = beautify_name_for_url($pd_name);
      $cyr_url = cyrialize_url($pd_name_for_link);
      $product_link = "/$current_lang/pid-$product_id/$cyr_url";
?>
      <li class="clearfix media">
        <div class="product-block">

          <div class="product-container media" itemscope itemtype="https://schema.org/Product">
            <a class="products-block-image img pull-left" href="<?=urldecode($product_link);?>">
              <img class="replace-2x img-responsive" src="<?=$gallery_img_home_default?>" <?=$default_img_dimensions;?> title="<?=$pd_name_for_title;?>" alt="<?=$pd_name_for_alt;?>" />
            </a>

            <div class="media-body">
              <div class="product-content">

                <div class="comments_note product-rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">	
                  <div class="star_content">
                    <?php
                      $product_rating_params = get_product_rating($product_id);

                      $product_rating = $product_rating_params['product_rating'];
                      $ratings_count = $product_rating_params['ratings_count'];
                      $product_rating_imgs = $product_rating_params['rating_imgs'];
                      $text_rate = $languages[$current_lang]['text_rate_vote'];
                      $text_rates = $languages[$current_lang]['text_rate_votes'];
                      $ratings_count_text = "<span class='text'>$ratings_count ";
                      $ratings_count_text .= ($ratings_count == 1) ? "$text_rate" : "$text_rates";
                      $ratings_count_text .= "</span>";

                      echo "$product_rating_imgs";
                    ?>
                    <meta itemprop="worstRating" content = "0" />
                    <meta itemprop="ratingValue" content = "<?=$product_rating?>" />
                    <meta itemprop="bestRating" content = "5" />
                  </div>
                  <span class="nb-comments"><span itemprop="reviewCount"><?=$product_viewed?></span> Прегледа</span>
                </div>

                <div itemprop="name" class="name media-heading" style="margin-bottom: 6px;">
                  <a class="product-name" href="<?=urldecode($product_link);?>" title="<?=$pd_name;?>">
                    <?=$pd_name;?>
                  </a>
                </div>
                <meta itemprop="manufacturer" content="<?=$languages[$current_lang]['merchant'];?>">
                <meta itemprop="category" content="<?=$cd_name;?>" />
                <div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="content_price">
                  <input type="hidden" class="basic_price" value="<?=$product_price?>" />
                  <span itemprop="price" class="price product-price"><?=$product_price;?><span class="currency">&nbsp;лв.</span></span>
                  <meta itemprop="priceCurrency" content="BGN" />
                  <link itemprop="availability" href="https://schema.org/InStock" />
                </div>
              </div>
            </div>
          </div>
        </div> 
      </li>
<?php
    $blocks_on_row++;
    } //while($product_row)

    mysqli_free_result($result_products);
  }
}
  
function print_similar_products($product_price = false, $category_id = false) {

  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  global $cd_pretty_url;
  global $current_page_path_string;

  $min_price = $product_price-20;
  $max_price = $product_price+20;
  $count = 2;
  
  $query_products = "SELECT DISTINCT(`products`.`product_id`)
                       FROM `products`
                 INNER JOIN `product_to_category` USING(`product_id`)
                 INNER JOIN `product_description` USING(`product_id`)
                  LEFT JOIN `product_discount` USING(`product_id`)
                 INNER JOIN `category_descriptions` ON (`product_to_category`.`category_id` = `category_descriptions`.`category_id` AND `category_descriptions`.`cd_is_active` = '1')
                      WHERE `products`.`product_is_active` = '1' AND `products`.`stock_status_id` = '1'
                        AND `products`.`product_price` BETWEEN $min_price AND $max_price
                        AND `product_to_category`.`category_id` = '$category_id'
                        AND `product_description`.`language_id` = '$current_language_id' AND `product_description`.`pd_is_active` = '1'
                        AND `category_descriptions`.`language_id` = '$current_language_id'
                   ORDER BY RAND()
                      LIMIT $count";
  $result_products = mysqli_query($db_link, $query_products);
  if(!$result_products) echo mysqli_error($db_link);
  $ids = [0 => 0];
  if(mysqli_num_rows($result_products) > 0) {
    while($product_row = mysqli_fetch_assoc($result_products)) {
      $ids[] = $product_row['product_id'];
    }
  }
  mysqli_free_result($result_products);
  
  //print_array_for_debug($ids);
  $query_products = "SELECT `products`.`product_id`,`products`.`stock_status_id`,`products`.`product_isbn`,`products`.`product_quantity`,`products`.`product_price`,
                            `products`.`product_subtract`,`products`.`product_viewed`,`product_discount`.`pd_price`,`product_description`.`pd_name`,
                            `product_description`.`pd_description`,`category_descriptions`.`cd_name`
                       FROM `products`
                 INNER JOIN `product_to_category` USING(`product_id`)
                 INNER JOIN `product_description` USING(`product_id`)
                  LEFT JOIN `product_discount` USING(`product_id`)
                 INNER JOIN `category_descriptions` ON (`product_to_category`.`category_id` = `category_descriptions`.`category_id` AND `category_descriptions`.`cd_is_active` = '1')
                      WHERE `products`.`product_id` IN(". implode(",", $ids).") AND `product_description`.`language_id` = '$current_language_id'
                   GROUP BY `products`.`product_id`
                       LIMIT $count";
  //echo $query_products."<br>";
  $result_products = mysqli_query($db_link, $query_products);
  if(!$result_products) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_products) > 0) {
?>
    <div class="products_block exclusive leomanagerwidgets block nopadding similar_products mb-20">
      <div class="page-subheading"> Подобни продукти </div>
<?php
    while($product_row = mysqli_fetch_assoc($result_products)) {

      $cd_name = $product_row['cd_name'];
      $product_id = $product_row['product_id'];
      $pd_name = $product_row['pd_name'];
      $pd_name_for_alt = ($current_lang == "bg") ? "Доставка на цветя $pd_name" : "";
      $pd_name_for_title = ($current_lang == "bg") ? "$pd_name с доставка на цветя от цветарски магазин LaRose" : "";
      $pd_description = stripslashes($product_row['pd_description']);
      if(mb_strlen($product_row['pd_description']) > 220) {
        $pd_description = mb_strimwidth(stripslashes($product_row['pd_description']), 0, 220,'', 'utf-8')." ...";
      }
      $product_quantity = $product_row['product_quantity'];
      $product_subtract = $product_row['product_subtract'];
      $product_isbn = $product_row['product_isbn'];
      $product_viewed = $product_row['product_viewed'];

      $pd_images_folder = "/frontstore/images/products/";
      $pd_images_watermark = "/frontstore/images/watermark-170-250.png";
      $default_img = get_product_default_image($product_id);

      $pi_names_array = get_product_images($product_id);
      if((isset($pi_names_array['default']['pi_name']) && !empty($pi_names_array['default']['pi_name']))) {
        $default_img = $pi_names_array['default']['pi_name'];
        $default_img_exploded = explode(".", $default_img);
        $default_img_name = $default_img_exploded[0];
        $default_img_exstension = $default_img_exploded[1];
        $gallery_img_home_default = $pd_images_folder.$default_img_name."_home_default.".$default_img_exstension;
        $gallery_img_cart = $pd_images_folder.$default_img_name."_small_default.".$default_img_exstension;
        $full_path = $_SERVER['DOCUMENT_ROOT'].$gallery_img_home_default;

        $file = $full_path;

        list($width,$height) = @getimagesize($file);
        $default_img_dimensions = "width='$width' height='$height'";

        if($width > $height) {
          $default_img_style = "";
        }
        else {
          $default_img_style = "style='height:100%;width:auto;'";
        }
      }
      else $gallery_img_path_large = $pd_images_folder."no_image.jpg";

      $product_price = $product_row['product_price'];
      $pd_price = (!empty($product_row['pd_price'])) ? $product_row['pd_price'] : "";
      $price_discount_class = (!empty($pd_price)) ? " class='line_through'" : "";
      $pd_name_for_link = beautify_name_for_url($pd_name);
      $cyr_url = cyrialize_url($pd_name_for_link);
      $product_link = "/$current_lang/pid-$product_id/$cyr_url";
?>
      <div class="product-block col-xs-12 col-sm-12 col-md-6 col-lg-6">

        <div class="product-container media" itemscope itemtype="https://schema.org/Product">
          <a class="products-block-image img pull-left" href="<?=urldecode($product_link);?>">
            <img class="replace-2x img-responsive" src="<?=$gallery_img_home_default?>" <?=$default_img_dimensions;?> title="<?=$pd_name_for_title;?>" alt="<?=$pd_name_for_alt;?>" />
          </a>

          <div class="media-body">
            <div class="product-content">
              
              <div itemprop="name" class="name media-heading" style="margin-bottom: 6px;">
                <a class="product-name mb-10" href="<?=urldecode($product_link);?>" title="<?=$pd_name;?>">
                  <?=$pd_name;?>
                </a>
                <p><?=$pd_description;?></p>
              </div>
              <meta itemprop="manufacturer" content="<?=$languages[$current_lang]['merchant'];?>">
              <meta itemprop="category" content="<?=$cd_name;?>" />
              <div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="content_price">
                <input type="hidden" class="basic_price" value="<?=$product_price?>" />
                <span itemprop="price" class="price product-price"><?=$product_price;?><span class="currency">&nbsp;лв.</span></span>
                <meta itemprop="priceCurrency" content="BGN" />
                <link itemprop="availability" href="https://schema.org/InStock" />
              </div>
              
              <div class="comments_note product-rating hidden" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">	
                <div class="star_content">
                  <?php
                    $product_rating_params = get_product_rating($product_id);

                    $product_rating = $product_rating_params['product_rating'];
                    $ratings_count = $product_rating_params['ratings_count'];
                    $product_rating_imgs = $product_rating_params['rating_imgs'];
                    $text_rate = $languages[$current_lang]['text_rate_vote'];
                    $text_rates = $languages[$current_lang]['text_rate_votes'];
                    $ratings_count_text = "<span class='text'>$ratings_count ";
                    $ratings_count_text .= ($ratings_count == 1) ? "$text_rate" : "$text_rates";
                    $ratings_count_text .= "</span>";

                    echo "$product_rating_imgs";
                  ?>
                  <meta itemprop="worstRating" content = "0" />
                  <meta itemprop="ratingValue" content = "<?=$product_rating?>" />
                  <meta itemprop="bestRating" content = "5" />
                </div>
                <span class="nb-comments"><span itemprop="reviewCount"><?=$product_viewed?></span> Прегледа</span>
              </div>
              
            </div>
          </div>
        </div>
      </div>
<?php
    } //while($product_row)
    mysqli_free_result($result_products);
?>
      <div class="clearfix"></div>
    </div>
<?php
  }
}
  
function list_products_by_category($category_id,$offset,$current_cat_href,$cd_pretty_url,$products_count = false) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $price_min;
  global $price_max;
  global $min_product_price;
  global $max_product_price;
  global $option;
  global $customer_wishlist;
  global $current_category_parent_id;
  
  if($current_category_parent_id === "all" || $category_id == 17) {
    /*
     * if $current_category_parent_id == 0 that means the current selected category
     * is a parent category, so we have to take all children's categories and take
     * all the products they have
     */
    $category_ids = array();
    $query_children = "SELECT `categories`.`category_id` 
                         FROM `categories` 
                   INNER JOIN `category_descriptions` USING(`category_id`)
                        WHERE `categories`.`category_parent_id` = '$category_id' 
                          AND `categories`.`category_is_active` = '1' AND `category_descriptions`.`cd_is_active` = '1'
                          AND `category_descriptions`.`language_id` = '$current_language_id'";
    $result_children = mysqli_query($db_link, $query_children);
    if(!$result_children) echo mysqli_error($db_link);
    $category_count = mysqli_num_rows($result_children);
    if($category_count > 0) {
 
      while($children_row = mysqli_fetch_assoc($result_children)) {
        $category_ids[] = $children_row['category_id'];
      }
    }
  }

//  $category_products_min_max_price = get_category_products_min_max_price($category_id);
//  $min_product_price = $category_products_min_max_price['min_product_price'];
//  $max_product_price = $category_products_min_max_price['max_product_price'];

  $order_by = "";
  if(!empty($option)) {
    switch($option) {
      case "date:desc":
          $order_by = " `products`.`product_date_added` DESC";
          break;
      case "price:asc":
          $order_by = " `products`.`product_price` ASC";
          break;
      case "price:desc":
          $order_by = " `products`.`product_price` DESC";
          break;
      case "name:asc":
          $order_by = " `product_description`.`pd_name` ASC";
          break;
      case "name:desc":
          $order_by = " `product_description`.`pd_name` DESC";
          break;

      default:
          break;
    }
  }

  if(empty($order_by)) {
    if($current_category_parent_id != "all") $order_by = " `product_to_category`.`product_sort_order` ASC"; // by default
    else $order_by = " `products`.`product_date_added` DESC"; // by default
  }

  $page_offset = 9;
  $page_path_string = (isset($_GET['page'])) ? mysqli_real_escape_string($db_link,strip_tags($_GET['page'])) : "";
  $page_path_array = explode("/", $page_path_string);
  if(strstr($page_path_string, "/page/") && strstr($page_path_string, "cid")) {
    $curr_page_num = intval(array_pop($page_path_array));
    $offset = ($curr_page_num-1)*$page_offset;
  }
  $query_limit = "";

  if((!isset($price_min) || $price_min == $min_product_price) && (!isset($price_max) || $price_max == $max_product_price) && empty($option)) {
    $query_limit = "LIMIT $offset,$page_offset";
  }
  
  $where_category = (isset($category_ids) && !empty($category_ids)) ? "`product_to_category`.`category_id` IN (".implode(",",$category_ids).")" : "`product_to_category`.`category_id` = '$category_id'";
  if(!$products_count) {
    $query_products = "SELECT `products`.`product_id`
                         FROM `products`
                   INNER JOIN `product_to_category` USING(`product_id`)
                   INNER JOIN `product_description` USING(`product_id`)
                        WHERE `products`.`product_is_active` = '1' AND $where_category
                          AND `product_description`.`language_id` = '$current_language_id' AND `product_description`.`pd_is_active` = '1'
                     GROUP BY `products`.`product_id`";
    //if($category_id == 12) echo $query_products."<br>";
    $result_products = mysqli_query($db_link, $query_products);
    if(!$result_products) echo mysqli_error($db_link);
    $products_count = mysqli_num_rows($result_products);
  }

  $query_products = "SELECT `products`.`product_id`,`products`.`product_isbn`,`products`.`stock_status_id`,`products`.`product_price`,`products`.`product_quantity`,
                            `products`.`product_subtract`,`products`.`product_viewed`,`product_discount`.`pd_price`,`product_description`.`pd_name`,
                            `product_description`.`pd_description`
                       FROM `products`
                 INNER JOIN `product_to_category` ON `product_to_category`.`product_id` = `products`.`product_id`
                 INNER JOIN `product_description` ON `product_description`.`product_id` = `products`.`product_id`
                  LEFT JOIN `product_discount` ON `product_discount`.`product_id` = `products`.`product_id`
                      WHERE `products`.`product_is_active` = '1' AND $where_category";
  if(!empty($price_min) && !empty($price_max)) {
    $query_products .= " AND (`products`.`product_price` >= '$price_min' AND `products`.`product_price` <= '$price_max')";
  }
  $query_products .= " AND `product_description`.`pd_is_active` = '1' AND `product_description`.`language_id` = '$current_language_id'
                  GROUP BY `products`.`product_id`
                  ORDER BY `products`.`stock_status_id` ASC, $order_by
                      $query_limit";
  //if($current_language_id == 2) echo $query_products."<br>";
  //if($category_id == 17) echo $query_products."<br>";
  $result_products = mysqli_query($db_link, $query_products);
  if(!$result_products) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_products) > 0) {

?>
  <div class="row">
<?php
    if((isset($price_min) && $price_min != $min_product_price) || (isset($price_max) && $price_max != $max_product_price)) {
      $products_count = mysqli_num_rows($result_products);
    }

    // if the results are more then $page_offset
    // making a pagination, finding how many pages will be needed
    $current_page = ($offset/$page_offset)+1;

    if($products_count > $page_offset) {
      $page_count = ceil($products_count/$page_offset);
    }
    // echo $page_count;
    $div_class = 1;
    $rows_count = 0;
    $blocks_on_row = 1;

    while($product_row = mysqli_fetch_assoc($result_products)) {

      if($blocks_on_row == 4) $blocks_on_row = 1;

      if($rows_count == $page_offset) {
        $rows_count = 0;
        $div_class++; 
      }
      if($div_class == 1) $div_class_hide = ""; 
      else $div_class_hide = " hide";

      $product_id = $product_row['product_id'];
      $pd_name = $product_row['pd_name'];
      $pd_name_for_alt = ($current_lang == "bg") ? "Доставка на цветя $pd_name" : "";
      $pd_name_for_title = ($current_lang == "bg") ? "$pd_name с доставка на цветя от цветарски магазин LaRose" : "";
      $pd_description = stripslashes($product_row['pd_description']);
      $product_quantity = $product_row['product_quantity'];
      $product_subtract = $product_row['product_subtract'];
      $product_viewed = $product_row['product_viewed'];
      $product_isbn = $product_row['product_isbn'];
      $stock_status_id = $product_row['stock_status_id'];

      $pd_images_folder = "/frontstore/images/products/";
      $pd_images_watermark = "/frontstore/images/watermark-170-250.png";

      $pi_names_array = get_product_images($product_id);
      if((isset($pi_names_array['default']['pi_name']) && !empty($pi_names_array['default']['pi_name']))) {
        $default_img = $pi_names_array['default']['pi_name'];
        $default_img_exploded = explode(".", $default_img);
        $default_img_name = $default_img_exploded[0];
        $default_img_exstension = $default_img_exploded[1];
        $gallery_img_home_default = $pd_images_folder.$default_img_name."_home_default.".$default_img_exstension;
        $gallery_img_cart = $pd_images_folder.$default_img_name."_small_default.".$default_img_exstension;
        $full_path = $_SERVER['DOCUMENT_ROOT'].$gallery_img_home_default;
        $file = $full_path;
        list($width,$height) = getimagesize($file);
        $default_img_dimensions = "width='$width' height='$height'";
        if($width > $height) {
          $default_img_style = "";
        }
        else {
          $default_img_style = "style='height:100%;width:auto;'";
        }
      }
      else {
        $gallery_img_home_default = $pd_images_folder."no_image.jpg";
        $full_path = $_SERVER['DOCUMENT_ROOT'].$gallery_img_home_default;
        $file = $full_path;
        list($width,$height) = getimagesize($file);
        $default_img_dimensions = "width='$width' height='$height'";
        if($width > $height) {
          $default_img_style = "";
        }
        else {
          $default_img_style = "style='height:100%;width:auto;'";
        }
        $gallery_img_cart = $pd_images_folder."no_image.jpg";
      }

      $additional_img = "";
      if(isset($pi_names_array['gallery'])) {
        //echo"<pre>";print_r($pi_names_array['gallery']);
        $gallery_img = $pi_names_array['gallery'][0]['pi_name'];
        $gallery_img_exploded = explode(".", $gallery_img);
        $gallery_img_name = $gallery_img_exploded[0];
        $gallery_img_exstension = $gallery_img_exploded[1];
        $gallery_additional_img = $pd_images_folder.$gallery_img_name."_home_default.".$gallery_img_exstension;
        $full_path = $_SERVER['DOCUMENT_ROOT'].$gallery_additional_img;

        $file = $full_path;

        list($width,$height) = @getimagesize($file);

        if($width > $height) {
          $img_style = "";
        }
        else {
          $img_style = "style='height:100%;width:auto;'";
        }

        $additional_img = "<img src='$gallery_additional_img' width='$width' height='$height' title='$pd_name_for_title' alt='$pd_name_for_alt' class='img-responsive' $img_style>";
      }

      $product_price = $product_row['product_price'];
      $pd_price = (!empty($product_row['pd_price'])) ? $product_row['pd_price'] : "";
      $price_discount_class = (!empty($pd_price)) ? " class='line_through'" : "";
      $class_first = ($blocks_on_row == 1) ? " first-in-line" : "";
      $pd_name_for_link = beautify_name_for_url($pd_name);
      $cyr_url = cyrialize_url($pd_name_for_link);
      $product_link = "/$current_lang/pid-$product_id/$cyr_url";
      $quick_view_link = "/$current_lang/product-quick-view?product_id=$product_id&current_cat_href=$current_cat_href";
      $onclick_wishlist_fn = (user_is_loged()) ? (in_array($product_id, $customer_wishlist)) ? "OpenModalWindow(product_already_in_wishlist)" : "AddProductToWishlist('$product_id','$pd_name')" : "OpenModalWindow(loggin_required_for_wishlist)";
?>
      <div class="ajax_block_product<?=$class_first;?> col-xs-12 col-sm-6 col-md-4<?=$div_class_hide;?> <?=$div_class;?>">
        <div id="product_block_<?=$product_id;?>" class="product-container product-block" itemscope itemtype="https://schema.org/Product">
          <div class="left-block">
            <input type="hidden" name="product_isbn" class="product_isbn" value="<?=$product_isbn;?>">
            <input type="hidden" name="product_price" class="product_price" value="<?=$product_price;?>">
            <input type="hidden" name="pd_price" class="pd_price" value="<?=$pd_price;?>">
            <input type="hidden" name="product_name" class="product_name" value="<?=$pd_name;?>">
            <input type="hidden" name="product_url" class="product_url" value="<?=urldecode($product_link);?>">
            <input type="hidden" name="product_qty" class="product_qty" value="1">
            <input type="hidden" name="product_img" class="product_img" value="<?=$gallery_img_cart;?>">
            <div class="product-image-container image">
              <a class="product_img_link" href="<?=urldecode($product_link);?>" itemprop="url">
                <?php if($stock_status_id == 3 || ($product_subtract == 1 && $product_quantity == 0)) { ?>
                <span class="leo-more-info" data-idproduct="2"><span class="text">Продуктът е изчерпан</span></span>
                <?php } ?>
                <img class="replace-2x img-responsive" src="<?=$gallery_img_home_default;?>" <?="$default_img_dimensions $default_img_style";?> alt="<?=$pd_name_for_alt;?>" title="<?=$pd_name_for_title;?>" itemprop="image" />
                <span class="product-additional" data-idproduct="<?=$product_id;?>">
                  <?=$additional_img;?>
                </span>
              </a>
            </div>
            <div class="functional-buttons-detail">
              <div class="action-button  clearfix">
                <div class="wishlist">
                  <a class="btn-tooltip addToWishlist" href="javascript:;" onclick="<?=$onclick_wishlist_fn;?>" data-toggle="tooltip" title="<?=$languages[$current_lang]['title_add_to_wishlist'];?>">
                    <i class="fa fa-heart"></i>
                  </a>
                </div>
                <div class="button-quick-view">
                  <a class="quick-view btn btn-tooltip fancybox fancybox.ajax" href="<?=$quick_view_link;?>" rel="nofollow" title="<?=$languages[$current_lang]['title_quick_view'];?>" >
                    <i class="fa fa-search-plus"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="right-block">
            <div class="product-meta">
              <div itemprop="name" class="name"> <a class="product-name" href="<?=urldecode($product_link);?>" title="<?=$pd_name;?>"> <?=$pd_name;?> </a> </div>
              <div class="comments_note product-rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                <div class="star_content">
                  <?php
                    $product_rating_params = get_product_rating($product_id);

                    $product_rating = $product_rating_params['product_rating'];
                    $ratings_count = $product_rating_params['ratings_count'];
                    $product_rating_imgs = $product_rating_params['rating_imgs'];
                      $text_rate = $languages[$current_lang]['text_rate_vote'];
                      $text_rates = $languages[$current_lang]['text_rate_votes'];
                      $ratings_count_text = "<span class='text'>$ratings_count ";
                      $ratings_count_text .= ($ratings_count == 1) ? "$text_rate</span>" : "$text_rates</span>";

                    echo "$product_rating_imgs $ratings_count_text";
                  ?>
                  <meta itemprop="worstRating" content = "0" />
                  <meta itemprop="ratingValue" content = "<?=$product_rating?>" />
                  <meta itemprop="bestRating" content = "5" />
                </div>
                <span class="nb-comments"><span itemprop="reviewCount"><?=$product_viewed?></span> Прегледа</span>
              </div>
              <meta itemprop="manufacturer" content="<?=$languages[$current_lang]['merchant'];?>">
              <meta itemprop="category" content="<?=$languages[$current_lang]['merchant_logo_text_alt'];?>" />
              <div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="content_price">
                <input type="hidden" class="basic_price" value="<?php if(!empty($pd_price)) echo $pd_price; else echo $product_price?>" />
                <span itemprop="price" class="price product-price"> <?php if(!empty($pd_price)) echo $pd_price; else echo $product_price?><span class="currency">&nbsp;лв.</span> </span>
                <meta itemprop="priceCurrency" content="BGN" />
                <?php
                  if(!empty($pd_price)) {
                    $pd_percent_reduction = 100-(ceil(($pd_price / $product_price) * 100));
                    echo '<span class="old-price product-price"> '.$product_price.'<span class="currency">&nbsp;лв.</span></span>';
                    echo '<span class="price-percent-reduction"> '.$pd_percent_reduction.'% </span>';
                  }
                ?>
                <?php
                  if($stock_status_id == 2) {
                    //upon request
                ?>
                  <span class="upon_request red pull-right">
                    <link itemprop="availability" href="https://schema.org/OutOfStock" /> <?=$languages[$current_lang]['text_upon_request'];?> 
                  </span>
                <?php
                  }
                  elseif($stock_status_id == 3 || ($product_subtract == 1 && $product_quantity == 0)) {
                    //not in stock
                ?>
                  <span class="out-of-stock pull-right">
                    <link itemprop="availability" href="https://schema.org/OutOfStock" /> <?=$languages[$current_lang]['text_out_in_stock'];?> 
                  </span>
                <?php
                  }
                  else {
                ?>
                  <span class="available-now pull-right hidden">
                    <link itemprop="availability" href="https://schema.org/InStock" /> <?=$languages[$current_lang]['text_in_stock']." ".$product_quantity." бр.";?> 
                  </span>
                <?php
                  }
                ?>
              </div>
              <div class="functional-buttons clearfix">
                <?php
                  if($stock_status_id != 2 && $stock_status_id != 3) {
                    //not in stock
                    if($product_subtract == 1 && $product_quantity == 0) {
                      
                    }
                    else {
                ?>
                <div class="cart">
                  <span onclick="AddProductToCart('<?=$product_id;?>','<?=$current_language_id;?>')" class="button add_to_cart btn btn-outline" rel="nofollow" title="<?=$languages[$current_lang]['btn_add_to_shopping_cart'];?>">
                    <span><?=$languages[$current_lang]['btn_add_to_shopping_cart'];?></span>
                  </span>
                </div>
                <?php
                    }
                  }
                ?>
              </div>
            </div>
          </div>
        </div>

      </div>
<?php
     $rows_count++;
    $blocks_on_row++;
    } //while($product_row)

    // if the results are more then $page_offset make pagination
    if(isset($page_count)) {
      $_SESSION['page_count'] = $page_count;
      if((isset($price_min) && $price_min != $min_product_price) || (isset($price_max) && $price_max != $max_product_price) || !empty($option)) {
?>
    </div><!--<div class="row">-->
    <div class="row">
      <div class="col-lg-12">
        <div class="text-center">
          <ul id="pagination" class="js_pagination pagination pagination-sm">
<?php
          while($current_page <= $page_count) {
            if($current_page == 1) {
              $li_current = ' class="active"'; 
              echo '<li class="disabled btn_prev_page"><a href="javascript:;">&laquo; </a></li>'."\n";
            }
            else {
              $li_current = "";
            }

            echo "<li id='pag_$current_page' $li_current><a href='javascript:;' data=\"$current_page\">$current_page</a></li>\n";

            $current_page++;
          }
?>
            <li class="btn_next_page"><a href="javascript:;" data="2"> &raquo;</a></li>
          </ul>
          <input type="hidden" class="page_count" value="<?=$page_count;?>" >
        </div>
      </div>
<?php
      }
      else {
?>
    </div><!--<div class="row">-->
    <div class="row">
      <div class="col-lg-12">
        <div class="text-center">
          <ul id="pagination" class="php_pagination pagination pagination-sm">
<?php
          $pages = 1;
          $current_offset = $offset;
          $offset = 0;
          $curr_page_num = 1;
          $page_path_string = mysqli_real_escape_string($db_link,strip_tags($_GET['page']));
          $curr_page = "/$page_path_string";
          $page_path_array = explode("/", $page_path_string);
          if(strstr($page_path_string, "/page/") && strstr($page_path_string, "cid")) {
            $curr_page_num = intval(array_pop($page_path_array));
            array_pop($page_path_array);
            $curr_page = "/".implode("/", $page_path_array);
          }

          if($curr_page_num == 1) {
            echo '<li class="disabled btn_prev_page"><a href="javascript:;">&laquo; </a></li>'."\n";
          }
          else {
            $prev_offset = $current_offset - $page_offset;
            $prev_page = $curr_page_num-1;
            $link = ($prev_page == 1) ? $curr_page : "$curr_page/page/$prev_page";
            echo '<li class="btn_prev_page"><a href="'."$link".'">&laquo; </a></li>'."\n";
          }

          while($pages <= $page_count) {

            $li_current = "";
            $link = ($pages == 1) ? $curr_page : "$curr_page/page/$pages";
            if($curr_page_num == $pages) {
              $li_current = ' class="active"';
              $link = "javascript:;";
            }

            echo "<li id='pag_$pages' $li_current><a href='$link'>$pages</a></li>\n";

            $pages++;
            $offset += $page_offset;
          }
          if($curr_page_num == $page_count) {
            echo '<li class="disabled btn_next_page"><a href="javascript:;"> &raquo;</a></li>'."\n";
          }
          else {
            $next_offset = $current_offset + $page_offset;
            $next_page = $curr_page_num+1;
            echo '<li class="btn_next_page"><a href="'."$curr_page/page/$next_page".'">&raquo; </a></li>'."\n";
          }
          
?>
          </ul>
          <input type="hidden" class="products_count" value="<?=$products_count;?>" >
        </div>
      </div>
    </div><!--<div class="row">-->
<?php
      }
    } // if(isset($page_count))
    mysqli_free_result($result_products);
  }else {
    $no_search_results = $languages[$current_lang]['text_no_search_results'];
    
    echo "<p style='margin:5%;'>$no_search_results</p>";
  }
?>
    <input type="hidden" class="current_lang" value="<?=$current_lang;?>" >
    <input type="hidden" class="language_id" value="<?=$current_language_id;?>" >
    <input type="hidden" class="current_cat_href" value="<?=$current_cat_href?>">
    <input type="hidden" class="cd_pretty_url" value="<?=$cd_pretty_url?>">
    <input type="hidden" class="current_cat_id" value="<?=$category_id?>">
<?php
}
  
function list_products_options_values() {

  global $db_link;
  global $current_language_id;
  global $categories_ids;
  global $current_category_id;
  global $current_option_id;
  global $option_desc_name;
  global $option_type;

  $query_option_values = "SELECT `product_option_value`.`option_value_id`
                          FROM `product_option_value`
                          INNER JOIN `product_to_category` ON `product_option_value`.`product_id` = `product_to_category`.`product_id`
                          WHERE `product_option_value`.`option_id` = '$current_option_id' AND `product_to_category`.`category_id` = '$current_category_id'";
  //echo "<input type='hidden' value='$query_option_values' />"; 
  $result_option_values = mysqli_query($db_link, $query_option_values);
  if(!$result_option_values) echo mysqli_error($db_link);
  $option_values_count = mysqli_num_rows($result_option_values);
  if($option_values_count > 0) {

    $option_value_ids = array();
    $products_with_option_count = array();

    while($option_value = mysqli_fetch_assoc($result_option_values)) {

      $option_value_id = $option_value['option_value_id'];
      if(!in_array($option_value_id, $option_value_ids)) {
        $option_value_ids[] = $option_value_id;
        $products_with_option_count[$option_value_id] = 1;
      }
      else $products_with_option_count[$option_value_id] = $products_with_option_count[$option_value_id]+1;

    }
    //echo "<input type='hidden' value='".print_r($products_with_option_count)."' />";
    //echo "<pre>";print_r($products_with_option_count);

    $query_ovd_names = "SELECT `option_value`.`option_value_id`,`option_value`.`ov_image_path`,`option_value`.`ov_sort_order`,`option_value_description`.`ovd_name`
                        FROM `option_value`
                        INNER JOIN `option_value_description` USING(`option_value_id`)
                        WHERE `option_value_description`.`option_value_id` IN (".  implode(",", $option_value_ids).") 
                          AND `option_value_description`.`language_id` = '$current_language_id'
                        ORDER BY `option_value`.`ov_sort_order` ASC ";
    //echo $query_ovd_names."<br>";
    $result_ovd_names = mysqli_query($db_link, $query_ovd_names);
    if(!$result_ovd_names) echo mysqli_error($db_link);
    $ovd_count = mysqli_num_rows($result_ovd_names);
    if($ovd_count > 0) {
      while($row_ovd_name = mysqli_fetch_assoc($result_ovd_names)) {

        $option_value_id = $row_ovd_name['option_value_id'];
        $ov_image_path = $row_ovd_name['ov_image_path'];
        $ov_sort_order = $row_ovd_name['ov_sort_order'];
        $products_count = $products_with_option_count[$option_value_id];
        $ovd_name = $row_ovd_name['ovd_name'];
        $data_sort_type = ($current_option_id == 1) ? "color" : "others";

        switch($option_type) {

        case "radio" :
          $ovd_name .= " &nbsp;($products_count)";
?>
        <div class="radio">
          <input type="radio" onClick="SortProductsByOptionValue(this,'<?=$current_language_id;?>')" data-sort-action="add" data-sort-type="<?=$data_sort_type;?>" name="option_radio_<?=$current_option_id;?>" id="option_radio_<?=$option_value_id;?>" value="<?=$option_value_id;?>" >
          <label for="option_radio_<?=$current_option_id;?>"><?=$ovd_name;?></label>
        </div>
<?php 
              break;

        case "checkbox" :
          $ovd_name .= " &nbsp;($products_count)";
?>
        <div class="checkbox">
          <input type="checkbox" onClick="SortProductsByOptionValue(this,'<?=$current_language_id;?>')" data-sort-action="add" data-sort-type="<?=$data_sort_type;?>" name="option_checkbox_<?=$option_value_id;?>" id="option_checkbox_<?=$option_value_id;?>" value="<?=$option_value_id;?>" >
          <label for="option_checkbox_<?=$option_value_id;?>"><?=$ovd_name;?></label>
        </div>
<?php 
              break;
        case "image" :
?>
        <div class="checkbox image">
          <input type="checkbox" onClick="SortProductsByOptionValue(this,'<?=$current_language_id;?>')" data-sort-action="add" data-sort-type="<?=$data_sort_type;?>" name="option_checkbox_<?=$option_value_id;?>" id="option_checkbox_<?=$option_value_id;?>" value="<?=$option_value_id;?>" >
          <label for="option_checkbox_<?=$option_value_id;?>">
            <img src="<?=$ov_image_path;?>" alt="<?=$ovd_name;?>" width='20' height="20" /> <?=$ovd_name;?> <?="&nbsp;($products_count)";?>
          </label>
        </div>
<?php 
              break;

        } // switch($option_type)
      } // if($ovd_count > 0)
    } // while($option_value)
    mysqli_free_result($result_option_values);

    echo '<hr class="divider">';
  }
}

function print_html_payment_methods () {

  global $db_link;
  global $languages;
  global $current_lang;
?>
    <div id="paypal" class="payment_method_img">
      <p>
        <table border="0" cellpadding="10" cellspacing="0" align="center"><tr><td align="center"></td></tr><tr><td align="center"><a href="https://www.paypal.com/webapps/mpp/paypal-popup" title="How PayPal Works" onclick="javascript:window.open('https://www.paypal.com/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;"><img src="https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg" border="0" alt="PayPal Acceptance Mark"></a></td></tr></table>
      </p>
    </div>
    <div id="bank_transfer" class="payment_method_img">
      <?=$languages[$current_lang]['text_bank_transfer'];?>
    </div>
    <div id="delivery" class="payment_method_img">
      <p>
        <i class="fa fa-truck fa-5x"></i>
        <img src="/frontstore/images/cash-on-delivery.jpg" alt="Cash on delivery" width="60" height="60">
      </p>
      <p><?=$languages[$current_lang]['text_cash_on_delivery'];?></p>
    </div>
    <div id="card" class="payment_method_img">
      <div class="single_card">
        <img src="/frontstore/images/visa_79x50.png" alt="Visa card logo" width="79" height="50">
      </div>
      <div class="single_card">
        <img src="/frontstore/images/visa_electron_79x50.png" alt="Visa Electron card logo" width="79" height="50">
      </div>
      <div class="single_card">
        <img src="/frontstore/images/v_pay_46x50.png" alt="V-pay card logo" width="46" height="50">
      </div>
      <div class="single_card">
        <a href="https://www.mastercard.com/index.html" target="_blank">
          <img src="/frontstore/images/mc_accpt_80x50.gif" alt="MasterCard logo" width="80" height="50">
        </a>
      </div>
      <div class="single_card">
        <a href="https://www.mastercard.com/index.html" target="_blank">
          <img src="/frontstore/images/me_accpt_80x50.gif" alt="MasterCard Electron logo" width="80" height="50">
        </a>
      </div>
      <div class="single_card more_margin">
        <a href="https://www.maestrocard.com/gateway/index.html" target="_blank">
          <img src="/frontstore/images/ms_accpt_80x50.gif" alt="Maestro card logo" width="80" height="50">
        </a>
      </div>
      <div class="single_card more_margin">
        <img src="/frontstore/images/vbv_98x50.gif" alt="Verified by Visa logo" width="98" height="50">
      </div>
      <div class="single_card">
        <img src="/frontstore/images/sclogo_92x50.gif" alt="MasterCard SecureCode logo" width="92" height="50">
      </div>
      <div class="clearfix"></div>
      <h3 class="red"><?=$languages[$current_lang]['text_card_payment_important'];?></h3>
      <p><?=$languages[$current_lang]['text_card_payment'];?></p>
    </div>
    <div id="epay" class="payment_method_img">
      <img src="/frontstore/images/easypay-logo.gif" alt="Easypay logo" width="240" height="68">
      <p><?=$languages[$current_lang]['text_easypay'];?></p>
    </div>
<?php
  
}

function print_error_page() {
  
  global $db_link;
  global $current_language_id;
  
  $content_type_id= 3;
      
  //error page
  $query_content = "SELECT `content_parent_id`,`content_hierarchy_ids`,`content_name`,`content_meta_title`,
                            `content_meta_keywords`,`content_meta_description`,`content_text` 
                      FROM `contents`
                     WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_id` = '$current_language_id')";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_parent_id = $content_array['content_parent_id'];
    $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
    $content_name = stripslashes($content_array['content_name']);
    $content_menu_text = $content_name;
    $content_meta_title = stripslashes($content_array['content_meta_title']);
    $content_meta_keywords = stripslashes($content_array['content_meta_keywords']);
    $content_meta_description = stripslashes($content_array['content_meta_description']);
    $content_text = stripslashes($content_array['content_text']);
    
    //header("HTTP/1.0 404 Not Found");
    http_response_code(404);
    //header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");

    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords,$additional_css = false,$additional_js = false,$body_css = "page");
    
?>
  <div class="container content">
    <div style="padding-top: 8px;">&nbsp;</div>
    
    <h1 class="page-heading"><?=$content_menu_text;?></h1>
    <!--start of main-->
    <main>
<?php
      echo $content_text;

?>
    </main>
    <!--end of main-->
  </div>
<?php
    }
  print_html_footer();
?>
</body>
</html>
<?php
  exit;
}