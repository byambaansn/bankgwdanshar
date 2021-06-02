<?php

# http://bankgw.local/ulusnet/UlusnetCharge.php?username=&prodid=&transby=&reason=
include("classes/Basic.php");
include("classes/UlusnetCharge.php");

$username = '';
$prodid = '';
$transby = '';
$reason = '';
$command = 0;

if (array_key_exists('username', $_REQUEST)) {
    $username = $_REQUEST['username'];
}
if (array_key_exists('prodid', $_REQUEST)) {
    $prodid = isset($_REQUEST['prodid']) ? $_REQUEST['prodid'] : "";
}
if (array_key_exists('transby', $_REQUEST)) {
    $transby = $_REQUEST['transby'];
}
if (array_key_exists('reason', $_REQUEST)) {
    $reason = $_REQUEST['reason'];
}
if (array_key_exists('command', $_REQUEST)) {
    $command = $_REQUEST['command'];
}

if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SERVER['REMOTE_ADDR'] != '172.30.14.101') {
    header('Content-Type: text/xml');
    $xmler = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<Response>
    <HttpCode>1005</HttpCode>
    <Result>Invalid ip address</Result>
</Response>
XML;
    echo $xmler;
    die();
}

if ($username == "" || $prodid == "" || $transby == "") {
    header('Content-Type: text/xml');
    $xmler = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<Response>
    <HttpCode>1004</HttpCode>
    <Result>Not enough paramater</Result>
</Response>
XML;
    echo $xmler;
    die();
}

$caller = new UlusnetCharge();
$caller->setXmlRequest($username, $prodid, $transby, $reason, $command);
$caller->call();
$caller->setAttr();

header('Content-Type: text/xml');
echo $caller->getXmlResponseRaw();
?>