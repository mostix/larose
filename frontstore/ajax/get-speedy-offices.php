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
    $taking_date =  $_POST['taking_date'];
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
  
  $arrSites = $eps->listSites($city_type, $city);
  $receiverSiteId = $arrSites[0]->getId();
  //echo "<pre>";print_r($arrSelectedServices);
  
  $listOffices = $eps->listOfficesEx("",$receiverSiteId);
  if ($listOffices) {
      foreach ($listOffices as $office) {
        $office_id = $office->getId();
        $office_name = $office->getName() . " [".$office_id."]";
        echo "<option value='$office_id'>$office_name</option>";
//        $offices[] = array(
//            'id' => $office->getId(),
//            'label' => $office->getId() . ' ' . $office->getName() . ', ' . $office->getAddress()->getFullAddressString(),
//            'value' => $office->getName()
//        );
      }
  }
?>