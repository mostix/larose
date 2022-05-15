<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  //echo $_SERVER['DOCUMENT_ROOT'];exit;
  
  define ("MAX_FILE_SIZE","2048000");
  $valid_formats = array("jpg", "jpeg", "png", "gif");
  $upload_path = "";

  $slider_id = $_POST['slider_id'];
  $slider_type = $_POST['slider_type'];
  $is_background = ($slider_type == "background") ? 1 : 0;
  
  //echo"<pre>";print_r($_POST);print_r($_FILES);echo"</pre>";exit;
  
  //first delete old image
  $query_slider_image = "SELECT `name` FROM `slider_images` WHERE `slider_id` = '$slider_id' AND `is_background` = '$is_background'";
  //echo $query_slider_image;exit;
  $result_slider_image = mysqli_query($db_link, $query_slider_image);
  if(!$result_slider_image) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_slider_image) > 0) {
    
    $slider_image_row = mysqli_fetch_assoc($result_slider_image);
      
    $slider_image = $slider_image_row['name'];

    $slider_image_exploded = explode(".", $slider_image);
    $current_slider_image_name = $slider_image_exploded[0];
    $current_slider_image_exstension = $slider_image_exploded[1];
    $upload_path = $_SERVER['DOCUMENT_ROOT']."/frontstore/images/slider/";

    $file = $upload_path.$slider_image;

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
    
    $query_delete_img = "DELETE FROM `slider_images` WHERE `slider_id` = '$slider_id' AND `is_background` = '$is_background'";
    $result_delete_img = mysqli_query($db_link, $query_delete_img);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
      exit;
    }

  }

  //print_r($_FILES);exit;