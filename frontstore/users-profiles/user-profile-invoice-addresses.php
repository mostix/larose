<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  $dirname = dirname(__FILE__);
  
  $customer_id = $_SESSION['customer']['customer_id'];
  $customer_fullname = $_SESSION['customer']['customer_firstname']." ".$_SESSION['customer']['customer_lastname'];
  
  $query_invoice_addresses = "SELECT `invoice_addresses`.`invoice_id`,`countries`.`country_name`,`invoice_addresses`.`invoice_firstname`,
                                      `invoice_addresses`.`invoice_lastname`,`invoice_addresses`.`invoice_company_name`,
                                      `invoice_addresses`.`invoice_bulstat`,`invoice_addresses`.`invoice_accountable_person`,
                                      `invoice_addresses`.`invoice_site_id`,`invoice_addresses`.`invoice_street`,
                                      `invoice_addresses`.`invoice_postcode`,`invoice_addresses`.`invoice_city`
                              FROM `invoice_addresses` 
                              INNER JOIN `countries` ON `countries`.`country_id` = `invoice_addresses`.`invoice_country_id`
                              WHERE `customer_id` = '$customer_id'";
  //echo $query_invoice_addresses;
  $result_invoice_addresses = mysqli_query($db_link, $query_invoice_addresses);
  if(!$result_invoice_addresses) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_invoice_addresses) > 0) {
    
    while($invoice_address = mysqli_fetch_assoc($result_invoice_addresses)) {

      $country_name = $invoice_address['country_name'];
      $invoice_id = $invoice_address['invoice_id'];
      $invoice_firstname = $invoice_address['invoice_firstname'];
      $invoice_lastname = $invoice_address['invoice_lastname'];
      $invoice_company_name = stripslashes($invoice_address['invoice_company_name']);
      $invoice_bulstat = $invoice_address['invoice_bulstat'];
      $invoice_accountable_person = $invoice_address['invoice_accountable_person'];
      $invoice_site_id = $invoice_address['invoice_site_id'];
      $invoice_street = stripslashes($invoice_address['invoice_street']);
      $invoice_postcode = $invoice_address['invoice_postcode'];
      $invoice_city = $invoice_address['invoice_city'];
      if($invoice_site_id != 0) {
        $query_invoice_addresses_speedy = "SELECT `site_type`, `site_name`, `site_postcode` FROM `sites` WHERE `site_id` = '$invoice_site_id'";
        $result_invoice_addresses_speedy = mysqli_query($db_link, $query_invoice_addresses_speedy);
        if(!$result_invoice_addresses_speedy) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_invoice_addresses_speedy) > 0) {
          $addresses_speedy = mysqli_fetch_assoc($result_invoice_addresses_speedy);

          $invoice_site_type = $addresses_speedy['site_type'];
          $invoice_site_name = mb_convert_case($addresses_speedy['site_name'], MB_CASE_TITLE, "UTF-8");
          $invoice_city = "$invoice_site_type $invoice_site_name";
          $invoice_postcode = $addresses_speedy['site_postcode'];
        }
      }
      $postcode_text = $languages[$current_lang]['header_customer_address_postcode'];
      $header_accountable_person = $languages[$current_lang]['header_accountable_person'];
      $header_bulstat = $languages[$current_lang]['header_bulstat'];
?>
    <div id="invoice_<?=$invoice_id;?>" class="customer_address">
      <div class="customer_address_title"><?=$languages[$current_lang]['header_invoice_address'];?></div>
      <div class="customer_address_padding">
<?php 

        echo "$invoice_firstname $invoice_lastname<br>$invoice_company_name<br>$header_accountable_person $invoice_accountable_person
              <br>$header_bulstat $invoice_bulstat<br>$invoice_street, $invoice_city, $invoice_postcode<br>$country_name";
?>
      </div>
      <a href="javascript:;" data-id="<?=$invoice_id;?>" class="red float_right delete_address_btn">
        <?=$languages[$current_lang]['btn_delete'];?>
      </a>
      <a href="/<?=$current_lang;?>/user-profiles/user-profile-invoice-address-edit?iaid=<?=$invoice_id;?>" class="blue edit_address">
        <?=$languages[$current_lang]['btn_edit'];?>
      </a>
    </div>
<?php 
    } //while($invoice_address)
  } //if(mysqli_num_rows($result_invoice_addresses) > 0)
  else {
    echo "<p class='alert alert-warning'>".$languages[$current_lang]['text_no_invoice_addresses_yet']."</p>";
  }
?>
  <p class="clearfix">&nbsp;</p>
  <div>
    <a href="/<?=$current_lang;?>/user-profiles/user-profile-invoice-address-add" class="btn btn-success btn-primary button outline-outward">
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
            DeleteInvoiceAddress();
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