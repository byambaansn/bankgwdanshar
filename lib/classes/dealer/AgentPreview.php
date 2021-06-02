<?php

/**
 * Description of Dealer
 *
 * @author belbayar
 */
class AgentPreview extends Basic_Agent {

    public function setXmlRequest($dealerId, $calcType, $productType, $productOptId, $amount, $payAmount, $invItems) {
        $params = array(
            "calcType" => $calcType,
            "productType" => $productType,
            "productOptId" => $productOptId
        );
        if ($amount) {
            $params['amount'] = $amount;
        } elseif ($payAmount) {
            $params['payAmount'] = $payAmount;
        } elseif ($invItems) {
            $params['invItems'] = $invItems;
        }

        $this->url .= "/" . $dealerId . "/purchase/preview";
        $this->customRequest = 'POST';
        $this->xmlRequest = json_encode($params);
        $this->htmlSpecialChar = 1;
        $this->defaultResponse = true;
        $this->number = $dealerId;
        parent::setNumber($dealerId);
    }

    public function setAttr() {
        $xml = parent::getXmlResponse();
    }

}
