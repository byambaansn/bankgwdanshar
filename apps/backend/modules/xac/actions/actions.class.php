<?php

/**
 * xac actions.
 *
 * @package    sf_sandbox
 * @subpackage xac
 * @author     Belbayar
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class xacActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'xac');
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
        $this->status = BankXacTable::getForSelectStatus(BankXacTable::TYPE_DEALER);

        $this->pager = BankXacTable::getList(array(BankXacAccountTable::ACCOUNT_DEALER));

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

        set_time_limit(180);
        ini_set("memory_limit", "1024M");
        $filename = 'xacBank';
        $xacList = BankXacTable::getListCustom(array(BankXacAccountTable::ACCOUNT_DEALER), TRUE);

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
        foreach ($xacList as $xac) {
            $data.='"' . $xac['order_id'] . '";';
            $data.='"' . $xac['bank_account'] . '";';
            $data.='"' . $xac['charge_mobile'] . '";';
            $data.='"' . $xac['order_mobile'] . '";';
            $data.='"' . $xac['order_type'] . '";';
            $data.='"' . $xac['charge_amount'] . '";';
            $data.='"' . $xac['order_amount'] . '";';
            $data.='"' . ($xac['charge_amount'] - $xac['order_amount']) . '";';
            $data.='"' . BankXacTable::getStatusName($xac['status'], BankXacTable::TYPE_DEALER) . '";';
            $data.='"' . $xac['created_at'] . '";';
            $data.='"' . $xac['updated_at'] . '";';

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
        $this->bankXac = BankXacTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankXac);

        $this->chargeResponse = LogTools::getLogXacCharge($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх
     * 
     * @param sfWebRequest $request
     */
    public function executeRecharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankXac = BankXacTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankXac);
            $chargeNumber = $request->getParameter('xacChargeMobile');

            if ($bankXac->canReCharge()) {
                $bankXac->charge_mobile = $chargeNumber;
                $bankXac->save();

                if (BaseSms::isAdShop($chargeNumber)) {
                    $result = BankXacTable::rechargeSMSApi($bankXac, "AD");
                } elseif (BaseSms::isSdDealer($chargeNumber)) {
                    $result = BankXacTable::rechargeSMSApi($bankXac, "SD");
                } else {
                    $result = BankXacTable::recharge($bankXac);
                }
                if ($result) {
                    $this->getUser()->setFlash('info', '[' . $bankXac->order_id . '] ' . BankXacTable::getStatusName($bankXac->status, BankXacTable::TYPE_DEALER));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankXac->order_id . '] ' . 'Уучлаарай, дахин цэнэглэлт амжилтгүй боллоо!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankXac->order_id . '] ' . 'Уучлаарай, дахин цэнэглэх боломжгүй байна!');
            }

            return $this->redirect('@bank_xac_list?orderId=' . $bankXac->order_id);
        }

        return $this->redirect('@bank_xac_list');
    }

    /**
     * Зарлага хийх
     * 
     * @param sfWebRequest $request
     */
    public function executeReoutcome(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankXac = BankXacTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankXac);

            if ($bankXac->canReOutcome()) {
                # Dealer AGENT check
                $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/dealer/process-' . date("Ymd") . '.log'));
                $dealerAgent = DealerGateway::findDealerByMobile($bankXac->charge_mobile, $logger);

                if (!$dealerAgent) {
                    $dealer = DealerCharge::getDealer($bankXac->charge_mobile);
                }
                $outcomeOrderId = BankXacTable::reoutcome($bankXac, $dealer, date('Y-m-d', strtotime($bankXac->getCreatedAt())), $dealerAgent);
                if ($outcomeOrderId) {
                    $bankXac->status = BankXacTable::STAT_SUCCESS;
                    $bankXac->sales_order_id = $outcomeOrderId;
                    $bankXac->transfer_sap = 0;
                    $bankXac->save();

                    $this->getUser()->setFlash('info', '[' . $bankXac->order_id . '] ' . 'Зарлагыг амжилттай үүсгэлээ. ' . BankXacTable::getStatusName($bankXac->status, BankXacTable::TYPE_DEALER) . ' төлөвт орууллаа');
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankXac->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![1]');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankXac->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![2]');
            }
        }
        return $this->redirect($request->getReferer());
    }

    /**
     * Зарлага хийгдсэн тул Амжилттай төлөвт оруулах
     * 
     * @param sfWebRequest $request
     */
    public function executeSuccessOutcome(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankXac = BankXacTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankXac);

            if ($bankXac->canReOutcome() && $bankXac->getSalesOrderId()) {
                $bankXac->status = BankXacTable::STAT_SUCCESS;
                $bankXac->save();
                $this->getUser()->setFlash('info', '[' . $bankXac->order_id . '] ' . BankXacTable::getStatusName($bankXac->status, BankXacTable::TYPE_DEALER) . ' төлөвт орууллаа');
            } else {
                $this->getUser()->setFlash('error', '[' . $bankXac->order_id . '] ' . 'Уучлаарай, зарлага амжилттай төлөвт оруулах боломжгүй байна!');
            }
        }

        return $this->redirect('@bank_xac_list');
    }

    /**
     * цэнэглэлтийн зогсоох
     * 
     * @param sfWebRequest $request
     */
    public function executeBlock(sfWebRequest $request)
    {

        $block = $request->getParameter('block');
        if ($request->isMethod('POST')) {
            $blockBank = BlockTable::retrieveByBank(VendorTable::BANK_XAC);
            if ($blockBank) {
                $blockBank->setBlock($block);
                $blockBank - save();
                $message = ($block == BlockTable::BLOCK) ? 'хаалаа' : 'нээлээ';
                $this->getUser()->setFlash('info', 'Хаан банкны автомат цэнэглэлтийг амжилттай ' . $message);
                $this->redirect($request->getReferer());
            }
        }

        $this->getUser()->setFlash('error', 'Олдсонгүй ');
        $this->redirect($request->getReferer());
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
        $this->status = BankXacTable::getForSelectStatus(BankXacTable::TYPE_CALLPAYMENT);

        $this->pager = BankXacTable::getList(BankXacAccountTable::ACCOUNT_CALLPAYMENT);

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
        $filename = 'xacBank';
        $xacList = BankXacTable::getList(BankXacAccountTable::ACCOUNT_CALLPAYMENT, TRUE);

        $data = "№ ГҮЙЛГЭЭ;";
        $data .= "ДАНСНЫ ДУГААР;";
        $data .= "ДУГААР/D/;";
        $data .= "ТӨРӨЛ;";
        $data .= "ЦЭНЭГЛЭЛТ/ТӨЛСӨН/;";
        $data .= "ТӨЛӨВ;";
        $data .= "ЭХЭЛСЭН;";
        $data .= "ДУУССАН\n";
        foreach ($xacList as $xac) {
            $data.='"' . $xac->order_id . '";';
            $data.='"' . $xac->bank_account . '";';
            $data.='"' . $xac->order_mobile . '";';
            $data.='"' . $xac->order_type . '";';
            $data.='"' . $xac->order_amount . '";';
            $data.='"' . BankXacTable::getStatusName($xac->status, BankXacTable::TYPE_CALLPAYMENT) . '";';
            $data.='"' . $xac->created_at . '";';
            $data.='"' . $xac->updated_at . '";';

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
        $this->bankXac = BankXacTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankXac);

        $this->chargeResponse = LogTools::getLogXacChargeCallPayment($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх Callpayment
     * 
     * @param sfWebRequest $request
     */
    public function executeRechargeCallpayment(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankXac = BankXacTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankXac);

            if ($bankXac->canReCharge()) {
                $bankXac->charge_mobile = $request->getParameter('xacChargeMobile');
                $bankXac->save();

                if (BankXacTable::callPayment($bankXac) == TRUE) {
                    $this->getUser()->setFlash('info', '[' . $bankXac->order_id . '] ' . BankXacTable::getStatusName($bankXac->status, BankXacTable::TYPE_CALLPAYMENT));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankXac->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах хүсэлт амжилтгүй боллоо');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankXac->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах боломжгүй байна!');
            }

            return $this->redirect('@bank_xac_call_payment_list?orderId=' . $bankXac->order_id);
        }

        return $this->redirect('@bank_xac_call_payment_list');
    }

}