<?php
require_once("config.php");

class SmartschoolConnection implements SmartSOAP {

	// URL opbouwen vanuit constante
	function getURL() {
		return "https://".self::SMART_PLATFORM."/Webservices/V3?wsdl";
	}
	
	// addCourse
	// retourneert boolean TRUE als gelukt, anders string met foutboodschap
	function addCourse($strCourseName, $strCourseDescription, $intVisibility = 1) {
		try {
			$smartClient = new SoapClient($this->getURL());
			$result = $smartClient->addCourse(self::SMART_WSP, $strCourseName, $strCourseDescription, $intVisibility); // retourneert string(1) "0" bij succes, anders string met foutcode
			$result = intval($result);
			if ($result == 0) {
				return TRUE;
			} else {
				return $this->returnErrorCode($result);
			}
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
	}
	
	// addCourseStudents
	// retourneert boolean TRUE als gelukt, anders string met foutboodschap
	function addCourseStudents($strCourseName, $strCourseDescription, $strGroupIds) {
		try {
			$smartClient = new SoapClient($this->getURL());
			$result = $smartClient->addCourseStudents(self::SMART_WSP, $strCourseName, $strCourseDescription, $strGroupIds); // retourneert int 0 bij succes, anders int met foutcode
			if ($result == 0) {
				return TRUE;
			} else {
				return $this->returnErrorCode($result);
			}
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
	}
	
	// addCourseTeacher *** WERKT NOG NIET!!!
	// retourneert boolean TRUE als gelukt, anders string met foutboodschap
	function addCourseTeacher($strCourseName, $strCourseDescription, $strLeerkrachtId, $strCoLeerkrachtIds = NULL) {
		try {
			$smartClient = new SoapClient($this->getURL());
			$result = $smartClient->addCourseTeacher(self::SMART_WSP, $strCourseName, $strCourseDescription, $strLeerkrachtId, $strCoLeerkrachtIds); // retourneert int 0 bij succes, anders int met foutcode
			if ($result == 0) {
				return TRUE;
			} else {
				return $this->returnErrorCode($result);
			}
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
	}
	
	// changeGroupOwners
	// retourneert boolean TRUE als gelukt, anders string met foutboodschap
	function changeGroupOwners($strGroupId, $strUserIds) {
		try {
			$smartClient = new SoapClient($this->getURL());
			$result = $smartClient->changeGroupOwners(self::SMART_WSP, $strGroupId, $strUserIds); // retourneert int 0 bij succes, anders int met foutcode
			if ($result == 0) {
				return TRUE;
			} else {
				return $this->returnErrorCode($result);
			}
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
	}

	// changeInternNumber
	// retourneert boolean TRUE als gelukt, anders string met foutboodschap
	function changeInternNumber($strUsername, $strNewInternNumber) {
		try {
			$smartClient = new SoapClient($this->getURL());
			$result = $smartClient->changeInternNumber(self::SMART_WSP, $strUsername, $strNewInternNumber); // retourneert int 0 bij succes, anders int met foutcode
			if ($result == 0) {
				return TRUE;
			} else {
				return $this->returnErrorCode($result);
			}
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
	}
	
	// Alle accounts ophalen
	function getAllAccountsExtended($sGroup) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("getAllAccountsExtended", array(self::SMART_WSP,$sGroup,'1'));
		$decoded = json_decode($encoded, TRUE);

		return $decoded;
	}

	function getAllAccounts($sGroupName, $bRecursive = FALSE) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}

		$encoded = $client->__soapCall("getAllAccounts", array(self::SMART_WSP, $sGroupName, $bRecursive));
		$decoded = base64_decode($encoded);

		$xml = simplexml_load_string($decoded, "SimpleXMLElement", LIBXML_NOCDATA);
		$json = json_encode($xml);
		$tmpArray = json_decode($json,TRUE);

		return $tmpArray['account'];
	}
	
	// Klaslijst ophalen
	function getClassList() {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("getClassList", array(self::SMART_WSP));
		$decoded = unserialize($encoded);
		
		return $decoded;
	}
	
	// Klastitularissen ophalen
	function getClassTeachers() {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("getClassTeachers", array(self::SMART_WSP, true));
		$decoded = json_decode($encoded, TRUE);
		
		return $decoded;
	}
	
	// Gedetailleerde persoonsinformatie opvragen
	function getUserDetails($sUserName) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}

		$encoded = $client->__soapCall("getUserDetailsByUsername", array(self::SMART_WSP, $sUserName));
		$decoded = json_decode($encoded, TRUE);
		
		return $decoded;
	}

