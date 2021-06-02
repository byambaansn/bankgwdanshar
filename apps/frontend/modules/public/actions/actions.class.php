<?php

class publicActions extends sfActions
{

    // HEADER action
    private function _setHeader()
    {
        $response = $this->getResponse();
        $response->setContentType('text/xml');
        $response->setHttpHeader('Content-Language', 'en');
        $response->addVaryHttpHeader('Accept-Language');
        $response->addCacheControlHttpHeader('no-cache');

//        function shutdown()
//        {
//            if (($error = error_get_last())) {
//                ob_clean();
//                
//                header('Location: /');
//            }
//        }
//
//        register_shutdown_function('shutdown');
    }

    // MAIN action
    public function executeIndex(sfWebRequest $request)
    {
        $this->_setHeader();

        $className = trim($request->getParameter('class_name'));

        $object = new $className();
        $object->doParse($request);
        $object->doProcess();

        $this->result = $object->getResponseXml();
//        return $this->renderText($object->getResponseXml());
    }

    // ERROR action
    public function executeError(sfWebRequest $request)
    {
        $this->_setHeader();

        $xml = '<response>';
        $xml .= '<code>' . $request->getParameter('code', 404) . '</code>';
        $xml .= '<info>' . $request->getParameter('info', 'bad request!') . '</info>';
        $xml .= '</response>';

        $res = simplexml_load_string($xml);

        echo $res->asXml();

        return sfView::NONE;
    }

    public function executeTest(sfWebRequest $request)
    {
        $url = "http://bankgw.local/BankStatement.xml";
        $request = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
                        <request>
                        <username>golomt</username>
                        <password>golomtgolomt</password>
                        <number>99237197</number>
                        <amount>5000</amount>
                        <value>5000</value>
                        <date>5000</date>
                        <order_branch>5000</order_branch>
                        <channel>5000</channel>
                        <account>5000</account>
                    </request>";
        $this->header = array();
        $this->header[] = "Content-Type: text/xml";

        // Initialisation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_TIMEOUT, 999);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        // execute the connexion
        $result = curl_exec($ch);
        // Close it
        curl_close($ch);

        $this->_setHeader();

        $res = simplexml_load_string($result);

        echo $res->asXml();

        return sfView::NONE;
    }

    public function executeTestSTGW(sfWebRequest $request)
    {
        $url = "http://gw.local/frontend_dev.php/VendorCharge.xml";
        $request = "<request>
                        <username>sainsain</username>
                        <password>sainsain</password>
                        <number>99237197</number>
                        <card>5000</card>
                    </request>";

        $this->header = array();
        $this->header[] = "Content-Type: text/xml";

        // Initialisation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_TIMEOUT, 999);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        // execute the connexion
        $result = curl_exec($ch);
        // Close it
        curl_close($ch);

//        echo $result;
//        die();

        $this->_setHeader();

        $res = simplexml_load_string($result);

        echo $res->asXml();

        return sfView::NONE;
    }

}
