<?php

/**
 * capital actions.
 *
 * @package    sf_sandbox
 * @subpackage capital
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class capitalActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'capital');
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
        $this->status = BankCapitalTable::getForSelectStatus(BankCapitalTable::TYPE_DEALER);

        $this->pager = BankCapitalTable::getList(array(BankCapitalAccountTable::ACCOUNT_DEALER, BankCapitalAccountTable::ACCOUNT_DEALER_MOBICOM));

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
        $filename = 'capitalBank';
        $capitalList = BankCapitalTable::getListCustom(array(BankCapitalAccountTable::ACCOUNT_DEALER, BankCapitalAccountTable::ACCOUNT_DEALER_MOBICOM), TRUE);

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
        foreach ($capitalList as $capital) {
            $data.='"' . $capital['order_id'] . '";';
            $data.='"' . $capital['bank_account'] . '";';
            $data.='"' . $capital['charge_mobile'] . '";';
            $data.='"' . $capital['order_mobile'] . '";';
            $data.='"' . $capital['order_type'] . '";';
            $data.='"' . $capital['charge_amount'] . '";';
            $data.='"' . $capital['order_amount'] . '";';
            $data.='"' . ($capital['charge_amount'] - $capital['order_amount']) . '";';
            $data.='"' . BankCapitalTable::getStatusName($capital['status'], BankCapitalTable::TYPE_DEALER) . '";';
            $data.='"' . $capital['created_at'] . '";';
            $data.='"' . $capital['updated_at'] . '";';

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
        $this->bankCapital = BankCapitalTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankCapital);

        $this->chargeResponse = LogTools::getLogCapitalCharge($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх
     * 
     * @param sfWebRequest $request
     */
    public function executeRecharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankCapital = BankCapitalTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankCapital);

            if ($bankCapital->canReCharge()) {
                $bankCapital->charge_mobile = $request->getParameter('capitalChargeMobile');
                $bankCapital->save();

                if (BankCapitalTable::recharge($bankCapital) == TRUE) {
                    $this->getUser()->setFlash('info', '[' . $bankCapital->order_id . '] ' . BankCapitalTable::getStatusName($bankCapital->status, BankCapitalTable::TYPE_DEALER));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankCapital->order_id . '] ' . 'Уучлаарай, дахин цэнэглэлт амжилтгүй боллоо!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankCapital->order_id . '] ' . 'Уучлаарай, дахин цэнэглэх боломжгүй байна!');
            }

            return $this->redirect('@bank_capital_list?orderId=' . $bankCapital->order_id);
        }

        return $this->redirect('@bank_capital_list');
    }

    /**
     * Зарлага хийх
     * 
     * @param sfWebRequest $request
     */
    public function executeReoutcome(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankCapital = BankCapitalTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankCapital);

            if ($bankCapital->canReOutcome()) {
                $dealer = DealerCharge::getDealer($bankCapital->charge_mobile);

                $outcomeOrderId = BankCapitalTable::reoutcome($bankCapital, $dealer, date('Y-m-d', strtotime($bankCapital->getCreatedAt())));
                if ($outcomeOrderId) {
                    $bankCapital->status = BankCapitalTable::STAT_SUCCESS;
                    $bankCapital->sales_order_id = $outcomeOrderId;
                    $bankCapital->transfer_sap = 0;
                    $bankCapital->save();

                    $this->getUser()->setFlash('info', '[' . $bankCapital->order_id . '] ' . 'Зарлагыг амжилттай үүсгэлээ. ' . BankCapitalTable::getStatusName($bankCapital->status, BankCapitalTable::TYPE_DEALER) . ' төлөвт орууллаа');
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankCapital->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![1]');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankCapital->order_id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![2]');
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
            $bankCapital = BankCapitalTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankCapital);

            if ($bankCapital->canReOutcome() && $bankCapital->getSalesOrderId()) {
                $bankCapital->status = BankCapitalTable::STAT_SUCCESS;
                $bankCapital->save();
                $this->getUser()->setFlash('info', '[' . $bankCapital->order_id . '] ' . BankCapitalTable::getStatusName($bankCapital->status, BankCapitalTable::TYPE_DEALER) . ' төлөвт орууллаа');
            } else {
                $this->getUser()->setFlash('error', '[' . $bankCapital->order_id . '] ' . 'Уучлаарай, зарлага амжилттай төлөвт оруулах боломжгүй байна!');
            }
        }

        return $this->redirect('@bank_capital_list');
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
            $blockBank = BlockTable::retrieveByBank(VendorTable::BANK_CAPITAL);
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
        $this->status = BankCapitalTable::getForSelectStatus(BankCapitalTable::TYPE_CALLPAYMENT);

        $this->pager = BankCapitalTable::getList(BankCapitalAccountTable::ACCOUNT_CALLPAYMENT);

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
        $filename = 'capitalBank';
        $capitalList = BankCapitalTable::getList(BankCapitalAccountTable::ACCOUNT_CALLPAYMENT, TRUE);

        $data = "№ ГҮЙЛГЭЭ;";
        $data .= "ДАНСНЫ ДУГААР;";
        $data .= "ДУГААР/D/;";
        $data .= "ТӨРӨЛ;";
        $data .= "ЦЭНЭГЛЭЛТ/ТӨЛСӨН/;";
        $data .= "ТӨЛӨВ;";
        $data .= "ЭХЭЛСЭН;";
        $data .= "ДУУССАН\n";
        foreach ($capitalList as $capital) {
            $data.='"' . $capital->order_id . '";';
            $data.='"' . $capital->bank_account . '";';
            $data.='"' . $capital->order_mobile . '";';
            $data.='"' . $capital->order_type . '";';
            $data.='"' . $capital->order_amount . '";';
            $data.='"' . BankCapitalTable::getStatusName($capital->status, BankCapitalTable::TYPE_CALLPAYMENT) . '";';
            $data.='"' . $capital->created_at . '";';
            $data.='"' . $capital->updated_at . '";';

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
        $this->bankCapital = BankCapitalTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankCapital);

        $this->chargeResponse = LogTools::getLogCapitalChargeCallPayment($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх Callpayment
     * 
     * @param sfWebRequest $request
     */
    public function executeRechargeCallpayment(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankCapital = BankCapitalTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankCapital);

            if ($bankCapital->canReCharge()) {
                $bankCapital->charge_mobile = $request->getParameter('capitalChargeMobile');
                $bankCapital->save();

                if (BankCapitalTable::callPayment($bankCapital) == TRUE) {
                    $this->getUser()->setFlash('info', '[' . $bankCapital->order_id . '] ' . BankCapitalTable::getStatusName($bankCapital->status, BankCapitalTable::TYPE_CALLPAYMENT));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankCapital->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах хүсэлт амжилтгүй боллоо');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankCapital->order_id . '] ' . 'Уучлаарай, дахин төлөлт оруулах боломжгүй байна!');
            }

            return $this->redirect('@bank_capital_call_payment_list?orderId=' . $bankCapital->order_id);
        }

        return $this->redirect('@bank_capital_call_payment_list');
    }

}