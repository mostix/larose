<?php
  $registration_was_successfull = false;
    
  if(isset($_POST['sign_up'])) {
  
    mysqli_query($db_link,"BEGIN");
    
    //echo"<pre>";print_r($_POST);
    
    $all_queries = "";
    $errors = array(); //defining an array errors, wich will collect them, if any
    
    $customer_group_id = 1; // regular user
    $customer_firstname = trim($_POST['customer_firstname']);
    $customer_lastname = trim($_POST['customer_lastname']);
    $customer_email = trim($_POST['customer_email']);
    $customer_email_retype = trim($_POST['customer_email_retype']);
    if($customer_email != $customer_email_retype) {
      $errors['customer_emails_mismatch'] = $languages[$current_lang]['error_create_customer_emails_mismatch'];
    }
    if(isset($_POST['customer_email_status'])) {
      $customer_email_status =  $_POST['customer_email_status'];    
      if($customer_email_status == "ok") {
        // check again if email is already taken if the form was autofilled 
        if(!check_if_user_email_is_valid($customer_email)) $errors['customer_email'] = $languages[$current_lang]['error_create_customer_email_taken'];
      }
      else {
        $errors['customer_email_status'] = $languages[$current_lang]['error_create_customer_email_taken'];
      }
    }
    else {
      $errors['customer_email'] = (!check_if_user_email_is_valid($customer_email)) ? $languages[$current_lang]['error_create_customer_email_not_valid'] : "";
    }
    $customer_password = $_POST['customer_password'];
    $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
    $bcrypt_password = crypt($customer_password , $bcrypt_salt);
    $customer_password_retype = $_POST['customer_password_retype'];
    $customer_phone = trim($_POST['customer_phone']);
    $customer_is_in_mailist = 0;
      if(isset($_POST['customer_is_in_mailist'])) $customer_is_in_mailist = 1;
    if(!isset($_POST['terms_and_conditions'])) {
      $errors['terms_and_conditions'] = $languages[$current_lang]['error_check_terms_and_conditions'];
    }
    if(!isset($_POST['privacy_policy'])) {
      $errors['privacy_policy'] = $languages[$current_lang]['error_check_privacy_policy'];
    }

    $recaptcha_response = false;
    if(isset($_POST['g-recaptcha-response'])) {
      $g_recaptcha_response = $_POST['g-recaptcha-response'];
      $url = 'https://www.google.com/recaptcha/api/siteverify';
      $data = array('secret' => '6LfQfQ0TAAAAAM2NJoDfnwt7-Id2azqkxu_zSEbd', 'response' => $g_recaptcha_response);

      // use key 'http' even if you send the request to https://...
      $options = array(
          'http' => array(
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'method'  => 'POST',
              'content' => http_build_query($data),
          ),
      );
      $context  = stream_context_create($options);
      $result = json_decode(file_get_contents($url, false, $context));
    }
    
    $recaptcha_response = $result->success;
    if($recaptcha_response) { }
    else { $errors['recaptcha_response_field'] = $languages[$current_lang]['error_create_customer_recaptcha']; }
    
    $uppercase = preg_match('@[A-Z]@', $customer_password);
    $lowercase = preg_match('@[a-z]@', $customer_password);
    $number    = preg_match('@[0-9]@', $customer_password);

    if(!$uppercase || !$lowercase || !$number || strlen($customer_password) < 8) {
      // tell the user something went wrong
      $errors['customer_password'] = $languages[$current_lang]['error_registration_password_is_not_valid'];
    }
    
    $customer_passwords_mismatch = check_if_users_passwords_match($customer_password,$customer_password_retype);
    if(!empty($customer_passwords_mismatch)) {
      $errors['customer_passwords_mismatch'] = $customer_passwords_mismatch;
    }
    
    //we will check if all the fields are filled in at all
    foreach($_POST as $name => $value) {
      $trimed_value = trim($value);
      if(empty($trimed_value) && ($name != "sign_up" && $name != "customer_address_info" && $name != "g-recaptcha-response")) {
        $field_name = "header_".$name;
        $field_name_text = mb_convert_case($languages[$current_lang][$field_name], MB_CASE_LOWER, "UTF-8");
        $errors[$name] = $languages[$current_lang]['error_registration_empty_field'].$field_name_text;
      }
    }
    //echo"<pre>";print_r($errors);
    
    //if all the requered fields was filled in correct by the user make a database record
    if(empty($errors)) {
      
      $customer_email = mysqli_real_escape_string($db_link,$customer_email);
      $customer_firstname = mysqli_real_escape_string($db_link,$customer_firstname);
      $customer_lastname = mysqli_real_escape_string($db_link,$customer_lastname);
      $customer_phone = mysqli_real_escape_string($db_link,$customer_phone);
      $customer_is_blocked = 0;
      $customer_is_active = 1;
    
      $query_insert_customer = "INSERT INTO `customers`(`customer_id`, 
                                                        `customer_group_id`,  
                                                        `customer_salted_password`, 
                                                        `customer_firstname`, 
                                                        `customer_lastname`, 
                                                        `customer_email`,  
                                                        `customer_phone`,  
                                                        `customer_is_in_mailist`,  
                                                        `customer_is_blocked`,
                                                        `customer_is_active`, 
                                                        `customer_registration_date`) 
                                                VALUES ('',
                                                        '$customer_group_id',
                                                        '$bcrypt_password',
                                                        '$customer_firstname',
                                                        '$customer_lastname',
                                                        '$customer_email',
                                                        '$customer_phone',
                                                        '$customer_is_in_mailist',
                                                        '$customer_is_blocked',
                                                        '$customer_is_active',
                                                        NOW())";
      //echo $query_insert_customer;
      $all_queries = "<br>".$query_insert_customer;
      $result_insert_customer = mysqli_query($db_link, $query_insert_customer);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
      
      $customer_id = mysqli_insert_id($db_link);
      
      $to      = $_POST['customer_email'];
      $subject = $languages[$current_lang]['email_subject_text'];
      $logo_image = "https://".$_SERVER['SERVER_NAME']."/frontstore/images/logo.png";
      //$logo_image_params = getimagesize($logo_image);
      //$logo_image_dimensions = $logo_image_params[3];
      $message = "<table>";
      $message .= "<tr>
                    <td>
                      <a href='".$_SERVER['SERVER_NAME']."' target='_blank'><img src='$logo_image'></a>
                    </td>
                  </tr>
                  <tr>
                    <td>
                    </td>
                  </tr>";
      $message .= "<tr><td>".$languages[$current_lang]['email_message_text_1']."</td></tr>";
      $message .= "<tr><td>".$languages[$current_lang]['email_message_text_2']." ".$_SERVER['SERVER_NAME']."/$current_lang/$customer_id/confirm-account</td></tr>";
      $message .= "<tr><td>".$languages[$current_lang]['email_message_text_3']."</td></tr>";
      $message .= "<tr><td>".$languages[$current_lang]['email_message_text_4']." ".$_SERVER['SERVER_NAME']."/$current_lang/$customer_id/confirm-account</td></tr>";
      $message .= "<tr><td>&nbsp;</td></tr>";
      $message .= "<tr><td>&nbsp;</td></tr>";
      $message .= "</table>";
      $headers = $languages[$current_lang]['email_headers_text'];
      //$headers .= 'Cc: idimitrov@eterrasystems.com' . "\r\n";

      if(mail($to, $subject, $message, $headers)) {
        mysqli_commit($db_link);
        $registration_was_successfull = true;
      }
      else {
        print_r(error_get_last());
        echo $languages[$current_lang]['error_registration_customer_send_email_fail'];
        mysqli_query($db_link, "ROLLBACK");
      }
    }
    
  }//if(isset($_POST['sign_up'])
  //
  //if not all the requered fields was filled in correct by the user make the sign up form showing the errors
  
  if($registration_was_successfull) {
?>
  <div class="form-group">&nbsp;</div>
  <div class="alert alert-success">
    <h2 class="no_margin"><?=$languages[$current_lang]['header_registration_was_successfull'];?></h2>
  </div>

  <h4><?=$languages[$current_lang]['create_customer_success_text'];?></h4>
  <div class="form-group">&nbsp;</div>
<?php
  }
  else {
?>
  <form name="sign_up_form" id="sign_up_form" class="form form-horizontal" method="post" action="/<?=$_GET['page'];?>">
      
    <div<?php if(!empty($errors['customer_firstname'])) echo ' class="form-error"';?>>
      <label for="customer_firstname"><?=$languages[$current_lang]['header_customer_firstname'];?><span class="red">*</span></label>
      <input type="text" name="customer_firstname" id="customer_firstname" value="<?php if(isset($customer_firstname)) echo $customer_firstname;?>" />
      <?php if(!empty($errors['customer_firstname'])) { ?><span class="error"><?=$errors['customer_firstname'];?></span><?php } ?>
    </div>
    <div class="clearfix"></div>

    <div<?php if(!empty($errors['customer_lastname'])) echo ' class="form-error"';?>>
      <label for="customer_lastname"><?=$languages[$current_lang]['header_customer_lastname'];?><span class="red">*</span></label>
      <input type="text" name="customer_lastname" id="customer_lastname" value="<?php if(isset($customer_lastname)) echo $customer_lastname;?>" />
      <?php if(!empty($errors['customer_lastname'])) { ?><span class="error"><?=$errors['customer_lastname'];?></span><?php } ?>
    </div>
    <div class="clearfix"></div>

    <div class="email<?php if(!empty($errors['customer_email_status']) || !empty($errors['customer_email'])) echo ' form-error';?>">
      <label for="customer_email"><?=$languages[$current_lang]['header_customer_email'];?><span class="red">*</span></label>
      <input type="text" name="customer_email" id="customer_email" value="<?php if(isset($customer_email)) echo $customer_email;?>" onBlur="CheckIfUserEmailIsValid(this.value,'<?=$current_lang;?>')" />
      <input type="hidden" name="customer_email_status" id="customer_email_status" value="<?php if(!empty($errors['customer_email_status'])) echo "error"; else echo "ok"?>" />
      <span id="customer_email_is_valid"></span>
      <?php if(!empty($errors['customer_email'])) { ?><span class="error"><?=$errors['customer_email'];?></span><?php } ?>
      <?php if(!empty($errors['customer_email_status'])) { ?><span class="error"><?=$errors['customer_email_status'];?></span><?php } ?>
    </div>
    <div class="clearfix"></div>

    <div<?php if(!empty($errors['customer_email_retype']) || !empty($errors['customer_passwords_mismatch'])) echo ' class="form-error"';?>>
      <label for="customer_email_retype"><?=$languages[$current_lang]['header_customer_email_retype'];?><span class="red">*</span></label>
      <input type="text" name="customer_email_retype" id="customer_email_retype" value="<?php if(isset($customer_email_retype)) echo $customer_email_retype;?>" />
      <?php if(!empty($errors['customer_emails_mismatch'])) { ?><span class="error"><?=$errors['customer_emails_mismatch'];?></span><?php } ?>
    </div>
    <p class="clearfix"></p>

    <p><i><?=$languages[$current_lang]['text_email_specs'];?></i></p>
    <div<?php if(!empty($errors['customer_password'])) echo ' class="form-error"';?>>
      <label for="customer_password"><?=$languages[$current_lang]['header_customer_password'];?><span class="red">*</span></label>
      <input type="password" name="customer_password" id="customer_password" value="<?php if(isset($customer_password)) echo $customer_password;?>" onBlur="ValidateUserPassword(this.value,'<?=$current_lang;?>')"  />
      <span id="customer_password_is_valid"></span>
      <?php if(!empty($errors['customer_password'])) { ?><span class="error"><?=$errors['customer_password'];?></span><?php } ?>
    </div>
    <div class="clearfix"></div>

    <div<?php if(!empty($errors['customer_password_retype'])) echo ' class="form-error"';?>>
      <label for="customer_password_retype"><?=$languages[$current_lang]['header_customer_password_retype'];?><span class="red">*</span></label>
      <input type="password" name="customer_password_retype" id="customer_password_retype" value="<?php if(isset($customer_password_retype)) echo $customer_password_retype;?>" />
      <?php if(!empty($errors['customer_passwords_mismatch'])) { ?><span class="error"><?=$errors['customer_passwords_mismatch'];?></span><?php } ?>
    </div>
    <div class="clearfix"></div>

    <div<?php if(!empty($errors['customer_phone'])) echo ' class="form-error"';?>>
      <label for="customer_phone"><?=$languages[$current_lang]['header_customer_phone'];?><span class="red">*</span></label>
      <input type="text" name="customer_phone" id="customer_phone" value="<?php if(isset($customer_phone)) echo $customer_phone;?>" />
      <?php if(!empty($errors['customer_phone'])) { ?><span class="error"><?=$errors['customer_phone'];?></span><?php } ?>
    </div>
    <p><i><?=$languages[$current_lang]['text_phone_example'];?></i></p>
    <div class="clearfix"></div>

    <div>
      <?php
        if(isset($customer_is_in_mailist)) {
          if($customer_is_in_mailist == 0) {echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" />';}
          else {echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" checked="checked" />';}
        }
        else echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" checked="checked" />';
      ?>
      <label for="customer_is_in_mailist" style="display: inline-block;"><?=$languages[$current_lang]['header_customer_is_in_mailist'];?></label>
    </div>
    <div class="clearfix"></div>
      
    <div>
      <?php
        if(isset($_POST['terms_and_conditions'])) {
          echo '<input type="checkbox" name="terms_and_conditions" id="terms_and_conditions" checked="checked" />';
        }
        else echo '<input type="checkbox" name="terms_and_conditions" id="terms_and_conditions" />';
      ?>
      <label for="terms_and_conditions" style="display: inline-block;">
        <?= $languages[$current_lang]['text_check_terms_and_conditions_01']; ?>
        <a href="/<?=$current_lang;?>/<?php if($current_lang == "bg") echo "общи-условия";else echo "terms-and-conditions"; ?>" target="_blank" class="red">
          <?= $languages[$current_lang]['text_terms_and_conditions']; ?>
        </a>
        <?= $languages[$current_lang]['text_check_terms_and_conditions_02']; ?>
      </label>
      <?php if(!empty($errors['terms_and_conditions'])) { ?><br><span class="error"><?=$errors['terms_and_conditions'];?></span><?php } ?>
    </div>
    <div class="clearfix"></div>

    <div>
      <?php
        if(isset($_POST['privacy_policy'])) {
          echo '<input type="checkbox" name="privacy_policy" id="privacy_policy" checked="checked" />';
        }
        else echo '<input type="checkbox" name="privacy_policy" id="privacy_policy" />';
      ?>
      <label for="privacy_policy" style="display: inline-block;">
        <?= $languages[$current_lang]['text_check_privacy_policy_01']; ?>
        <a href="/<?=$current_lang;?>/<?php if($current_lang == "bg") echo "общи-условия#privacy";else echo "terms-and-conditions"; ?>" target="_blank" class="red">
        <!--<a href="/<?=$current_lang;?>/<?php if($current_lang == "bg") echo "политика-на-поверителност";else echo "privacy-policy"; ?>" target="_blank" class="red">-->
          <?= $languages[$current_lang]['text_privacy_policy']; ?>
        </a>
        <?= $languages[$current_lang]['text_check_privacy_policy_02']; ?>
      </label>
      <?php if(!empty($errors['privacy_policy'])) { ?><br><span class="error"><?=$errors['privacy_policy'];?></span><?php } ?>
    </div>
    <div class="clearfix"></div>
      
    <div class="<?php if(!empty($errors['recaptcha_response_field'])) echo "form-error";?>">
      <?php if(!empty($errors['recaptcha_response_field'])) { ?>
        <div class="error"><?=$errors['recaptcha_response_field'];?></div>
      <?php } ?>
      <div class="g-recaptcha" data-sitekey="6LfQfQ0TAAAAAPi-W41iBhwNxPvjy-_DOJXhk02u"></div>
    </div>
    <p class="clearfix">&nbsp;</p>
<!--    <p>
      <i class="fa fa-info-circle"></i><i><?=$languages[$current_lang]['info_customer_add_address_after_registration'];?></i>
    </p>-->

    <div>
      <button type="submit" name="sign_up" class="btn btn-outline button button-medium"><span><?=$languages[$current_lang]['btn_sign_up'];?></span></button>
    </div>

  </form>
  <script src='https://www.google.com/recaptcha/api.js'></script>

<?php
  }
  