<?php

  include_once '../config.php';
  
  $db_link = DB_OpenI();
  
  check_for_csrf();
  
  //print_r($_POST);EXIT;
  if(isset($_GET['term'])) {
    $term = $_GET['term'];
  }

  $sites_array = array();
  $query_sites = "SELECT `site_id`,`site_type`, `site_name`,`site_municipality`, `site_region`, `site_postcode` FROM `sites`
                   WHERE `site_name` LIKE '%$term%'
                ORDER BY `site_name` ASC";
  //echo $query_sites;
  $result_sites = mysqli_query($db_link, $query_sites);
  if(mysqli_num_rows($result_sites) > 0) {
    while($sites = mysqli_fetch_assoc($result_sites)) {

      $label = $sites['site_type']." ".$sites['site_name']." (обл. ".$sites['site_region'].")";
      $sites['label'] = $label;
      $sites_array[] = $sites;
    }
  }
  else {
    $cyr = [
        'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
        'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
        'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
        'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
    ];
    $lat = [
        'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
        'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
        'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
        'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya'
    ];
    $term_lat_to_cyr = str_replace($lat, $cyr, $term);
    $query_sites = "SELECT `site_id`,`site_type`, `site_name`,`site_municipality`, `site_region`, `site_postcode` FROM `sites`
                      WHERE `site_name` LIKE '%$term_lat_to_cyr%'
                   ORDER BY `site_name` ASC";
     //echo $query_sites;
     $result_sites = mysqli_query($db_link, $query_sites);
     if(mysqli_num_rows($result_sites) > 0) {
       while($sites = mysqli_fetch_assoc($result_sites)) {

         $label = $sites['site_type']." ".$sites['site_name']." (обл. ".$sites['site_region'].")";
         $sites['label'] = $label;
         $sites_array[] = $sites;
       }
     }
  }
  //print_r($sites_array);exit;
  if(!empty($sites_array)) echo json_encode($sites_array);
?>