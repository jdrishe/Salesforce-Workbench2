<?php
require_once ('session.php');
require_once ('shared.php');
if(isset($_REQUEST['switchApiVersionTo'])){
	$previousVersion = getApiVersion();
	clearSessionCache();
	$_SESSION['location'] = preg_replace("/\d\d?\.\d/",$_REQUEST['switchApiVersionTo'], $_SESSION['location']);
	$_SESSION['wsdl'] = 'soapclient/sforce.' . str_replace('.', '', $_REQUEST['switchApiVersionTo']) . '.partner.wsdl';
	header("Location: $_SERVER[PHP_SELF]?previousVersion=" . $previousVersion);
}

global $partnerConnection;
if(isset($_REQUEST['previousVersion'])){
	try {
		$partnerConnection->getServerTimestamp();
	} catch (Exception $e) {
		if(stripos($e->getMessage(),'UNSUPPORTED_API_VERSION') > -1) {
			clearSessionCache();
			$_SESSION['location'] = preg_replace("/\d\d?\.\d/",$_REQUEST['previousVersion'], $_SESSION['location']);
			$_SESSION['wsdl'] = 'soapclient/sforce.' . str_replace('.', '', $_REQUEST['previousVersion']) . '.partner.wsdl';
			header("Location: $_SERVER[PHP_SELF]?UNSUPPORTED_API_VERSION");
		}
		show_error($e->getMessage(),true,true);
		exit;
	}	
}

require_once ('header.php');
?>
<p/>
<p class='instructions'>Below is information regarding the current user session:</p>
<div style='float:right;'>
	<form name="changeApiVersionForm" action="<?php $_SERVER['PHP_SELF'] ?>">
		Change API Version: 
		<?php
		print "<select  method='POST' name='switchApiVersionTo' onChange='document.changeApiVersionForm.submit();'>";
		foreach($GLOBALS['API_VERSIONS'] as $v) {
			print "<option value='$v'";
			if (getApiVersion() == $v) print " selected=\"selected\"";
			print ">" . $v . "</option>";
		}
		print "</select>";
		?>
	</form>
</div>

<?php

if (isset($_REQUEST['UNSUPPORTED_API_VERSION'])) {
	print "<div style='margin-top: 3em;'>";
	show_error("Selected API version is not supported by this Salesforce organization. Automatically reverted to prior version.",false,false);
	print "<p/>";
} else {
	print "<div>";
}

$sessionInfo = array();
$sessionInfo['Connection'] = array(
	'API Version' => getApiVersion(),
	'Client Id' => isset($_SESSION['tempClientId']) ? $_SESSION['tempClientId'] : $_SESSION['config']['callOptions_client'], 
	'Endpoint' => $partnerConnection->getLocation(),
	'Session Id' => $partnerConnection->getSessionId(), 
);

try {
	$freshGetUserInfoResult = $partnerConnection->getUserInfo();
} catch (Exception $e) {
	show_error($e->getMessage(), false, true);
}

foreach($freshGetUserInfoResult as $uiKey => $uiValue) {
	if(stripos($uiKey,'org') !== 0) {
		$sessionInfo['User'][$uiKey] = $uiValue;
	} else {
		$sessionInfo['Organization'][$uiKey] = $uiValue;		
	}
}

if(apiVersionIsAtLeast(10.0)) {
	global $metadataConnection;
	try {
		$describeMetadataResult = $metadataConnection->describeMetadata(getApiVersion());
	} catch (Exception $e) {
		show_error($e->getMessage(), false, true);
	}
	
	foreach($describeMetadataResult as $resultsKey => $resultsValue) {
		if($resultsKey != 'metadataObjects' && !is_array($resultsValue)){
			$sessionInfo['Metadata'][$resultsKey] = $resultsValue;
		}
	}
}

printTree("sessionInfoTree", $sessionInfo);

print "</div>";
require_once ('footer.php');
?>