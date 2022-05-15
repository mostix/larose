<?php

// Utility methods
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'speedy-eps-lib'.DIRECTORY_SEPARATOR.'util'.DIRECTORY_SEPARATOR.'Util.class.php';

// Facade class
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'speedy-eps-lib'.DIRECTORY_SEPARATOR.'ver01'.DIRECTORY_SEPARATOR.'EPSFacade.class.php';

// Implementation class
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'speedy-eps-lib'.DIRECTORY_SEPARATOR.'ver01'.DIRECTORY_SEPARATOR.'soap'.DIRECTORY_SEPARATOR.'EPSSOAPInterfaceImpl.class.php';




try {
	header("Content-Type: application/json; charset=utf-8");

    if (function_exists("date_default_timezone_set")) {
        date_default_timezone_set(Util::SPEEDY_TIME_ZONE);
        $timeZone = date_default_timezone_get();
    } else {
        putenv("TZ=".Util::SPEEDY_TIME_ZONE);
        $timeZone = getenv("TZ");
    }

    $method = "login";
    
    $username = 999816;
    $password = 1367878673;
    $eps = new EPSFacade(new EPSSOAPInterfaceImpl("https://www.speedy.bg/eps/main01.wsdl"), $username,  $password);
    
    
    if ($method == 'login') {
		$arrJson = login($eps);    	
    } else if ($method == 'listSites') {
		$arrJson = listSites($eps);
    } else if ($method == 'listQuarterTypes') {
    	$arrJson = listQuarterTypes($eps);
   	} else if ($method == 'listStreetTypes') {
    	$arrJson = listStreetTypes($eps);
   	} else if ($method == 'listStreets') {
    	$arrJson = listStreets($eps);
   	} else if ($method == 'listQuarters') {
   		$arrJson = listQuarters($eps);
	} else if ($method == 'listBlocks') {
		$arrJson = listBlocks($eps);
	} else if ($method == 'listCommonObjects') {
		$arrJson = listCommonObjects($eps);
	} else if ($method == 'listOffices') {
		$arrJson = listOffices($eps);
	} else if ($method == 'listOfficesEx') {
		$arrJson = listOfficesEx($eps);
	} else if ($method == 'validateAddress') {
		$arrJson = validateAddress($eps);
    } else {
    	throw new Exception('Unknown method.');
    }
  
	echo json_encode($arrJson);

} catch (Exception $ex) {
    echo json_encode($ex);
}

function login($eps) {
	$arrJson = array();
	try {
		$resultLogin = $eps->login();
		$arrJson = array(
				"status" => 0,
				"message" => "OK",
				"exception" => "",
				"clientId" => $resultLogin->getClientId()
		);
	} catch (Exception $sf) {
		$arrJson = array(
				"status" => 1,
				"message" => "Invalid user or communication error",
				"exception" => $sf->getMessage(),
				"clientId" => ""
		);
	}
	return $arrJson;
}

function listSites($eps) {
	$siteType = $_REQUEST['siteType'];
	$siteName = $_REQUEST['siteName'];
	
	try {
		$arrResultSites = $eps->listSites($siteType, $siteName);
		$arrJsonResult = array();
		for($i = 0; $i < count($arrResultSites); $i++) {
			$resultSite = $arrResultSites[$i];
			$arrJsonResult[$i] = array(
					"id" => $resultSite->getId(),
					"type" => $resultSite->getType(),
					"name" => $resultSite->getName(),
					"municipality" => $resultSite->getMunicipality(),
					"region" => $resultSite->getRegion(),
					"postCode" => $resultSite->getPostCode(),
					"addrNomen" => $resultSite->getAddrNomen()->getValue()
			);
		}
		$arrJson = array(
				"status" => 0,
				"message" => "OK",
				"exception" => "",
				"result" => $arrJsonResult
		);
	} catch (Exception $sf) {
		$arrJson = array(
				"status" => 1,
				"message" => "Invalid user or communication error",
				"exception" => $sf->getMessage(),
				"result" => array()
		);
	}
	return $arrJson;
}


function listQuarterTypes($eps) {
	try {
		$arrQuarterTypes = $eps->listQuarterTypes();
		$arrJsonResult = array();
		for($i = 0; $i < count($arrQuarterTypes); $i++) {
			$quarterType = $arrQuarterTypes[$i];
			$arrJsonResult[$i] = array(
					"name" => $quarterType
			);
		}
		$arrJson = array(
				"status" => 0,
				"message" => "OK",
				"exception" => "",
				"result" => $arrJsonResult
		);
	} catch (Exception $sf) {
		$arrJson = array(
				"status" => 1,
				"message" => "Invalid user or communication error",
				"exception" => $sf->getMessage(),
				"result" => array()
		);
	}
	return $arrJson;
}

