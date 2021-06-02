<?php

/**
 * Description of UlusnetCharge
 *
 * @author Belbayar
 */
class UlusnetCharge extends Basic
{

    public function setXmlRequest($username, $prodid, $transby, $reason, $command)
    {
        $this->xmlRequest = '<Ulusnet>
                                <Username>976' . $username . '</Username>
                                <Prodid>' . $prodid . '</Prodid>
                                <Transby>' . $transby . '</Transby>
                                <Reason>' . $this->appName . ':' . $reason . '</Reason>
                                <Action>Charge</Action>
                            </Ulusnet>';

        $this->defaultResponse = true;
        $this->number = $username;
        $this->command = $command;
        parent::setNumber($username);
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

}
