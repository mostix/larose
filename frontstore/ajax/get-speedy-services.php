<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../functions/include-functions.php';
  include_once '../languages/languages.php';
  
  if(isset($_POST['city_type'])) {
    $city_type =  $_POST['city_type'];
  }
  if(isset($_POST['city'])) {
    $city =  $_POST['city'];
  }
  if(isset($_POST['postcode'])) {
    $postcode =  $_POST['postcode'];
  }
  if(isset($_POST['taking_date'])) {
    $post_taking_date =  $_POST['taking_date'];
  }
  //echo "<pre>";print_r($_POST);exit;
  
  // Utility methods
  require_once '/home/art93/public_html/frontstore/speedy/speedy-eps-lib'.DIRECTORY_SEPARATOR.'util'.DIRECTORY_SEPARATOR.'Util.class.php';

  // Facade class
  require_once '/home/art93/public_html/frontstore/speedy/speedy-eps-lib'.DIRECTORY_SEPARATOR.'ver01'.DIRECTORY_SEPARATOR.'EPSFacade.class.php';

  // Implementation class
  require_once '/home/art93/public_html/frontstore/speedy/speedy-eps-lib'.DIRECTORY_SEPARATOR.'ver01'.DIRECTORY_SEPARATOR.'soap'.DIRECTORY_SEPARATOR.'EPSSOAPInterfaceImpl.class.php';

  $username = 999816;
  $password = 1367878673;

  // Configure client EPS facade class instance to web services
  $eps = new EPSFacade(new EPSSOAPInterfaceImpl(), $username,  $password);

  // Get authenticated user client data
  $senderClientData = $eps->getClientById($eps->getResultLogin()->getClientId());

  // Get current session data in ResultLogin class instance.
  // User can use this method if session details are needed. 
  $resultLogin = $eps->getResultLogin(false);
  if (isset($resultLogin)) {
      // ... session is active and its data is accessible from ResultLogin methods
  }
  
  $senderSiteId = $senderClientData->getAddress()->getSiteId();
  $senderCountryId = 100;
  $senderPostCode = 1000;
  $clientConfiguration = new StdClass();
  $clientConfiguration->arrEnabledServices = array(0=>2,1=>3,2=>112,3=>113);

  $bringToOfficeId = null;
  $fromMoment = time();
  $todayDate = date("c"); //echo $todayDate;
  // 100 - bulgaria
  $arrSites = $eps->listSites($city_type, $city);
  $receiverSiteId = $arrSites[0]->getId();
  $receiverCountryId = 100;
  $receiverPostCode = $postcode;
  $arrAvailableServices = $eps->listServicesForSites($post_taking_date, $senderSiteId, $receiverSiteId, null, null, null, null);
  $arrSelectedServices = Util::serviceIntersection($arrAvailableServices, $clientConfiguration->arrEnabledServices);
  //echo "<pre>";print_r($arrSelectedServices);

  foreach($arrAvailableServices as $services) {
    //echo "<pre>";print_r($services);
    $service_id = $services->getTypeId();
    if(in_array($service_id, $arrSelectedServices)) {
      
      $service_name = $services->getName();
      $ComplementaryServiceAllowance = $services->getAllowanceFixedTimeDelivery();
      $fixed_time_is_allowed = ($ComplementaryServiceAllowance->getValue() == "ALLOWED") ? 1 : 0; //allowed or banned
      $deadline = $services->getDeliveryDeadline();
      $deadline_date_exploded = explode("T", $deadline);
      $deadline_date_formatted = $deadline_date_exploded[0];
      $taking_dates = $eps->getAllowedDaysForTaking(
        $service_id, is_null($bringToOfficeId) ? $senderSiteId : null, $bringToOfficeId, $fromMoment
      );
      if(count($taking_dates) > 0) {
        $taking_date = $taking_dates[0];
        $taking_date_exploded = explode("T", $taking_date);
        $taking_date_formatted = $taking_date_exploded[0];
      }
      if(isset($taking_date_formatted) && $post_taking_date >= $taking_date_formatted) {
        echo "<option value='$service_id' fixed_time='$fixed_time_is_allowed' deadline_date='$deadline_date_formatted'>$service_id - $service_name (доставка до $deadline_date_formatted)</option>";
      }
      //echo "<option value='$service_id'>$service_id - $service_name - $deadline</option>";
      //print_r($services);
    }
  }
?>