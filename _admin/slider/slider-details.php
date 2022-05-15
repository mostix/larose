<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: sliders.php');
  }
  
  $languages_array = array();
  $query_languages = "SELECT `language_id`,`language_code`,`language_menu_name` 
                        FROM `languages` 
                       WHERE `language_is_active` = '1' 
                    ORDER BY `language_menu_order` ASC";
  $result_languages = mysqli_query($db_link, $query_languages);
  if (!$result_languages) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_languages) > 0) {
    while($row_languages = mysqli_fetch_assoc($result_languages)) {
      $languages_array[] = $row_languages; 
    }
  }
  
  if(isset($_POST['edit_slider'])) {
    
    //echo"<pre>";print_r($_POST);
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['slider_header'] as $language_id => $slider_header) {
//      if(empty($slider_header)) $slider_errors['slider_header'][$language_id] = $languages[$current_lang]['required_field_error'];
//      if(empty($_POST['slider_text'][$language_id])) $slider_errors['slider_text'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $slider_headers_array[$language_id] = $_POST['slider_header'][$language_id];
      $slider_texts_array[$language_id] = $_POST['slider_text'][$language_id];
      $slider_links_array[$language_id] = $_POST['slider_link'][$language_id];
      $slider_img_alts_array[$language_id] = $_POST['slider_img_alt'][$language_id];
      $slider_img_titles_array[$language_id] = $_POST['slider_img_title'][$language_id];
    }
    
    $slider_is_active = 0;
    if(isset($_POST['slider_is_active'])) $slider_is_active = 1;
    
    if(!isset($slider_errors)) {
      //if there are no form errors we can insert the information
      
      $query_update_slider = "UPDATE `sliders` SET `slider_is_active`='$slider_is_active' WHERE `slider_id` = '$current_slider_id'";
      $all_queries .= "<br>".$query_update_slider;
      $result_update_slider = mysqli_query($db_link, $query_update_slider);
      if(!$result_update_slider) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      foreach($slider_headers_array as $language_id => $slider_header) {
        
        $slider_header_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $slider_header));
        $slider_text_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $slider_texts_array[$language_id]));
        $slider_link_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $slider_links_array[$language_id]));
        $slider_img_alt_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $slider_img_alts_array[$language_id]));
        $slider_img_title_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $slider_img_titles_array[$language_id]));
      
        if(isset($_POST['no_record'][$language_id])) {
          $query_insert_slider_desc = "INSERT INTO `sliders_descriptions`(`slider_id`, 
                                                                        `language_id`, 
                                                                        `slider_header`, 
                                                                        `slider_text`, 
                                                                        `slider_link`, 
                                                                        `slider_img_alt`, 
                                                                        `slider_img_title`) 
                                                                  VALUES ('$current_slider_id',
                                                                        '$language_id',
                                                                        $slider_header_db,
                                                                        $slider_text_db,
                                                                        $slider_link_db,
                                                                        $slider_img_alt_db,
                                                                        $slider_img_title_db)";
          $all_queries .= "<br>".$query_insert_slider_desc;
          $result_insert_slider_desc = mysqli_query($db_link, $query_insert_slider_desc);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages[$current_lang]['sql_error_insert']." - sliders_descriptions ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        else {
          $query_update_slider_desc = "UPDATE `sliders_descriptions` SET `slider_header`=$slider_header_db,
                                                                          `slider_text`=$slider_text_db, 
                                                                          `slider_link`=$slider_link_db, 
                                                                          `slider_img_alt`=$slider_img_alt_db, 
                                                                          `slider_img_title`=$slider_img_title_db 
                                                              WHERE `slider_id` = '$current_slider_id' AND `language_id` = '$language_id'";
          $all_queries .= "<br>".$query_update_slider_desc;
          $result_update_slider_desc = mysqli_query($db_link, $query_update_slider_desc);
          if(!$result_update_slider_desc) {
            echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: sliders.php');
      
    } //if(!isset($slider_errors))
      
  } //if(isset($_POST['submit'])
  else {
    $query_slider_details = "SELECT `slider_id`,`slider_is_active`,`slider_sort_order`
                               FROM `sliders`
                              WHERE `slider_id` = '$current_slider_id'";
    //echo $query_slider_details;exit;
    $result_slider_details = mysqli_query($db_link, $query_slider_details);
    if(!$result_slider_details) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_slider_details) > 0) {
      $slider_details = mysqli_fetch_assoc($result_slider_details);

      $slider_id = $slider_details['slider_id'];
      $slider_is_active = $slider_details['slider_is_active'];
    }
  }
  
  $page_title = $languages[$current_lang]['slider_details_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/slider/sliders.php"><?=$languages[$current_lang]['header_sliders'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_slider_edit'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_slider_edit'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF']."?slider_id=$current_slider_id";?>">
        <p>
          <button type="submit" name="edit_slider" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </p>
        
        <p><i class="info"><?=$languages[$current_lang]['required_fields'];?></i></p>
        
        <ul id="languages" class="language_tabs tabs">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $row_languages) {

            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];
