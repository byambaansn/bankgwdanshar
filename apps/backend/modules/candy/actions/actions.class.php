<?php

/**
 * candy actions.
 *
 * @package    sf_sandbox
 * @subpackage savings
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class candyActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'candy');
    }

    public function executeDealer(sfWebRequest $request)
    {
        $this->hasExcelCredential = in_array('bankpayment_excel', $this->getUser()->getCredentials());
        $this->getRequest()->setParameter('sub_tab', 'dealer');
        $this->dateFrom = $request->getParameter('dateFrom', date('Y-m-d'));
        $this->dateTo = $request->getParameter('dateTo', date('Y-m-d'));

        $this->chargedMobile = $request->getParameter('chargedMobile');
        $this->orderedMobile = $request->getParameter('orderedMobile');
        $this->orderId = $request->getParameter('orderId');

        $this->sta = (int) $request->getParameter('status');
        $this->status = BankCandyTable::getForSelectStatus();

        $this->pager = BankCandyTable::getList();

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

    public function executeDealerExcel(sfWebRequest $request)
    {
        $filename = 'candyBank';
        $candyList = BankCandyTable::getList(TRUE);

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
        foreach ($candyList as $candy) {
            $data .= '"' . $candy->order_id . '";';
            $data .= '"' . $candy->bank_account . '";';
            $data .= '"' . $candy->charge_mobile . '";';
            $data .= '"' . $candy->order_mobile . '";';
            $data .= '"' . $candy->order_type . '";';
            $data .= '"' . $candy->charge_amount . '";';
            $data .= '"' . $candy->order_amount . '";';
            $data .= '"' . ($candy->charge_amount - $candy->order_amount) . '";';
            $data .= '"' . BankCandyTable::getStatusName($candy->status) . '";';
            $data .= '"' . $candy->created_at . '";';
            $data .= '"' . $candy->updated_at . '";';

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
    public function executeDealerStatus(sfWebRequest $request)
    {
        $this->bankCandy = BankCandyTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($this->bankCandy);

        $this->chargeResponse = LogTools::getLogCandyCharge($request->getParameter('id'));
    }

    /**
     * Дахин цэнэглэх
     * 
     * @param sfWebRequest $request
     */
    public function executeDealerRecharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankCandy = BankCandyTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankCandy);
            $number = $request->getParameter('chargeMobile');
                    
            if ($bankCandy->canReCharge()) {
                $bankCandy->charge_mobile = $number;
                $bankCandy->save();

                if (BankCandyTable::recharge($bankCandy) == TRUE) {
                    $this->getUser()->setFlash('info', '[' . $bankCandy->order_id . '] ' . BankCandyTable::getStatusName($bankCandy->status));
                } else {
                    $this->getUser()->setFlash('error', '[' . $bankCandy->order_id . '] ' . 'Уучлаарай, дахин цэнэглэлт амжилтгүй боллоо!');
                }
            } else {
                $this->getUser()->setFlash('error', '[' . $bankCandy->order_id . '] ' . 'Уучлаарай, дахин цэнэглэх боломжгүй байна!');
            }

            return $this->redirect('@bank_candy_dealer?orderId=' . $bankCandy->order_id);
        }

        return $this->redirect('@bank_candy_dealer');
    }

    /**
     * Дугаар солих
     * 
     * @param sfWebRequest $request
     */
    public function executeDealerChangeNumber(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $bankCandy = BankCandyTable::retrieveByPK($request->getParameter('id'));
            $this->forward404Unless($bankCandy);

            if ($bankCandy->status == BankCandyTable::STAT_NEW && $bankCandy->charge_mobile == '') {
                $bankCandy->charge_mobile = $request->getParameter('changeNumber');
                $bankCandy->save();
            } else {
                $this->getUser()->setFlash('error', 'Уучлаарай, дугаар солих боломжгүй байна!');
            }

            return $this->redirect('@bank_candy_dealer?id=' . $bankCandy->id);
        }

        return $this->redirect('@bank_candy_dealer');
    }

    public function executeList(sfWebRequest $request)
    {
        $this->hasExcelCredential = in_array('bankpayment_excel', $this->getUser()->getCredentials());
        $this->getRequest()->setParameter('sub_tab', 'candy');
        $this->dateFrom = $request->getParameter('dateFrom', date('Y-m-d'));
        $this->dateTo = $request->getParameter('dateTo', date('Y-m-d'));

        $this->orderId = $request->getParameter('orderId');
        $this->keyword = $request->getParameter('keyword');

        $bank = $request->getParameter('bank', 0);

        $status = $request->getParameter('status', 0);
        $type = $request->getParameter('type', 0);
        if (!$status) {
            $status = CandyLoanCore::getStatusesFailed();
        }
        $this->statuses = BankpaymentTable::getForSelectStatus();
        $this->types = CandyLoanCore::getForSelectType();
        $this->banks = VendorTable::getForSelect();

        $this->rows = CandyLoanCore::getList($this->dateFrom, $this->dateTo, $status, $bank, $this->keyword, $type);

        $this->status = $status;
        $this->type = $type;
        $this->bank = $bank;
        if ($request->getParameter('excel', 0)) {
            set_time_limit(180);
            ini_set("memory_limit", "1024M");
            $data = "БАНК;";
            $data .= "№ ГҮЙЛГЭЭ;";
            $data .= "ТӨРӨЛ;";
            $data .= "ДАНС;";
            $data .= "ДУГААР;";
            $data .= "ЦЭНЭГЛЭЛТ;";
            $data .= "ТӨЛӨВ;";
            $data .= "ЭХЭЛСЭН;";
            $data .= "ДУУССАН\n";

            foreach ($this->rows as $dealer) {
                $type = CandyLoanCore::typeToName($dealer['type']);
                $data .= '"' . $dealer['bank_name'] . '";';
                $data .= '"' . $dealer['bank_order_id'] . '";';
                $data .= '"' . $dealer['bank_account'] . '";';
                $data .= '"' . $type . '";';
                $data .= '"' . $dealer['number'] . '";';
                $data .= '"' . $dealer['paid_amount'] . '";';
                $data .= '"' . BankpaymentTable::getStatusName($dealer['status']) . '";';
                $data .= '"' . $dealer['created_at'] . '";';
                $data .= '"' . $dealer['updated_at'] . '";';

                $data .= "\n";
            }
            $filename = 'candyLoanBank';
            AppTools::ExportCsv($data, $filename, false);
            die();
        }
    }

    /**
     * Төлөв харах
     * 
     * @param sfWebRequest $request
     */
    public function executeStatus(sfWebRequest $request)
    {
        $vendorId = $request->getParameter('vendor_id');
        $id = $request->getParameter('id');
        $bankId = $request->getParameter('bank_order_id');

        $bank = BankpaymentTable::getBankTransaction($vendorId, $bankId);
        $bankpayment = BankpaymentTable::retrieveByPK($id);
        $this->forward404Unless($bank);
        $this->bank = $bank;
        $this->bankpayment = $bankpayment;
        $number = $bank['charge_mobile'];
        if ($bank['order_mobile'] == 'QPAY') {
            $number = $id;
        }
        $this->canRefund = 1;
        if ($bankpayment['try_count'] >= 1) {
            $this->canRefund = 0;
        }
        $chargeResponse = LogTools::getLogLoyaltyApiResponse($id, $number);
        if (isset($chargeResponse) && $chargeResponse != null) {
            $chargeJsonResponse = json_decode($chargeResponse['response_xml'], TRUE);
            if (isset($chargeJsonResponse['items'][0]['refundOverRepayment'])) {
                switch ($chargeJsonResponse['items'][0]['refundOverRepayment']) {
                    case 'NO_REFUND':
                        $chargeJsonResponse['info'] = "Амжилттай. Хэрэглэгч зээлтэй байсан, ямар нэг илүү төлөлт хийгдээгүй учир хэрэглэгчид буцаан олголт хийхгүй.";
                        break;
                    case 'REFUND':
                        $chargeJsonResponse['info'] = "Амжилттай. Хэрэглэгч зээлтэй байсан, илүү төлөлт хийгдсэн учир буцаалт хийнэ.";
                        break;
                    case 'REFUND_NO_LOAN':
                        $chargeJsonResponse['info'] = "Амжилттай. Хэрэглэгч зээлгүй байсан тул буцаан олголтыг хийнэ.";
                        break;
                    case 'REFUND_NO_REGISTRATION':
                        $chargeJsonResponse['info'] = "Амжилттай. Хэрэглэгч бүртгэлгүй байсан тул буцаан олголтыг хийнэ.";
                        break;
                    case 'REFUND_NO_CONTRACT':
                        $chargeJsonResponse['info'] = "Амжилттай. Хэрэглэгч гэрээ байгуулаагүй эсвэл дууссан байсан тул буцаан олголтыг хийнэ.";
                        break;
                    default:
                        $chargeJsonResponse['info'] = "Амжилттай.";
                        break;
                }
            }
            $this->chargeResponse = $chargeJsonResponse;
        } else {
            $chargeJsonResponse['info'] = $bankpayment['status_comment'];
            $this->chargeResponse = $chargeJsonResponse;
        }
    }

    /**
     * Дахин цэнэглэх
     * 
     * @param sfWebRequest $request
     */
    public function executeRecharge(sfWebRequest $request)
    {
        if ($request->isMethod('POST')) {
            $chargeNumber = $request->getParameter('number');
            $bankpaymentId = $request->getParameter('id');

            $bankpayment = BankpaymentTable::retrieveByPK($bankpaymentId);

            if ($bankpayment['type'] == 10) {
                $txnDesc = preg_replace("/\([0-9]{8}\)/", "", $chargeNumber);
                preg_match_all("/([9][954][0-9]{6})|(85[0-9]{6})/", $txnDesc, $matches);
                if (count($matches[0])) {
                    $loanCheck = LoyaltyCharge::checkLoan($chargeNumber);
                    if ($loanCheck['Code'] !== 0) {
                        $this->getUser()->setFlash('error', 'Уучлаарай, цэнэглэх боломжгүй! тухайн дугаараар зээлийн үлдэгдэл байхгүй байна');
                        return $this->redirect($request->getReferer());
                    }
                }
            }
            $result = CandyLoanCore::recharge($request->getParameter('bank_order_id'), $chargeNumber, $bankpaymentId);
            $this->getUser()->setFlash('info', '[' . $result['order_id'] . '] төлөлтийг дахин дуудлаа');
        }
        return $this->redirect($request->getReferer());
    }

    /**
     * Candy remain by ISDN
     * 
     * @param sfWebRequest $request
     */
    public function executeRemainShow(sfWebRequest $request)
    {
        $isdn = $request->getParameter('isdn');
        $this->isdn = $isdn;
        $candyResult = LoyaltyCharge::lapiGetCustomer($isdn);
        $candyLoanResult = LoyaltyCharge::checkLoan($isdn);

        if ($candyLoanResult['Code'] == 0) {
//            print_r($candyLoanResult);die();
            $candyLoan = $candyLoanResult['Result'];
            $this->candyLoan = $candyLoan;
        }
        if ($candyResult) {
            $this->candyHtml = 'Мэдээлэл олдсонгүй';
            if ($candyResult['HttpCode'] != 200) {
                if ($candyResult['HttpCode'] == 403) {
                    $option = LoyaltyCore::getOption($this->phoneNumber);
                    if ($option == 'SHARED') {
                        $this->candyHtml = '<span class="red text"><b>MANAGED</b></span>';
                    } else {
                        $this->candyHtml = '<span class="red text"><b>Байгууллагын бүртгэлгүй байна.</b></span>';
                    }
                }
                if ($candyResult['HttpCode'] == 404) {
                    $this->candyHtml = '<span class="red text"><b>Хэрэглэгч бүртгэлгүй байна.</b></span>';
                }
            } else {
                $row = $candyResult['Result'];
                if (isset($row['balance'])) {
                    $this->candyHtml = '<b class="green">Үлдэгдэл: ' . $row['balance'] . '</b>';
                } else {
                    $this->candyHtml = $candyResult['Result'];
                }
            }
        }
        $this->accountType = 1;
    }

}