function listStreetTypes($eps) {
	try {
		$arrStreetTypes = $eps->listStreetTypes();
		$arrJsonResult = array();
		for($i = 0; $i < count($arrStreetTypes); $i++) {
			$streetType = $arrStreetTypes[$i];
			$arrJsonResult[$i] = array(
					"name" => $streetType
			);
		}
		$arrJson = array(
				"status" => 0,
				"message" => "OK",
				"exception" => "",
				"result" => $arrJsonResult
		);
	} catch (Exception $sf) {
		$arrJson = array(
				"status" => 1,
				"message" => "Invalid user or communication error",
				"exception" => $sf->getMessage(),
				"result" => array()
		);
	}
	return $arrJson;
}

function listStreets($eps) {
	$siteId = $username = $_REQUEST['siteId'];
	$name = $_REQUEST['name'];
	try {
		$arrResultStreets = $eps->listStreets($name, $siteId);
		$arrJsonResult = array();
		for($i = 0; $i < count($arrResultStreets); $i++) {
			$resultStreet = $arrResultStreets[$i];
			$arrJsonResult[$i] = array(
					"id" => $resultStreet->getId(),
					"type" => $resultStreet->getType(),
					"name" => $resultStreet->getName(),
					"actualName" => $resultStreet->getActualName()
			);
		}
		$arrJson = array(
				"status" => 0,
				"message" => "OK",
				"exception" => "",
				"result" => $arrJsonResult
		);
	} catch (Exception $sf) {
		$arrJson = array(
				"status" => 1,
				"message" => "Invalid user or communication error",
				"exception" => $sf->getMessage(),
				"result" => array()
		);
	}
	return $arrJson;
}

function listQuarters($eps) {
	$siteId = $username = $_REQUEST['siteId'];
	$name = $_REQUEST['name'];
	try {
		$arrResultQuarters = $eps->listQuarters($name, $siteId);
		$arrJsonResult = array();
		for($i = 0; $i < count($arrResultQuarters); $i++) {
			$resultQuarter = $arrResultQuarters[$i];
			$arrJsonResult[$i] = array(
					"id" => $resultQuarter->getId(),
					"type" => $resultQuarter->getType(),
					"name" => $resultQuarter->getName(),
					"actualName" => $resultQuarter->getActualName()
			);
		}
		$arrJson = array(
				"status" => 0,
				"message" => "OK",
				"exception" => "",
				"result" => $arrJsonResult
		);
	} catch (Exception $sf) {
		$arrJson = array(
				"status" => 1,
				"message" => "Invalid user or communication error",
				"exception" => $sf->getMessage(),
				"result" => array()
		);
	}
	return $arrJson;
}

function listBlocks($eps) {
	$siteId = $username = $_REQUEST['siteId'];
	$name = $_REQUEST['name'];
	try {
		$arrBlocks = $eps->listBlocks($name, $siteId);
		$arrJsonResult = array();
		for($i = 0; $i < count($arrBlocks); $i++) {
			$block = $arrBlocks[$i];
			$arrJsonResult[$i] = array(
					"name" => $block
			);
		}
		$arrJson = array(
				"status" => 0,
				"message" => "OK",
				"exception" => "",
				"result" => $arrJsonResult
		);
	} catch (Exception $sf) {
		$arrJson = array(
				"status" => 1,
				"message" => "Invalid user or communication error",
				"exception" => $sf->getMessage(),
				"result" => array()
		);
	}	
	return $arrJson;
}

function listCommonObjects($eps) {
	$siteId = $username = $_REQUEST['siteId'];
	$name = $_REQUEST['name'];
	try {
		$arrResultCommonObjects = $eps->listCommonObjects($name, $siteId);
		$arrJsonResult = array();
		for($i = 0; $i < count($arrResultCommonObjects); $i++) {
			$resultCommonObject = $arrResultCommonObjects[$i];
			$arrJsonResult[$i] = array(
					"id" => $resultCommonObject->getId(),
					"type" => $resultCommonObject->getType(),
					"name" => $resultCommonObject->getName(),
					"address" => $resultCommonObject->getAddress()
			);
		}
		$arrJson = array(
				"status" => 0,
				"message" => "OK",
				"exception" => "",
				"result" => $arrJsonResult
		);
	} catch (Exception $sf) {
		$arrJson = array(
				"status" => 1,
				"message" => "Invalid user or communication error",
				"exception" => $sf->getMessage(),
				"result" => array()
		);
	}
	return $arrJson;
}

