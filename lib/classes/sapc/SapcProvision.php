<?php

class SapcProvision extends Basic_Sapc
{

    public function setXmlRequest($MSISDN)
    {
        $attr1 = "<MSISDN>$MSISDN</MSISDN>";
        $this->xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
                <SAPCProvisionRequest>
                  ' . $attr1 . '
                      <Command>getPackageList</Command>
                </SAPCProvisionRequest>';
        $this->number = $MSISDN;
        parent::setNumber($MSISDN);
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

}

?>