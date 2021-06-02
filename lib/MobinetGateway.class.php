<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MobinetGateway
 *
 * @author Enkhsaikhan.da
 */
class MobinetGateway
{

    /**
     * Payment төлбөр төлөлт
     * @param String $account
     * @param String $contractNumber
     * @param Double $paid
     * @return array Төлөлт орсон тухай
     */
    public static function doPayment($contractNumber, $paid, $account)
    {
        if (!$contractNumber || !$paid || !$account) {
            return null;
        }

        $caller = new MobinetHBBPayment();
        $caller->setXmlRequest($contractNumber, 2, $paid, $account);
        $caller->setUserId(0);
        $caller->call($caller->getXmlRequest());
        $caller->setAttr();

        $xmlStr = $caller->getXmlResponseRaw();

        $result = array();
        $response = json_decode($xmlStr);
        $result['code'] = $response->code;
        $result['info'] = $response->info;
        $result['res'] = $response->result;
        return $result;
    }

    /**
     * Contract Info
     * @param String $contract гэрээний дугаар
     * @return array contract дээрх хэрэглэгчийн мэдээлэл ирнэ
     */
    public static function contractInfo($contract)
    {
        if (!$contract) {
            return null;
        }

        $caller = new ContractInfo();
        $caller->setXmlRequest($contract);
        $caller->setUserId(0);
        $caller->call($caller->getXmlRequest());
        $caller->setAttr();

        $xmlStr = $caller->getXmlResponseRaw();

        $result = array();

        $response = json_decode($xmlStr);
        $result['code'] = $response->code;
        $result['info'] = $response->info;
        $result['id'] = $response->result->id;
        $result['contract'] = $response->result->contractNumber;
        $result['email'] = $response->result->email;
        $result['mobile'] = $response->result->mobile;
        $result['username'] = $response->result->username;
        $result['speed'] = $response->result->packageSpeed;        
        $result['firstname'] = $response->result->firstname;        
        $result['lastname'] = $response->result->lastname;        
        return $result;
    }

}