?>
            <li>
              <a href="#<?=$language_code;?>">
                <img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" /> <?=$language_menu_name;?>
              </a>
            </li>
<?php
  }
}
?>
        </ul>
<?php
      if(!empty($languages_array)) {
        
        foreach($languages_array as $key => $row_languages) {

          $language_id = $row_languages['language_id'];
          $language_code = $row_languages['language_code'];
          $no_record = "";
          
          $query_slider_desc = "SELECT `slider_header`,`slider_text` ,`slider_link`,`slider_img_alt`,`slider_img_title`
                                  FROM `sliders_descriptions` 
                                 WHERE `slider_id` = '$current_slider_id' AND `language_id` = '$language_id'";
          $result_slider_desc = mysqli_query($db_link, $query_slider_desc);
          if(!$result_slider_desc) { echo mysqli_error($db_link); }
          if(mysqli_num_rows($result_slider_desc) > 0) {
            $slider_desc = mysqli_fetch_assoc($result_slider_desc);
            
            $slider_headers_array[$language_id] = $slider_desc['slider_header'];
            $slider_texts_array[$language_id] = $slider_desc['slider_text'];
            $slider_links_array[$language_id] = $slider_desc['slider_link'];
            $slider_img_alts_array[$language_id] = $slider_desc['slider_img_alt'];
            $slider_img_titles_array[$language_id] = $slider_desc['slider_img_title'];
          }
          else {
            $no_record = '<input type="hidden" name="no_record['.$language_id.']" value="1" >';
          }
          
?>
        <div id="<?=$language_code;?>" class="language_tab tab">
          <?=$no_record;?>
          <div>
            <label for="slider_header" class="title"><?=$languages[$current_lang]['header_slider_header'];?></label>
            <?php
              if(isset($slider_errors['slider_header'][$language_id])) {
                echo "<div class='error'>".$slider_errors['slider_header'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="slider_header[<?=$language_id;?>]" class="slider_header" style="width: 400px;" value="<?php if(isset($slider_headers_array[$language_id])) { echo $slider_headers_array[$language_id]; }?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="slider_link" class="title"><?=$languages[$current_lang]['header_slider_link'];?></label>
            <?php
              if(isset($slider_errors['slider_link'][$language_id])) {
                echo "<div class='error'>".$slider_errors['slider_link'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="slider_link[<?=$language_id;?>]" class="slider_link" style="width: 400px;" value="<?php if(isset($slider_links_array[$language_id])) echo $slider_links_array[$language_id];?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="slider_img_alt" class="title">Мета Alt на снимката</label>
            <?php
              if(isset($slider_errors['slider_img_alt'][$language_id])) {
                echo "<div class='error'>".$slider_errors['slider_img_alt'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="slider_img_alt[<?=$language_id;?>]" class="slider_img_alt" style="width: 400px;" value="<?php if(isset($slider_img_alts_array[$language_id])) echo $slider_img_alts_array[$language_id];?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="slider_img_title" class="title">Мета Title на снимката</label>
            <?php
              if(isset($slider_errors['slider_img_title'][$language_id])) {
                echo "<div class='error'>".$slider_errors['slider_img_title'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="slider_img_title[<?=$language_id;?>]" class="slider_img_title" style="width: 400px;" value="<?php if(isset($slider_img_titles_array[$language_id])) echo $slider_img_titles_array[$language_id];?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="slider_text" class="title"><?=$languages[$current_lang]['header_slider_text'];?></label>
            <?php
              if(isset($slider_errors['slider_text'][$language_id])) {
                echo "<div class='error'>".$slider_errors['slider_text'][$language_id]."</div>";
              }
            ?>
            <textarea name="slider_text[<?=$language_id;?>]" id="ckeditor_slider_text_<?=$language_code;?>" class="default_text"><?php if(isset($slider_texts_array[$language_id])) echo $slider_texts_array[$language_id];?></textarea>
          </div>
          <div class="clearfix"></div>
        </div>
<?php
    }
  }
?>
        <div>
          <label for="slider_is_active" class="title"><?=$languages[$current_lang]['header_status'];?></label>
          <?php
            if(isset($slider_is_active)) {
              if($slider_is_active == 0) echo '<input type="checkbox" name="slider_is_active" id="slider_is_active" />';
              else echo '<input type="checkbox" name="slider_is_active" id="slider_is_active" checked="checked" />';
            }
            else echo '<input type="checkbox" name="slider_is_active" id="slider_is_active" checked="checked" />';
          ?>
        </div>
        <div class="clearfix"><p>&nbsp;</p></div>
<?php
      $query_slider_details = "SELECT `name`, `is_background` FROM `slider_images` WHERE `slider_id` = '$current_slider_id'";
      //echo $query_slider_details;exit;
      $result_slider_details = mysqli_query($db_link, $query_slider_details);
      if(!$result_slider_details) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_slider_details) > 0) {

        $there_is_forground_image = false;
        
        while($image_row = mysqli_fetch_assoc($result_slider_details)){
          $is_background = $image_row['is_background'];
          $slider_image = $image_row['name'];
          if($is_background == 1) {
            $slider_background_image_name_exploded = explode(".", $slider_image);
            $slider_background_image_name = $slider_background_image_name_exploded[0];
            $slider_background_image_exstension = mb_convert_case($slider_background_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
            $slider_background_image_name_orig = "/frontstore/images/slider/".$slider_background_image_name."_admin_thumb.".$slider_background_image_exstension;
            @$slider_background_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$slider_background_image_name_orig);
            $slider_background_dimensions = $slider_background_params[3];
          }
          else {
            $slider_forground_image_name_exploded = explode(".", $slider_image);
            $slider_forground_image_name = $slider_forground_image_name_exploded[0];
            $slider_forground_image_exstension = mb_convert_case($slider_forground_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
            $slider_forground_image_name_orig = "/frontstore/images/slider/".$slider_forground_image_name."_admin_thumb.".$slider_forground_image_exstension;
            @$slider_forground_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$slider_forground_image_name_orig);
            $slider_forground_dimensions = $slider_forground_params[3];
            $there_is_forground_image = true;
          }
        }

      }
?>
        <div id="dropzone_box_holder" class="clearfix"></div>
        
        <div>
          <button type="submit" name="edit_slider" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        <input type="hidden" id="text_drag_and_drop_upload" value="<?=$languages[$current_lang]['text_drag_and_drop_upload'];?>" >
        
      </form>
      
      <div id="dropzone_box">
        <div id="dropzone_background_image" style="position: relative;padding-bottom: 300px;">
          <h2><?=$languages[$current_lang]['header_current_background_image'];?></h2>
          <p></p>
          <p><i class="info"><?=$languages[$current_lang]['info_slider_image']." ".$languages[$current_lang]['btn_save'];?></i></p>
          <div id="current_background_image">
            <img src="<?=$slider_background_image_name_orig;?>" <?=$slider_background_dimensions;?>>
          </div>
          <p>&nbsp;</p>
          <h2><?=$languages[$current_lang]['header_change_background_image'];?> (892x343px)</h2>
          <form action="ajax/upload_images.php" id="filedrop" class="dropzone" style="display: block;">
            <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_background_image_success" value="<?=$languages[$current_lang]['ajaxmessage_update_background_image_success'];?>" >
            <input type="hidden" name="slider_id" class="slider_id" value="<?=$current_slider_id;?>" >
            <input type="hidden" name="slider_type" class="slider_type" value="background" >
          </form>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
          
        <div id="dropzone_forground_image" style="position: relative;padding-bottom: 300px;">
          <h2><?=$languages[$current_lang]['header_current_forground_image'];?></h2>
          <p></p>
          <p><i class="info"><?=$languages[$current_lang]['info_slider_image']." ".$languages[$current_lang]['btn_save'];?></i></p>
          <div id="current_forground_image">
        <?php
            if($there_is_forground_image) {
        ?>
            <a href="javscript:;" onClick="DeleteSliderImage('dropzone_forground_image')" class="delete_forground_image button red">
              <?=$languages[$current_lang]['btn_delete'];?>
            </a><br><br>
            <img src='<?=$slider_forground_image_name_orig?>' <?=$slider_forground_dimensions?>>
        <?php
            }
        ?>
          </div>
          <p>&nbsp;</p>
          <h2><?=$languages[$current_lang]['header_change_forground_image'];?></h2>
          <form action="ajax/upload_images.php" id="filedrop-forground" class="dropzone" style="display: block;">
            <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_forground_image_success" value="<?=$languages[$current_lang]['ajaxmessage_update_forground_image_success'];?>" >
            <input type="hidden" name="slider_id" class="slider_id" value="<?=$current_slider_id;?>" >
            <input type="hidden" name="slider_type" class="slider_type" value="forground" >
          </form>
          <div class="clearfix"></div>
        </div>
      </div>
      
    </div>
  </main>
<!--navigation-->

<?php
 
  print_html_admin_footer();
  
?>
  <script type="text/javascript" src="/_admin/filemanager/ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      CalculateDropzoneBoxHeight();
      
      Dropzone.options.filedrop = {
        dictDefaultMessage: $("#text_drag_and_drop_upload").val(),
        init: function () {
          this.on("complete", function (file) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
              GetSliderImage('<?=$current_slider_id;?>','background');
            }
            this.removeFile(file);
          });
          this.on("success", function(file, responseText) {
            if(responseText == "" || responseText == " ") {
              
            }
            else {
              alert(responseText);
              this.removeFile(file);
            }
          });
        }
      };
      
      Dropzone.options.filedropForground = {
        dictDefaultMessage: $("#text_drag_and_drop_upload").val(),
        init: function () {
          this.on("complete", function (file) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
              GetSliderImage('<?=$current_slider_id;?>','forground');
            }
            this.removeFile(file);
          });
          this.on("success", function(file, responseText) {
            if(responseText == "" || responseText == " ") {
              
            }
            else {
              alert(responseText);
              this.removeFile(file);
            }
          });
        }
      };
<?php
    if(!empty($languages_array)) {
      foreach($languages_array as $row_languages) {

        $language_code = $row_languages['language_code'];
?>
        CKEDITOR.replace('ckeditor_slider_text_<?=$language_code;?>');
<?php
      }
    }
?>
      // language tab switcher
      $(".language_tabs li").removeClass("active");
      $(".language_tab").hide();
      $(".language_tabs li:first").addClass("active");
      $(".language_tab:first").show();
      $(".language_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".language_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".language_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end language tab switcher
    });
  </script>
</body>
</html>