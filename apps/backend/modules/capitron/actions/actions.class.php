<?php

/**
 * capitron actions.
 *
 * @package    sf_sandbox
 * @subpackage capitron
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class capitronActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'capitron');
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
        $this->status = BankCapitronTable::getForSelectStatus(BankCapitronTable::TYPE_CALLPAYMENT);
        $this->pager = BankCapitronTable::getList(BankCapitronAccountTable::ACCOUNT_CALLPAYMENT);
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
        $filename = 'CapitronBank';
        $capitronList = BankCapitronTable::getListCustom(BankCapitronAccountTable::ACCOUNT_CALLPAYMENT);

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
        foreach ($capitronList as $capitron) {
            $data.='"' . $capitron['order_id'] . '";';
            $data.='"' . $capitron['bank_account'] . '";';
            $data.='"' . $capitron['charge_mobile'] . '";';
            $data.='"' . $capitron['order_mobile'] . '";';
            $data.='"' . $capitron['order_type'] . '";';
            $data.='"' . $capitron['charge_amount'] . '";';
            $data.='"' . $capitron['order_amount'] . '";';
            $data.='"' . ($capitron['charge_amount'] - $capitron['order_amount']) . '";';
            $data.='"' . BankCapitronTable::getStatusName($capitron['status'], BankCapitronTable::TYPE_CALLPAYMENT) . '";';
            $data.='"' . $capitron['created_at'] . '";';
            $data.='"' . $capitron['updated_at'] . '";';

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
        $this->bankCapitron = BankCapitronTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankCapitron);

        $this->chargeResponse = LogTools::getLogCapitronCharge($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх
     * 
     * @param sfWebRequest $request
     */
    public function executeRecharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankCapitron = BankCapitronTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankCapitron);
            $chargeNumber = $request->getParameter('capitronChargeMobile');

            if ($bankCapitron->canReCharge()) {
                $bankCapitron->charge_mobile = $chargeNumber;

                if (BaseSms::isAdShop($chargeNumber)) {
                    $result = BankCapitronTable::rechargeSMSApi($bankCapitron, "AD");
                } elseif (BaseSms::isSdDealer($chargeNumber)) {
                    $result = BankCapitronTable::rechargeSMSApi($bankCapitron, "SD");
                } else {
                    $result = BankCapitronTable::recharge($bankCapitron);
                }
                if ($result) {
                    $this->getUser()->setFlash('info', '[' . $bankCapitron->order_id . '] ' . BankCapitronTable::getStatusName($bankCapitron->status, BankCapitronTable::TYPE_CALLPAYMENT));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankCapitron->order_id . '] ' . 'Уучлаарай, дахин цэнэглэлт амжилтгүй боллоо!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankCapitron->order_id . '] ' . 'Уучлаарай, дахин цэнэглэх боломжгүй төлөвт байна!');
            }

            return $this->redirect('@bank_capitron_list?orderId=' . $bankCapitron->order_id . '&dateFrom=' . $bankCapitron->order_date);
        }

        return $this->redirect('@bank_capitron_list');
    }

    /**
     * Зарлага хийх
     * 
     * @param sfWebRequest $request
     */
    public function executeReoutcome(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankCapitron = BankCapitronTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankCapitron);

            if ($bankCapitron->canReOutcome()) {
                $dealer = DealerCharge::getDealer($bankCapitron->charge_mobile);

                $outcomeOrderId = BankCapitronTable::reoutcome($bankCapitron, $dealer, date('Y-m-d', strtotime($bankCapitron->getCreatedAt())));
                if ($outcomeOrderId) {
                    $bankCapitron->status = BankCapitronTable::STAT_SUCCESS;
                    $bankCapitron->sales_order_id = $outcomeOrderId;
                    $bankCapitron->transfer_sap = 0;
                    $bankCapitron->save();

                    $this->getUser()->setFlash('info', '[' . $bankCapitron->order_id . '] ' . 'Зарлагыг амжилттай үүсгэлээ. ' . BankCapitronTable::getStatusName($bankCapitron->status, BankCapitronTable::TYPE_CALLPAYMENT) . ' төлөвт орууллаа');
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankCapitron->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![1]');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankCapitron->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![2]');
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
            $bankCapitron = BankCapitronTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankCapitron);

            if ($bankCapitron->canReOutcome() && $bankCapitron->getSalesOrderId()) {
                $bankCapitron->status = BankCapitronTable::STAT_SUCCESS;
                $bankCapitron->save();
                $this->getUser()->setFlash('info', '[' . $bankCapitron->order_id . '] ' . BankCapitronTable::getStatusName($bankCapitron->status, BankCapitronTable::TYPE_CALLPAYMENT) . ' төлөвт орууллаа');
            } else {
                $this->getUser()->setFlash('error', '[' . $bankCapitron->order_id . '] ' . 'Уучлаарай, зарлага амжилттай төлөвт оруулах боломжгүй байна!');
            }
        }

        return $this->redirect('@bank_capitron_list');
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
            $blockBank = BlockTable::retrieveByBank(VendorTable::BANK_CAPITRON);
            if ($blockBank) {
                $blockBank->setBlock($block);
                $blockBank - save();
                $message = ($block == BlockTable::BLOCK) ? 'хаалаа' : 'нээлээ';
                $this->getUser()->setFlash('info', 'Капитрон банкны автомат цэнэглэлтийг амжилттай ' . $message);
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
        $this->status = BankCapitronTable::getForSelectStatus(BankCapitronTable::TYPE_CALLPAYMENT);

        $this->pager = BankCapitronTable::getList(BankCapitronAccountTable::ACCOUNT_CALLPAYMENT);

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
        $filename = 'capitronBank';
        $capitronList = BankCapitronTable::getList(BankCapitronAccountTable::ACCOUNT_CALLPAYMENT, TRUE);

        $data = "№ ГҮЙЛГЭЭ;";
        $data .= "ДАНСНЫ ДУГААР;";
        $data .= "ДУГААР/D/;";
        $data .= "ТӨРӨЛ;";
        $data .= "ЦЭНЭГЛЭЛТ/ТӨЛСӨН/;";
        $data .= "ТӨЛӨВ;";
        $data .= "ЭХЭЛСЭН;";
        $data .= "ДУУССАН\n";
        foreach ($capitronList as $capitron) {
            $data.='"' . $capitron->order_id . '";';
            $data.='"' . $capitron->bank_account . '";';
            $data.='"' . $capitron->order_mobile . '";';
            $data.='"' . $capitron->order_type . '";';
            $data.='"' . $capitron->order_amount . '";';
            $data.='"' . BankCapitronTable::getStatusName($capitron->status, BankCapitronTable::TYPE_CALLPAYMENT) . '";';
            $data.='"' . $capitron->created_at . '";';
            $data.='"' . $capitron->updated_at . '";';

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
        $this->bankCapitron = BankCapitronTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankCapitron);

        $this->chargeResponse = LogTools::getLogCapitronChargeCallPayment($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх Callpayment
     * 
     * @param sfWebRequest $request
     */
    public function executeRechargeCallpayment(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankCapitron = BankCapitronTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankCapitron);

            if ($bankCapitron->canReCharge()) {
                $bankCapitron->charge_mobile = $request->getParameter('capitronChargeMobile');
                $bankCapitron->save();

                if (BankCapitronTable::callPayment($bankCapitron) == TRUE) {
                    $this->getUser()->setFlash('info', '[' . $bankCapitron->order_id . '] ' . BankCapitronTable::getStatusName($bankCapitron->status, BankCapitronTable::TYPE_CALLPAYMENT));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankCapitron->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах хүсэлт амжилтгүй боллоо');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankCapitron->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах боломжгүй байна!');
            }

            return $this->redirect('@bank_capitron_call_payment_list?orderId=' . $bankCapitron->order_id);
        }

        return $this->redirect('@bank_capitron_call_payment_list');
    }

}