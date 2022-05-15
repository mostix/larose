<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Utility methods
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'speedy-eps-lib'.DIRECTORY_SEPARATOR.'util'.DIRECTORY_SEPARATOR.'Util.class.php';

// Facade class
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'speedy-eps-lib'.DIRECTORY_SEPARATOR.'ver01'.DIRECTORY_SEPARATOR.'EPSFacade.class.php';

// Implementation class
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'speedy-eps-lib'.DIRECTORY_SEPARATOR.'ver01'.DIRECTORY_SEPARATOR.'soap'.DIRECTORY_SEPARATOR.'EPSSOAPInterfaceImpl.class.php';

header("Content-Type: text/html; charset=utf-8");

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
//Determine courier taking date
//Taking date is used to determine available services depending on sender site and is also a factor in courier service price calculation. Therefore the same picking could have different price for different taking dates.


$serviceTypeID   = 113;      // Example service type from Speedy nomenclature
$bringToOfficeId = null;   // 683 The picking will not be brought to office, hence this ID is null. Otherwise it should be ID of valid Speedy office
$fromMoment      = time(); // Current time. We are interested for available taking days from current moment

// Get sender site ID
// In this example sender is authenticated client
$senderClientData = $eps->getClientById($eps->getResultLogin()->getClientId());
$senderSiteId = $senderClientData->getAddress()->getSiteId();
$clientConfiguration = new StdClass();
$clientConfiguration->arrEnabledServices = array(0=>2,1=>3,2=>112,3=>113);  // Конфигурирайте ограничен списък от услуги на Speedy, с които клиентът ще работи

$arrTakingDates = $eps->getAllowedDaysForTaking(
    $serviceTypeID, is_null($bringToOfficeId) ? $senderSiteId : null, $bringToOfficeId, $fromMoment
);

if (count($arrTakingDates) == 0) {
    throw new ClientException('There are no dates available for taking');
} else if (count($arrTakingDates) == 1) {
    // There is only one date available for taking
    $takingDate = $arrTakingDates[0];
} else {
    // There are several dates available. We take first one, but user is free to select taking date from all of them
    $takingDate = $arrTakingDates[1];
}
echo "<pre>";
//var_dump($takingDate);
print_r($arrTakingDates);
print_r($senderClientData);

//Calculate
//Calculate the price of a picking (before picking registration)


// Init current date
$todayDate = date("Y-m-d"); 
//$takingDate       = strtotime(date("Y-m-d", strtotime($todayDate)) . " +1 day"); // We need taking date to be tomorrow

$calculationData = new StdClass();
$calculationData->weightDeclared   = 1.25;  // Decalred weight
$calculationData->bringToOfficeId  = $bringToOfficeId;  // Id of the office that sender should bring the parcel(s). Courier will visit sender address if this value is null
$calculationData->takeFromOfficeId = null;  // Id of the office that recipient should take the parcel(s) from. Courier will visit recipient address if this value is null
$calculationData->parcelsCount     = 1;     // Parcels count
$calculationData->documents        = false; // Flag for documents
$calculationData->palletized       = false; // Flag for pallets
$calculationData->fragile          = false; // flag for fragile content
$calculationData->payerType        = ParamCalculation::PAYER_TYPE_RECEIVER; // Determine the payer
$calculationData->amountCODBase    = 25;      // Collecton on Delivery amount
$calculationData->serviceTypeID    = $serviceTypeID;       // Service type 2 from Speedy nomenclature
$calculationData->takingDate       = $takingDate; // We need taking date to be tomorrow

// Get sender client data
// In this example sender is authenticated client
$senderClientData = $eps->getClientById($eps->getResultLogin()->getClientId());

// In this example receiver address is in city of Sofia
// Lookup for example receiver city of Sofia in Speedy address nomenclature
// We are sure we have results therefore array is not verified for null or empty values.
$receiverCity = "софия";
$arrSites = $eps->listSites('гр', $receiverCity);
$receiverSiteId = $arrSites[0]->getId();
$arrAvailableServices = $eps->listServicesForSites($takingDate, $senderSiteId, $receiverSiteId);
$arrSelectedServices = Util::serviceIntersection($arrAvailableServices, $clientConfiguration->arrEnabledServices);
//print_r($arrSelectedServices);
  foreach($arrAvailableServices as $services) {
    $type_id = $services->getTypeId();
    $type_name = $services->getName();
    $deadline = $services->getDeliveryDeadline();
    if(in_array($type_id, $arrSelectedServices)) {
      echo "<br>$type_id - $type_name - $deadline<br>";
      //print_r($services);
    }
  }

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
$paramCalculation->setServiceTypeId($calculationData->serviceTypeID);
$resultCalculation = $eps->calculate($paramCalculation);

print_r($resultCalculation);
//echo $resultCalculation->getAmounts()->getTotal();

