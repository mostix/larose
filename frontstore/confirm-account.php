<?php

  $current_date = strtotime(date("Y-m-d"));
  $valid_date_for_confirmation = date("Y-m-d", strtotime('-2 days', $current_date));
  
  $query_activate_user = "UPDATE `customers` SET `customer_is_active`='1' WHERE `customer_id` = '$customer_id' AND `customer_registration_date` >= '$valid_date_for_confirmation'";
  //echo $query_activate_user;
  $result_activate_user = mysqli_query($db_link, $query_activate_user);
  if(!$result_activate_user) {
    echo mysqli_error($db_link);
  }
  else {
    
    $query_user = "SELECT `customer_group_id`,`customer_firstname`,`customer_lastname`,`customer_email`,`customer_phone`
                  FROM `customers` 
                  WHERE `customer_id` = '$customer_id' AND `customers`.`customer_is_active` = '1' AND `customers`.`customer_is_blocked` = '0'";
    //$_SESSION['query'] = $query_user."<br>";
    $result_user = mysqli_query($db_link,$query_user);
    if (!$result_user) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_user) > 0) {
      $customer = mysqli_fetch_assoc($result_user);

      $customer_group_id = $customer['customer_group_id'];
      $customer_firstname = $customer['customer_firstname'];
      $customer_lastname = $customer['customer_lastname'];
      $customer_email = $customer['customer_email'];
      $customer_phone = $customer['customer_phone'];

      $_SESSION['customer']['customer_id'] = $customer_id;
      $_SESSION['customer']['customer_group_id'] = $customer_group_id;
      $_SESSION['customer']['customer_firstname'] = $customer_firstname;
      $_SESSION['customer']['customer_lastname'] = $customer_lastname;
      $_SESSION['customer']['customer_email'] = $customer_email;
      $_SESSION['customer']['customer_phone'] = $customer_phone;
      unset($_SESSION['captcha123']);
      unset($_SESSION['captcha_error']);
      unset($_SESSION['login_error']);
      unset($_SESSION['bfa']);
      //exit;
      //echo "<script type='text/jscript'>\n window.location='".$_SERVER['PHP_SELF']."'\n</script>\n";

    } // if(mysqli_num_rows($result_user) > 0)
?>
  <div class="form-group">&nbsp;</div>
  <div class="alert alert-success">
    <h2 class="no_margin"><?=$languages[$current_lang]['header_registration_confirmed_successfully'];?></h2>
  </div>
  <div class="form-group">&nbsp;</div>

  <p><a href="/<?=$home_page_url;?>" class="btn btn-outline button button-medium"><?=$languages[$current_lang]['login_sign_in'];?></a></p>
  <div class="form-group">&nbsp;</div>
<?php
  }   
?>
