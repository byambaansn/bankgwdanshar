<?php

/**
 * Description of ContractInfo
 *
 * @author enkhsaikhan.da
 */
class ContractInfo extends BasicMobinetGW
{

    public function setXmlRequest($contract)
    {
        $this->xmlRequest = '/api/services/search-by-uc/' . $contract;
        $this->url .= $this->xmlRequest;
        parent::setNumber($contract);
        $this->customRequest = 'GET';
        $this->defaultResponse = 1;
    }

    public function getXmlRequest()
    {
        return $this->xmlRequest;
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

}

?>