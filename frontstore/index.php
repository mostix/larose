<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  if(isset($current_page_pretty_url) && $current_page_pretty_url == "logout") {
    if(session_id() != '') {
      unset($_SESSION['customer']);
      unset($_SESSION['cart']);
      unset($_SESSION['currencies']);
      session_unset();
      session_destroy();
    }
    header('Location: /');
    exit;
  }
  
  $query_current_params = "SELECT `languages`.`language_id`,`languages`.`language_root_content_id`,`contents`.`content_hierarchy_path`,`contents`.`content_is_default` 
                             FROM `languages` 
                       INNER JOIN `contents` ON `contents`.`content_id` = `languages`.`language_default_content_id`
                            WHERE `language_code` = '$current_lang'";
  //echo $query_content;exit;
  $result_current_params = mysqli_query($db_link, $query_current_params);
  if(!$result_current_params) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_current_params) > 0) {
    $row_current_params = mysqli_fetch_assoc($result_current_params);
    $current_language_id = $row_current_params['language_id'];
    $content_hierarchy_ids = $row_current_params['language_root_content_id'];
    $content_is_default = $row_current_params['content_is_default'];
    $home_page_url = ($content_is_default == 1) ? "" : $row_current_params['content_hierarchy_path'];
  }
  
  $content_is_home_page = 0;
  $content_meta_title = "Larose - Онлайн Цветарски магазин.Доставка на цветя до адрес в София и страната.";
  $content_meta_description = "Цветарски магазин Ла Роз.Доставки на цветя и букети.Онлайн магазин за цветя Ви предлага букет от рози,букет от гербери,букет от лалета,букет от хризантема,букет от лилиуми,букет от орхидеи,кошници";
  $content_meta_keywords = "cvetarski magazin, цветарски магазин, цветарски магазин софия, цветарски магазин студентски град, цветя онлайн, cvetia online, buketi online, cvetarski magazin sofia, доставка на цветя, dostavka cvetq, cvetia dostavka,";
  
  if(isset($current_page_pretty_url) && ($current_page_pretty_url == "product-quick-view")) {
    
    include_once 'product-quick-view.php';
    exit;
    
  }
  if(isset($current_page_pretty_url) && ($current_page_pretty_url == "registration")) {
    
    if($current_lang == "en") {
      $content_meta_title = "Registration - Larose";
      $content_meta_description = "";
      $content_meta_keywords = "";
    }
    $robots_meta = "noindex,follow";
    
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords,$additional_css = false,$additional_js = false,$body_css = "registration");
?>
<div class="container content">

  <div class="form-group">&nbsp;</div>
  
  <h1 class="page-heading"><?=$languages[$current_lang]['header_registration'];?></h1>

  <main>
    <?php include_once 'registration.php';?>
  </main>

</div>

<?php
  }
  elseif(isset($current_page_pretty_url) && $current_page_pretty_url == "confirm-account") {
    $customer_id = intval($current_page_path[1]);
    $robots_meta = "noindex,follow";
    
    if(!empty($customer_id)) {

      print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords);
?>
<div class="container content">

  <div class="form-group">&nbsp;</div>
  
  <h1 class="page-heading"><?=$languages[$current_lang]['header_registration_confirm_account'];?></h1>

  <main>
    <?php include_once 'confirm-account.php'; ?>
  </main>

</div>
<?php
    }
  }
  elseif(isset($current_page_pretty_url) && 
    ($current_page_pretty_url == "user-profile-data" || $current_page_pretty_url == "user-profile-addresses" || $current_page_pretty_url == "user-profile-address-add" 
    || $current_page_pretty_url == "user-profile-address-edit"  || $current_page_pretty_url == "user-profile-invoice-addresses"  
    || $current_page_pretty_url == "user-profile-invoice-address-add" || $current_page_pretty_url == "user-profile-invoice-address-edit" 
    || $current_page_pretty_url == "user-profile-orders" || $current_page_pretty_url == "user-profile-wishlist" 
    || $current_page_pretty_url == "user-profile-change-password")) {
    
    if($current_lang == "en") {
      if($current_page_pretty_url == "user-profile-change-password") {
        $content_meta_title = "Profile change Forgotten password";
        $content_meta_description = "";
        $content_meta_keywords = "";
      }
    }
    
    $additional_css = '<link rel="stylesheet" href="/frontstore/css/my-account.css" type="text/css" media="all" />';
    $robots_meta = "noindex,follow";
    $body_css = "my-account";
    $center_column_class = "col-lg-9 col-md-9 col-sm-12 col-xs-12";
    if($current_page_pretty_url == "user-profile-change-password") {
      $center_column_class = "col-lg-12 col-md-12 col-sm-12 col-xs-12";
      $body_css .= "my-account change-password";
    }
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css, $additional_js = false, $body_css);
?>
  <section id="columns" class="columns-container">
    
    <div class="container content">

      <div class="row">
