<?php

/**
 * Description of Dealer
 *
 * @author belbayar
 */
class AgentByPhone extends Basic_Agent
{

    public function setXmlRequest($dealerPhone)
    {
        $this->url .= '?status=1&branchPhone=' . $dealerPhone;
        $this->customRequest = 'GET';
        $this->xmlRequest = $this->url;
        $this->defaultResponse = true;
        $this->htmlSpecialChar = 1;
        $this->number = $dealerPhone;
        parent::setNumber($dealerPhone);
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

}
