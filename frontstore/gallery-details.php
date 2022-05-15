<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  if(isset($_GET['gid'])) {
    $current_gallery_id = mysqli_real_escape_string($db_link,intval($_GET['gid'])); // current selected gallery
  }
  if(!isset($current_gallery_id) && isset($directory_id)) $current_gallery_id = mysqli_real_escape_string($db_link,intval($directory_id));
  
  if(isset($_GET['page']) && !empty($_GET['page'])) {
    //$current_gallery_path = $_GET['page'];
    
    $current_page_path_string = mysqli_real_escape_string($db_link,strip_tags($_GET['page']));
    //echo $current_page_path_string;
    $current_gallery_path = explode("/", $current_page_path_string);
    $count_gallery_path_elements = count($current_gallery_path)-1;
    $current_gallery_pretty_url = $current_gallery_path[$count_gallery_path_elements];
    $current_lang = $current_gallery_path[0];
    $current_gallery_name = str_replace("-", " ", $current_gallery_path[$count_gallery_path_elements]);
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
  
  $content_type_id = 8; // galleries
  $query_content = "SELECT `content_hierarchy_ids`,`content_name`,`content_pretty_url`
                    FROM `contents`
                    WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    
    $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
    $content_name = $content_array['content_name'];
    $content_pretty_url = $content_array['content_pretty_url'];
  }
  
  $gallery_text = get_gallery_text($current_gallery_id);
  $meta_title = "$gallery_text - LaRose";
  if($current_gallery_id == 4) $gallery_text = $current_gallery_name;
  
  print_html_header($meta_title, "", "",$additional_css = false, $additional_js = false, $body_css = 'gallery');
  //echo "<pre>";print_r($_SERVER);
  //echo "<pre>";print_r($_SESSION);
?>
  <section id="columns" class="columns-container">
    <div class="container content cart">
      <div class="row">
        <section id="center_column" class="col-sp-12 col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div id="breadcrumb" class="clearfix"> 

            <!-- Breadcrumb -->
            <div class="breadcrumb clearfix">
              <a class="home" href="/" title="Доставка на цветя"><img src="/frontstore/images/home.png" width="12" height="12" alt="Доставка на цветя"></a>
              <span class="navigation-pipe">&gt;</span>
              <a href="/<?=$current_lang;?>/<?=$content_pretty_url;?>" title="<?=$content_name;?>"><?=$content_name;?></a>
              <span class="navigation-pipe">&gt;</span>
              <?=$current_gallery_name;?>            
            </div>
            <!-- /Breadcrumb -->
            
          </div>
          
          <h1 class="page-heading"><?=$gallery_text;?></h1>
          <div id="contentbottom" class="no-border clearfix block">
<?php
          $images_counter = 1;
          $gi_names_array = get_gallery_images($current_gallery_id);
          $gallery_images_folder = "/frontstore/images/galleries/";

          if(count($gi_names_array) > 0) {

            foreach($gi_names_array as $key => $prod_gallery_image) {

              if($images_counter == 1) echo "<div class='row'>";
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
              <div class="image-item col-lg-1 col-md-1 col-sm-3 col-xs-4">
                <a class="fancybox" rel="gal_details" href= "<?=$gallery_image_orig;?>" title="<?=$gallery_image_title;?>">
                  <img class="img-responsive" src="<?=$gallery_img_path_small?>" <?=$gallery_img_dimensions?> />
                </a>
              </div>
<?php
              if($images_counter == 12) {
                echo "</div>";
                $images_counter = 0;
              }

              $images_counter++;
            }

          } //if(count($gi_names_array) > 0)
?>
          </div>
        </section>
        <section id="right_column" class="column sidebar col-md-0">
    
        </section>
      </div>
    </div>
  </section>
<?php
  
  print_html_footer();
?>