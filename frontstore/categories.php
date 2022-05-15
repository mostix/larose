<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  if(isset($_GET['cid'])) {
    $current_category_id = mysqli_real_escape_string($db_link,intval($_GET['cid'])); // current selected category_id
  }
  $current_category_parent_id = 0;
  if(isset($_GET['cpid'])) {
    if($_GET['cpid'] == "all") {
      $current_category_parent_id = mysqli_real_escape_string($db_link,$_GET['cpid']);
    }
    else {
      $current_category_parent_id = mysqli_real_escape_string($db_link,intval($_GET['cpid'])); // category_parent_id
    }
  }
  if(!isset($current_category_id) && isset($directory_id)) $current_category_id = mysqli_real_escape_string($db_link,intval($directory_id));

  if(isset($_GET['option'])) {
    $option_array = mysqli_real_escape_string($db_link,$_GET['option']);
  }
  if(isset($_GET['offset'])) {
    $offset = intval(mysqli_real_escape_string($db_link,$_GET['offset']));
  }
  else $offset = 0;
  if(isset($_GET['obp'])) {
    $order_by_price =  $_GET['obp'];
  }
  if(isset($_GET['pmin'])) {
    $price_min =  mysqli_real_escape_string($db_link, intval($_GET['pmin']));
  }
  if(isset($_GET['pmax'])) {
    $price_max =  mysqli_real_escape_string($db_link, intval($_GET['pmax']));
  }
  if(isset($_GET['sort'])) {
    $option =  $_GET['sort'];
  }
  
  if(isset($_GET['page']) && !empty($_GET['page'])) {
    //$current_category_path = $_GET['page'];
    
    $current_page_path_string = mysqli_real_escape_string($db_link,strip_tags($_GET['page']));
    //echo $current_page_path_string;
    $current_category_path = explode("/", $current_page_path_string);
    $count_category_path_elements = count($current_category_path)-1;
    $current_category_pretty_url = $current_category_path[$count_category_path_elements];
    $current_lang = $current_category_path[0];
  }
  
  $query_current_params = "SELECT `languages`.`language_id`,`languages`.`language_root_content_id`,`contents`.`content_hierarchy_path` ,`contents`.`content_is_default`
                             FROM `languages` 
                       INNER JOIN `contents` ON `contents`.`content_id` = `languages`.`language_default_content_id`
                            WHERE `language_code` = '$current_lang'";
  //echo $query_current_params;
  $result_current_params = mysqli_query($db_link, $query_current_params);
  if(!$result_current_params) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_current_params) > 0) {
    $row_current_params = mysqli_fetch_assoc($result_current_params);
    $current_language_id = $row_current_params['language_id'];
    $content_hierarchy_ids = $row_current_params['language_root_content_id'];
    $content_is_default = $row_current_params['content_is_default'];
    $home_page_url = ($content_is_default == 1) ? "" : $row_current_params['content_hierarchy_path'];
    
    mysqli_free_result($result_current_params);
  }
  
  $query_categories = "SELECT `categories`.`category_hierarchy_ids`,`categories`.`category_parent_id`,`categories`.`category_hierarchy_level`,`categories`.`category_has_children`,
                              `category_descriptions`.`cd_name`,`category_descriptions`.`cd_page_title`,`category_descriptions`.`cd_pretty_url`,
                              `category_descriptions`.`cd_hierarchy_path`,`category_descriptions`.`cd_description`,
                              `category_descriptions`.`cd_meta_title`,`category_descriptions`.`cd_meta_description`,`category_descriptions`.`cd_meta_keywords`  
                         FROM `categories`
                   INNER JOIN `category_descriptions` USING(`category_id`)
                        WHERE `categories`.`category_id` = '$current_category_id' 
                          AND `categories`.`category_is_active` = '1' AND `category_descriptions`.`cd_is_active` = '1'
                          AND `category_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_categories."<br>";
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  $category_count = mysqli_num_rows($result_categories);
  $categories_array = array();
  $categories_ids = array();
  if($category_count > 0) {

    $category_row = mysqli_fetch_assoc($result_categories);
        
    $current_category_parent_id = $category_row['category_parent_id'];
    $category_hierarchy_ids = $category_row['category_hierarchy_ids'];
    $current_cd_name = $category_row['cd_name'];
    $cd_page_title = $category_row['cd_page_title'];
    if(empty($cd_page_title)) {
      $cd_page_title = $current_cd_name;
    }
    $cd_pretty_url = $category_row['cd_pretty_url'];
    $cd_url = str_replace(array('(',')'), array('',''), mb_convert_case(mb_strimwidth($cd_pretty_url, 0, 95,'', 'utf-8'), MB_CASE_LOWER, "UTF-8"));
    $cyr_url = cyrialize_url($cd_url);
    $cd_hierarchy_path = $category_row['cd_hierarchy_path'];
    $category_hierarchy_level = $category_row['category_hierarchy_level'];
    $cd_description = stripslashes($category_row['cd_description']);
    $cd_meta_title = $category_row['cd_meta_title'];
    $cd_meta_description = $category_row['cd_meta_description'];
    $cd_meta_keywords = $category_row['cd_meta_keywords'];
    
    mysqli_free_result($result_categories);
  }
  else {
    print_error_page();
  }
  if($cyr_url !== $current_page_pretty_url) {
    print_error_page();
  }
  //if(isset($_COOKIE['category_id'])) $current_category_id = $_COOKIE['category_id'];
  
  if($current_category_id == 17) {
    //$current_category_parent_id = "all";
  }
  $_SESSION['ccid'] = $current_category_id;
  $_SESSION['ccpid'] = $current_category_parent_id;
  
  if($current_category_parent_id === "all") {
    //echo "$current_category_parent_id";
    /*
     * if $current_category_parent_id == all that means the current selected category
     * is a parent category and we have to take all children's categories and take
     * all the products they have
     */
    $category_ids = array();
    $query_children = "SELECT `categories`.`category_id` 
                         FROM `categories` 
                   INNER JOIN `category_descriptions` USING(`category_id`)
                        WHERE `categories`.`category_parent_id` = '$current_category_id' 
                          AND `categories`.`category_is_active` = '1' AND `category_descriptions`.`cd_is_active` = '1'
                          AND `category_descriptions`.`language_id` = '$current_language_id'";
    $result_children = mysqli_query($db_link, $query_children);
    if(!$result_children) echo mysqli_error($db_link);
    $category_count = mysqli_num_rows($result_children);
    if($category_count > 0) {
 
      while($children_row = mysqli_fetch_assoc($result_children)) {
        $category_ids[] = $children_row['category_id'];
      }
      //print_array_for_debug($category_ids);
      mysqli_free_result($result_children);
    }
  }
  
  $category_has_products = check_if_category_has_products($current_category_id);
  $category_products_min_max_price = get_category_products_min_max_price($current_category_id);
  $min_product_price = $category_products_min_max_price['min_product_price'];
  $max_product_price = $category_products_min_max_price['max_product_price'];
  //echo "$min_product_price - $max_product_price - asfasf";
  
  $customer_id = isset($_SESSION['customer']['customer_id']) ? $_SESSION['customer']['customer_id'] : 0;
  $customer_wishlist = get_customer_wishlist($customer_id);
 
  if($current_category_id != 17 && !$category_has_products) {
    $robots_meta = "noindex,follow";
    $canonical_link = "";
  }
  $additional_css = false;
  $additional_js = '<script type="text/javascript" src="/frontstore/js/category.js"></script>';
  $additional_js .= '<script type="text/javascript" src="/frontstore/js/autoload/15-jquery.total-storage.min.js"></script>';
  $body_css = 'category';
  if(isset($page)) {
    if($page != 1) {
      $cd_page_title .= " {$languages[$current_lang]['text_page']} $page";
      $cd_meta_title .= " ({$languages[$current_lang]['text_page']} $page)";
      $cd_meta_description .= " ({$languages[$current_lang]['text_page']} $page)";
    }
    $url = "https://".urldecode($_SERVER['SERVER_NAME'])."/$current_lang/cid-$current_category_id/$current_page_pretty_url";
    $prev_page = $page-1;
    $prev_link = ($prev_page == 1) ? $url : "$url/page/$prev_page";
    $prev_meta_link = ($page != 1) ? '<link rel="prev" href="'.$prev_link.'" />' : "";
    $next_page = $page+1;
    $next_link = "$url/page/$next_page";
    $next_meta_link = '<link rel="next" href="'.$next_link.'" />';
    if(isset($_SESSION['page_count']) && $_SESSION['page_count'] == $page) {
      $next_meta_link = "";
    }
    $meta_links = $prev_meta_link.$next_meta_link;
  }
  
  $use_hreflang = false;
  $href_count = 0;
  $hreflang = "";

  foreach (get_languages() as $lang) {
    $lang_id = $lang['language_id'];
    $language_code = $lang['language_code']; 

    $query_categories = "SELECT `category_descriptions`.`cd_hierarchy_path` 
                           FROM `category_descriptions`
                          WHERE `category_descriptions`.`category_id` = '$current_category_id'
                            AND `category_descriptions`.`cd_is_active` = '1'
                            AND `category_descriptions`.`language_id` = '$lang_id'";
    //echo "$query_categories<br>";
    $result_categories = mysqli_query($db_link, $query_categories);
    if(!$result_categories) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_categories) > 0) {

      $hreflang_row = mysqli_fetch_assoc($result_categories);

      $hreflang_path = $hreflang_row['cd_hierarchy_path'];
      $hreflang_url = str_replace(array('(',')'), array('',''), mb_convert_case(mb_strimwidth($hreflang_path, 0, 95,'', 'utf-8'), MB_CASE_LOWER, "UTF-8"));
      $hreflang_url = cyrialize_url($hreflang_url);
      $href = BASEPATHNOSLASH."/$language_code/cid-$current_category_id/$hreflang_url";

      $hreflang .= "<link rel='alternate' hreflang='$language_code' href='$href' />\n";
      if($language_code == 'bg') {
        $hreflang .= "<link rel='alternate' hreflang='x-default' href='$href' />\n";
      }

      $href_count++;
     
      mysqli_free_result($result_categories);
    }
  }
  if($href_count > 1) $use_hreflang = true;
  
  print_html_header($cd_meta_title, $cd_meta_description, $cd_meta_keywords, $additional_css, $additional_js, $body_css);
?>
<!-- Content -->
<section id="columns" class="columns-container">
  <input type="hidden" name="cid" id="cid" value="<?=$current_category_id;?>" />
  <input type="hidden" name="cpid" id="cpid" value="<?=$current_category_parent_id;?>" />
  <div class="container">
    <div class="row">
      <!-- Center -->
      <div id="right_column" class="column sidebar col-md-3">
<?php
          if(!isset($current_category_parent_id)) {
            $category_hierarchy_ids_array = explode(".", $category_hierarchy_ids);
            $ids_count = count($category_hierarchy_ids_array);
            $current_category_parent_id = ($ids_count == 1) ?  $category_hierarchy_ids_array[0] : $category_hierarchy_ids_array[$ids_count-2];
          }

          if($current_category_parent_id != 0 || $current_category_parent_id == "all") {
            //echo $current_category_parent_id;
            print_category_submenu($current_category_id,$current_category_parent_id,$number_of_hierarchy_levels = 2);
          }
          
          if($min_product_price != $max_product_price) {
?>
        <div id="sort_by_price" class="block sort_by_price nopadding">
          <div class="title_block">
            <a href="" title="<?=$languages[$current_lang]['header_sort_by']." ".$languages[$current_lang]['header_price'];?>"><?=$languages[$current_lang]['header_sort_by']." ".$languages[$current_lang]['header_price'];?></a>
          </div>
          <div class="options_header">
            <p>
              <label for="amount"></label>
              <input type="text" id="amount" readonly style="border:0; color:#333333; font-weight:inherit;">
            </p>

            <div id="slider-range"></div>
            <input type="hidden" id="price_range_min" name="pmin" value="<?=$min_product_price;?>">
            <input type="hidden" id="price_range_max" name="pmax" value="<?=$max_product_price;?>">
          </div>
        </div>
<?php } ?>
        
        <!-- MODULE Block cart -->
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
              <!-- block list of products -->
              <div class="cart_block_list expanded">
                <?php print_shopping_cart() ?>
              </div>
            </div>
          </div><!-- .cart_block -->
          
        </div>
        <!-- /MODULE Block cart -->
              
      </div>
      
      <div id="center_column" class="col-md-9">
        <div id="breadcrumb" class="clearfix"> 

          <!-- Breadcrumb -->
          <div class="breadcrumb clearfix">
            <?php print_categories_breadcrumbs($category_hierarchy_ids, $current_cd_name, $is_category_page = true); ?>
          </div>
          <!-- /Breadcrumb --> 
        </div>
        
        <h1 class="page-heading product-listing"><?=$cd_page_title;?></h1>
<?php
        if(!isset($page) || (isset($page) && $page == 1)) {
          if(!empty($cd_description)) {
            $desc_length = strlen($cd_description);
            $class_hidden = ($desc_length < 680) ? " hidden" : "";
?>
        <div id="category_description" class="short content_sortPagiBar">
          <?=$cd_description;?> <span class="gradient<?=$class_hidden;?>">&nbsp;</span>
        </div>
        <a href="javascript:;" class="toggle_cat_desc more<?=$class_hidden;?>" onClick="ToggleDescDiv()"><?=$languages[$current_lang]['btn_show_more'];?> <i class="fa fa-arrow-down" aria-hidden="true"></i></a>
        <a href="javascript:;" class="toggle_cat_desc less hidden" onClick="ToggleDescDiv()"><?=$languages[$current_lang]['btn_show_less'];?> <i class="fa fa-arrow-up" aria-hidden="true"></i></a>
<?php
          }
        }
?>   
        <div class="content_sortPagiBar clearfix">
          <div class="sortPagiBar clearfix row">
            <div class="col-md-10 col-sm-8 col-xs-6">
              <div class="sort">
                <div class="display hidden-xs btn-group pull-left">
                  <div id="grid" class="selected">
                    <a rel="nofollow" href="#" title="Grid"><i class="fa fa-th-large"></i></a>
                  </div>
                  <div id="list">
                    <a rel="nofollow" href="#" title="List"><i class="fa fa-th-list"></i></a>
                  </div>
                </div>
                <div class="select options_header">
                  <label for="selectProductSort"><?=$languages[$current_lang]['header_sort_by'];?></label>
                  <select class="form-control" name="sort" id="selectProductSort" onchange="PushSortProductsState('sort','<?=$current_language_id;?>',this.value)">
                    <option value="date:desc" selected="selected"><?=$languages[$current_lang]['option_date_asc'];?></option>
                    <option value="price:asc"><?=$languages[$current_lang]['option_price_lowest_first'];?></option>
                    <option value="price:desc"><?=$languages[$current_lang]['option_price_highest_first'];?></option>
                    <option value="name:asc"><?=$languages[$current_lang]['option_name_a_z'];?></option>
                    <option value="name:desc"><?=$languages[$current_lang]['option_name_z_a'];?></option>
                  </select>
                    
                </div>
                <!-- /Sort products --> 

              </div>
            </div>
          </div>
        </div>

        <!-- Products list -->
        <div class="product_list grid">
<?php
          if($category_count > 0) {

            list_products_by_category($current_category_id,$offset,$current_page_path_string,$cd_pretty_url);
        
          } // if($category_count > 0)
          else {
            //must not happen :)
          }
?>
        </div>
      </div>
      <script>
        $(function() {
          bindGrid();
          // Bind to StateChange Event
          History.Adapter.bind(window, 'statechange', function() {
            var State = History.getState();

            // returns { data: { params: params }, title: "Search": url: "?search" }
            console.log(State);

            if(State.data.type == "pagination") {
              //console.log(State.data.offset);
              LoadPaginationProductsForCategory(State.data.offset);
            }
            else if(State.data.type== "sort") {
              //console.log(State.data.language_id);
              SortProductsByOptionValue(State.data.language_id, State.data.option);
            }
            else {
              LoadPaginationProductsForCategory('0');
            }
          });
          
//          $(".php_pagination a").bind('click', function() {
//            var offset = $(this).attr("data");
//            PushSortProductsState('pagination','<?=$current_language_id;?>',offset);
//            //LoadPaginationProductsForCategory(offset);
//          });
          
<?php if($min_product_price != $max_product_price) { ?>
          $("#slider-range").slider({
            range: true,
            min: <?=$min_product_price;?>,
            max: <?=$max_product_price;?>,
            values: [<?php if(isset($price_min)) echo $price_min; else echo $min_product_price;?>, <?php if(isset($price_max)) echo $price_max; else echo $max_product_price;?>],
            slide: function( event, ui ) {
              $("#amount").val(ui.values[0] + "лв. - " + ui.values[1] + "лв." );
              $("#price_range_min").val(ui.values[0]);
              $("#price_range_max").val(ui.values[1]);
            },
            change: function( event, ui ) {
              PushSortProductsState('sort','<?=$current_language_id;?>');
            }
          });
          $("#amount").val($("#slider-range").slider("values", 0) + "лв. - " + $("#slider-range").slider("values", 1 ) + "лв." );
<?php } ?>
        });
      </script>
      
    </div>
  </div>
</section>

<?php
  print_html_footer();
?>