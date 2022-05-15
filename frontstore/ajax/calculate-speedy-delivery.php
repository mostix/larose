<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

//  include_once '../config.php';
//  include_once '../functions/include-functions.php';
//  include_once '../languages/languages.php';
  
  if(isset($_POST['taking_date'])) {
    $taking_date =  $_POST['taking_date'];
  }
  if(isset($_POST['city_type'])) {
    $city_type =  $_POST['city_type'];
  }
  if(isset($_POST['city'])) {
    $city =  $_POST['city'];
  }
  if(isset($_POST['postcode'])) {
    $postcode =  $_POST['postcode'];
  }
  if(isset($_POST['service_id'])) {
    $service_id =  $_POST['service_id'];
  }
  if(isset($_POST['fixed_time_is_allowed'])) {
    $fixed_time_is_allowed =  $_POST['fixed_time_is_allowed'];
  }
  if(isset($_POST['fixed_hour'])) {
    $fixed_hour =  $_POST['fixed_hour'];
  }
  if(isset($_POST['fixed_minutes'])) {
    $fixed_minutes =  $_POST['fixed_minutes'];
  }
  if(isset($_POST['speedy_office_id'])) {
    $speedy_office_id =  $_POST['speedy_office_id'];
  }
  if(isset($_POST['order_weight'])) {
    $order_weight =  $_POST['order_weight'];
  }
  if(isset($_POST['total_order_price'])) {
    $total_order_price =  $_POST['total_order_price'];
  }
  //echo "<pre>";print_r($_POST);
  
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
  //echo "<pre>";print_r($senderClientData);
  // Get current session data in ResultLogin class instance.
  // User can use this method if session details are needed. 
  $resultLogin = $eps->getResultLogin(false);
  if (isset($resultLogin)) {
      // ... session is active and its data is accessible from ResultLogin methods
  }
  
  $senderSiteId = $senderClientData->getAddress()->getSiteId();
  $arrSites = $eps->listSites($city_type, $city);
  $receiverSiteId = $arrSites[0]->getId();
  
  $bringToOfficeId = null;
  
  $calculationData = new StdClass();
  $calculationData->weightDeclared   = $order_weight;  // Decalred weight
  $calculationData->bringToOfficeId  = $bringToOfficeId;  // Id of the office that sender should bring the parcel(s). Courier will visit sender address if this value is null
  $calculationData->takeFromOfficeId = ($speedy_office_id == 0) ? null : $speedy_office_id;  // Id of the office that recipient should take the parcel(s) from. Courier will visit recipient address if this value is null
  $calculationData->parcelsCount     = 1;     // Parcels count
  $calculationData->documents        = false; // Flag for documents
  $calculationData->palletized       = false; // Flag for pallets
  $calculationData->fragile          = false; // flag for fragile content
  $calculationData->payerType        = ParamCalculation::PAYER_TYPE_RECEIVER; // Determine the payer
  $calculationData->amountCODBase    = $total_order_price;      // Collecton on Delivery amount $total_order_price
  $calculationData->serviceTypeID    = $service_id;       // Service type 2 from Speedy nomenclature
  $calculationData->takingDate       = $taking_date; // We need taking date to be tomorrow
  $calculationData->fixedTimeDelivery= ($fixed_time_is_allowed == 1) ? $fixed_hour.$fixed_minutes : null;
  
//  echo '$calculationData = new StdClass()<br>';
//  echo '$calculationData->weightDeclared = '.$calculationData->weightDeclared."<br>";
//  echo '$calculationData->bringToOfficeId = '.$calculationData->bringToOfficeId."<br>";
//  echo '$calculationData->takeFromOfficeId = '.$calculationData->takeFromOfficeId."<br>";
//  echo '$calculationData->parcelsCount = '.$calculationData->parcelsCount."<br>";
//  echo '$calculationData->documents = '.$calculationData->documents."<br>";
//  echo '$calculationData->palletized = '.$calculationData->palletized."<br>";
//  echo '$calculationData->fragile = '.$calculationData->fragile."<br>";
//  echo '$calculationData->payerType = '.$calculationData->payerType."<br>";
//  echo '$calculationData->amountCODBase = '.$calculationData->amountCODBase."<br>";
//  echo '$calculationData->serviceTypeID = '.$calculationData->serviceTypeID."<br>";
//  echo '$calculationData->takingDate = '.$calculationData->takingDate."<br>";
//  echo '$calculationData->fixedTimeDelivery = '.$calculationData->fixedTimeDelivery."<br><br>";
//  
//  echo '$paramCalculation = new ParamCalculation()<br>';
//  echo '$paramCalculation->setBroughtToOffice(!is_null('.$calculationData->bringToOfficeId."));<br>";
//  echo '$paramCalculation->setToBeCalled(!is_null('.$calculationData->takeFromOfficeId."));<br>";
//  echo '$paramCalculation->setParcelsCount('.$calculationData->parcelsCount.");<br>";
//  echo '$paramCalculation->setWeightDeclared('.$calculationData->weightDeclared.");<br>";
//  echo '$paramCalculation->setDocuments('.$calculationData->documents.");<br>";
//  echo '$paramCalculation->setPalletized('.$calculationData->palletized.");<br>";
//  echo '$paramCalculation->setFragile('.$calculationData->fragile.");<br>";
//  echo '$paramCalculation->setSenderId('.$senderClientData->getClientId().");<br>";
//  echo '$paramCalculation->setReceiverSiteId('.$receiverSiteId."); // София<br>";
//  echo '$paramCalculation->setPayerType('.$calculationData->payerType.");<br>";
//  echo '$paramCalculation->setAmountCodBase('.$calculationData->amountCODBase.");<br>";
//  echo '$paramCalculation->setTakingDate('.$calculationData->takingDate.");<br>";
//  echo '$paramCalculation->setFixedTimeDelivery('.$calculationData->fixedTimeDelivery.");<br>";
//  echo '$paramCalculation->setServiceTypeId('.$calculationData->serviceTypeID.");<br>";
//  echo '$resultCalculation = $eps->calculate($paramCalculation)<br><br>';
  
  $paramCalculation = new ParamCalculation();
  $paramCalculation->setBroughtToOffice(!is_null($calculationData->bringToOfficeId));
  $paramCalculation->setToBeCalled(!is_null($calculationData->takeFromOfficeId));
  $paramCalculation->setParcelsCount($calculationData->parcelsCount );
  $paramCalculation->setWeightDeclared($calculationData->weightDeclared);
  $paramCalculation->setDocuments($calculationData->documents);
  $paramCalculation->setPalletized($calculationData->palletized);
  $paramCalculation->setFragile($calculationData->fragile);
  $paramCalculation->setSenderId($senderClientData->getClientId());
  $paramCalculation->setReceiverSiteId($receiverSiteId);
  $paramCalculation->setPayerType($calculationData->payerType);
  $paramCalculation->setAmountCodBase($calculationData->amountCODBase);
  $paramCalculation->setTakingDate($calculationData->takingDate);
  $paramCalculation->setFixedTimeDelivery($calculationData->fixedTimeDelivery);
  $paramCalculation->setServiceTypeId($calculationData->serviceTypeID);
  $resultCalculation = $eps->calculate($paramCalculation);

  echo $resultCalculation->getAmounts()->getTotal();
  //echo "<pre>";print_r($resultCalculation);
  
?>