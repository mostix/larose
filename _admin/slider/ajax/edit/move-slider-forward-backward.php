<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['slider_id'])) {
    $slider_id =  $_POST['slider_id'];
  }
  if(isset($_POST['slider_sort_order'])) {
    $slider_sort_order =  $_POST['slider_sort_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($slider_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_slider_sort_order = $slider_sort_order-1;
      $query_update_slider_1 = "UPDATE `sliders` SET `slider_sort_order`='$slider_sort_order' WHERE `slider_sort_order` = '$previous_slider_sort_order'";
      $all_queries .= "\n".$query_update_slider_1;
        //echo $query_update_slider_1;
      $result_update_slider_1 = mysqli_query($db_link, $query_update_slider_1);
      if(!$result_update_slider_1) {
        echo $sliders[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_slider_2 = "UPDATE `sliders` SET `slider_sort_order`='$previous_slider_sort_order' WHERE `slider_id` = '$slider_id'";
      $all_queries .= "\n".$query_update_slider_2;
        //echo $query_update_slider_2;
      $result_update_slider_2 = mysqli_query($db_link, $query_update_slider_2);
      if(!$result_update_slider_2) {
        echo $sliders[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_slider_sort_order = $slider_sort_order+1;
      $query_update_slider_1 = "UPDATE `sliders` SET `slider_sort_order`='$slider_sort_order' WHERE `slider_sort_order` = '$next_slider_sort_order'";
      $all_queries .= "\n".$query_update_slider_1;
        //echo $query_update_slider_1;
      $result_update_slider_1 = mysqli_query($db_link, $query_update_slider_1);
      if(!$result_update_slider_1) {
        echo $sliders[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_slider_2 = "UPDATE `sliders` SET `slider_sort_order`='$next_slider_sort_order' WHERE `slider_id` = '$slider_id'";
      $all_queries .= "\n".$query_update_slider_2;
        //echo $query_update_slider_2;
      $result_update_slider_2 = mysqli_query($db_link, $query_update_slider_2);
      if(!$result_update_slider_2) {
        echo $sliders[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    list_sliders();

  }
?>
