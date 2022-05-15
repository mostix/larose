<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  $dirname = dirname(__FILE__);
  
  $customer_id = $_SESSION['customer']['customer_id'];
  $customer_fullname = $_SESSION['customer']['customer_firstname']." ".$_SESSION['customer']['customer_lastname'];
  
  $query_customer_addresses = "SELECT `customers_addresses`.`customer_address_id`,
                                      `customers_addresses`.`customer_address_firstname`,`customers_addresses`.`customer_address_lastname`,
                                      `customers_addresses`.`customer_address_site_id`,`customers_addresses`.`customer_address_street`,
                                      `customers_addresses`.`customer_address_info`,`customers_addresses`.`customer_address_postcode`,
                                      `customers_addresses`.`customer_address_city`,`customers_addresses`.`customer_address_phone`
                              FROM `customers_addresses` 
                              WHERE `customer_id` = '$customer_id'";
  //echo $query_customer_addresses;
  $result_customer_addresses = mysqli_query($db_link, $query_customer_addresses);
  if(!$result_customer_addresses) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_customer_addresses) > 0) {
    
    $key = 0;
    while($address = mysqli_fetch_assoc($result_customer_addresses)) {

      $customer_address_id = $address['customer_address_id'];
      $customer_address_firstname = $address['customer_address_firstname'];
      $customer_address_lastname = $address['customer_address_lastname'];
      $customer_address_site_id = $address['customer_address_site_id'];
      $customer_address_street = stripslashes($address['customer_address_street']);
      $customer_address_info = empty ($address['customer_address_info']) ? "" : "<br>".$address['customer_address_info'];
      $customer_address_postcode = $address['customer_address_postcode'];
      $customer_address_city = $address['customer_address_city'];
      $customer_address_phone = $address['customer_address_phone'];
      if($customer_address_site_id != 0) {
        $query_customer_addresses_speedy = "SELECT `site_type`, `site_name`, `site_postcode` FROM `sites` WHERE `site_id` = '$customer_address_site_id'";
        $result_customer_addresses_speedy = mysqli_query($db_link, $query_customer_addresses_speedy);
        if(!$result_customer_addresses_speedy) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_customer_addresses_speedy) > 0) {
          $addresses_speedy = mysqli_fetch_assoc($result_customer_addresses_speedy);

          $customer_address_site_type = $addresses_speedy['site_type'];
          $customer_address_site_name = mb_convert_case($addresses_speedy['site_name'], MB_CASE_TITLE, "UTF-8");
          $customer_address_city = "$customer_address_site_type $customer_address_site_name";
          $customer_address_postcode = $addresses_speedy['site_postcode'];
        }
      }
      $checkbox_checked = ($key == 0) ? 'checked="checked"' : "";
      $postcode_text = $languages[$current_lang]['header_customer_address_postcode'];
      $phone_text = $languages[$current_lang]['header_customer_phone'];
?>
      <div id="customer_address_<?=$customer_address_id;?>" class="customer_address">
        <div class="customer_address_title"><?=$languages[$current_lang]['header_delivery_address'];?></div>
        <div class="customer_address_padding">
<?php 

        echo "$customer_address_firstname $customer_address_lastname<br>$customer_address_street 
        $customer_address_info <br>$customer_address_city,<br>$postcode_text $customer_address_postcode<br>$phone_text $customer_address_phone";
?>
        </div>
        <a href="javascript:;" data-id="<?=$customer_address_id;?>" class="red float_right delete_address_btn">
          <?=$languages[$current_lang]['btn_delete'];?>
        </a>
        <a href="/<?=$current_lang;?>/user-profiles/user-profile-address-edit?caid=<?=$customer_address_id;?>" class="blue edit_address">
          <?=$languages[$current_lang]['btn_edit'];?>
        </a>
      </div>
<?php 
    } //while($address)
  } //if(mysqli_num_rows($result_customer_addresses) > 0)
  else {
    echo "<p class='alert alert-warning'>".$languages[$current_lang]['text_no_delivery_addresses_yet']."</p>";
  }
?>
  <p class="clearfix">&nbsp;</p>
  <div>
    <a href="/<?=$current_lang;?>/user-profiles/user-profile-address-add" class="btn btn-success btn-primary button outline-outward">
      <?=$languages[$current_lang]['btn_add_address'];?>
    </a>
  </div>

  <!--modal_confirm-->
  <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure'];?>">
    <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages[$current_lang]['delete_address'];?></p>
  </div>
  <script>
  $(function() {
    $("#modal_confirm").dialog({
      resizable: false,
      width: 400,
      height: 200,
      autoOpen: false,
      modal: true,
      draggable: false,
      closeOnEscape: true,
      dialogClass: "modal_confirm",
      buttons: {
        "<?=$languages[$current_lang]['btn_delete'];?>": function() {
          DeleteCustomerAddress();
        },
        "<?=$languages[$current_lang]['btn_cancel'];?>": function() {
          $(".delete_address_btn").removeClass("active");
          $(this).dialog("close");
        }
      }
    });
    $(".delete_address_btn").click(function() {
      $(".delete_address_btn").removeClass("active");
      $(this).addClass("active");
      $("#modal_confirm").dialog("open");
    });
  });
  </script>