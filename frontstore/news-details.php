<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  if(isset($_GET['nid'])) {
    $current_news_id = mysqli_real_escape_string($db_link,intval($_GET['nid'])); // current selected news
  }
  if(!isset($current_news_id) && isset($directory_id)) $current_news_id = mysqli_real_escape_string($db_link,intval($directory_id));
  
  if(isset($_GET['page']) && !empty($_GET['page'])) {
    //$current_category_path = $_GET['page'];
    
    $current_page_path_string = mysqli_real_escape_string($db_link,strip_tags($_GET['page']));
    //echo $current_page_path_string;
    $current_page_path_exploded = explode("/", $current_page_path_string);
    $count_path_elements = count($current_page_path_exploded)-1;
    $current_news_pretty_url = $current_page_path_exploded[$count_path_elements];
    $current_lang = $current_page_path_exploded[0];
    $current_news_page = $current_page_path_exploded[0]."/".$current_page_path_exploded[1];
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
  
  $query_news = "SELECT `news`.`news_id`,`news`.`news_post_date`,`news`.`news_image`,`news_descriptions`.`news_title`,`news_descriptions`.`news_summary`,
                        `news_descriptions`.`news_text` 
                    FROM `news` 
                    INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                    WHERE `news`.`news_id` = '$current_news_id' AND `news_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_news;exit;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news) > 0) {
    $news_row = mysqli_fetch_assoc($result_news);

    $news_id = $news_row['news_id'];
    $news_post_date_day = date("d", strtotime($news_row['news_post_date']));
    $news_post_date_month_text = "text_date_month_".date("m", strtotime($news_row['news_post_date']));
    $news_post_date_month = $languages[$current_lang][$news_post_date_month_text];
    $news_post_date_year = date("Y", strtotime($news_row['news_post_date']));
    $news_images_folder = "/frontstore/images/news/";
    $news_image = $news_images_folder.$news_row['news_image'];
    $news_image_splitted = explode(".", $news_row['news_image']);
    $news_image_name = $news_image_splitted[0];
    $news_image_ext = $news_image_splitted[1];
    $fb_image = $_SERVER['SERVER_NAME'].$news_images_folder.$news_image_name."_thumb.".$news_image_ext;
    @$image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$news_image);
    $image_dimensions = @$image_params[3];
    $news_title = $news_row['news_title'];
    $news_summary = stripslashes($news_row['news_summary']);
    $news_text = stripslashes($news_row['news_text']);
  }
  
  print_html_header($news_title, $news_summary, "");
  //echo "<pre>";print_r($_SERVER);
  //echo "<pre>";print_r($_SESSION);
?>
  <div class="container content">
    
    <div style="padding-top: 8px;">&nbsp;</div>
    <h1 class="page-heading"><?=$news_title;?></h1>
    
<!--main-->
    <main>
      
      <div class="news_details clearfix">
        <img src="<?=$news_image;?>" alt="<?=$news_title;?>" title="<?=$news_title;?>" width="100%">
        <div class="news_postdate">
          <?=$languages[$current_lang]['text_published'].": $news_post_date_day $news_post_date_month $news_post_date_year";?>
        </div>
        <div class="news_text clearfix">
          <?=$news_text;?>
        </div>
        <div class="news_back_link">
          <a href="/<?=$current_news_page;?>">
            <i class="fa fa-angle-double-left fa-lg"></i> <?=$languages[$current_lang]['btn_back_to_all_news'];?>
          </a>
        </div>
      </div>
      
    </main>
<!--main-->
  
  </div>
<?php
  print_html_footer();
?>