//Create Bill Of Lading
//This example registers Bill Of Lading, but to trigger the process of taking and delivery, the client needs to order a courier.

// Init example picking structure with data
$pickingData = new StdClass();
$pickingData->weightDeclared   = 5.25;  // Decalred weight
$pickingData->bringToOfficeId  = null;  // Id of the office that sender should bring the parcel(s). Courier will visit sender address if this value is null
$pickingData->takeFromOfficeId = null;  // Id of the office that recipient should take the parcel(s) from. Courier will visit recipient address if this value is null
$pickingData->parcelsCount     = 1;     // Parcels count
$pickingData->documents        = false; // Flag for documents
$pickingData->palletized       = false; // Flag for pallets
$pickingData->fragile          = false; // flag for fragile content
$pickingData->payerType        = ParamCalculation::PAYER_TYPE_RECEIVER; // Determine the payer
$pickingData->amountCODBase    = $resultCalculation->getAmounts()->getTotal();      // Collecton on Delivery amount
$pickingData->backDocumentReq  = true;    // Back dcoument request flag
$pickingData->backReceiptReq   = false;   // Back receipt request flag
$pickingData->contents         = 'Дрехи'; // Content description
$pickingData->packing          = 'ПАКЕТ'; // Type of packing
$pickingData->serviceTypeID    = $serviceTypeID;       // Service type 3 from Speedy nomenclature
$pickingData->takingDate       = time();  // Taking date is today

// In this example sender is authenticated client. 
// Therefore we fill Bill Of Lading sender client data with authenticated client data
$senderClientData = $eps->getClientById($eps->getResultLogin()->getClientId());
$sender = new ParamClientData();
$sender->setClientId($senderClientData->getClientId());
$sender->setContactName('TEST TEST TEST');
$senderPhoneNumber = new ParamPhoneNumber();
$senderPhoneNumber->setNumber("7001 7001");
$sender->setPhones(array(0 => $senderPhoneNumber));

// In this example receiver address is "СОФИЯ, к-с ЛЮЛИН 7, бл.702 вх.1, ет.54, ап.1229"
// Lookup for example receiver city of Sofia in Speedy address nomenclature
// We are sure we have results therefore array is not verified for null or empty values.
$arrSites = $eps->listSites('гр', $receiverCity);
$receiverSiteId = $arrSites[0]->getId();

//$listOffices = $eps->listOfficesEx("",$receiverSiteId);
//if ($listOffices) {
//    foreach ($listOffices as $office) {
//        $offices[] = array(
//            'id' => $office->getId(),
//            'label' => $office->getId() . ' ' . $office->getName() . ', ' . $office->getAddress()->getFullAddressString(),
//            'value' => $office->getName()
//        );
//    }
//}
//print_r($offices);

// Lookup example receiver address quarter "Liulin 7" in city of Sofia in Speedy address nomenclature
// We are sure we have results therefore array is not verified for null or empty values.
// EPS supports similar methods for streets also
$arrQuarters = $eps->listQuarters('СТОРГОЗИЯ', $receiverSiteId);
$receiverResultQuarter = $arrQuarters[0];

// Finally set receiver address fields
$receiverAddress = new ParamAddress();
$receiverAddress->setSiteId($receiverSiteId);
$receiverAddress->setQuarterType($receiverResultQuarter->getType());
$receiverAddress->setQuarterName($receiverResultQuarter->getName());
$receiverAddress->setQuarterId($receiverResultQuarter->getId());
$receiverAddress->setBlockNo('5');
$receiverAddress->setEntranceNo('1');
$receiverAddress->setFloorNo('2');
$receiverAddress->setApartmentNo('201');

print_r($receiverAddress);

// Note that if you cannot determine address fields from input address text (f.e. you cannot structurally parse the input address)
// clients could use method setAddressNote. In that case setting an address will look like:
//
// $receiverAddress = new ParamAddress();
// $receiverAddress->setSiteId($receiverSiteId);
// $receiverAddress->setAddressNote('к-с ЛЮЛИН 7, бл.702 вх.1, ет.54, ап.1229');
//
// NOTE: Site name should not be placed in addressNote field, because site Id is passed separately and cannot be omitted

// Set receiver client data
$receiver = new ParamClientData();
$receiver->setAddress($receiverAddress);
$receiver->setPartnerName('RECEIVER PARTNER NAME');
$paramPhoneNumber = new ParamPhoneNumber();
$paramPhoneNumber->setNumber("7001 7001");
$receiver->setPhones(array(0 => $paramPhoneNumber));
$receiver->setContactName("TEST TEST TEST");

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
$picking->setAmountCodBase($pickingData->amountCODBase);
$resultBOL = $eps->createBillOfLading($picking);

print_r($resultBOL);

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

