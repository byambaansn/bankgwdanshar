<?php

# http://bankgw.local/ulusnet/UlusnetUserInfo.php?number=63312121
include("classes/Basic.php");
include("classes/UlusnetUserInfo.php");
include("../tools/Serializer.php");
$username = '';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (array_key_exists('number', $_REQUEST)) {
    $username = $_REQUEST['number'];
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

if ($username == "") {
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

$caller = new UlusnetUserInfo();

$caller->setXmlRequest($username);
$caller->call();
$caller->setAttr();

header('Content-Type: text/xml');
echo $caller->getXmlResponseRaw();
?>