function listOffices($eps) {
	$siteId = $username = $_REQUEST['siteId'];
	$name = $_REQUEST['name'];
	try {		
		$arrResultOffices = $eps->listOffices($name, $siteId);
		$arrJsonResult = array();
		for($i = 0; $i < count($arrResultOffices); $i++) {
			$resultOffice = $arrResultOffices[$i];
			$resultOfficeAddress = $resultOffice->getAddress();
			if (!$resultOfficeAddress->isFullNomenclature()) {
				$officeSite = $eps->getSiteById($resultOfficeAddress->getSiteId());
				$addrNomen = $officeSite->getAddrNomen()->getValue();
			} else {
				$addrNomen = 'FULL';
			}
			$arrJsonResult[$i] = array(
					"id" => $resultOffice->getId(),
					"name" => $resultOffice->getName(),
					"workingTimeFrom" => $resultOffice->getWorkingTimeFrom(),
					"workingTimeTo" => $resultOffice->getWorkingTimeTo(),
					"workingTimeHalfFrom" => $resultOffice->getWorkingTimeHalfFrom(),
					"workingTimeHalfTo" => $resultOffice->getWorkingTimeHalfTo(),
					"address" => array(
							"streetId" => $resultOfficeAddress->getStreetId(),
							"streetType" => $resultOfficeAddress->getStreetType(),
							"streetName" => $resultOfficeAddress->getStreetName(),
							"streetNo" => $resultOfficeAddress->getStreetNo(),
							"quarterId" => $resultOfficeAddress->getQuarterId(),
							"quarterType" => $resultOfficeAddress->getQuarterType(),
							"quarterName" => $resultOfficeAddress->getQuarterName(),
							"blockNo" => $resultOfficeAddress->getBlockNo(),
							"entranceNo" => $resultOfficeAddress->getEntranceNo(),
							"floorNo" => $resultOfficeAddress->getFloorNo(),
							"apartmentNo" => $resultOfficeAddress->getApartmentNo(),
							"commonObjectId" => $resultOfficeAddress->getCommonObjectId(),
							"commonObjectName" => $resultOfficeAddress->getCommonObjectName(),
							"addressNote" => $resultOfficeAddress->getAddressNote(),
							"coordX" => $resultOfficeAddress->getCoordX(),
							"coordY" => $resultOfficeAddress->getCoordY(),
							"coordTypeId" => $resultOfficeAddress->getCoordTypeId(),
							"siteDetails" => $resultOfficeAddress->getSiteDetails(),
							"site" => array(
								"id" => $resultOfficeAddress->getSiteId(),
								"type" => $resultOfficeAddress->getSiteType(),
								"name" => $resultOfficeAddress->getSiteName(),
								"municipality" => $resultOfficeAddress->getMunicipalityName(),
								"region" => $resultOfficeAddress->getRegionName(),
								"postCode" => $resultOfficeAddress->getPostCode(),
								"addrNomen" => $addrNomen
							)
						)
			);
		}
		$arrJson = array(
				"status" => 0,
				"message" => "OK",
				"exception" => "",
				"result" => $arrJsonResult
		);
	} catch (Exception $sf) {
		$arrJson = array(
				"status" => 1,
				"message" => "Invalid user or communication error",
				"exception" => $sf->getMessage(),
				"result" => array()
		);
	}	
	return $arrJson;
}

