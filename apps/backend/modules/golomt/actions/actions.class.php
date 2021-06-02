<?php

/**
 * golomt actions.
 *
 * @package    sf_sandbox
 * @subpackage golomt
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class golomtActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'golomt');
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
        $this->status = BankGolomtTable::getForSelectStatus(BankGolomtTable::TYPE_DEALER);

        $this->pager = BankGolomtTable::getList(array(BankGolomtTable::ACCOUNT_DEALER));

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
        $filename = 'golomtBank';
        $golomtList = BankGolomtTable::getListCustom(array(BankGolomtTable::ACCOUNT_DEALER, BankGolomtTable::ACCOUNT_DEALER_MOBICOM), TRUE);

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
        foreach ($golomtList as $golomt) {
            $data.='"' . $golomt['order_id'] . '";';
            $data.='"' . $golomt['bank_account'] . '";';
            $data.='"' . $golomt['charge_mobile'] . '";';
            $data.='"' . $golomt['order_mobile'] . '";';
            $data.='"' . $golomt['order_type'] . '";';
            $data.='"' . $golomt['charge_amount'] . '";';
            $data.='"' . $golomt['order_amount'] . '";';
            $data.='"' . ($golomt['charge_amount'] - $golomt['order_amount']) . '";';
            $data.='"' . BankGolomtTable::getStatusName($golomt['status'], BankGolomtTable::TYPE_DEALER) . '";';
            $data.='"' . $golomt['created_at'] . '";';
            $data.='"' . $golomt['updated_at'] . '";';

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
        $this->bankGolomt = BankGolomtTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankGolomt);

        $this->chargeResponse = LogTools::getLogGolomtCharge($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх
     * 
     * @param sfWebRequest $request
     */
    public function executeRecharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankGolomt = BankGolomtTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankGolomt);
            $chargeNumber = $request->getParameter('golomtChargeMobile');

            if ($bankGolomt->canReCharge()) {
                $bankGolomt->charge_mobile = $chargeNumber;
                $bankGolomt->save();

                if (BaseSms::isAdShop($chargeNumber)) {
                    $result = BankGolomtTable::rechargeSMSApi($bankGolomt, "AD");
                } elseif (BaseSms::isSdDealer($chargeNumber)) {
                    $result = BankGolomtTable::rechargeSMSApi($bankGolomt, "SD");
                } else {
                    $result = BankGolomtTable::recharge($bankGolomt);
                }
                if ($result) {
                    $this->getUser()->setFlash('info', '[' . $bankGolomt->order_id . '] ' . BankGolomtTable::getStatusName($bankGolomt->status, BankGolomtTable::TYPE_DEALER));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankGolomt->order_id . '] ' . 'Уучлаарай, дахин цэнэглэлт амжилтгүй боллоо!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankGolomt->order_id . '] ' . 'Уучлаарай, дахин цэнэглэх боломжгүй байна!');
            }

            return $this->redirect('@bank_golomt_list?orderId=' . $bankGolomt->order_id . '&dateFrom=' . $bankGolomt->order_date);
        }

        return $this->redirect('@bank_golomt_list');
    }

    /**
     * Зарлага хийх
     * 
     * @param sfWebRequest $request
     */
    public function executeReoutcome(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankGolomt = BankGolomtTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankGolomt);

            if ($bankGolomt->canReOutcome()) {
                # Dealer AGENT check
                $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/dealer/process-' . date("Ymd") . '.log'));
                $dealerAgent = DealerGateway::findDealerByMobile($bankGolomt->charge_mobile, $logger);
                if (!$dealerAgent) {
                    $dealer = DealerCharge::getDealer($bankGolomt->charge_mobile);
                }
                $outcomeOrderId = BankGolomtTable::reoutcome($bankGolomt, $dealer, date('Y-m-d', strtotime($bankGolomt->getCreatedAt())), $dealerAgent);
                if ($outcomeOrderId) {
                    $bankGolomt->status = BankGolomtTable::STAT_SUCCESS;
                    $bankGolomt->sales_order_id = $outcomeOrderId;
                    $bankGolomt->transfer_sap = 0;
                    $bankGolomt->save();

                    $this->getUser()->setFlash('info', '[' . $bankGolomt->order_id . '] ' . 'Зарлагыг амжилттай үүсгэлээ. ' . BankGolomtTable::getStatusName($bankGolomt->status, BankGolomtTable::TYPE_DEALER) . ' төлөвт орууллаа');
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankGolomt->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![1]');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankGolomt->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![2]');
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
            $bankGolomt = BankGolomtTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankGolomt);

            if ($bankGolomt->canReOutcome() && $bankGolomt->getSalesOrderId()) {
                $bankGolomt->status = BankGolomtTable::STAT_SUCCESS;
                $bankGolomt->save();
                $this->getUser()->setFlash('info', '[' . $bankGolomt->order_id . '] ' . BankGolomtTable::getStatusName($bankGolomt->status, BankGolomtTable::TYPE_DEALER) . ' төлөвт орууллаа');
            } else {
                $this->getUser()->setFlash('error', '[' . $bankGolomt->order_id . '] ' . 'Уучлаарай, зарлага амжилттай төлөвт оруулах боломжгүй байна!');
            }
        }

        return $this->redirect('@bank_golomt_list');
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
            $blockBank = BlockTable::retrieveByBank(VendorTable::GOLOMT);
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
        $this->status = BankGolomtTable::getForSelectStatus(BankGolomtTable::TYPE_CALLPAYMENT);

        $this->pager = BankGolomtTable::getList(BankGolomtTable::ACCOUNT_CALLPAYMENT);

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
        $filename = 'golomtBank';
        $golomtList = BankGolomtTable::getList(BankGolomtTable::ACCOUNT_CALLPAYMENT, TRUE);

        $data = "№ ГҮЙЛГЭЭ;";
        $data .= "ДАНСНЫ ДУГААР;";
        $data .= "ДУГААР/D/;";
        $data .= "ТӨРӨЛ;";
        $data .= "ЦЭНЭГЛЭЛТ/ТӨЛСӨН/;";
        $data .= "ТӨЛӨВ;";
        $data .= "ЭХЭЛСЭН;";
        $data .= "ДУУССАН\n";
        foreach ($golomtList as $golomt) {
            $data.='"' . $golomt->order_id . '";';
            $data.='"' . $golomt->bank_account . '";';
            $data.='"' . $golomt->order_mobile . '";';
            $data.='"' . $golomt->order_type . '";';
            $data.='"' . $golomt->order_amount . '";';
            $data.='"' . BankGolomtTable::getStatusName($golomt->status, BankGolomtTable::TYPE_CALLPAYMENT) . '";';
            $data.='"' . $golomt->created_at . '";';
            $data.='"' . $golomt->updated_at . '";';

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
        $this->bankGolomt = BankGolomtTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankGolomt);

        $this->chargeResponse = LogTools::getLogGolomtChargeCallPayment($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх Callpayment
     * 
     * @param sfWebRequest $request
     */
    public function executeRechargeCallpayment(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankGolomt = BankGolomtTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankGolomt);

            if ($bankGolomt->canReCharge()) {
                $bankGolomt->charge_mobile = $request->getParameter('golomtChargeMobile');
                $bankGolomt->save();

                if (BankGolomtTable::callPayment($bankGolomt) == TRUE) {
                    $this->getUser()->setFlash('info', '[' . $bankGolomt->order_id . '] ' . BankGolomtTable::getStatusName($bankGolomt->status, BankGolomtTable::TYPE_CALLPAYMENT));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankGolomt->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах хүсэлт амжилтгүй боллоо');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankGolomt->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах боломжгүй байна!');
            }

            return $this->redirect('@bank_golomt_call_payment_list?orderId=' . $bankGolomt->order_id);
        }

        return $this->redirect('@bank_golomt_call_payment_list');
    }

}