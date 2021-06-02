<?php

class SapcBuyPackage extends Basic_Sapc
{

    public function setXmlRequest($MSISDN, $Parameter, $Command, $SendSms = 1, $Force = 0, $NoCharge = 1)
    {
        $attr1 = "<MSISDN>$MSISDN</MSISDN>";
        $attr1 .= "<Parameter>$Parameter</Parameter>";

        $sendSms = '<SendSms>true</SendSms>';
        if ($SendSms != 1) {
            $sendSms = '<SendSms>false</SendSms>';
        }
        if ($Force) {
            $attr1 .= '<Force>true</Force> ';
        } else {
            $attr1 .= '<Force>false</Force> ';
        }
        if ($NoCharge) {
            $attr1 .= '<NoCharge>false</NoCharge> ';
        } else {
            $attr1 .= '<NoCharge>true</NoCharge> ';
        }


        $this->xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
                <SAPCProvisionRequest>
                    ' . $attr1 . '
                      <Command>buyPackage</Command>
                      ' . $sendSms . '
                </SAPCProvisionRequest>';
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