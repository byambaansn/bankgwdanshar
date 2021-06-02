<?php

/**
 * Description of CreateCard
 *
 * @author belbayar
 */
class CreateCard extends Basic_WifiOnline
{

    public function setXmlRequest($isdn, $paymentWay, $charge, $cardCode)
    {
        $this->xmlRequest = '/createCard?isdn=' . $isdn . '&paymentWay=' . $paymentWay . '&charge=' . $charge . '&cardCode=' . $cardCode;
        $this->url .= $this->xmlRequest;

        $this->customRequest = 'GET';
        $this->defaultResponse = 1;
        $this->number = $isdn;
        parent::setNumber($isdn);
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

}

?>
