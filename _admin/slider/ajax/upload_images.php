<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  //echo $_SERVER['DOCUMENT_ROOT'];exit;
  
  define ("MAX_FILE_SIZE","2048000");
  $valid_formats = array("jpg", "jpeg", "png", "gif");
  $upload_path = $_SERVER['DOCUMENT_ROOT']."/frontstore/images/slider/";

  $slider_id = $_POST['slider_id'];
  $slider_type = $_POST['slider_type'];
  $is_background = ($slider_type == "background") ? 1 : 0;
  $there_is_image = false;
  
  //echo"<pre>";print_r($_POST);print_r($_FILES);echo"</pre>";exit;
  
  //first delete old image
  $query_slider_image = "SELECT `name` FROM `slider_images` WHERE `slider_id` = '$slider_id' AND `is_background` = '$is_background'";
  //echo $query_slider_image;exit;
  $result_slider_image = mysqli_query($db_link, $query_slider_image);
  if(!$result_slider_image) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_slider_image) > 0) {
    
    $slider_image_row = mysqli_fetch_assoc($result_slider_image);
    $there_is_image = true;
    $slider_image = $slider_image_row['name'];

    $slider_image_exploded = explode(".", $slider_image);
    $current_slider_image_name = $slider_image_exploded[0];
    $current_slider_image_exstension = $slider_image_exploded[1];

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

  }

  //print_r($_FILES);exit;
  
  if(isset($_FILES['file']) && $_FILES['file']['error'] != 4) {
    $extension_array = explode("/", $_FILES['file']['type']);
    $extension = mb_convert_case($extension_array[1], MB_CASE_LOWER, "UTF-8");
    if(!in_array($extension, $valid_formats)) {
      echo "Не е позлволено качването на снимка с разширение $extension<br>";
      exit;
    }

    if((isset($_FILES['file'])) && ($_FILES['file']['size'] < MAX_FILE_SIZE) && ($_FILES['file']['error'] == 0)) {
      // no error
      $slider_image_tmp_name  = $_FILES['file']['tmp_name'];
      $slider_image_name_orig = $_FILES['file']['name'];
      $slider_image_name_exploded = explode(".", $slider_image_name_orig);
      $slider_image_name = str_replace(" ", "-", $slider_image_name_exploded[0]);
      $slider_image_exstension = mb_convert_case($slider_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      $slider_image_name_orig = $slider_image_name.".".$slider_image_exstension;
      //echo $upload_path;
    }
    elseif((isset($_FILES['file'])) && ($_FILES['file']['size'] > MAX_FILE_SIZE) || ($_FILES['file']['error'] == 1 || $_FILES['file']['error'] == 2)) {
      echo "You have exceeded the size limit! Please choose a default image smaller then 4MB<br>";
        exit;
    }
    else {
      if($_FILES['file']['error'] != 4) { // error 4 means no file was uploaded
        echo "An error occured while uploading the file<br>";
        exit;
      }
    }

    if($there_is_image) {
      $query_update_product = "UPDATE `slider_images` SET `name` = '$slider_image_name_orig' WHERE `slider_id` = '$slider_id' AND `is_background` = '$is_background'";
      $result_update_product = mysqli_query($db_link, $query_update_product);
      if(!$result_update_product) {
        echo $languages[$current_lang]['sql_error_update']." - 1 ".mysqli_error($db_link);
        exit;
      }
    }
    else {
      $query_slider_images = "INSERT INTO `slider_images`(`id`, `slider_id`, `name`, `is_background`, `sort_order`)
                                                  VALUES ('','$slider_id','$slider_image_name_orig','$is_background','1')";
      $result_slider_images = mysqli_query($db_link, $query_slider_images);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - slider_images ".mysqli_error($db_link);
        exit;
      }
    }
      
    if(is_uploaded_file($slider_image_tmp_name)) {
      move_uploaded_file($slider_image_tmp_name, $upload_path.$slider_image_name_orig);
    }
    else {
      echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
      exit;
    }
    
    save_slider_image($slider_image_name_orig,$slider_image_name,$slider_image_exstension,$slider_type);
    
  }