//------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------
// Коментираният в секцията по-долу код може да се ползва за печат на етикет за пратка 1,
// вместо печат на товарителница
//------------------------------------------------------------------------------------------------------

// Печат на етикет
$paramPDF = new ParamPDF();
$paramPDF->setIds(array(0=>$pickingId));
$paramPDF->setType(ParamPDF::PARAM_PDF_TYPE_LBL);
$paramPDF->setIncludeAutoPrintJS(true);
echo "<br><br>";
echo "Печат на етикет за пратка 1 с товарителница No.".$pickingId." [createPDF]...<br>";
echo "<br><br>";
echo "Параметри:<br>";
echo "----------<br>";
print_r($paramPDF);

// Запис на pdf-а на етикет във файл
$fileNameOnly = $eps->getUsername()."_lbl_".$pickingId."_".time().".pdf";
$fileName = $outputPDFFolder.$fileNameOnly;
//file_put_contents($fileName, $eps->createPDF($paramPDF), FILE_APPEND | LOCK_EX);

echo "<br>";
echo "Етикет за пратка 1 с товарителница No.".$pickingId." е съхранен във файл: ".$fileName."<br>";

//-------------------------------------------------------------------------------------------------------------------


//-------------------------------------------------------------------------------------------------------------------
// ОТКРИВАНЕ НА ВТОРА ТОВАРИТЕЛНИЦА (ПО-КОМПЛЕКСЕН ВАРИАНТ - ТРИПАКЕТНА СЪС ЗАСТРАХОВКА)
//-------------------------------------------------------------------------------------------------------------------
/*
//Предвиждаме пратката да е до адрес на получателя, в този случай е адрес на получател е необхдим.
$receiver->setAddress($receiverAddress);
// Данни за товарителница
echo "<br><br><br><br>";
echo "Откриване на товарителница за пратка 2 [createBillOfLading]...<br>";
echo "--------------------------------------------------------------<br>";
$picking2 = new ParamPicking();
$picking2->setServiceTypeId($serviceTypeID);
$picking2->setBackDocumentsRequest($pickingData->backDocumentReq);
$picking2->setBackReceiptRequest($pickingData->backReceiptReq);
$picking2->setWillBringToOffice(!is_null($pickingData->bringToOfficeId));

$picking2->setWeightDeclared($pickingData->weightDeclared);
$picking2->setContents($pickingData->contents);
$picking2->setPacking($pickingData->packing);
$picking2->setDocuments($pickingData->documents);
$picking2->setPalletized($pickingData->palletized);
$picking2->setFragile($pickingData->fragile);
$picking2->setSender($sender);
$picking2->setReceiver($receiver);
$picking2->setPayerType($pickingData->payerType);
$picking2->setTakingDate($pickingData->takingDate);
$picking2->setAmountInsuranceBase(20);
$picking2->setPayerTypeInsurance(ParamCalculation::PAYER_TYPE_SENDER);
$picking2->setFragile(true);
$picking2->setParcelsCount(3); // Пратка с 3 пакета

echo "Данни на товарителница за пратка 2:<br>";
print_r($picking2);
// Откриване на товарителница. Откриването се прави след окомплектоване на пратката, а не при поръчка (в онлайн магазина)
$resultBOL2 = $eps->createBillOfLading($picking2);
echo "<br><br>";
echo "Резултат:<br>";
echo "---------<br>";
print_r($resultBOL2);

// Идентификатор на откритата товарителница. Идентификаторът на откритата товарителница е и идентификатор на първия пакет
$arrParcels = $resultBOL2->getGeneratedParcels();
$pickingId2 = $arrParcels[0]->getParcelId();

$pickingId2Parcel1Id = $pickingId2; // Същото като $arrParcels[0]->getParcelId();
$pickingId2Parcel2Id = $arrParcels[1]->getParcelId();
$pickingId2Parcel3Id = $arrParcels[2]->getParcelId();

echo "<br><br>";
echo "Товарителницата за пратка 2 е открита с No.".$pickingId2."<br>";
*/
//-------------------------------------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------------------------
// Коментираният в секцията по-долу код може да се ползва за печат на окритата товарителница за пратка 2
//------------------------------------------------------------------------------------------------------
/*
$paramPDF = new ParamPDF();
$paramPDF->setIds(array(0=>$pickingId2));
$paramPDF->setType(ParamPDF::PARAM_PDF_TYPE_BOL);
$paramPDF->setIncludeAutoPrintJS(true);
echo "<br><br>";
echo "Печат на товарителница No.".$pickingId2." за пратка 2 [createPDF]...<br>";
echo "<br><br>";
echo "Параметри:<br>";
echo "----------<br>";
print_r($paramPDF);

// Запис на pdf-а на товарителницата във файл
$fileNameOnly = $eps->getUsername()."_picking_".$pickingId2."_".time().".pdf";
$fileName = $outputPDFFolder.$fileNameOnly;
file_put_contents($fileName, $eps->createPDF($paramPDF), FILE_APPEND | LOCK_EX);

echo "<br>";
echo "Tоварителница No.".$pickingId2." за пратка 2 е съхранена във файл: ".$fileName."<br>";
*/
//-------------------------------------------------------------------------------------------------------


