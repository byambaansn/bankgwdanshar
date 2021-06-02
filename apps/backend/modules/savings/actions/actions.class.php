<?php

/**
 * savings actions.
 *
 * @package    sf_sandbox
 * @subpackage savings
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class savingsActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'savings');
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
        $this->status = BankSavingsTable::getForSelectStatus(BankSavingsAccountTable::ACCOUNT_DEALER);

        $this->pager = BankSavingsTable::getList(false, BankSavingsAccountTable::ACCOUNT_DEALER);

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
        $filename = 'stateBank';
        $savingsList = BankSavingsTable::getList(TRUE);

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
        foreach ($savingsList as $savings) {
            $data.='"' . $savings->order_id . '";';
            $data.='"' . $savings->bank_account . '";';
            $data.='"' . $savings->charge_mobile . '";';
            $data.='"' . $savings->order_mobile . '";';
            $data.='"' . $savings->order_type . '";';
            $data.='"' . $savings->charge_amount . '";';
            $data.='"' . $savings->order_amount . '";';
            $data.='"' . ($savings->charge_amount - $savings->order_amount) . '";';
            $data.='"' . BankSavingsTable::getStatusName($savings->status) . '";';
            $data.='"' . $savings->created_at . '";';
            $data.='"' . $savings->updated_at . '";';

            $data.="\n";
        }

        AppTools::ExportCsv($data, $filename, false);
        die();
    }

    public function executeMx(sfWebRequest $request)
    {
        $this->hasExcelCredential = in_array('bankpayment_excel', $this->getUser()->getCredentials());
        $this->getRequest()->setParameter('sub_tab', 'mx');
//        
        $this->dateFrom = $request->getParameter('dateFrom', date('Y-m-d'));
        $this->dateTo = $request->getParameter('dateTo', date('Y-m-d'));

        $this->chargedMobile = $request->getParameter('chargedMobile');
        $this->orderedMobile = $request->getParameter('orderedMobile');
        $this->orderId = $request->getParameter('orderId');

        $this->sta = (int) $request->getParameter('status');
        $this->status = BankSavingsTable::getForSelectStatus(BankSavingsAccountTable::ACCOUNT_MOBIXPRESS);

        $this->pager = BankSavingsTable::getList(false, BankSavingsAccountTable::ACCOUNT_MOBIXPRESS);

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

    public function executeMxListExcel(sfWebRequest $request)
    {
        $filename = 'stateBank';
        $savingsList = BankSavingsTable::getList(TRUE, BankSavingsAccountTable::ACCOUNT_MOBIXPRESS);

        $data = "№ ГҮЙЛГЭЭ;";
        $data .= "ДАНСНЫ ДУГААР;";
        $data .= "ДУГААР/C/;";
        $data .= "ДУГААР/D/;";
        $data .= "ТӨРӨЛ;";
        $data .= "ШИЛЖҮҮЛГЭ/ДҮН/;";
        $data .= "ТӨЛӨВ;";
        $data .= "ЭХЭЛСЭН;";
        $data .= "ДУУССАН\n";
        foreach ($savingsList as $savings) {
            $data.='"' . $savings->order_id . '";';
            $data.='"' . $savings->bank_account . '";';
            $data.='"' . $savings->charge_mobile . '";';
            $data.='"' . $savings->order_mobile . '";';
            $data.='"' . $savings->order_type . '";';
            $data.='"' . $savings->charge_amount . '";';
            $data.='"' . BankSavingsTable::getStatusName($savings->status, BankSavingsAccountTable::ACCOUNT_MOBIXPRESS) . '";';
            $data.='"' . $savings->created_at . '";';
            $data.='"' . $savings->updated_at . '";';

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
        $this->bankSavings = BankSavingsTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankSavings);

//        $this->bankSavingsLogs = BankSavingsTable::getInstance()->findBy('bank_savings_id', $this->bankSavings->id, PDO::FETCH_ASSOC);
        $this->chargeResponse = LogTools::getLogSavingsCharge($request->getParameter('id'));
    }

    /**
     * Төлөв харах
     * 
     * @param sfWebRequest $request
     */
    public function executeMxStatus(sfWebRequest $request)
    {
        $this->bankSavings = BankSavingsTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankSavings);

        $this->chargeResponse = LogTools::getLogMxCharge($request->getParameter('id'));
