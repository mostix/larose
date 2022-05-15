<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../functions/include-functions.php';
  include_once '../languages/languages.php';

  if(isset($_POST['current_category_id'])) {
    $current_category_id =  $_POST['current_category_id'];
  }
  if(isset($_POST['language_id'])) {
    $current_language_id =  $_POST['language_id'];
    $query_language_code = "SELECT `language_code` FROM `languages` WHERE `language_id` = '$current_language_id'";
    //echo $query_content;exit;
    $result_language_code = mysqli_query($db_link, $query_language_code);
    if(!$result_language_code) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_language_code) > 0) {
      $row_language_code = mysqli_fetch_assoc($result_language_code);
      $current_lang = $row_language_code['language_code'];
    }
  }
  $options = array();
  $query_options = "SELECT `options`.`option_type`,`options`.`option_id`,`option_description`.`option_desc_name`
                    FROM `options` 
                    INNER JOIN `option_description` ON `option_description`.`option_id` = `options`.`option_id`
                    INNER JOIN `product_option_value` ON `product_option_value`.`option_id` = `options`.`option_id`
                    INNER JOIN `product_to_category` ON `product_option_value`.`product_id` = `product_to_category`.`product_id`
                    WHERE `product_to_category`.`category_id` = '$current_category_id'
                      AND `options`.`option_is_frontend_sortable` = '1'
                      AND `option_description`.`language_id` = '$current_language_id'
                    GROUP BY `options`.`option_id`
                    ORDER BY `options`.`option_sort_order` ASC ";
  //echo "<br>$query_options";
  $result_options = mysqli_query($db_link, $query_options);
  if(!$result_options) echo mysqli_error($db_link);
  $options_count = mysqli_num_rows($result_options);
  if($options_count > 0) {
    while($row_options = mysqli_fetch_assoc($result_options)) {
      $options[] = $row_options;
    }
  }
  
  $category_products_min_max_price = get_category_products_min_max_price($current_category_id);
  $min_product_price = $category_products_min_max_price['min_product_price'];
  $max_product_price = $category_products_min_max_price['max_product_price'];
?>
          <h3 class="widget-title"><?=$languages[$current_lang]['header_sort_by'];?>:</h3>
<?php
    if(!empty($options)) {
      
      foreach($options as $option) {
        
        $current_option_id = $option['option_id'];
        $option_type = $option['option_type'];
        $option_desc_name = $option['option_desc_name'];
        $options_box_class = ($current_option_id == 1) ? " colors" : "";
?>
        <div class="options_header<?=$options_box_class;?>">
          <b><?=$option_desc_name;?></b>
<?php
          list_products_options_values($current_option_id);
?>
        </div>
<?php
      }
    }
    
      if($min_product_price != $max_product_price) {
?>
          <div class="options_header">
            <b><?=$languages[$current_lang]['header_price'];?></b>
            <a class="order_by_price asc active" href="javascript:;">възходяща (0 &rarr; 9)</a> 
            <a class="order_by_price desc" href="javascript:;">низходяща (9 &rarr; 0)</a>
            <input type="hidden" name="obp" id="order_by_price" value="ASC" /> 
            <p>
              <label for="amount"></label>
              <input type="text" id="amount" readonly style="border:0; color:#333333; font-weight:inherit;">
            </p>

            <div id="slider-range"></div>
            <input type="hidden" id="price_range_min" name="pmin" value="<?=$min_product_price;?>">
            <input type="hidden" id="price_range_max" name="pmax" value="<?=$max_product_price;?>">
          </div>
          <script>
            $(function() {
              $(".order_by_price").bind('click', function() {
                $('.order_by_price').removeClass('active');
                $(this).addClass('active');
                if($(this).hasClass('asc')) $('#order_by_price').val('ASC');
                else $('#order_by_price').val('DESC');
                SortProductsByOptionValue('<?=$current_language_id;?>')
              });
              $("#slider-range").slider({
                range: true,
                min: <?=$min_product_price;?>,
                max: <?=$max_product_price;?>,
                values: [<?=$min_product_price;?>, <?php echo ($min_product_price*$max_product_price)/2;?>],
                slide: function( event, ui ) {
                  $("#amount").val(ui.values[0] + "лв. - " + ui.values[1] + "лв." );
                  $("#price_range_min").val(ui.values[0]);
                  $("#price_range_max").val(ui.values[1]);
                },
                change: function( event, ui ) {
                  SortProductsByOptionValue('<?=$current_language_id;?>');
                }
              });
              $("#amount").val($("#slider-range").slider("values", 0) + "лв. - " + $("#slider-range").slider("values", 1 ) + "лв." );
            });
          </script>
<?php } ?>
