<?php

class SapcBuyPackageNoCharge extends Basic_Sapc
{

    public function setXmlRequest($MSISDN, $Parameter, $Command, $SendSms = 1, $Force = false, $NoCharge = true)
    {
        $attr1 = "<MSISDN>$MSISDN</MSISDN>";
        $attr1 .= "<Parameter>$Parameter</Parameter>";

        $sendSms = '<SendSms>true</SendSms>';
        if ($SendSms != 1) {
            $sendSms = '<SendSms>false</SendSms>';
        }
        $attr1 .= '<Force>' . $Force . '</Force>';
        $attr1 .= '<NoCharge>' . $NoCharge . '</NoCharge>';

        $this->xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
                <SAPCProvisionRequest>
                    ' . $attr1 . '
                      <Command>buyPackage</Command>
                      ' . $sendSms . '
                </SAPCProvisionRequest>';

        $this->sentInfo();

        $this->number = $MSISDN;
        $this->command = $Command;
        parent::setNumber($MSISDN);
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

}

?>