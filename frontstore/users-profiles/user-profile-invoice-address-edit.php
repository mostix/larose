<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
 
  $customer_id = $_SESSION['customer']['customer_id'];
  $customer_fullname = $_SESSION['customer']['customer_firstname']." ".$_SESSION['customer']['customer_lastname'];
  
  $invoice_id =  $_GET['iaid'];
  
  $query_invoices = "SELECT `invoice_country_id`, `invoice_firstname`, `invoice_lastname`, `invoice_company_name`,`invoice_bulstat`, 
                            `invoice_accountable_person`, `invoice_site_id`, `invoice_street`, `invoice_postcode`, `invoice_city`,`invoice_is_default` 
                    FROM `invoice_addresses`
                    WHERE `invoice_id` = '$invoice_id'";
  //echo $query_invoicees;
  $result_invoices = mysqli_query($db_link, $query_invoices);
  if(!$result_invoices) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_invoices) > 0) {
    $address = mysqli_fetch_assoc($result_invoices);
    
    $invoice_country_id_db = $address['invoice_country_id'];
    $invoice_firstname = $address['invoice_firstname'];
    $invoice_lastname = $address['invoice_lastname'];
    $invoice_company_name = stripslashes($address['invoice_company_name']);
    $invoice_bulstat = $address['invoice_bulstat'];
    $invoice_accountable_person = $address['invoice_accountable_person'];
    $invoice_site_id = $address['invoice_site_id'];
    $invoice_street = stripslashes($address['invoice_street']);
    $invoice_postcode = $address['invoice_postcode'];
    $invoice_city = $address['invoice_city'];
    $invoice_is_default = $address['invoice_is_default'];
    if($invoice_site_id != 0) {
      $query_invoice_addresses_speedy = "SELECT `site_type`, `site_name`, `site_postcode` FROM `sites` WHERE `site_id` = '$invoice_site_id'";
      $result_invoice_addresses_speedy = mysqli_query($db_link, $query_invoice_addresses_speedy);
      if(!$result_invoice_addresses_speedy) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_invoice_addresses_speedy) > 0) {
        $addresses_speedy = mysqli_fetch_assoc($result_invoice_addresses_speedy);

        $invoice_site_type = $addresses_speedy['site_type'];
        $invoice_site_name = $addresses_speedy['site_name'];
        $invoice_postcode = $addresses_speedy['site_postcode'];
      }
    }
  }
  
  $all_queries = "";
  
  if(isset($_POST['update_invoice_bg'])) {
    //echo "<pre>";print_r($_POST);exit;
    
    mysqli_query($db_link,"BEGIN");
  
    //we will check if all the fields are filled in at all
    foreach($_POST as $name => $value) {
      $trimed_value = trim($value);
      if(empty($trimed_value) && ($name != "update_invoice_bg" && $name != "invoice_site_name_label" && $name != "invoice_site_id" && $name != "invoice_is_default")) {
        $field_name = "header_".  str_replace("invoice_", "", $name);
        $field_name_text = mb_convert_case($languages[$current_lang][$field_name], MB_CASE_LOWER, "UTF-8");
        $errors[$name] = $languages[$current_lang]['error_registration_empty_field'].$field_name_text;
      }
    }
    
    $invoice_firstname = $_POST['invoice_firstname'];
    $invoice_lastname = $_POST['invoice_lastname'];
    $invoice_company_name = $_POST['invoice_company_name'];
    $invoice_bulstat = $_POST['invoice_bulstat'];
    $invoice_accountable_person = $_POST['invoice_accountable_person'];
    $invoice_country_id_db = $_POST['invoice_country_id'];
    $invoice_site_id = $_POST['invoice_site_id'];
    $invoice_site_name = $_POST['invoice_site_name'];
    $invoice_street = $_POST['invoice_street'];
    $invoice_site_postcode = "NULL";
    $invoice_is_default = 0;
    if(isset($_POST['invoice_is_default'])) $invoice_is_default = 1;
    
    //if all the requered fields was filled in correct by the user make a database record
    if(empty($errors) && !empty($customer_id)) {
      
      if($invoice_is_default == 1) {
        
        /*
         * if the user wants to make some invoice address default
         * we have to make the one that is already default - not default
         */
        
        $query_select_default = "SELECT `invoice_id` FROM `invoice_addresses` WHERE `customer_id` = '$customer_id' AND `invoice_is_default` = '1'";
        $all_queries .= $query_select_default."<br>\n";
        //echo $query_select_defaultc;exit;
        $result_select_default = mysqli_query($db_link, $query_select_default);
        if(!$result_select_default) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_select_default) > 0) {
          
          $query_update_default_addr = "UPDATE `invoice_addresses` SET `invoice_is_default` = '0' WHERE `customer_id` = '$customer_id' AND `invoice_is_default` = '1'";
          $all_queries .= $query_update_default_addr."<br>\n";
          $result_update_default_addr = mysqli_query($db_link, $query_update_default_addr);
          if(!$result_update_default_addr) {
            echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
          
      }
      
      $invoice_street = mysqli_real_escape_string($db_link,$invoice_street);
      $invoice_company_name = mysqli_real_escape_string($db_link,$_POST['invoice_company_name']);

      $query_update_invoice_addr = "UPDATE `invoice_addresses` SET `invoice_country_id`='$invoice_country_id_db',
                                                                    `invoice_firstname`='$invoice_firstname',
                                                                    `invoice_lastname`='$invoice_lastname',
                                                                    `invoice_company_name`='$invoice_company_name',
                                                                    `invoice_bulstat`='$invoice_bulstat',
                                                                    `invoice_accountable_person`='$invoice_accountable_person',
                                                                    `invoice_site_id`='$invoice_site_id',
                                                                    `invoice_street`='$invoice_street',
                                                                    `invoice_postcode`='$invoice_site_postcode', 
                                                                    `invoice_city`='$invoice_site_name',
                                                                    `invoice_is_default`='$invoice_is_default'
                                                        WHERE `invoice_id` = '$invoice_id'";
      $all_queries .= $query_update_invoice_addr."<br>\n";
      //echo $query_update_invoice_addr."<br>";exit;
      $result_update_invoice_addr = mysqli_query($db_link, $query_update_invoice_addr);
      if(!$result_update_invoice_addr) {
        echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      else {
        $success = true;
        if($invoice_is_default == 1) {
          if(isset($_SESSION['customer']['invoice_address'])) unset($_SESSION['customer']['invoice_address']);
        }
      }
      
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
      mysqli_query($db_link,"COMMIT");
    }
      
  }
  
  if(isset($_POST['update_invoice_not_bg'])) {
    //echo "<pre>";print_r($_POST);exit;
    
    //we will check if all the fields are filled in at all
    foreach($_POST as $name => $value) {
      $trimed_value = trim($value);
      if(empty($trimed_value) && ($name != "update_invoice_not_bg" && $name != "invoice_is_default")) {
        $field_name = "header_".  str_replace("invoice_", "", $name);
        $field_name_text = mb_convert_case($languages[$current_lang][$field_name], MB_CASE_LOWER, "UTF-8");
        $errors[$name] = $languages[$current_lang]['error_registration_empty_field'].$field_name_text;
      }
    }
    
    $invoice_firstname = $_POST['invoice_firstname'];
    $invoice_lastname = $_POST['invoice_lastname'];
    $invoice_company_name = $_POST['invoice_company_name'];
    $invoice_bulstat = $_POST['invoice_bulstat'];
    $invoice_accountable_person = $_POST['invoice_accountable_person'];
    $invoice_site_id = 0;
    $invoice_country_id_db = $_POST['invoice_country_id'];
    $invoice_street = $_POST['invoice_street'];
    $invoice_postcode = $_POST['invoice_postcode'];
    $invoice_city = $_POST['invoice_city'];
    $invoice_is_default = 0;
    if(isset($_POST['invoice_is_default'])) $invoice_is_default = 1;
    
    //if all the requered fields was filled in correct by the user make a database record
    if(empty($errors) && !empty($customer_id)) {

      if($invoice_is_default == 1) {
        
        /*
         * if the user wants to make some invoice address default
         * we have to make the one that is already default - not default
         */
        
        $query_select_default = "SELECT `invoice_id` FROM `invoice_addresses` WHERE `customer_id` = '$customer_id' AND `invoice_is_default` = '1'";
        $all_queries .= $query_select_default."<br>\n";
        //echo $query_select_defaultc;exit;
        $result_select_default = mysqli_query($db_link, $query_select_default);
        if(!$result_select_default) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_select_default) > 0) {
          
          $query_update_default_addr = "UPDATE `invoice_addresses` SET `invoice_is_default` = '0' WHERE `customer_id` = '$customer_id' AND `invoice_is_default` = '1'";
          $all_queries .= $query_update_default_addr."<br>\n";
          $result_update_default_addr = mysqli_query($db_link, $query_update_default_addr);
          if(!$result_update_default_addr) {
            echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }
      
      $invoice_street = mysqli_real_escape_string($db_link,$invoice_street);
      
      $query_update_invoice_addr = "UPDATE `invoice_addresses` SET `invoice_country_id`='$invoice_country_id_db',
                                                                    `invoice_firstname`='$invoice_firstname',
                                                                    `invoice_lastname`='$invoice_lastname',
                                                                    `invoice_company_name`='$invoice_company_name',
                                                                    `invoice_bulstat`='$invoice_bulstat',
                                                                    `invoice_accountable_person`='$invoice_accountable_person',
                                                                    `invoice_site_id`='$invoice_site_id',
                                                                    `invoice_street`='$invoice_street',
                                                                    `invoice_postcode`='$invoice_postcode', 
                                                                    `invoice_city`='$invoice_site_name',
                                                                    `invoice_is_default`='$invoice_is_default'
                                                        WHERE `invoice_id` = '$invoice_id'";
      $all_queries .= $query_update_invoice_addr."<br>\n";
      //echo $query_update_invoice_addr."<br>";exit;
      $result_update_invoice_addr = mysqli_query($db_link, $query_update_invoice_addr);
      if(!$result_update_invoice_addr) {
        echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      else {
        $success = true;
        if($invoice_is_default == 1) {
          if(isset($_SESSION['customer']['invoice_address'])) unset($_SESSION['customer']['invoice_address']);
        }
      }
      
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
      mysqli_query($db_link,"COMMIT");
    }
      
  }
  
  $bg_form_style = 'style="display:none"';
  $not_bg_form_style = 'style="display:none"';

  if($invoice_country_id_db == 33) {
    $bg_form_style = "";
  }
  else {
    $not_bg_form_style = "";
  }
?>
    <form name="edit_invoice_address" method="post" id="bg_form" class="form form-horizontal"action="/<?=$_GET['page']."?iaid=".$invoice_id;?>" <?=$bg_form_style;?> >
<?php
      if(isset($success)) echo "<div class='success_field'>Промерните бяха запазени успешно</div>";
      if(!empty($errors)) echo "<div class='warning_field'>Моля попълнете всички задължителни полета отбелязани с *</div>";
?>
      <div<?php if(!empty($errors['invoice_firstname'])) echo ' class="error_field"';?>>
        <label for="invoice_firstname"><?=$languages[$current_lang]['header_invoice_firstname'];?><span class="red">*</span></label>
        <input type="text" name="invoice_firstname" class="invoice_firstname" value="<?php if(isset($invoice_firstname)) echo $invoice_firstname;?>" />
        <?php if(!empty($errors['invoice_firstname'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_firstname'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_lastname'])) echo ' class="error_field"';?>>
        <label for="invoice_lastname"><?=$languages[$current_lang]['header_invoice_lastname'];?><span class="red">*</span></label>
        <input type="text" name="invoice_lastname" class="invoice_lastname" value="<?php if(isset($invoice_lastname)) echo $invoice_lastname;?>" />
        <?php if(!empty($errors['invoice_lastname'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_lastname'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_company_name'])) echo ' class="error_field"';?>>
        <label for="invoice_company_name"><?=$languages[$current_lang]['header_company_name'];?><span class="red">*</span></label>
        <input type="text" name="invoice_company_name" class="invoice_company_name" value="<?php if(isset($invoice_company_name)) echo $invoice_company_name;?>" />
        <?php if(!empty($errors['invoice_company_name'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_company_name'];?></span><?php } ?>
      </div>
      <i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['info_invoice_company_name'];?></i>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_bulstat'])) echo ' class="error_field"';?>>
        <label for="invoice_bulstat"><?=$languages[$current_lang]['header_bulstat'];?><span class="red">*</span></label>
        <input type="text" name="invoice_bulstat" class="invoice_bulstat" value="<?php if(isset($invoice_bulstat)) echo $invoice_bulstat;?>" />
        <?php if(!empty($errors['invoice_bulstat'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_bulstat'];?></span><?php } ?>
      </div>
      <i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['info_invoice_bulstat'];?></i>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_accountable_person'])) echo ' class="error_field"';?>>
        <label for="invoice_accountable_person"><?=$languages[$current_lang]['header_accountable_person'];?><span class="red">*</span></label>
        <input type="text" name="invoice_accountable_person" class="invoice_accountable_person" value="<?php if(isset($invoice_accountable_person)) echo $invoice_accountable_person;?>" />
        <?php if(!empty($errors['invoice_accountable_person'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_accountable_person'];?></span><?php } ?>
      </div>
      <i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['info_invoice_accountable_person'];?></i>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_country_id'])) echo ' class="error_field"';?>>
        <label for="invoice_country"><?=$languages[$current_lang]['header_country'];?><span class="red">*</span></label>
        <select name="invoice_country_id" class="invoice_country_id" style="width: 200px;" onChange="DisplayCountryAddressForm(this.value)">
          <?php
            $country_list_for_delivery = "14,21,33,53,55,56,57,67,72,74,81,84,97,103,105,117,123,124,150,170,171,175,189,190,195,203,222";
            $query_countries = "SELECT `country_id`,`country_name` FROM  `countries` WHERE `country_id` IN($country_list_for_delivery) ORDER BY `country_name` ASC ";
            //echo $query_countries;
            $result_countries = mysqli_query($db_link, $query_countries);
            if (!$result_countries) echo mysqli_error($db_link);
            if(mysqli_num_rows($result_countries) > 0) {

              while ($country = mysqli_fetch_assoc($result_countries)) {

                $country_id = $country['country_id'];
                $country_name = stripslashes($country['country_name']);
                if(isset($_POST['invoice_country_id'])) {
                  $selected = ($country_id == $_POST['invoice_country_id']) ? 'selected="selected"' : "";
                }
                else {
                  $selected = ($invoice_country_id_db == $country_id) ? 'selected="selected"' : "";
                }

                echo "<option value='$country_id' $selected>$country_name</option>";

              }
              mysqli_free_result($result_countries);
            }
          ?> 
        </select>
      </div>
      <div class="clearfix"></div>

      <div class="float_left<?php if(!empty($errors['invoice_city'])) echo ' error_field';?>">
        <label for="invoice_city"><?=$languages[$current_lang]['header_site_name'];?><span class="red">*</span></label>
        <input type="text" name="invoice_site_type" class="disabled float_left" id="invoice_site_type" value="<?php if(isset($invoice_site_type)) echo $invoice_site_type;else echo $languages[$current_lang]['header_site_type'];?>" disabled="disabled" style="width: 30px;" />
        <input type="text" name="invoice_site_name_label" id="invoice_site_name_label" value="<?php if(isset($invoice_site_name)) echo $invoice_site_name;?>" style="width: 300px;" />
      </div>
      <div class="float_left">
        <label for="invoice_postcode"><?=$languages[$current_lang]['header_postcode'];?></label>
        <input type="text" name="invoice_site_postcode" class="disabled" id="invoice_site_postcode" value="<?php if(isset($invoice_site_postcode)) echo $invoice_site_postcode;else echo $languages[$current_lang]['header_site_postcode'];?>" disabled="disabled" style="width: 100px;" />
        <input type="hidden" name="invoice_site_id" id="invoice_site_id" value="<?php if(isset($invoice_site_id)) echo $invoice_site_id;?>" />
        <input type="hidden" name="invoice_site_name" id="invoice_site_name" value="<?php if(isset($invoice_site_name)) echo $invoice_site_name;?>" />
        <?php if(!empty($errors['invoice_site_name'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_site_name'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_street'])) echo ' class="error_field"';?>>
        <label for="invoice_street"><?=$languages[$current_lang]['header_street'];?><span class="red">*</span></label>
        <input type="text" name="invoice_street" class="invoice_street" value='<?php if(isset($invoice_street)) echo stripslashes($invoice_street);?>' style="width: 95%;" />
        <?php if(!empty($errors['invoice_street'])) { ?>&nbsp;&nbsp;<br><span class="error"><?=$errors['invoice_street'];?></span><?php } ?>
      </div>
      <i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['info_address_example'];?></i>
      <div class="clearfix"></div>

      <div>
        <?php
          if(isset($invoice_is_default)) {
            if($invoice_is_default == 0) {echo '<input type="checkbox" name="invoice_is_default" id="invoice_is_default" />';}
            else {echo '<input type="checkbox" name="invoice_is_default" id="invoice_is_default" checked="checked" />';}
          }
          else echo '<input type="checkbox" name="invoice_is_default" id="invoice_is_default" />';
        ?>
        <label for="invoice_is_default" style="display: inline;"><?=$languages[$current_lang]['header_use_as_default'];?></label>
      </div>
      <p>&nbsp;</p>

      <div>
        <button type="submit" name="update_invoice_bg" class="btn btn-success button outline-outward">
          <?=$languages[$current_lang]['btn_save'];?>
        </button>
        <a href="/<?=$current_lang;?>/user-profiles/user-profile-invoice-addresses" class="btn btn-primary button outline-outward">
          <?=$languages[$current_lang]['btn_cancel'];?>
        </a>
      </div>

      <div class="clearfix"></div>
    </form>

    <form name="edit_invoice_address" method="post" id="not_bg_form" class="form form-horizontal" action="/<?=$_GET['page']."?iaid=".$invoice_id;?>" <?=$not_bg_form_style;?>>
<?php
      if(isset($success)) echo "<div class='success_field'>Промерните бяха запазени успешно</div>";
      if(!empty($errors)) echo "<div class='warning_field'>Моля попълнете всички задължителни полета отбелязани с *</div>";
?>
      <div<?php if(!empty($errors['invoice_firstname'])) echo ' class="error_field"';?>>
        <label for="invoice_firstname"><?=$languages[$current_lang]['header_invoice_firstname'];?><span class="red">*</span></label>
        <input type="text" name="invoice_firstname" class="invoice_firstname" value="<?php if(isset($invoice_firstname)) echo $invoice_firstname;?>" />
        <?php if(!empty($errors['invoice_firstname'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_firstname'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_lastname'])) echo ' class="error_field"';?>>
        <label for="invoice_lastname"><?=$languages[$current_lang]['header_invoice_lastname'];?><span class="red">*</span></label>
        <input type="text" name="invoice_lastname" class="invoice_lastname" value="<?php if(isset($invoice_lastname)) echo $invoice_lastname;?>" />
        <?php if(!empty($errors['invoice_lastname'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_lastname'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_company_name'])) echo ' class="error_field"';?>>
        <label for="invoice_company_name"><?=$languages[$current_lang]['header_company_name'];?><span class="red">*</span></label>
        <input type="text" name="invoice_company_name" class="invoice_company_name" value="<?php if(isset($invoice_company_name)) echo $invoice_company_name;?>" />
        <?php if(!empty($errors['invoice_company_name'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_company_name'];?></span><?php } ?>
      </div>
      <i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['info_invoice_company_name'];?></i>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_bulstat'])) echo ' class="error_field"';?>>
        <label for="invoice_bulstat"><?=$languages[$current_lang]['header_bulstat'];?><span class="red">*</span></label>
        <input type="text" name="invoice_bulstat" class="invoice_bulstat" value="<?php if(isset($invoice_bulstat)) echo $invoice_bulstat;?>" />
        <?php if(!empty($errors['invoice_bulstat'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_bulstat'];?></span><?php } ?>
      </div>
      <i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['info_invoice_bulstat'];?></i>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_accountable_person'])) echo ' class="error_field"';?>>
        <label for="invoice_accountable_person"><?=$languages[$current_lang]['header_accountable_person'];?><span class="red">*</span></label>
        <input type="text" name="invoice_accountable_person" class="invoice_accountable_person" value="<?php if(isset($invoice_accountable_person)) echo $invoice_accountable_person;?>" />
        <?php if(!empty($errors['invoice_accountable_person'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_accountable_person'];?></span><?php } ?>
      </div>
      <i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['info_invoice_accountable_person'];?></i>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_country_id'])) echo ' class="error_field"';?>>
        <label for="invoice_country"><?=$languages[$current_lang]['header_country'];?><span class="red">*</span></label>
        <select name="invoice_country_id" class="invoice_country_id" style="width: 200px;" onChange="DisplayCountryAddressForm(this.value)">
          <?php
            $country_list_for_delivery = "14,21,33,53,55,56,57,67,72,74,81,84,97,103,105,117,123,124,150,170,171,175,189,190,195,203,222";
            $query_countries = "SELECT `country_id`,`country_name` FROM  `countries` WHERE `country_id` IN($country_list_for_delivery) ORDER BY `country_name` ASC ";
            //echo $query_countries;
            //echo $query_countries;
            $result_countries = mysqli_query($db_link, $query_countries);
            if (!$result_countries) echo mysqli_error($db_link);
            if(mysqli_num_rows($result_countries) > 0) {

              while ($country = mysqli_fetch_assoc($result_countries)) {

                $country_id = $country['country_id'];
                $country_name = stripslashes($country['country_name']);
                if(isset($_POST['invoice_country_id'])) {
                  $selected = ($country_id == $_POST['invoice_country_id']) ? 'selected="selected"' : "";
                }
                else {
                  $selected = ($invoice_country_id_db == $country_id) ? 'selected="selected"' : "";
                }

                echo "<option value='$country_id' $selected>$country_name</option>";

              }
              mysqli_free_result($result_countries);
            }
          ?> 
        </select>
      </div>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_city'])) echo ' class="error_field"';?>>
        <label for="invoice_city"><?=$languages[$current_lang]['header_city'];?><span class="red">*</span></label>
        <input type="text" name="invoice_city" class="invoice_city" value="<?php if(isset($invoice_city)) echo $invoice_city;?>" style="width: 300px;" />
        <?php if(!empty($errors['invoice_city'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_city'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_street'])) echo ' class="error_field"';?>>
        <label for="invoice_street"><?=$languages[$current_lang]['header_street'];?><span class="red">*</span></label>
        <input type="text" name="invoice_street" class="invoice_street" value='<?php if(isset($invoice_street)) echo stripslashes($invoice_street);?>' style="width: 95%;" />
        <?php if(!empty($errors['invoice_street'])) { ?>&nbsp;&nbsp;<br><span class="error"><?=$errors['invoice_street'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div<?php if(!empty($errors['invoice_postcode'])) echo ' class="error_field"';?>>
        <label for="invoice_postcode"><?=$languages[$current_lang]['header_postcode'];?><span class="red">*</span></label>
        <input type="text" name="invoice_postcode" class="invoice_postcode" value="<?php if(isset($invoice_postcode)) echo $invoice_postcode;?>" style="width: 100px;" />
        <?php if(!empty($errors['invoice_postcode'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['invoice_postcode'];?></span><?php } ?>
      </div>
      <div class="clearfix"></div>

      <div>
        <?php
          if(isset($invoice_is_default)) {
            if($invoice_is_default == 0) {echo '<input type="checkbox" name="invoice_is_default" id="invoice_is_default" />';}
            else {echo '<input type="checkbox" name="invoice_is_default" id="invoice_is_default" checked="checked" />';}
          }
          else echo '<input type="checkbox" name="invoice_is_default" id="invoice_is_default" />';
        ?>
        <label for="invoice_is_default" style="display: inline;"><?=$languages[$current_lang]['header_use_as_default'];?></label>
      </div>
      <p>&nbsp;</p>

      <div>
        <button type="submit" name="update_invoice_not_bg" class="btn btn-success button outline-outward">
          <?=$languages[$current_lang]['btn_save'];?>
        </button>
        <a href="/<?=$current_lang;?>/user-profiles/user-profile-invoice-addresses" class="btn btn-primary button outline-outward">
          <?=$languages[$current_lang]['btn_cancel'];?>
        </a>
      </div>

      <div class="clearfix"></div>
    </form>
    <script>
    $(function() {
      //autocomplete sites
      $("#invoice_site_name_label").autocomplete({
        source: "/frontstore/ajax/get-sites-autocomplete.php",
        minLength: 2,
        select: function( event, ui ) {
          //alert(ui.item.site_name);
          $('#invoice_site_id').val(ui.item.site_id);
          $('#invoice_site_type').val(ui.item.site_type);
          $('#invoice_site_name').val(ui.item.site_name);
          $('#invoice_site_postcode').val(ui.item.site_postcode);
        },
        close: function( event, ui ) {
          $('#invoice_site_name_label').val($('#invoice_site_name').val());
        }
      });
    });
    </script>