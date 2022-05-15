<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../languages/languages.php';
  include_once '../functions/include-functions.php';

  //echo "<pre>";print_r($_POST);
  if(isset($_POST['current_category_id'])) {
    $current_category_id =  $_POST['current_category_id'];
  }
  if(isset($_POST['cpid'])) {
    $current_category_parent_id =  $_POST['cpid'];
  }
  if(isset($_POST['cd_name'])) {
    $cd_name =  $_POST['cd_name'];
  }
  if(isset($_POST['current_lang'])) {
    $current_lang =  $_POST['current_lang'];
  }
  if(isset($_POST['language_id'])) {
    $current_language_id =  $_POST['language_id'];
  }
  if(isset($_POST['current_cat_href'])) {
    $current_cat_href =  $_POST['current_cat_href'];
  }
  $cd_pretty_url = "";
  if(isset($_POST['cd_pretty_url'])) {
    $cd_pretty_url =  $_POST['cd_pretty_url'];
  }
  if(isset($_GET['offset'])) {
    $offset = $_GET['offset'];
  }
  else $offset = 0;
  if(isset($_GET['pmin'])) {
    $price_min =  $_GET['pmin'];
  }
  if(isset($_GET['pmax'])) {
    $price_max =  $_GET['pmax'];
  }
  $option = "";
  if(isset($_GET['sort'])) {
    $option =  $_GET['sort'];
  }
  if(isset($_POST['option'])) {
    $option =  $_POST['option'];
  }

  $customer_id = isset($_SESSION['customer']['customer_id']) ? $_SESSION['customer']['customer_id'] : 0;
  $customer_wishlist = get_customer_wishlist($customer_id);
  
  list_products_by_category($current_category_id,$offset,$current_cat_href,$cd_pretty_url);
?>
  <script>
    $(function() {
      bindGrid();
      $(".js_pagination a").bind('click', function() {
        var pag_id = $(this).attr("data");
        if(pag_id == "") return;
        var page_count = $(".page_count").val();
        var prev_page = "";
        var next_page = "";
        if(pag_id == "1") {
          $(".btn_prev_page").addClass("disabled");
          $(".btn_prev_page a").attr("data","");
          $(".btn_next_page").removeClass("disabled");
          $(".btn_next_page a").attr("data","2");
        }
        else if(pag_id == page_count){
          prev_page = parseInt(pag_id)-1;
          $(".btn_prev_page").removeClass("disabled");
          $(".btn_prev_page a").attr("data",prev_page);
          $(".btn_next_page").addClass("disabled");
          $(".btn_next_page a").attr("data","");
        }
        else {
          prev_page = parseInt(pag_id)-1;
          next_page = parseInt(pag_id)+1;
          $(".btn_prev_page").removeClass("disabled");
          $(".btn_prev_page a").attr("data",prev_page);
          $(".btn_next_page").removeClass("disabled");
          $(".btn_next_page a").attr("data",next_page);
        }
        if($(this).parent().hasClass("active")) {
          // do nothing
        }
        else {
          $(".js_pagination li").removeClass("active");
          $("#pag_"+pag_id).addClass("active");
          $("div.ajax_block_product").addClass("hide");
          $("div."+pag_id).removeClass("hide");
        }
        event.preventDefault();
      });
      $(".php_pagination a").bind('click', function() {
        var offset = $(this).attr("data");
        LoadPaginationProductsForCategory(offset);
      });
    });
  </script>