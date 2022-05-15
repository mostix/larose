<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../../config.php';
  include_once '../../functions/include-functions.php';
  include_once '../../languages/languages.php';
  
  if(isset($_POST['customer_address_firstname'])) {
    $customer_address_firstname =  $_POST['customer_address_firstname'];
  }
  if(isset($_POST['customer_address_lastname'])) {
    $customer_address_lastname =  $_POST['customer_address_lastname'];
  }
  $customer_fullname = "$customer_address_firstname $customer_address_lastname";
  if(isset($_POST['customer_address_phone'])) {
    $customer_address_phone =  $_POST['customer_address_phone'];
  }
  if(isset($_POST['customer_address_street'])) {
    $customer_address_street =  $_POST['customer_address_street'];
  }
  if(isset($_POST['customer_address_info'])) {
    $customer_address_info =  $_POST['customer_address_info'];
    $customer_address_street .= (!empty($customer_address_info)) ? " $customer_address_info" : "";
  }
  if(isset($_POST['taking_date'])) {
    $taking_date =  $_POST['taking_date'];
  }
  if(isset($_POST['city_type'])) {
    $city_type =  $_POST['city_type'];
  }
  if(isset($_POST['city'])) {
    $city =  $_POST['city'];
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
  if(isset($_POST['delivery_content_description'])) {
    $delivery_content_description =  $_POST['delivery_content_description'];
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

  // Get current session data in ResultLogin class instance.
  // User can use this method if session details are needed. 
  $resultLogin = $eps->getResultLogin(false);
  if (isset($resultLogin)) {
      // ... session is active and its data is accessible from ResultLogin methods
  }
  else {
    
  }
  
  $senderContactName = $senderClientData->getContactName();
  if(empty($senderContactName)) $senderContactName = "Art 93";
  $senderPhonesArray = $senderClientData->getPhones(); // array
  $senderPhone = (count($senderPhonesArray) > 0) ? $senderPhonesArray[0] : "0887 987 277"; // array
  $arrSites = $eps->listSites($city_type, $city);
  $receiverSiteId = $arrSites[0]->getId();

  //Create Bill Of Lading
  //This example registers Bill Of Lading, but to trigger the process of taking and delivery, the client needs to order a courier.

  // Init example picking structure with data
  $pickingData = new StdClass();
  $pickingData->weightDeclared   = $order_weight;  // Decalred weight
  $pickingData->bringToOfficeId  = null;  // Id of the office that sender should bring the parcel(s). Courier will visit sender address if this value is null
  $pickingData->takeFromOfficeId = ($speedy_office_id == 0) ? null : $speedy_office_id;  // Id of the office that recipient should take the parcel(s) from. Courier will visit recipient address if this value is null
  $pickingData->parcelsCount     = 1;     // Parcels count
  $pickingData->documents        = false; // Flag for documents
  $pickingData->palletized       = false; // Flag for pallets
  $pickingData->fragile          = false; // flag for fragile content
  $pickingData->payerType        = ParamCalculation::PAYER_TYPE_RECEIVER; // Determine the payer
  $pickingData->amountCODBase    = $total_order_price; // Collecton on Delivery amount $total_order_price+$resultCalculation->getAmounts()->getTotal()
  $pickingData->backDocumentReq  = true;    // Back dcoument request flag
  $pickingData->backReceiptReq   = false;   // Back receipt request flag
  $pickingData->contents         = $delivery_content_description; // Content description
  $pickingData->packing          = 'ПАКЕТ'; // Type of packing
  $pickingData->serviceTypeID    = $service_id;   // Service type 3 from Speedy nomenclature
  $pickingData->takingDate       = $taking_date;  // Taking date is today
  $pickingData->fixedTimeDelivery= ($fixed_time_is_allowed == 1) ? $fixed_hour.$fixed_minutes : null;

  // In this example sender is authenticated client. 
  // Therefore we fill Bill Of Lading sender client data with authenticated client data
  $sender = new ParamClientData();
  $sender->setClientId($senderClientData->getClientId());
  $sender->setContactName($senderContactName);
  $senderPhoneNumber = new ParamPhoneNumber();
  $senderPhoneNumber->setNumber($senderPhone);
  $sender->setPhones(array(0 => $senderPhoneNumber));

//  $arrQuarters = $eps->listQuarters('СТОРГОЗИЯ', $receiverSiteId);
//  if(count($arrQuarters) > 0) $receiverResultQuarter = $arrQuarters[0];

  // Finally set receiver address fields
  $receiverAddress = new ParamAddress();
  $receiverAddress->setSiteId($receiverSiteId);
//  if(isset($receiverResultQuarter)) {
  if(false) {
    $receiverAddress->setQuarterType($receiverResultQuarter->getType());
    $receiverAddress->setQuarterName($receiverResultQuarter->getName());
    $receiverAddress->setQuarterId($receiverResultQuarter->getId());
    $receiverAddress->setBlockNo('5');
    $receiverAddress->setEntranceNo('1');
    $receiverAddress->setFloorNo('2');
    $receiverAddress->setApartmentNo('201');
  }
  else {
    $receiverAddress->setAddressNote($customer_address_street);
  }
  
  // Set receiver client data
  $receiver = new ParamClientData();
  if($speedy_office_id == 0) $receiver->setAddress($receiverAddress);
  $receiver->setPartnerName($customer_fullname);
  $paramPhoneNumber = new ParamPhoneNumber();
  $paramPhoneNumber->setNumber($customer_address_phone);
  $receiver->setPhones(array(0 => $paramPhoneNumber));
  $receiver->setContactName($customer_fullname);

  $picking = new ParamPicking();
  $picking->setServiceTypeId($pickingData->serviceTypeID);
  $picking->setBackDocumentsRequest($pickingData->backDocumentReq);
  $picking->setBackReceiptRequest($pickingData->backReceiptReq);
  $picking->setWillBringToOffice(!is_null($pickingData->bringToOfficeId));
  $picking->setOfficeToBeCalledId($pickingData->takeFromOfficeId);
  $picking->setParcelsCount($pickingData->parcelsCount);
  $picking->setWeightDeclared($pickingData->weightDeclared);
  $picking->setContents($pickingData->contents);
  $picking->setPacking($pickingData->packing);
  $picking->setDocuments($pickingData->documents);
  $picking->setPalletized($pickingData->palletized);
  $picking->setFragile($pickingData->fragile);
  $picking->setSender($sender);
  $picking->setReceiver($receiver);
  $picking->setPayerType($pickingData->payerType);
  $picking->setTakingDate($pickingData->takingDate);
  $picking->setFixedTimeDelivery($pickingData->fixedTimeDelivery);
  $picking->setAmountCodBase($pickingData->amountCODBase);
  $resultBOL = $eps->createBillOfLading($picking);
  
//  echo "<pre>"print_r($resultBOL);
  
//  $resultCalculation = $eps->calculatePicking($picking);
//  echo "<pre>";print_r($resultCalculation);exit;


  // Идентификатор на откритата товарителница
  $arrParcels = $resultBOL->getGeneratedParcels();
  $pickingId  = $arrParcels[0]->getParcelId();

  echo "<br><br>";
  echo "Товарителницата за пратка 1 е открита с No.".$pickingId."<br>";
  
  // Печат на товарителница
  $paramPDF = new ParamPDF();
  $paramPDF->setIds(array(0=>$pickingId));
  $paramPDF->setType(ParamPDF::PARAM_PDF_TYPE_BOL);
  $paramPDF->setIncludeAutoPrintJS(true);
  echo "<br><br>";
  echo "Печат на товарителница No.".$pickingId." за пратка 1 [createPDF]...<br>";
  echo "<br><br>";
  echo "Параметри:<br>";
  echo "----------<br>";
  print_r($paramPDF);
      // Запис на pdf-а на товарителницата във файл
  $fileNameOnly = $eps->getUsername()."_picking_".$pickingId."_".time().".pdf";
  $outputPDFFolder = "/home/art93/public_html/frontstore/speedy/pdf-BOL/";
  $fileName = $outputPDFFolder.$fileNameOnly;
  file_put_contents($fileName, $eps->createPDF($paramPDF), FILE_APPEND | LOCK_EX);
  echo "<br>";
  echo "Tоварителница No.".$pickingId." за пратка 1 е съхранена във файл: ".$fileName."<br>";
  
?>