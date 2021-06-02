<?php

/**
 * Description of Dealer
 *
 * @author belbayar
 */
class AgentById extends Basic_Agent {

    public function setXmlRequest($dealerId) {
        $this->url .= '/' . $dealerId;
        $this->customRequest = 'GET';
        $this->xmlRequest = '';
        $this->defaultResponse = true;
        $this->htmlSpecialChar = 1;
        $this->number = $dealerId;
        parent::setNumber($dealerId);
    }

    public function setAttr() {
        $xml = parent::getXmlResponse();
    }

}
