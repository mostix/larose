<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['slider_id'])) {
    $slider_id = $_POST['slider_id'];
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $all_queries = "";
  
  $query = "DELETE FROM `sliders` WHERE `slider_id` = '$slider_id'";
  $all_queries .= $query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `sliders_descriptions` WHERE `slider_id` = '$slider_id'";
  $all_queries .= $query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  //delete images
  $query_slider_details = "SELECT `name`
                            FROM `slider_images`
                            WHERE `slider_id` = '$slider_id'";
  $all_queries .= $query_slider_details."\n";
  //echo $query_slider_details;exit;
  $result_slider_details = mysqli_query($db_link, $query_slider_details);
  if(!$result_slider_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_slider_details) > 0) {
    
    while($slider_details = mysqli_fetch_assoc($result_slider_details)) {
      
      $slider_image = $slider_details['name'];
      
      $slider_image_exploded = explode(".", $slider_image);
      $current_slider_image_name = $slider_image_exploded[0];
      $current_slider_image_exstension = $slider_image_exploded[1];
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/frontstore/images/slider/";

      $file = $upload_path.$slider_image;

      //echo "$file/n<br>";
      if(file_exists($file)) {
        unlink($file);
      }

      $image_admin_thumb_name = $current_slider_image_name."_admin_thumb.".$current_slider_image_exstension;
      $image_admin_thumb = "$upload_path$image_admin_thumb_name";

      if(file_exists($image_admin_thumb)) {
        unlink($image_admin_thumb);
      }

      $image_frontstore_name = $current_slider_image_name."_frontstore.".$current_slider_image_exstension;
      $image_frontstore = "$upload_path$image_frontstore_name";

      if(file_exists($image_frontstore)) {
        unlink($image_frontstore);
      }
    }
    
    $query = "DELETE FROM `slider_images` WHERE `slider_id` = '$slider_id'";
    $all_queries .= $query."\n";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
  }
  
    
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
