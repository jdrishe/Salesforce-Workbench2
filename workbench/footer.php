<?php
include_once 'shared.php';
?>
</div>

<div id='disclaimer'><br />

<?php
//print $_SERVER[SERVER_NAME];
if (isset($_SESSION["config"]["checkSSL"]) && $_SESSION["config"]["checkSSL"] == true) {
    //is connection unsecure from this machine to Workbench?
    $unsecureLocal2Wb = !isset($_SERVER['HTTPS']) && $_SERVER['SERVER_NAME'] !== 'localhost' && $_SERVER['SERVER_NAME'] !== '127.0.0.1' && $_SERVER['SERVER_NAME'] !== 'workbench';

    //is connection unsecure from Workbench to Salesforce?
    $unsecureWb2sfdc = isset($_SESSION['location']) && !strstr($_SESSION['location'],'https');

    if ($unsecureLocal2Wb || $unsecureWb2sfdc) {
        print "<span style='font-size: 8pt; color: red;'>WARNING: Unsecure connection detected";

        if($unsecureLocal2Wb) print " to Workbench";
        if($unsecureLocal2Wb && $unsecureWb2sfdc) print " and";
        if($unsecureWb2sfdc) print " to Salesforce";

        print "</span><br/>";
    }
}

if (isset($GLOBALS['requestTimeStart']) && isset($_SESSION["config"]["displayRequestTime"]) && $_SESSION["config"]["displayRequestTime"]) {
    $requestTimeEnd = microtime(true);
    $requestTimeElapsed = $requestTimeEnd - $GLOBALS['requestTimeStart'];
    printf ("Requested in %01.3f sec<BR/>", $requestTimeElapsed);
}

print "Workbench " . ($GLOBALS["WORKBENCH_VERSION"] != "trunk" ? $GLOBALS["WORKBENCH_VERSION"] : "") . "<br/>\n";

?></div>

</body>
<script type="text/javascript" src="script/pro_dropdown.js"></script>

<script type="text/javascript" src="script/wz_tooltip.js"></script>

<script type="text/javascript" src="script/simpletreemenu.js">
/***********************************************
* Simple Tree Menu - Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/
</script>

<?php
if (isset($_REQUEST["footerScripts"])) {
    foreach ($_REQUEST["footerScripts"] as $script) {
        print "$script";
    }
}
?>

</html>

<?php
//USAGE: debug($showSuperVars = true, $showSoap = true, $customName = null, $customValue = null)
debug(true,true,null,null);
?>