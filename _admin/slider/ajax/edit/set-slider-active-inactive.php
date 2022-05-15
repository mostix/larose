<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['slider_id'])) {
    $slider_id =  $_POST['slider_id'];
  }
  if(isset($_POST['set_slider'])) {
    $set_slider =  $_POST['set_slider'];
  }
  
  if(!empty($slider_id)) {
 
    $query_update_slider = "UPDATE `sliders` SET  `slider_is_active`='$set_slider' WHERE `slider_id` = '$slider_id'";
 
    //echo $query_update_slider;
    $result_update_slider = mysqli_query($db_link, $query_update_slider);
    if(!$result_update_slider) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>