<?php 
      if(user_is_loged()) {
        $customer_fullname = $_SESSION['customer']['customer_firstname']." ".$_SESSION['customer']['customer_lastname']; 
?>
        <section id="right_column" class="column sidebar col-lg-3 col-md-3 col-sm-12 col-xs-12">
          <div class="shopping_cart">
            <div class="media heading">
              <div class="cart-inner media-body">
                <h4 class="title_block"><?=$customer_fullname;?></h4>
                <?php print_html_user_profile_menu(); ?>
              </div>
            </div>
          </div>
        </section>
<?php } ?>
        
        <section id="center_column" class="<?=$center_column_class;?>">
          <div id="breadcrumb" class="clearfix">
            <div class="breadcrumb clearfix">
              <?php
                $current_page_text = "header_".str_replace("-", "_", $current_page_pretty_url);
                $content_name = $languages[$current_lang][$current_page_text];
                print_content_breadcrumbs($content_hierarchy_ids, $content_name)
              ?>
            </div>
          </div>
          
          <h1 class="page-heading"><?=$content_name;?></h1>
          <div id="contentbottom" class="no-border clearfix block">
            <div class="row">

              <?php 
                if(!user_is_loged() && $current_page_pretty_url != "user-profile-change-password") {
                  echo "<h1>".$languages[$current_lang]['error_secured']."</h1>";
                }
                else {
                  include_once 'users-profiles/'.$current_page_pretty_url.'.php'; 
                }
              ?>

            </div>
          </div>
        </section>
      </div>

    </div>
  </section>

<?php
  }
  elseif(isset($current_page_pretty_url) && 
    ($current_page_pretty_url == "shopping-cart-overview" || $current_page_pretty_url == "shopping-cart-addresses" || $current_page_pretty_url == "shopping-cart-address-add" 
    || $current_page_pretty_url == "shopping-cart-address-edit" || $current_page_pretty_url == "shopping-cart-registration" 
    || $current_page_pretty_url == "shopping-cart-invoice-address-add" || $current_page_pretty_url == "shopping-cart-invoice-address-edit" 
    || $current_page_pretty_url == "shopping-cart-order" || $current_page_pretty_url == "shopping-cart-checkout-card-success" 
    || $current_page_pretty_url == "shopping-cart-checkout-card-failure" || $current_page_pretty_url == "shopping-cart-checkout-cash-success" 
    || $current_page_pretty_url == "paypal-request" || $current_page_pretty_url == "paypal-response" 
    || $current_page_pretty_url == "borica-request" || $current_page_pretty_url == "borica-response" 
    || $current_page_pretty_url == "shopping-cart-checkout-paypal-failure" || $current_page_pretty_url == "shopping-cart-checkout-paypal-success"
    || $current_page_pretty_url == "shopping-cart-checkout-bank-transfer-success" || $current_page_pretty_url == "shopping-cart-checkout-easypay-success"
    || $current_page_pretty_url == "shopping-cart-addresses-test")) {
    
    if($current_page_pretty_url == "paypal-request" || $current_page_pretty_url == "paypal-response" || $current_page_pretty_url == "borica-request") {
      include_once 'shopping-cart/'.$current_page_pretty_url.'.php';
      exit(1);
    }
    else {
      $robots_meta = "noindex,follow";
      
      print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css = false, $additional_js = false, $body_css = "cart");

      $content_name = "";
      if($current_page_pretty_url == "shopping-cart-address-add" || $current_page_pretty_url == "shopping-cart-address-edit" || $current_page_pretty_url == "shopping-cart-registration") {
        $current_page_text = "header_".str_replace("-", "_", $current_page_pretty_url);
        $content_name = $languages[$current_lang][$current_page_text];
      } 
?>
  <section id="columns" class="columns-container">
    
    <div class="container content cart">

      <div class="row">
        <section id="center_column" class="col-sp-12 col-xs-12 col-sm-12 col-md-12 col-lg-12">
          
          <div id="breadcrumb" class="clearfix">
            
            <div class="breadcrumb clearfix">
              
            </div>
            
          </div>
          
          <h1 class="page-heading"><?=$content_name;?></h1>
          <div id="contentbottom" class="no-border clearfix block">
            <div class="row">

              <?php include_once 'shopping-cart/'.$current_page_pretty_url.'.php'; ?>

            </div>
          </div>
        </section>

        <section id="right_column" class="column sidebar col-md-0">
     
        </section>

      </div>

    </div>
  </section>

<?php
    }
  }
  else {
    
    $content_name = "";
    $content_text = "";
    
    $query_content = "SELECT `content_type_id`,`content_parent_id`,`content_hierarchy_ids`,`content_is_home_page`,`content_name`,`content_menu_text`,`content_meta_title`,
                             `content_meta_keywords`,`content_meta_description`,`content_text`,`content_pretty_url`,`content_attribute_1`,`content_attribute_2`
                        FROM `contents`
                       WHERE `content_is_active` = '1' AND $query_where_page";
    //if($_SESSION['debug']) echo $query_content."<br><br>";
    $result_content = mysqli_query($db_link, $query_content);
    if(!$result_content) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_content) > 0) {
      $content_array = mysqli_fetch_assoc($result_content);
      $content_type_id = $content_array['content_type_id'];
      $content_parent_id = $content_array['content_parent_id'];
      $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
      $content_is_home_page = $content_array['content_is_home_page'];
      $content_name = stripslashes($content_array['content_name']);
      $content_menu_text = stripslashes($content_array['content_menu_text']);
      $content_meta_title = stripslashes($content_array['content_meta_title']);
      $content_meta_keywords = stripslashes($content_array['content_meta_keywords']);
      $content_meta_description = stripslashes($content_array['content_meta_description']);
      $content_text = stripslashes($content_array['content_text']);
      $content_pretty_url = $content_array['content_pretty_url'];
      $content_attribute_1 = $content_array['content_attribute_1'];
      $content_attribute_2 = $content_array['content_attribute_2'];
    }
    else {
      print_error_page();
    }
    
    if($content_is_home_page == 1) {
      
      $use_hreflang = true;
      $hreflang = "<link rel='alternate' hreflang='bg' href='".BASEPATHNOSLASH."' />\n";
      $hreflang .= "<link rel='alternate' hreflang='x-default' href='".BASEPATHNOSLASH."' />\n";
      $hreflang .= "<link rel='alternate' hreflang='en' href='".BASEPATHNOSLASH."/en/home' />\n";

      if(!$GLOBALS['is_mobile']) $additional_js = '<script type="text/javascript" src="/frontstore/modules/leosliderlayer/js/jquery.themepunch.tools.min.js"></script>';
      else $additional_js = false;
      if(!empty($home_page_url)) $home_page_url = "/".$home_page_url;
      $canonical_link = "<link rel=\"canonical\" href=\"https://".urldecode($_SERVER['SERVER_NAME'].$home_page_url)."\" />";
              
      print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css = false, $additional_js);
      
      $customer_id = isset($_SESSION['customer']['customer_id']) ? $_SESSION['customer']['customer_id'] : 0;
      $customer_wishlist = get_customer_wishlist($customer_id);
