<?php

  include_once '../config.php';
  include_once '../functions/include-functions.php';
  include_once '../languages/languages.php';
  
  check_for_csrf();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['selected_date'])) {
    $selected_date = mysqli_real_escape_string($db_link,$_POST['selected_date']);
  }
  if(isset($_POST['current_lang'])) {
    $current_lang = $_POST['current_lang'];
  }

  print_html_shipping_time_interval_form($selected_date);
?>
  <script type="text/javascript">
    $(document).ready(function () {
      $("#time_interval .time_interval").bind('click', function () {
        var link = $(this);
        if(link.hasClass("hint_not_aval")) {
          $("#modal_shipping_time_interval_not_available").dialog("open");
          return;
        }
        $(".time_interval input").attr("disabled",true);
        $(".time_interval").removeClass("active");
        link.addClass("active");
        link.children("input").attr("disabled",false);
      });
      $("#modal_shipping_time_interval_not_available").dialog({
        resizable: false,
        width: 400,
        height: 200,
        autoOpen: false,
        modal: true,
        draggable: false,
        closeOnEscape: true,
        dialogClass: "modal_confirm",
        buttons: {
          "<?=$languages[$current_lang]['btn_close'];?>": function() {
            $(this).dialog("close");
          }
        }
      });
    });
  </script>