//        $this->bankSavingsLogs = BankSavingsTable::getInstance()->findBy('bank_savings_id', $this->bankSavings->id, PDO::FETCH_ASSOC);
    }

    /**
     * Дахин цэнэглэх
     * 
     * @param sfWebRequest $request
     */
    public function executeRecharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankSavings = BankSavingsTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankSavings);
            $chargeNumber = $request->getParameter('savingsChargeMobile');

            if ($bankSavings->canReCharge()) {
                $bankSavings->charge_mobile = $chargeNumber;
                $bankSavings->save();

                if (BaseSms::isAdShop($chargeNumber)) {
                    $result = BankSavingsTable::rechargeSMSApi($bankSavings, "AD");
                } elseif (BaseSms::isSdDealer($chargeNumber)) {
                    $result = BankSavingsTable::rechargeSMSApi($bankSavings, "SD");
                } else {
                    $result = BankSavingsTable::recharge($bankSavings);
                }
                if ($result) {
                    $this->getUser()->setFlash('info', '[' . $bankSavings->order_id . '] ' . BankSavingsTable::getStatusName($bankSavings->status));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankSavings->order_id . '] ' . 'Уучлаарай, дахин цэнэглэлт амжилтгүй боллоо!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankSavings->order_id . '] ' . 'Уучлаарай, дахин цэнэглэх боломжгүй байна!');
            }

            return $this->redirect('@bank_savings_list?orderId=' . $bankSavings->order_id);
        }

        return $this->redirect('@bank_savings_list');
    }

    /**
     * Дугаар солих
     * 
     * @param sfWebRequest $request
     */
    public function executeChangeNumber(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankSavings = BankSavingsTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankSavings);

            if ($bankSavings->status == BankSavingsTable::STAT_NEW && $bankSavings->charge_mobile == '') {
                $bankSavings->charge_mobile = $request->getParameter('changeNumber');
                $bankSavings->save();
            } else {
                $this->getUser()->setFlash('error', 'Уучлаарай, дугаар солих боломжгүй байна!');
            }

            return $this->redirect('@bank_savings_list?id=' . $bankSavings->id);
        }

        return $this->redirect('@bank_savings_list');
    }

    /**
     * Зарлага хийх
     * 
     * @param sfWebRequest $request
     */
    public function executeReoutcome(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankSavings = BankSavingsTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankSavings);

            if ($bankSavings->canReOutcome()) {
                # Dealer AGENT check
                $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/dealer/process-' . date("Ymd") . '.log'));
                $dealerAgent = DealerGateway::findDealerByMobile($bankSavings->charge_mobile, $logger);
                if (!$dealerAgent) {
                    $dealer = DealerCharge::getDealer($bankSavings->charge_mobile);
                }
                $outcomeOrderId = BankSavingsTable::reoutcome($bankSavings, $dealer, date('Y-m-d', strtotime($bankSavings->getCreatedAt())), $dealerAgent);
                if ($outcomeOrderId) {
                    $bankSavings->status = BankSavingsTable::STAT_SUCCESS;
                    $bankSavings->sales_order_id = $outcomeOrderId;
                    $bankSavings->transfer_sap = 0;
                    $bankSavings->save();

                    $this->getUser()->setFlash('info', '[' . $bankSavings->order_id . '] ' . BankSavingsTable::getStatusName($bankSavings->status));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankSavings->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankSavings->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна!');
            }
        }

        return $this->redirect('@bank_savings_list');
    }

    /**
     * Зарлага хийгдсэн тул Амжилттай төлөвт оруулах
     * 
     * @param sfWebRequest $request
     */
    public function executeSuccessOutcome(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankSavings = BankSavingsTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankSavings);

            if ($bankSavings->canReOutcome() && $bankSavings->getSalesOrderId()) {
                $bankSavings->status = BankSavingsTable::STAT_SUCCESS;
                $bankSavings->save();
                $this->getUser()->setFlash('info', '[' . $bankSavings->order_id . '] ' . BankSavingsTable::getStatusName($bankSavings->status) . ' төлөвт орууллаа');
            } else {
                $this->getUser()->setFlash('error', '[' . $bankSavings->order_id . '] ' . 'Уучлаарай, зарлага амжилттай төлөвт оруулах боломжгүй байна!');
            }
        }

        return $this->redirect('@bank_savings_list');
    }

    /**
     * Дахин цэнэглэх
     * 
     * @param sfWebRequest $request
     */
    public function executeReMxCharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankSavings = BankSavingsTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankSavings);

            if ($bankSavings->canReCharge()) {
                $bankSavings->charge_mobile = $request->getParameter('savingsChargeMobile');
                $bankSavings->save();

                if (BankSavingsTable::mxCharge($bankSavings) == TRUE) {
                    $this->getUser()->setFlash('info', '[' . $bankSavings->order_id . '] ' . BankSavingsTable::getStatusName($bankSavings->status, BankSavingsAccountTable::ACCOUNT_MOBIXPRESS));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankSavings->order_id . '] ' . 'Уучлаарай, дахин цэнэглэлт амжилтгүй боллоо!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankSavings->order_id . '] ' . 'Уучлаарай, дахин цэнэглэх боломжгүй байна!');
            }
            return $this->redirect('@bank_savings_mx_list?orderId=' . $bankSavings->order_id);
        }
        return $this->redirect('@bank_savings_mx_list');
    }

    /**
     *  CallPayment charge
     * 
     * @param sfWebRequest $request
     */
    public function executeCallPayment(sfWebRequest $request)
    {
        $this->hasExcelCredential = in_array('bankpayment_excel', $this->getUser()->getCredentials());
        $this->getRequest()->setParameter('sub_tab', 'callpayment');
        $this->dateFrom = $request->getParameter('dateFrom', date('Y-m-d'));
        $this->dateTo = $request->getParameter('dateTo', date('Y-m-d'));

        $this->chargedMobile = $request->getParameter('chargedMobile');
        $this->orderedMobile = $request->getParameter('orderedMobile');
        $this->orderId = $request->getParameter('orderId');

        $this->sta = (int) $request->getParameter('status');
        $this->status = BankSavingsTable::getForSelectStatus(BankSavingsAccountTable::ACCOUNT_CALLPAYMENT);

        $this->pager = BankSavingsTable::getList(false, array(BankSavingsAccountTable::ACCOUNT_CALLPAYMENT, BankSavingsAccountTable::ACCOUNT_MOBINET));

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

    public function executeCallPaymentExcel(sfWebRequest $request)
    {
        $filename = 'savingsBank';
        $savingsList = BankSavingsTable::getList(TRUE, array(BankSavingsAccountTable::ACCOUNT_CALLPAYMENT, BankSavingsAccountTable::ACCOUNT_MOBINET));

        $data = "№ ГҮЙЛГЭЭ;";
        $data .= "ДАНСНЫ ДУГААР;";
        $data .= "ДУГААР/D/;";
        $data .= "ТӨРӨЛ;";
        $data .= "ЦЭНЭГЛЭЛТ/ТӨЛСӨН/;";
        $data .= "ТӨЛӨВ;";
        $data .= "ЭХЭЛСЭН;";
        $data .= "ДУУССАН\n";
        foreach ($savingsList as $savings) {
            $data.='"' . $savings->order_id . '";';
            $data.='"' . $savings->bank_account . '";';
            $data.='"' . $savings->order_mobile . '";';
            $data.='"' . $savings->order_type . '";';
            $data.='"' . $savings->order_amount . '";';
            $data.='"' . BankSavingsTable::getStatusName($savings->status, BankSavingsAccountTable::ACCOUNT_CALLPAYMENT) . '";';
            $data.='"' . $savings->created_at . '";';
            $data.='"' . $savings->updated_at . '";';

            $data.="\n";
        }

        AppTools::ExportCsv($data, $filename, false);
        die();
    }

    /**
     * Төлөв харах CallPayment
     * 
     * @param sfWebRequest $request
     */
    public function executeCallPaymentStatus(sfWebRequest $request)
    {
        $this->bankSavings = BankSavingsTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankSavings);

        $this->chargeResponse = LogTools::getLogSavingsChargeCallPayment($request->getParameter('id'));
    }

    /**
     * Дахин төлөлт оруулах
     * 
     * @param sfWebRequest $request
     */
    public function executeReCallpaymentCharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankSavings = BankSavingsTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankSavings);

            if ($bankSavings->canReCharge()) {
                $bankSavings->charge_mobile = $request->getParameter('savingsChargeMobile');
                $bankSavings->save();

                if (BankSavingsTable::callPayment($bankSavings) == TRUE) {
                    $this->getUser()->setFlash('info', '[' . $bankSavings->order_id . '] ' . BankSavingsTable::getStatusName($bankSavings->status, BankSavingsAccountTable::ACCOUNT_CALLPAYMENT));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankSavings->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах хүсэлт амжилтгүй боллоо!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankSavings->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах боломжгүй байна!');
            }
            return $this->redirect('@bank_savings_call_payment_list?orderId=' . $bankSavings->order_id);
        }
        return $this->redirect('@bank_savings_call_payment_list');
    }

}