?>
      <div class="container no_padding slider_banners">
        <div class="row">
          <div class="hidden-xs hidden-sm col-md-9">
            <?php
              if(!$GLOBALS['is_mobile']) {
                 print_index_sliders($count = 3); 
              }
            ?>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3">
            <?php
              if(!$GLOBALS['is_mobile']) {
                 print_index_banners($count = 3); 
              }
            ?>
            <div class="clearfix"></div>
          </div>
          <script type="text/javascript">
            $(document).ready(function () {
              if ($(window).width() < 971) {
                $("body").addClass('mobile');
              }
              else {
                $("body").removeClass('mobile');
              }
              $(window).resize(function () {
                if ($(window).width() < 971) {
                  $("body").addClass('mobile');
                }
                else {
                  $("body").removeClass('mobile');
                }
              });
<?php
              if(!$GLOBALS['is_mobile']) {
?>
              if (typeof(homeslider_speed) == 'undefined')
                      homeslider_speed = 500;
              if (typeof(homeslider_pause) == 'undefined')
                      homeslider_pause = 3000;
              if (typeof(homeslider_loop) == 'undefined')
                      homeslider_loop = true;
              if (typeof(homeslider_width) == 'undefined')
                      homeslider_width = 779;

              $('.homeslider-description').click(function () {
                      window.location.href = $(this).prev('a').prop('href');
              });

              if ($('#htmlcontent_top').length > 0)
                      $('#homepage-slider').addClass('col-xs-8');
              else
                      $('#homepage-slider').addClass('col-xs-12');

              if (!!$.prototype.bxSlider) {
                $('#homeslider').bxSlider({
                    useCSS: false,
                    maxSlides: 1,
                    slideWidth: homeslider_width,
                    infiniteLoop: homeslider_loop,
                    hideControlOnEnd: true,
                    pager: false,
                    autoHover: true,
                    auto: homeslider_loop,
                    speed: parseInt(homeslider_speed),
                    pause: homeslider_pause,
                    controls: true
                });
              }
<?php
              }
?>
              window.tplogs = true;
            });
          </script>
        </div>
      </div>
        
      <!-- Content -->
      <section id="columns" class="columns-container">
        <div class="container content">
          <div style="padding-top: 8px;">&nbsp;</div>
          
          <div class="row">
            
            <?php print_left_column(); ?>
            
            <section id="center_column" class="col-md-9">
              <div id="contentbottom" class="no-border clearfix block">
                <div class="row">
                  <div class="widget col-lg-12 col-md-12 col-sm-12 col-xs-12 col-sp-12">
                    <div class="products_block exclusive leomanagerwidgets  block nopadding">
                      
                    <h1 class="page-heading"><?=$content_name;?></h1>
