<?php

/**
 * Description of Agent
 *
 * @author belbayar
 */
class AgentProductOptions extends Basic_Agent
{

    public function setXmlRequest($dealerId)
    {
        $this->url .= "/" . $dealerId . "/purchase/products/options?provCode=dealer/charge";
        $this->customRequest = 'GET';
        $this->xmlRequest = $this->url;
        $this->defaultResponse = true;
        $this->htmlSpecialChar = 1;
        $this->number = $dealerId;
        parent::setNumber($dealerId);
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

}
