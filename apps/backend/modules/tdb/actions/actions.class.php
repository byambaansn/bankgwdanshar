<?php

/**
 * tdb actions.
 *
 * @package    sf_sandbox
 * @subpackage tdb
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tdbActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'tdb');
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
        $this->status = BankTdbTable::getForSelectStatus(BankTdbTable::TYPE_DEALER);

        $this->pager = BankTdbTable::getList(array(BankTdbTable::ACCOUNT_DEALER));

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
        $filename = 'tdbBank';
        $tdbList = BankTdbTable::getListCustom(array(BankTdbTable::ACCOUNT_DEALER), TRUE);

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
        foreach ($tdbList as $tdb) {
            $data.='"' . $tdb['order_id'] . '";';
            $data.='"' . $tdb['bank_account'] . '";';
            $data.='"' . $tdb['charge_mobile'] . '";';
            $data.='"' . $tdb['order_mobile'] . '";';
            $data.='"' . $tdb['order_type'] . '";';
            $data.='"' . $tdb['charge_amount'] . '";';
            $data.='"' . $tdb['order_amount'] . '";';
            $data.='"' . ($tdb['charge_amount'] - $tdb['order_amount']) . '";';
            $data.='"' . BankTdbTable::getStatusName($tdb['status'], BankTdbTable::TYPE_DEALER) . '";';
            $data.='"' . $tdb['created_at'] . '";';
            $data.='"' . $tdb['updated_at'] . '";';

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
        $this->bankTdb = BankTdbTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankTdb);

        $this->chargeResponse = LogTools::getLogTDBCharge($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх
     * 
     * @param sfWebRequest $request
     */
    public function executeRecharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankTdb = BankTdbTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankTdb);

            $chargeNumber = $request->getParameter('tdbChargeMobile');

            if ($bankTdb->canReCharge()) {
                $bankTdb->charge_mobile = $chargeNumber;
                $bankTdb->save();

                if (BaseSms::isAdShop($chargeNumber)) {
                    $result = BankTdbTable::rechargeSMSApi($bankTdb, "AD");
                } elseif (BaseSms::isSdDealer($chargeNumber)) {
                    $result = BankTdbTable::rechargeSMSApi($bankTdb, "SD");
                } else {
                    $result = BankTdbTable::recharge($bankTdb);
                }
                if ($result) {
                    $this->getUser()->setFlash('info', '[' . $bankTdb->order_id . '] ' . BankTdbTable::getStatusName($bankTdb->status, BankTdbTable::TYPE_DEALER));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankTdb->order_id . '] ' . 'Уучлаарай, дахин цэнэглэлт амжилтгүй боллоо!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankTdb->order_id . '] ' . 'Уучлаарай, дахин цэнэглэх боломжгүй байна!');
            }

            return $this->redirect('@bank_tdb_list?orderId=' . $bankTdb->order_id);
        }

        return $this->redirect('@bank_tdb_list');
    }

    /**
     * Зарлага хийх
     * 
     * @param sfWebRequest $request
     */
    public function executeReoutcome(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankTdb = BankTdbTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankTdb);

            if ($bankTdb->canReOutcome()) {
                # Dealer AGENT check
                $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/dealer/process-' . date("Ymd") . '.log'));
                $dealerAgent = DealerGateway::findDealerByMobile($bankTdb->charge_mobile, $logger);
                if (!$dealerAgent) {
                    $dealer = DealerCharge::getDealer($bankTdb->charge_mobile);
                }
                $outcomeOrderId = BankTdbTable::reoutcome($bankTdb, $dealer, date('Y-m-d', strtotime($bankTdb->getCreatedAt())), $dealerAgent);
                if ($outcomeOrderId) {
                    $bankTdb->status = BankTdbTable::STAT_SUCCESS;
                    $bankTdb->sales_order_id = $outcomeOrderId;
                    $bankTdb->transfer_sap = 0;
                    $bankTdb->save();

                    $this->getUser()->setFlash('info', '[' . $bankTdb->order_id . '] ' . 'Зарлагыг амжилттай үүсгэлээ. ' . BankTdbTable::getStatusName($bankTdb->status, BankTdbTable::TYPE_DEALER) . ' төлөвт орууллаа');
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankTdb->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![1]');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankTdb->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![2]');
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
            $bankTdb = BankTdbTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankTdb);

            if ($bankTdb->canReOutcome() && $bankTdb->getSalesOrderId()) {
                $bankTdb->status = BankTdbTable::STAT_SUCCESS;
                $bankTdb->save();
                $this->getUser()->setFlash('info', '[' . $bankTdb->order_id . '] ' . BankTdbTable::getStatusName($bankTdb->status, BankTdbTable::TYPE_DEALER) . ' төлөвт орууллаа');
            } else {
                $this->getUser()->setFlash('error', '[' . $bankTdb->order_id . '] ' . 'Уучлаарай, зарлага амжилттай төлөвт оруулах боломжгүй байна!');
            }
        }

        return $this->redirect('@bank_tdb_list');
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
            $blockBank = BlockTable::retrieveByBank(VendorTable::BANK_TDB);
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
        $this->status = BankTdbTable::getForSelectStatus(BankTdbTable::TYPE_CALLPAYMENT);

        $this->pager = BankTdbTable::getList(BankTdbTable::ACCOUNT_CALLPAYMENT);

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
        $filename = 'tdbBank';
        $tdbList = BankTdbTable::getList(BankTdbTable::ACCOUNT_CALLPAYMENT, TRUE);

        $data = "№ ГҮЙЛГЭЭ;";
        $data .= "ДАНСНЫ ДУГААР;";
        $data .= "ДУГААР/D/;";
        $data .= "ТӨРӨЛ;";
        $data .= "ЦЭНЭГЛЭЛТ/ТӨЛСӨН/;";
        $data .= "ТӨЛӨВ;";
        $data .= "ЭХЭЛСЭН;";
        $data .= "ДУУССАН\n";
        foreach ($tdbList as $tdb) {
            $data.='"' . $tdb->order_id . '";';
            $data.='"' . $tdb->bank_account . '";';
            $data.='"' . $tdb->order_mobile . '";';
            $data.='"' . $tdb->order_type . '";';
            $data.='"' . $tdb->order_amount . '";';
            $data.='"' . BankTdbTable::getStatusName($tdb->status, BankTdbTable::TYPE_CALLPAYMENT) . '";';
            $data.='"' . $tdb->created_at . '";';
            $data.='"' . $tdb->updated_at . '";';

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
        $this->bankTdb = BankTdbTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankTdb);

        $this->chargeResponse = LogTools::getLogTDBhargeCallPayment($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх Callpayment
     * 
     * @param sfWebRequest $request
     */
    public function executeRechargeCallpayment(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankTdb = BankTdbTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankTdb);

            if ($bankTdb->canReCharge()) {
                $bankTdb->charge_mobile = $request->getParameter('tdbChargeMobile');
                $bankTdb->save();

                if (BankTdbTable::callPayment($bankTdb) == TRUE) {
                    $this->getUser()->setFlash('info', '[' . $bankTdb->order_id . '] ' . BankTdbTable::getStatusName($bankTdb->status, BankTdbTable::TYPE_CALLPAYMENT));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankTdb->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах хүсэлт амжилтгүй боллоо');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankTdb->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах боломжгүй байна!');
            }

            return $this->redirect('@bank_tdb_call_payment_list?orderId=' . $bankTdb->order_id);
        }

        return $this->redirect('@bank_tdb_call_payment_list');
    }

}