<?php
        if(!empty($content_text)) {
?>
                    <div id="category_description" class="short content_sortPagiBar">
                      <?=$content_text;?> <span class="gradient">&nbsp;</span>
                    </div>
                    <a href="javascript:;" class="toggle_cat_desc more" onClick="ToggleDescDiv()"><?=$languages[$current_lang]['btn_show_more'];?> <i class="fa fa-arrow-down" aria-hidden="true"></i></a>
                    <a href="javascript:;" class="toggle_cat_desc less hidden" onClick="ToggleDescDiv()"><?=$languages[$current_lang]['btn_show_less'];?> <i class="fa fa-arrow-up" aria-hidden="true"></i></a>
<?php
          }
?> 
                      <div style="padding-top: 6px;"></div>

                      <div class="page-subheading"> <?=$languages[$current_lang]['header_newest_products'];?> </div>

                      <div class="block_content">
                        <div class="carousel slide">

                          <div class="carousel-inner">
                            <div class="item active">
                              <div class="product_list grid">
                                <div class="row">
                                  
                                  <?php print_newest_products($count = 15); ?>
                                  
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="page-subheading"> <?=$languages[$current_lang]['header_most_viewed_products'];?> </div>

                      <div class="block_content">
                        <div class="carousel slide">

                          <div class="carousel-inner">
                            <div class="item active">
                              <div class="product_list grid">
                                <div class="row">
                                  
                                  <?php print_most_viewed_products($count = 15); ?>
                                  
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="page-subheading"> <?=$languages[$current_lang]['header_top_sellers'];?> </div>

                      <div class="block_content">
                        <div class="carousel slide">

                          <div class="carousel-inner">
                            <div class="item active">
                              <div class="product_list grid">
                                <div class="row">
                                  
                                  <?php print_top_sellers_products($count = 15); ?>
                                  
                                </div>
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

          </div>
        </div>
      </section>
<?php
       
    }
    else {
      if(isset($current_page_pretty_url) && ($current_page_pretty_url == "карта-на-сайта")) {
    
        print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords,$additional_css = false,$additional_js = false,$body_css = "sitemap");
?>
    <div class="container content">

      <div style="padding-top: 8px;">&nbsp;</div>

      <h1 class="page-heading"><?=$content_menu_text;?></h1>
      
      <main>
        <div class="row pt-10">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <p class="page-heading">Продукти</p>
            <ul class="styled_ul">
<?php
              print_sitemap_categories_menu($content_parent_id = 0, $number_of_hierarchy_levels = 3);
?>
            </ul>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <p class="page-heading">Страници</p>
            <ul class="styled_ul">
<?php
              print_sitemap_content_menu($content_hierarchy_level_start = 2,$number_of_hierarchy_levels = 1,$offset = 0);
?>
            </ul>
          </div>
        </div>
      </main>
      <!--end of main-->
    </div>
<?php
      }
      elseif(isset($current_page_pretty_url) && ($current_page_pretty_url == "контакти" || $current_page_pretty_url == "често-задавани-въпроси")) {
    
        print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords,$additional_css = false,$additional_js = false,$body_css = "page");
