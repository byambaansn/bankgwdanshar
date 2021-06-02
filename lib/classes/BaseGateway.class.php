<?php

/**
 * Base gateway
 */
class BaseGateway
{

    // request
    protected $requestXml;
    protected $requestXmlArray;
    // response
    protected $responseXml;
    // remote address
    protected $remoteAddress;
    // access log id
    protected $logId;
    // connection
    private $conn;
    public $status;

    public function __construct()
    {
        $this->conn = new BaseConnection(array(
            'host' => '127.0.0.1',
            'username' => 'bankgw',
            'password' => 'B4H3sAWyDvsjC382',
            'database' => 'bankgw_log',
        ));
    }	 

    /**
     * Parse request xml
     * 
     * @param sfWebRequest $request
     */
    protected function doParse(sfWebRequest $request)
    {
        // set request xml
        $this->requestXml = $request->getContent();

        // parse request xml
        $xml = simplexml_load_string($this->requestXml);
        if ($xml === FALSE) {
            $this->doStop();
        }
        $json = json_encode($xml);
        $this->requestXmlArray = json_decode($json, TRUE);

        // set remote address
        $this->remoteAddress = $request->getRemoteAddress();

        // set access log
        $this->logId = $this->conn->mysqlExecute("INSERT INTO `log_bank_call` (`function`, `ip` ,`request`) VALUES ('" . get_class($this) . "', '" . $this->remoteAddress . "', '" . mysql_escape_string(print_r($this->requestXmlArray, true)) . "')", TRUE);
    }

    /**
     * Do something...
     */
    protected function doProcess()
    {
        
    }

    /**
     * Do validate tags
     * 
     * @param array $allowedTags
     */
    protected function doValidate($allowedTags)
    {
        $requestTags = array_keys((array) $this->requestXmlArray);

        $missedTags = array_merge(array_diff(array_keys($allowedTags), $requestTags));

        if (sizeof($missedTags)) {
            $this->doStop(100, ' missing tag "' . $missedTags[0] . '"');
        } else {
            foreach ($allowedTags as $tag => $type) {
                if (!is_array($type)) {
                    if (!call_user_func("is_$type", $this->requestXmlArray[$tag])) {
                        $this->doStop(100, 'invalid value in "' . $tag . '" tag!');
                    }
                } else {
                    if (!in_array($this->requestXmlArray[$tag], $type)) {
                        $this->doStop(100, 'invalid value in "' . $tag . '" tag!');
                    }
                }
            }
        }
    }

    /**
     * Stop
     * 
     * @param string $code
     * @param string $info
     */
    public function doStop($code = null, $info = null)
    {
        $res = array();

        if ($code) {
            $res['code'] = $code;
        } else {
            $res['code'] = 404;
        }

        if ($info) {
            $res['info'] = $info;
        } else {
            $res['info'] = 'Error!';
        }

        header("Content-Type:text/xml");

        $xml = AppTools::arrayToXml($res);
        $this->conn->mysqlExecute("UPDATE `log_bank_call` SET `response` = '" . mysql_escape_string(print_r($xml, true)) . "' WHERE `id` = " . $this->logId . " LIMIT 1");
        echo $xml->asXML();
        die();
    }

    public function setResponseXml($array)
    {
        $array = (array) $array;

        $xml = AppTools::arrayToXml($array);

        $this->responseXml = $xml->asXML();

        // set access log
        $this->conn->mysqlExecute("UPDATE `log_bank_call` SET `response` = '" . mysql_escape_string(print_r($array, true)) . "' WHERE `id` = " . $this->logId . " LIMIT 1");
    }

    public function getResponseXml()
    {
        return $this->responseXml;
    }

}

?>
