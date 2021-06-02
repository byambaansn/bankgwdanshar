<?php

/**
 * Description of Dealer
 *
 * @author belbayar
 */
class Agent extends Basic_Agent {

    public function setXmlRequest($dealerCode, $msisdn) {
        $this->url .= '?dealerCode=' . urlencode($dealerCode);
        $this->customRequest = 'GET';
        $this->xmlRequest = '';
        $this->defaultResponse = true;
        $this->htmlSpecialChar = 1;
        $this->number = $msisdn;
        parent::setNumber($msisdn);
    }

    public function setAttr() {
        $xml = parent::getXmlResponse();
    }

}
