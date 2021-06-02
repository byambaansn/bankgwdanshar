<?php

/**
 * Description of Dealer
 *
 * @author belbayar
 */
class AgentConfirm extends Basic_Agent
{

    public function setXmlRequest($dealerId, $calcType, $productOptId, $amount, $bank)
    {
        $params = array(
            "calcType" => $calcType,
            "productOptId" => $productOptId,
            "from" => $bank,
            "branch" => 'BANKGW',
            "payAmount" => $amount
        );
        $this->url .= "/" . $dealerId . "/purchase/confirm";
        $this->customRequest = 'POST';
        $this->xmlRequest = json_encode($params);
        $this->htmlSpecialChar = 1;
        $this->defaultResponse = true;
        $this->number = $dealerId;
        parent::setNumber($dealerId);
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

}
