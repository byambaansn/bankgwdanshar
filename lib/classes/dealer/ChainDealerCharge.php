<?php

class ChainDealerCharge extends BasicNTCGW
{

    public function ChainDealerCharge()
    {
        parent::__construct();
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_ntcgw.yml');
        $url = $yml['all']['api']['vendor_charge'];
        $this->setUrl($url);
    }

    public function setXmlRequest($vendor, $invoiceId, $dater, $chargerId, $amount)
    {
        $xml_request = '<?xml version="1.0" encoding="UTF-8"?>
                <request>
                  <username>' . $vendor . '</username>
                  <invoice_id>' . $invoiceId . '</invoice_id>
                  <charge_date>' . $dater . '</charge_date>
                  <charger_id>' . $chargerId . '</charger_id>
                  <amount>' . $amount . '</amount>
                </request>';
        parent::setNumber($vendor);
        parent::setXmlRequest($xml_request);
    }

    public function getResponse()
    {
        $return = array(
            'success' => FALSE,
            'log_request' => $this->url,
            'percent' => '0',
            'log_response' => '',
            'error_code' => 0
        );
        $result = $this->getArrayResponse();
        
        if ($result['code'] === '0') {
            $return['success'] = true;
            $return['percent'] = $result['percent'];
        }
        $return['error_code'] = $result['info'];
        $return['log_response'] = $this->getXmlResponseRaw();
        return $return;
    }

}

?>