function listOfficesEx($eps) {
	$siteId = $username = $_REQUEST['siteId'];
	$name = $_REQUEST['name'];
	try {	
		$arrResultOfficesEx = $eps->listOfficesEx($name, $siteId);
		$arrJsonResult = array();
		for($i = 0; $i < count($arrResultOfficesEx); $i++) {
			$resultOfficeEx = $arrResultOfficesEx[$i];
			$resultOfficeAddressEx = $resultOfficeEx->getAddress();
			$resultOfficeSite = $resultOfficeAddressEx->getResultSite();
			$arrJsonResult[$i] = array(
					"id" => $resultOfficeEx->getId(),
					"name" => $resultOfficeEx->getName(),
					"workingTimeFrom" => $resultOfficeEx->getWorkingTimeFrom(),
					"workingTimeTo" => $resultOfficeEx->getWorkingTimeTo(),
					"workingTimeHalfFrom" => $resultOfficeEx->getWorkingTimeHalfFrom(),
					"workingTimeHalfTo" => $resultOfficeEx->getWorkingTimeHalfTo(),
					"address" => array(
							"postCode" => $resultOfficeAddressEx->getPostCode(),
							"streetId" => $resultOfficeAddressEx->getStreetId(),
							"streetType" => $resultOfficeAddressEx->getStreetType(),
							"streetName" => $resultOfficeAddressEx->getStreetName(),
							"streetNo" => $resultOfficeAddressEx->getStreetNo(),
							"quarterId" => $resultOfficeAddressEx->getQuarterId(),
							"quarterType" => $resultOfficeAddressEx->getQuarterType(),
							"quarterName" => $resultOfficeAddressEx->getQuarterName(),
							"blockNo" => $resultOfficeAddressEx->getBlockNo(),
							"entranceNo" => $resultOfficeAddressEx->getEntranceNo(),
							"floorNo" => $resultOfficeAddressEx->getFloorNo(),
							"apartmentNo" => $resultOfficeAddressEx->getApartmentNo(),
							"commonObjectId" => $resultOfficeAddressEx->getCommonObjectId(),
							"commonObjectName" => $resultOfficeAddressEx->getCommonObjectName(),
							"addressNote" => $resultOfficeAddressEx->getAddressNote(),
							"coordX" => $resultOfficeAddressEx->getCoordX(),
							"coordY" => $resultOfficeAddressEx->getCoordY(),
							"coordTypeId" => $resultOfficeAddressEx->getCoordTypeId(),
							"fullAddressString" => $resultOfficeAddressEx->getFullAddressString(),
							"site" => ($resultOfficeSite == null) ?
								array(
										"id" => 0,
										"type" => "",
										"name" => "",
										"municipality" => "",
										"region" => "",
										"postCode" => "",
										"addrNomen" => ""
								):
								array(
									"id" => $resultOfficeAddressEx->getResultSite()->getId(),
									"type" => $resultOfficeAddressEx->getResultSite()->getType(),
									"name" => $resultOfficeAddressEx->getResultSite()->getName(),
									"municipality" => $resultOfficeAddressEx->getResultSite()->getMunicipality(),
									"region" => $resultOfficeAddressEx->getResultSite()->getRegion(),
									"postCode" => $resultOfficeAddressEx->getResultSite()->getPostCode(),
									"addrNomen" => $resultOfficeAddressEx->getResultSite()->getAddrNomen()->getValue()
									
							)
					)
			);
		}
		$arrJson = array(
				"status" => 0,
				"message" => "OK",
				"exception" => "",
				"result" => $arrJsonResult
		);
	} catch (Exception $sf) {
		$arrJson = array(
				"status" => 1,
				"message" => "Invalid user or communication error",
				"exception" => $sf->getMessage(),
				"result" => array()
		);
	}
	return $arrJson;
}


function validateAddress($eps) {
	$paramAddress = new ParamAddress();
	$paramAddress->setSiteId($_REQUEST['siteId']);
	$paramAddress->setStreetName($_REQUEST['streetName']);
	$paramAddress->setStreetType($_REQUEST['streetType']);
	$paramAddress->setStreetId($_REQUEST['streetId']);
	$paramAddress->setQuarterName($_REQUEST['quarterName']);
	$paramAddress->setQuarterType($_REQUEST['quarterType']);
	$paramAddress->setQuarterId($_REQUEST['quarterId']);
	$paramAddress->setStreetNo($_REQUEST['streetNo']);
	$paramAddress->setBlockNo($_REQUEST['blockNo']);
	$paramAddress->setEntranceNo($_REQUEST['entranceNo']);
	$paramAddress->setFloorNo($_REQUEST['floorNo']);
	$paramAddress->setApartmentNo($_REQUEST['apartmentNo']);
	$paramAddress->setAddressNote($_REQUEST['addressNote']);
	$paramAddress->setCommonObjectId((float)$_REQUEST['commonObjectId']);
	$paramAddress->setCoordX($_REQUEST['coordX']);
	$paramAddress->setCoordY($_REQUEST['coordY']);

	$arrJson = null;
	try {
		$result = $eps->validateAddress($paramAddress, 0);
		if ($result) {
			$arrJson = array(
				"status" => 0,
				"message" => "OK",
				"exception" => "",
				"result" => "" + $result
			);
		} else {
			$arrJson = array(
					"status" => 1,
					"message" => "Address is invalid",
					"exception" => "",
					"result" => "" + $result
			);
		}
	} catch (Exception $sf) {
		$arrJson = array(
			"status" => 2,
			"message" => "Address is invalid",
			"exception" => $sf->getMessage(),
			"result" => "none"
		);
	}
	return $arrJson;
}
?>	