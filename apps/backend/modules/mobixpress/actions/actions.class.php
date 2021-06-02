<?php

/**
 * mobixpress actions.
 *
 * @package    sf_sandbox
 * @subpackage savings
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class mobixpressActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'mobixpress');
    }

    public function executeList(sfWebRequest $request)
    {
        $this->hasExcelCredential = in_array('bankpayment_excel', $this->getUser()->getCredentials());
        $this->getRequest()->setParameter('sub_tab', 'dealer');
        $this->dateFrom = $request->getParameter('dateFrom', date('Y-m-d'));
        $this->dateTo = $request->getParameter('dateTo', date('Y-m-d'));

        $this->chargedMobile = $request->getParameter('chargedMobile');
        $this->orderedMobile = $request->getParameter('orderedMobile');
        $this->orderId = $request->getParameter('orderId');

        $this->sta = (int) $request->getParameter('status');
        $this->status = BankMobixpressTable::getForSelectStatus();

        $this->pager = BankMobixpressTable::getList();

        $urlParams = array();
        $urlParams[] = 'dateFrom=' . $this->dateFrom;
        $urlParams[] = 'dateTo=' . $this->dateTo;
        $urlParams[] = 'chargedMobile=' . $this->chargedMobile;
        $urlParams[] = 'orderedMobile=' . $this->orderedMobile;
        $urlParams[] = 'orderId=' . $this->orderId;
        $urlParams[] = 'sta=' . $this->sta;
        $this->urlParams = join('&', $urlParams);

        return sfView::SUCCESS;
    }

    public function executeListExcel(sfWebRequest $request)
    {
        $filename = 'mobixpressBank';
        $mobixpressList = BankMobixpressTable::getList(TRUE);

        $data = "№ ГҮЙЛГЭЭ;";
        $data .= "ДАНСНЫ ДУГААР;";
        $data .= "ДУГААР/C/;";
        $data .= "ДУГААР/D/;";
        $data .= "ТӨРӨЛ;";
        $data .= "ЦЭНЭГЛЭЛТ/НЭГЖ/;";
        $data .= "ЦЭНЭГЛЭЛТ/ТӨЛСӨН/;";
        $data .= "ЦЭНЭГЛЭЛТ/ЗӨРҮҮ/;";
        $data .= "ТӨЛӨВ;";
        $data .= "ЭХЭЛСЭН;";
        $data .= "ДУУССАН\n";
        foreach ($mobixpressList as $mobixpress) {
            $data.='"' . $mobixpress->order_id . '";';
            $data.='"' . $mobixpress->bank_account . '";';
            $data.='"' . $mobixpress->charge_mobile . '";';
            $data.='"' . $mobixpress->order_mobile . '";';
            $data.='"' . $mobixpress->order_type . '";';
            $data.='"' . $mobixpress->charge_amount . '";';
            $data.='"' . $mobixpress->order_amount . '";';
            $data.='"' . ($mobixpress->charge_amount - $mobixpress->order_amount) . '";';
            $data.='"' . BankMobixpressTable::getStatusName($mobixpress->status) . '";';
            $data.='"' . $mobixpress->created_at . '";';
            $data.='"' . $mobixpress->updated_at . '";';

            $data.="\n";
        }

        AppTools::ExportCsv($data, $filename, false);
        die();
    }

    /**
     * Төлөв харах
     * 
     * @param sfWebRequest $request
     */
    public function executeStatus(sfWebRequest $request)
    {
        $this->bankMobixpress = BankMobixpressTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankMobixpress);

        $this->chargeResponse = LogTools::getLogMobixpressCharge($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх
     * 
     * @param sfWebRequest $request
     */
    public function executeRecharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankMobixpress = BankMobixpressTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankMobixpress);

            if ($bankMobixpress->canReCharge()) {
                $bankMobixpress->charge_mobile = $request->getParameter('chargeMobile');
                $bankMobixpress->save();

                if (BankMobixpressTable::recharge($bankMobixpress) == TRUE) {
                    $this->getUser()->setFlash('info', '[' . $bankMobixpress->order_id . '] ' . BankMobixpressTable::getStatusName($bankMobixpress->status));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankMobixpress->order_id . '] ' . 'Уучлаарай, дахин цэнэглэлт амжилтгүй боллоо!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankMobixpress->order_id . '] ' . 'Уучлаарай, дахин цэнэглэх боломжгүй байна!');
            }

            return $this->redirect('@bank_mobixpress_list?orderId=' . $bankMobixpress->order_id);
        }

        return $this->redirect('@bank_mobixpress_list');
    }

    /**
     * Дугаар солих
     * 
     * @param sfWebRequest $request
     */
    public function executeChangeNumber(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankMobixpress = BankMobixpressTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankMobixpress);

            if ($bankMobixpress->status == BankMobixpressTable::STAT_NEW && $bankMobixpress->charge_mobile == '') {
                $bankMobixpress->charge_mobile = $request->getParameter('changeNumber');
                $bankMobixpress->save();
            } else {
                $this->getUser()->setFlash('error', 'Уучлаарай, дугаар солих боломжгүй байна!');
            }

            return $this->redirect('@bank_mobixpress_list?id=' . $bankMobixpress->id);
        }

        return $this->redirect('@bank_mobixpress_list');
    }

}