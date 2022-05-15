<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  $customer_id = $_SESSION['customer']['customer_id'];
  
  $customer_address_id =  $_GET['caid'];
  $all_queries = "";
  
  $query_customer_address = "SELECT `customer_address_firstname`,`customer_address_lastname`,`customer_address_site_id`,
                                      `customer_address_street`,`customer_address_info`,`customer_address_postcode`,`customer_address_city`,`customer_address_phone`,
                                      `customer_address_is_default`
                              FROM `customers_addresses` 
                              WHERE `customer_address_id` = '$customer_address_id'";
  //echo $query_customer_address;
  $result_customer_address = mysqli_query($db_link, $query_customer_address);
  if(!$result_customer_address) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_customer_address) > 0) {
    $customer_address = mysqli_fetch_assoc($result_customer_address);
    $customer_address_firstname = $customer_address['customer_address_firstname'];
    $customer_address_lastname = $customer_address['customer_address_lastname'];
    $customer_address_site_id = $customer_address['customer_address_site_id'];
    $customer_address_city = $customer_address['customer_address_city'];
    $customer_address_postcode = ($customer_address['customer_address_postcode'] === "NULL") ? "" : $customer_address['customer_address_postcode'];
    
    if($customer_address_site_id != 0) {
      $query_customer_addresses_speedy = "SELECT `site_type`, `site_name`, `site_postcode` FROM `sites` WHERE `site_id` = '$customer_address_site_id'";
      $result_customer_addresses_speedy = mysqli_query($db_link, $query_customer_addresses_speedy);
      if(!$result_customer_addresses_speedy) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_customer_addresses_speedy) > 0) {
        $addresses_speedy = mysqli_fetch_assoc($result_customer_addresses_speedy);

        $customer_address_site_type = $addresses_speedy['site_type'];
        $customer_address_site_name = $addresses_speedy['site_name'];
        $customer_address_site_postcode = $addresses_speedy['site_postcode'];
      }
    }
    $customer_address_street = stripslashes($customer_address['customer_address_street']);
    $customer_address_info = empty ($customer_address['customer_address_info']) ? "" : $customer_address['customer_address_info'];
    $customer_address_phone = $customer_address['customer_address_phone'];
    $customer_address_is_default = $customer_address['customer_address_is_default'];
  }
  
  if(isset($_POST['update_customer_address_bg'])) {
    //echo "<pre>";print_r($_POST);exit;

    mysqli_query($db_link,"BEGIN");

    //we will check if all the fields are filled in at all
    foreach($_POST as $name => $value) {
      $trimed_value = trim($value);
      if(empty($trimed_value) && ($name != "update_customer_address_bg" && $name != "customer_address_site_name_label" && $name != "customer_address_site_id" && $name != "customer_address_info" && $name != "customer_address_is_default")) {
        $field_name = "header_".$name;
        $field_name_text = mb_convert_case($languages[$current_lang][$field_name], MB_CASE_LOWER, "UTF-8");
        $errors[$name] = $languages[$current_lang]['error_registration_empty_field'].$field_name_text;
      }
    }
    
    $customer_address_firstname = $_POST['customer_address_firstname'];
    $customer_address_lastname = $_POST['customer_address_lastname'];
    $customer_address_site_id = $_POST['customer_address_site_id'];
    $customer_address_site_name = $_POST['customer_address_site_name'];
    $customer_address_street = $_POST['customer_address_street'];
    $customer_address_info = $_POST['customer_address_info'];
    $customer_address_site_postcode = "NULL";
    $customer_address_phone = $_POST['customer_address_phone'];
    $customer_address_is_default = 0;
    if(isset($_POST['customer_address_is_default'])) $customer_address_is_default = 1;
    
    //if all the requered fields was filled in correct by the user make a database record
    if(empty($errors) && !empty($customer_id)) {
      
      if($customer_address_is_default == 1) {
        
        /*
         * if the user wants to make some customer's address default
         * we have to make the one that is already default - not default
         */
        
        $query_select_default = "SELECT `customer_address_id` FROM `customers_addresses` WHERE `customer_id` = '$customer_id' AND `customer_address_is_default` = '1'";
        $all_queries .= $query_select_default."<br>\n";
        //echo $query_select_defaultc;exit;
        $result_select_default = mysqli_query($db_link, $query_select_default);
        if(!$result_select_default) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_select_default) > 0) {
          
          $query_update_default_addr = "UPDATE `customers_addresses` SET `customer_address_is_default` = '0' WHERE `customer_id` = '$customer_id' AND `customer_address_is_default` = '1'";
          $all_queries .= $query_update_default_addr."<br>\n";
          $result_update_default_addr = mysqli_query($db_link, $query_update_default_addr);
          if(!$result_update_default_addr) {
            echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }
      
      $customer_address_street = mysqli_real_escape_string($db_link,$customer_address_street);
      $customer_address_info = prepare_for_null_row(mysqli_real_escape_string($db_link,$customer_address_info));

      $query_update_user_address = "UPDATE `customers_addresses` SET `customer_address_firstname`='$customer_address_firstname',
                                                                    `customer_address_lastname`='$customer_address_lastname',
                                                                    `customer_address_site_id`='$customer_address_site_id',
                                                                    `customer_address_street`='$customer_address_street',
                                                                    `customer_address_info`=$customer_address_info,
                                                                    `customer_address_postcode`='$customer_address_site_postcode', 
                                                                    `customer_address_city`='$customer_address_site_name', 
                                                                    `customer_address_phone`='$customer_address_phone',
                                                                    `customer_address_is_default`='$customer_address_is_default' 
                                                        WHERE `customer_address_id` = '$customer_address_id'";
      //echo $query_update_user_address."<br>";
      $result_update_user_address = mysqli_query($db_link, $query_update_user_address);
      if(!$result_update_user_address) {
        echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      else {
        
        //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
        mysqli_query($db_link,"COMMIT");
        if($customer_address_is_default == 1) {
          if(isset($_SESSION['customer']['shipping_address'])) unset($_SESSION['customer']['shipping_address']);
        }
?>
        <script>window.location.href="/<?=$current_lang;?>/shopping-cart/shopping-cart-addresses"</script>
<?php
      }
    }
  }
?>
    <form name="edit_customer_address" method="post" id="bg_form" class="form form-horizontal" action="/<?=$_GET['page']."?caid=".$customer_address_id;?>"> 
<?php
    if(!empty($errors)) echo "<div class='warning_field'>Моля попълнете всички задължителни полета отбелязани с *</div>";
?>
      <div<?php if(!empty($errors['customer_address_firstname'])) echo ' class="error_field"';?>>
        <label for="customer_address_firstname"><?=$languages[$current_lang]['header_customer_firstname'];?><span class="red">*</span></label>
        <input type="text" name="customer_address_firstname" id="customer_address_firstname" value="<?php if(isset($customer_address_firstname)) echo $customer_address_firstname;?>" />
        <?php if(!empty($errors['customer_address_firstname'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_address_firstname'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['customer_address_lastname'])) echo ' class="error_field"';?>>
        <label for="customer_address_lastname"><?=$languages[$current_lang]['header_customer_lastname'];?><span class="red">*</span></label>
        <input type="text" name="customer_address_lastname" id="customer_address_lastname" value="<?php if(isset($customer_address_lastname)) echo $customer_address_lastname;?>" />
        <?php if(!empty($errors['customer_address_lastname'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_address_lastname'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div class="float_left<?php if(!empty($errors['customer_address_city'])) echo ' error_field';?>">
        <label for="customer_address_city"><?=$languages[$current_lang]['header_customer_address_site_name'];?><span class="red">*</span></label>
        <input type="text" name="customer_address_site_type" class="disabled float_left" id="customer_address_site_type" value="<?php if(isset($customer_address_site_type)) echo $customer_address_site_type;else echo $languages[$current_lang]['header_customer_address_site_type'];?>" disabled="disabled" style="width: 30px;" />
        <input type="text" name="customer_address_site_name_label" id="customer_address_site_name_label" value="<?php if(isset($customer_address_site_name)) echo $customer_address_site_name;?>" style="width: 300px;" />
      </div>
      <div class="float_left">
        <label for="customer_address_postcode"><?=$languages[$current_lang]['header_customer_address_postcode'];?></label>
        <input type="text" name="customer_address_postcode" class="disabled" id="customer_address_postcode" value="<?php if(isset($customer_address_site_postcode)) echo $customer_address_site_postcode;else echo $languages[$current_lang]['header_customer_address_site_postcode'];?>" disabled="disabled" style="width: 100px;" />
        <input type="hidden" name="customer_address_site_id" id="customer_address_site_id" value="<?php if(isset($customer_address_site_id)) echo $customer_address_site_id;?>" />
        <input type="hidden" name="customer_address_site_name" id="customer_address_site_name" value="<?php if(isset($customer_address_site_name)) echo $customer_address_site_name;?>" />
        <?php if(!empty($errors['customer_address_site_name'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_address_site_name'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['customer_address_street'])) echo ' class="error_field"';?>>
        <label for="customer_address_street"><?=$languages[$current_lang]['header_customer_address_street'];?><span class="red">*</span></label>
        <input type="text" name="customer_address_street" id="customer_address_street" value='<?php if(isset($customer_address_street)) echo stripslashes($customer_address_street);?>' style="width: 95%;" />
        <?php if(!empty($errors['customer_address_street'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_address_street'];?></span><?php } ?>
      </div>
      <i class="fa fa-info-circle"></i>
      <i><?=$languages[$current_lang]['info_customer_address_example'];?></i>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['customer_address_info'])) echo ' class="error_field"';?>>
        <label for="customer_address_info"><?=$languages[$current_lang]['header_customer_address_info'];?></label>
        <input type="text" name="customer_address_info" id="customer_address_info" value='<?php if(isset($customer_address_info)) echo stripslashes($customer_address_info);?>' style="width: 95%;" />
        <?php if(!empty($errors['customer_address_info'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_address_info'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['customer_address_phone'])) echo ' class="error_field"';?>>
        <label for="customer_address_phone"><?=$languages[$current_lang]['header_customer_address_phone'];?><span class="red">*</span></label>
        <input type="text" name="customer_address_phone" id="customer_address_phone" value="<?php if(isset($customer_address_phone)) echo $customer_address_phone;?>" style="width: 300px;" />
        <?php if(!empty($errors['customer_address_phone'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_address_phone'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div>
        <?php
          if(isset($customer_address_is_default)) {
            if($customer_address_is_default == 0) {echo '<input type="checkbox" name="customer_address_is_default" id="customer_address_is_default" />';}
            else {echo '<input type="checkbox" name="customer_address_is_default" id="customer_address_is_default" checked="checked" />';}
          }
          else echo '<input type="checkbox" name="customer_address_is_default" id="customer_address_is_default" />';
        ?>
        <label for="customer_address_is_default" style="display: inline;"><?=$languages[$current_lang]['header_use_as_default'];?></label>
      </div>
      <p>&nbsp;</p>

      <div>
        <button type="submit" name="update_customer_address_bg" class="btn btn-success button outline-outward">
          <?=$languages[$current_lang]['btn_save'];?>
        </button>
        <a href="/<?=$current_lang;?>/shopping-cart/shopping-cart-addresses" class="btn btn-primary button outline-outward">
          <?=$languages[$current_lang]['btn_cancel'];?>
        </a>
      </div>
      <div class="clearfix"></div>
    </form>

    <script>
    $(function() {
      //autocomplete sites
      $("#customer_address_site_name_label").autocomplete({
        source: "/frontstore/ajax/get-sites-autocomplete.php",
        minLength: 2,
        select: function( event, ui ) {
          //alert(ui.item.site_name);
          $('#customer_address_site_id').val(ui.item.site_id);
          $('#customer_address_site_type').val(ui.item.site_type);
          $('#customer_address_site_name').val(ui.item.site_name);
          $('#customer_address_postcode').val(ui.item.site_postcode);
        },
        close: function( event, ui ) {
          //alert(ui.item.site_name);
          $('#customer_address_site_name_label').val($('#customer_address_site_name').val());
        }
      });
    });
    </script>