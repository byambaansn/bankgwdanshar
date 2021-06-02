<?php

/**
 * khaan actions.
 *
 * @package    sf_sandbox
 * @subpackage khaan
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class khaanActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'khaan');
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
        $this->status = BankKhaanTable::getForSelectStatus(BankKhaanTable::TYPE_DEALER);

        $this->pager = BankKhaanTable::getList(array(BankKhaanAccountTable::ACCOUNT_DEALER, BankKhaanAccountTable::ACCOUNT_DEALER_MOBICOM));

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
        $filename = 'khaanBank';
        $khaanList = BankKhaanTable::getListCustom(array(BankKhaanAccountTable::ACCOUNT_DEALER, BankKhaanAccountTable::ACCOUNT_DEALER_MOBICOM), TRUE);

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
        foreach ($khaanList as $khaan) {
            $data .= '"' . $khaan['order_id'] . '";';
            $data .= '"' . $khaan['bank_account'] . '";';
            $data .= '"' . $khaan['charge_mobile'] . '";';
            $data .= '"' . $khaan['order_mobile'] . '";';
            $data .= '"' . $khaan['order_type'] . '";';
            $data .= '"' . $khaan['charge_amount'] . '";';
            $data .= '"' . $khaan['order_amount'] . '";';
            $data .= '"' . ($khaan['charge_amount'] - $khaan['order_amount']) . '";';
            $data .= '"' . BankKhaanTable::getStatusName($khaan['status'], BankKhaanTable::TYPE_DEALER) . '";';
            $data .= '"' . $khaan['created_at'] . '";';
            $data .= '"' . $khaan['updated_at'] . '";';

            $data .= "\n";
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
        $this->bankKhaan = BankKhaanTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankKhaan);

        $chargeResponse = LogTools::getLogKhaanCharge($request->getParameter('id'));
        if (!$chargeResponse) {
            $chargeResponse = LogTools::getLogDealerCharge($request->getParameter('id'), 'ChainDealerCharge');
        }
        $this->chargeResponse = $chargeResponse;
    }

    /**
     * Дахин цэнэглэх
     * 
     * @param sfWebRequest $request
     */
    public function executeRecharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankKhaan = BankKhaanTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankKhaan);
            $chargeNumber = $request->getParameter('khaanChargeMobile');

            if ($bankKhaan->canReCharge()) {
                $bankKhaan->charge_mobile = $chargeNumber;

                if (BaseSms::isAdShop($chargeNumber)) {
                    $result = BankKhaanTable::rechargeSMSApi($bankKhaan, "AD");
                } elseif (BaseSms::isSdDealer($chargeNumber)) {
                    $result = BankKhaanTable::rechargeSMSApi($bankKhaan, "SD");
                } else {
                    $result = BankKhaanTable::recharge($bankKhaan, $chargeNumber);
                }
                if ($result) {
                    $this->getUser()->setFlash('info', '[' . $bankKhaan->order_id . '] ' . BankKhaanTable::getStatusName($bankKhaan->status, BankKhaanTable::TYPE_DEALER));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankKhaan->order_id . '] ' . 'Уучлаарай, дахин цэнэглэлт амжилтгүй боллоо!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankKhaan->order_id . '] ' . 'Уучлаарай, дахин цэнэглэх боломжгүй төлөвт байна!');
            }

            return $this->redirect('@bank_khaan_list?orderId=' . $bankKhaan->order_id . '&dateFrom=' . $bankKhaan->order_date);
        }

        return $this->redirect('@bank_khaan_list');
    }

    /**
     * Зарлага хийх
     * 
     * @param sfWebRequest $request
     */
    public function executeReoutcome(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankKhaan = BankKhaanTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankKhaan);

            if ($bankKhaan->canReOutcome()) {
                # Dealer AGENT check
                $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/dealer/process-' . date("Ymd") . '.log'));
                $dealerAgent = DealerGateway::findDealerByMobile($bankKhaan->charge_mobile, $logger);

                if (!$dealerAgent) {
                    $dealer = DealerCharge::getDealer($bankKhaan->charge_mobile);
                }

                $outcomeOrderId = BankKhaanTable::reoutcome($bankKhaan, $dealer, date('Y-m-d', strtotime($bankKhaan->getCreatedAt())), $dealerAgent);
                if ($outcomeOrderId) {
                    $bankKhaan->status = BankKhaanTable::STAT_SUCCESS;
                    $bankKhaan->sales_order_id = $outcomeOrderId;
                    $bankKhaan->transfer_sap = 0;
                    $bankKhaan->save();

                    $this->getUser()->setFlash('info', '[' . $bankKhaan->order_id . '] ' . 'Зарлагыг амжилттай үүсгэлээ. ' . BankKhaanTable::getStatusName($bankKhaan->status, BankKhaanTable::TYPE_DEALER) . ' төлөвт орууллаа');
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankKhaan->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![1]');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankKhaan->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![2]');
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
            $bankKhaan = BankKhaanTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankKhaan);

            if ($bankKhaan->canReOutcome() && $bankKhaan->getSalesOrderId()) {
                $bankKhaan->status = BankKhaanTable::STAT_SUCCESS;
                $bankKhaan->save();
                $this->getUser()->setFlash('info', '[' . $bankKhaan->order_id . '] ' . BankKhaanTable::getStatusName($bankKhaan->status, BankKhaanTable::TYPE_DEALER) . ' төлөвт орууллаа');
            } else {
                $this->getUser()->setFlash('error', '[' . $bankKhaan->order_id . '] ' . 'Уучлаарай, зарлага амжилттай төлөвт оруулах боломжгүй байна!');
            }
        }

        return $this->redirect('@bank_khaan_list');
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
            $blockBank = BlockTable::retrieveByBank(VendorTable::BANK_KHAAN);
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
        $this->status = BankKhaanTable::getForSelectStatus(BankKhaanTable::TYPE_CALLPAYMENT);

        $this->pager = BankKhaanTable::getList(BankKhaanAccountTable::ACCOUNT_CALLPAYMENT);

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
        $filename = 'khaanBank';
        $khaanList = BankKhaanTable::getList(BankKhaanAccountTable::ACCOUNT_CALLPAYMENT, TRUE);

        $data = "№ ГҮЙЛГЭЭ;";
        $data .= "ДАНСНЫ ДУГААР;";
        $data .= "ДУГААР/D/;";
        $data .= "ТӨРӨЛ;";
        $data .= "ЦЭНЭГЛЭЛТ/ТӨЛСӨН/;";
        $data .= "ТӨЛӨВ;";
        $data .= "ЭХЭЛСЭН;";
        $data .= "ДУУССАН\n";
        foreach ($khaanList as $khaan) {
            $data .= '"' . $khaan->order_id . '";';
            $data .= '"' . $khaan->bank_account . '";';
            $data .= '"' . $khaan->order_mobile . '";';
            $data .= '"' . $khaan->order_type . '";';
            $data .= '"' . $khaan->order_amount . '";';
            $data .= '"' . BankKhaanTable::getStatusName($khaan->status, BankKhaanTable::TYPE_CALLPAYMENT) . '";';
            $data .= '"' . $khaan->created_at . '";';
            $data .= '"' . $khaan->updated_at . '";';

            $data .= "\n";
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
        $this->bankKhaan = BankKhaanTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankKhaan);

        $this->chargeResponse = LogTools::getLogKhaanChargeCallPayment($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх Callpayment
     * 
     * @param sfWebRequest $request
     */
    public function executeRechargeCallpayment(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankKhaan = BankKhaanTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankKhaan);

            if ($bankKhaan->canReCharge()) {
                $bankKhaan->charge_mobile = $request->getParameter('khaanChargeMobile');
                $bankKhaan->save();

                if (BankKhaanTable::callPayment($bankKhaan) == TRUE) {
                    $this->getUser()->setFlash('info', '[' . $bankKhaan->order_id . '] ' . BankKhaanTable::getStatusName($bankKhaan->status, BankKhaanTable::TYPE_CALLPAYMENT));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankKhaan->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах хүсэлт амжилтгүй боллоо');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankKhaan->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах боломжгүй байна!');
            }

            return $this->redirect('@bank_khaan_call_payment_list?orderId=' . $bankKhaan->order_id);
        }

        return $this->redirect('@bank_khaan_call_payment_list');
    }

}