?>
    <div class="container content">

      <div style="padding-top: 8px;">&nbsp;</div>

      <h1 class="page-heading"><?=$content_name;?></h1>

      <main>
        <?=$content_text;?>
        		
        <p>&nbsp;</p>
        <!-- CONTACT FORM-->
        <h3><?=$languages[$current_lang]['header_inquiry_form'];?></h3>
        <div class="contact-form-container form">
          <div class="row">
            <p class="alert alert-success hidden"><?=$languages[$current_lang]['text_email_send_successfully'];?></p>
            <form action="/frontstore/inquiery.php" id="contact-form" method="post" class="clearfix">			
              <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 input_field">
                <input type="hidden" name="current_lang" value="<?=$current_lang;?>">
                <input type="text" name="name" id="myname" placeholder="<?=$languages[$current_lang]['text_enter_name'];?>..." class="text requiredField m-bot-20" >
                <div class="alert alert-danger error hidden"><?=$languages[$current_lang]['required_field_error'];?></div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 input_field">
                <input type="text" name="phone" id="myphone"  placeholder="<?=$languages[$current_lang]['text_enter_phone'];?>..." class="text requiredField subject m-bot-20" >
                <div class="alert alert-danger error hidden"><?=$languages[$current_lang]['required_field_error'];?></div>
              </div>	
              <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 input_field">
                <input type="text" name="email" id="myemail" placeholder="<?=$languages[$current_lang]['text_enter_email'];?>..."  class="text requiredField email m-bot-20" >
                <div class="alert alert-danger error hidden"><?=$languages[$current_lang]['required_field_error'];?></div>
                <div class="alert alert-danger invalid_email hidden"><?=$languages[$current_lang]['error_create_customer_email_not_valid'];?></div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input_field">
                <textarea name="message" id="mymessage" rows="5" cols="30" class="text requiredField m-bot-20" placeholder="<?=$languages[$current_lang]['text_enter_inquiry'];?>..."></textarea>
                <div class="alert alert-danger error hidden"><?=$languages[$current_lang]['required_field_error'];?></div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input_field">
                <input type="checkbox" name="terms" id="terms"  class="text requiredField" style="display: inline-block; width: auto;">
                <?= $languages[$current_lang]['text_check_terms_and_conditions_01']; ?>
                <a href="/<?=$current_lang;?>/<?php if($current_lang == "bg") echo "общи-условия";else echo "terms-and-conditions"; ?>" target="_blank" class="red">
                  <?= $languages[$current_lang]['text_terms_and_conditions']; ?>
                </a>
                <?= $languages[$current_lang]['text_check_terms_and_conditions_02']; ?>
                <div class="alert alert-danger error hidden terms_error" style="width: 100%;"><?= $languages[$current_lang]['error_check_terms_and_conditions']; ?></div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input_field">
                <input type="checkbox" name="privacy_policy" id="privacy_policy"  class="text requiredField" style="display: inline-block; width: auto;">
                <?= $languages[$current_lang]['text_check_privacy_policy_01']; ?>
                <a href="/<?=$current_lang;?>/<?php if($current_lang == "bg") echo "общи-условия#privacy";else echo "terms-and-conditions"; ?>" target="_blank" class="red">
                  <?= $languages[$current_lang]['text_privacy_policy']; ?>
                </a>
                <?= $languages[$current_lang]['text_check_privacy_policy_02']; ?>
                <div class="alert alert-danger error hidden privacy_policy_error" style="width: 100%;"><?= $languages[$current_lang]['error_check_privacy_policy']; ?></div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input_field">
                <input name="Mysubmitted" id="Mysubmitted" value="<?=$languages[$current_lang]['btn_submit_inquiry'];?>" class="button btn btn-outline" type="submit" >
              </div>
            </form>
          </div>
        </div>
        <?php if($current_page_pretty_url == "контакти") { ?>
          <iframe width="100%" height="400" frameborder="0" scrolling="no" src="https://www.bgmaps.com/link/map/F0363DF7CE2612AA08F87CC601FAAC38"></iframe><br>
        <?php } ?>
      </main>

    </div>
<?php
    }
    else {
      if($current_page_pretty_url == "общи-условия" || $current_page_pretty_url == "политика-за-поверителност-") {
        $robots_meta = "noindex,follow";
      }
      print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords,$additional_css = false,$additional_js = false,$body_css = "page");
?>
  <div class="container content">
    <div style="padding-top: 8px;">&nbsp;</div>
    
    <h1 class="page-heading"><?=$content_menu_text;?></h1>
    <!--start of main-->
    <main>
<?php
    if($content_type_id == 8) {
      //($content_type_id == 8) means gallery page
      
      $galleries_array = get_galleries($count = 0); //&count = 0 means no limit
      print_galleries($galleries_array);

    }
    elseif($content_type_id == 7) {
      // ($content_type_id == 7) means news page

      list_news();

    }
    else {

      echo $content_text;
      
      if(!empty($content_attribute_1)) echo "<br><br>$content_attribute_1";
      if(!empty($content_attribute_2)) echo "<br><br>$content_attribute_2";

    }
?>
    </main>
    <!--end of main-->
  </div>
<?php
      }
    }
  }
  print_html_footer();
?>
</body>
</html>