	function getUserDetailsByNumber($iNumber) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}

		$encoded = $client->__soapCall("getUserDetailsByNumber", array(self::SMART_WSP, $iNumber));
		$decoded = json_decode($encoded, TRUE);
		
		return $decoded;
	}
	
	function getUserOfficialClass($sUserName) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}

		$encoded = $client->__soapCall("getUserOfficialClass", array(self::SMART_WSP, $sUserName));
		$decoded = json_decode($encoded, TRUE);
		
		return $decoded;
	}

	function sendMsg($sRecipient, $sTitle, $sBody, $sSender = null, $aAttachments = null, $iType = 0) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$client->__soapCall("sendMsg", array(self::SMART_WSP, $sRecipient, $sTitle, $sBody, $sSender, $aAttachments, $iType));
	}
	
	function returnErrorCode($iNum) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("returnJsonErrorCodes", array());
		$decoded = json_decode($encoded, TRUE);
		
		return $decoded[$iNum];
	}

	function returnErrorCodes() {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("returnJsonErrorCodes", array());
		$decoded = json_decode($encoded, TRUE);
		
		return $decoded;
	}
	
	function saveUserParameter($sPupilId, $sParamName, $sParamValue) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("saveUserParameter", array(self::SMART_WSP, $sPupilId, $sParamName, $sParamValue));
		
		return $encoded;
	}
	
	function savePassword($sPupilId, $sPassword, $sAccountType = 0) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("setAccountStatus", array(self::SMART_WSP, $sPupilId, 'active'));
		$encoded = $client->__soapCall("savePassword", array(self::SMART_WSP, $sPupilId, $sPassword, $sAccountType));
		
		return $encoded;
		
	}
	
	// Gedetailleerde persoonsinformatie opvragen obv gebruikersnaam
	// NIEUW SINDS 2016-10!!!
	function getUserDetailsByUsername($sUserName) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}

		$encoded = $client->__soapCall("getUserDetailsByUsername", array(self::SMART_WSP, $sUserName));
		$decoded = json_decode($encoded, TRUE);
		
		return $decoded;
	}

	// Skore-koppelingen ophalen
	function getSkoreClassTeacherCourseRelation() {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("getSkoreClassTeacherCourseRelation", array(self::SMART_WSP));
		return $encoded;
	}

	function isUserInGroup($iUserId, $sGroupName) { // boolean
		$rawData = $this->getUserDetailsByNumber($iUserId);
		if (array_search($sGroupName, array_column($rawData['groups'], 'code')) === FALSE) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	function isUserInOneOfGroups($iUserId, $aGroupNames) { // boolean
		$rawData = $this->getUserDetailsByNumber($iUserId);
		$blnReturn = FALSE;
		foreach ($aGroupNames as $groupName) {
			if (array_search($groupName, array_column($rawData['groups'], 'code')) !== FALSE) {
				$blnReturn = TRUE;
			}
		}
		return $blnReturn;
	}
	
	function getClassCodeFromName($sClassName) { // string
		$classList = $this->getClassList();
		return $classList[array_search($sClassName, array_column($classList, 'name'))]['code'];
	}
	
	function getClassNameFromCode($sClassCode) { // string
		$classList = $this->getClassList();
		return $classList[array_search($sClassCode, array_column($classList, 'code'))]['name'];
	}

	function getAllGroupsAndClasses() {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("getAllGroupsAndClasses", array(self::SMART_WSP));
		$decoded = base64_decode($encoded);

		$xml = simplexml_load_string($decoded, "SimpleXMLElement", LIBXML_NOCDATA);
		$json = json_encode($xml);
		$tmpArray = json_decode($json,TRUE);
		
		return $tmpArray;
	}
	
	function saveUserToClasses($iUserId, $sGroupId) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("saveUserToClasses", array(self::SMART_WSP, $iUserId, $sGroupId));
		return $encoded;
	}
	
	function removeUserFromGroup($iUserId, $sGroupId) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("removeUserFromGroup", array(self::SMART_WSP, $iUserId, $sGroupId));
		return $encoded;
	}
	
	function clearGroup($sGroupId) {
		try {
			$client = @new SoapClient($this->getURL());
		} catch (SoapFault $e) {
			return $e->faultstring();
		}
		
		$encoded = $client->__soapCall("clearGroup", array(self::SMART_WSP, $sGroupId));
		return $encoded;
	}
}

$smartschool = new SmartschoolConnection;
?>