// ------------------------------------------------------------------------------------------------------
// Групов печат на етикети за всеки пакет по окритата товарителница за пратка 2.
// (Етикетите могат да се печат и последователно един по един, по същия начин,
// с подаване на списък с един идентификатор на сътоветния пакет в аргументите на метода за печат)
// ------------------------------------------------------------------------------------------------------
/*
$paramPDF = new ParamPDF();
$paramPDF->setIds(array(0=>$pickingId2Parcel1Id, 1=>$pickingId2Parcel2Id, 2=>$pickingId2Parcel3Id));
$paramPDF->setType(ParamPDF::PARAM_PDF_TYPE_LBL);
$paramPDF->setIncludeAutoPrintJS(true);
echo "<br><br>";
echo "Групов печат на етикети за пратка 2 с товарителница No.".$pickingId2." [createPDF]...<br>";
echo "<br><br>";
echo "Параметри:<br>";
echo "----------<br>";
print_r($paramPDF);

// Запис на pdf-а на етикетите във файл
$fileNameOnly = $eps->getUsername()."_lbl_".$pickingId2."_".time().".pdf";
$fileName = $outputPDFFolder.$fileNameOnly;
file_put_contents($fileName, $eps->createPDF($paramPDF), FILE_APPEND | LOCK_EX);

echo "<br>";
echo "Етикетите за пратка 2 с товарителница No.".$pickingId2." са съхранени във файл: ".$fileName."<br>";
*/
//-----------------------------------------------------------------------------------------------------


//-------------------------------------------------------------------------------------------------------------------
// ЗАЯВКА ЗА КУРИЕР
// Заявката се прави в края на работния ден - за предпочитане веднъж дневно, като включва всички окомплектовани пратки за деня
// За целта се подава списък от всички пратки, които са за този ден.
//-------------------------------------------------------------------------------------------------------------------

// Данни за заявка за куриер
// ReadinessTime не може да бъде време преди текущото време на генериране на заявката
echo "<br><br><br><br>";
echo "Заявка за куриер за двете окомплектовани пратки [createOrder]...<br>";
echo "----------------------------------------------------------------<br>";
$order = new ParamOrder();
$order->setBillOfLadingsList(array(0 => $pickingId));             // Списък от товарителници
//$order->setBillOfLadingsList(array(0 => $pickingId, 1 => $pickingId2));             // Списък от товарителници
$order->setBillOfLadingsToIncludeType(ParamOrder::ORDER_BOL_INCLUDE_TYPE_EXPLICIT); // Заявка за куриер за списъка
$order->setPickupDate($pickingData->takingDate);                                    // Дата на вземане на пратката от куриер
$order->setReadinessTime(1730);                                                     // Пакетите са готови за вземане след 17:30
$order->setContactName($senderClientData->getContactName());                          // Име за контакт
$paramPhoneNumber = new ParamPhoneNumber();
$paramPhoneNumber->setNumber($senderClientData->getPhones());
$order->setPhoneNumber($paramPhoneNumber);                                          // Тел. номер за контакт
$order->setWorkingEndTime(1800);                                                    // Край на работното време на подателя - 18:00

echo "<br><br>";
echo "Данни на заявката за куриер за двете окомплектовани пратки:<br>";
print_r($order);

// Създаване на заявка
$arrResultOrderPickingInfo = $eps->createOrder($order);

echo "<br>";
echo "Заявката за куриер е направена.<br>";
echo "<br><br>";
echo "Резултат:<br>";
echo "---------<br>";
print_r($arrResultOrderPickingInfo);
echo "<br><br>";

// Проверка за успешна заявка
for ($i = 0; $i < count($arrResultOrderPickingInfo); ++$i) {
    $arrErrorDescriptions = $arrResultOrderPickingInfo[$i]->getErrorDescriptions();
    if (count($arrErrorDescriptions) > 0) {
        // Неуспешна заявка. Грешките се съдържат в масива. Обработка на грешките
        echo " Грешки при заявка за куриер за пратка с товарителница ".$arrResultOrderPickingInfo[$i]->getBillOfLading().".<br>";
        for ($j = 0; $j < count($arrErrorDescriptions); ++$j) {
            echo "    Грешкa ".($j + 1).": ".$arrErrorDescriptions[$j]."<br>";
        }
    } else {
        // Успешна заявка за куриер
        echo "<br>";
        echo "Товарителница ".$arrResultOrderPickingInfo[$i]->getBillOfLading()." е успешно заявена.<br>";
    }
}
?>
