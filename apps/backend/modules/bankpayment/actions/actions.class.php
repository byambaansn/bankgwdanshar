<?php

/**
 * bankpayment actions.
 *
 * @package    sf_sandbox
 * @subpackage bankpayment
 * @author     Belbayar
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class bankpaymentActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'bankpayment');
    }

    /**
     * GSM төлбөр 
     * 
     * @param sfWebRequest $request
     */
    public function executeCallPayment(sfWebRequest $request)
    {
        set_time_limit(0);
        ini_set("memory_limit", "1024M");
        $this->getRequest()->setParameter('sub_tab', 'call_payment');
//        $this->getRequest()->setParameter('account_type', BankAccountTable::TYPE_CALLPAYMET);
        $this->dateType = $request->getParameter('date_type', 1);
        $this->dateFrom = (AppTools::isDate($request->getParameter('dateFrom', date('Y-m-d')))) ? $request->getParameter('dateFrom', date('Y-m-d')) : date('Y-m-d');
        $this->dateTo = (AppTools::isDate($request->getParameter('dateTo', date('Y-m-d')))) ? $request->getParameter('dateTo', date('Y-m-d')) : date('Y-m-d');

        $this->bank = (int) $request->getParameter('bank');
        if ($request->getParameter('staff')) {
            $this->staff = mysql_escape_string($request->getParameter('staff'));
            //echo $request->getParameter('staff') ;die();
        }
        $this->staff = mysql_escape_string($request->getParameter('staff'));
        $this->keyword = mysql_escape_string($request->getParameter('keyword'));
        $this->type = BankpaymentTable::TYPE_CALL_PAYMENT;
        $status = $request->getParameter('status', 0);
        if (!$status) {
            $status = BankpaymentTable::getStatusFailed();
        }
        
        $user = sfContext::getInstance()->getUser();
        $credentials = $user->getCredentials();
        
        $this->refundCredential = 0;
        if (in_array('bankpayment_refund', $credentials)) {
            $this->refundCredential = 1;
        }

        $this->rows = BankpaymentTable::getList($this->dateType, $this->dateFrom, $this->dateTo, $this->type, $this->bank, $this->keyword, $status, $this->staff);
        $urlParams = array();
        $urlParams[] = 'dateFrom=' . $this->dateFrom;
        $urlParams[] = 'dateTo=' . $this->dateTo;
        $urlParams[] = 'chargedMobile=' . $this->chargedMobile;
        $urlParams[] = 'orderedMobile=' . $this->orderedMobile;
        $urlParams[] = 'orderId=' . $this->orderId;
        $urlParams[] = 'sta=' . $this->sta;
        $urlParams[] = 'staff=' . $this->staff;
        $this->urlParams = join('&', $urlParams);
        $this->status = $status;

        if ($request->getParameter('excel')) {
            set_time_limit(0);
            ini_set("memory_limit", "1024M");

            $filename = 'callpayment';

            $data = "БАНК;";
            $data .= "ДАНСНЫ ДУГААР;";
            $data .= "БАНК ОГНОО;";
            $data .= "ГҮЙЛГЭЭНИЙ УТГА;";
            $data .= "Төлөлтийн ДҮН ;";
            $data .= "Төлөв;";
            $data .= "Төлөлтийн хариу;";
            $data .= "Утасны дугаар;";
            $data .= "Гэрээний дугаар;";
            $data .= "Гэрээний үлдэгдэл;";
            $data .= "Гэрээний нэр;";
            $data .= "Гэрээний цикл;";
            $data .= "Ажилтан;";
            $data .= "Төлөлтийн огноо;";
            $data .= "Огноо;";
            $data .= "Буцаалтын төрөл;";
            $data .= "Хувилсан гүйлгээ;";
            $data.="\n";
            foreach ($this->rows as $row) {
                $statusName = BankpaymentTable::getStatusName($row['status'], false);
                $data.='"' . $row['bank_name'] . '";';
                $data.='"' . $row['bank_account'] . '";';
                $data.='"' . $row['order_date'] . '";';
                $data.='"' . $row['order_p'] . '";';
                $data.='"' . $row['order_amount'] . '";';
                $data.='"' . $statusName . '(' . $row['try_count'] . ')' . '";';
                $data.='"' . $row['status_comment'] . '";';
                $data.='"' . $row['number'] . '";';
                $data.='"' . $row['contract_number'] . '";';
                $data.='"' . $row['contract_amount'] . '";';
                $data.='"' . $row['contract_name'] . '";';
                $data.='"' . $row['bill_cycle'] . '";';
                $data.='"' . $row['username'] . '";';
                $data.='"' . $row['updated_at'] . '";';
                $data.='"' . $row['created_at'] . '";';
                $data.='"' . $row['pay_type'] . '";';
                $data.='"' . $row['copy_inv'] . '";';
                $data.="\n";
            }

            AppTools::ExportCsv($data, $filename, false);
            die();
        }

        return sfView::SUCCESS;
    }

    /**
     * Мобинэт төлбөр 
     * 
     * @param sfWebRequest $request
     */
    public function executeMobinetPayment(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'mobinetpayment');
        $this->dateType = $request->getParameter('date_type', 1);
        $this->dateFrom = (AppTools::isDate($request->getParameter('dateFrom', date('Y-m-d')))) ? $request->getParameter('dateFrom', date('Y-m-d')) : date('Y-m-d');
        $this->dateTo = (AppTools::isDate($request->getParameter('dateTo', date('Y-m-d')))) ? $request->getParameter('dateTo', date('Y-m-d')) : date('Y-m-d');

        $this->bank = (int) $request->getParameter('bank');
        $this->staff = mysql_escape_string($request->getParameter('staff'));
        $this->keyword = mysql_escape_string($request->getParameter('keyword'));
        $this->type = BankpaymentTable::TYPE_MOBINET;
        $status = $request->getParameter('status', 0);
        if (!$status) {
            $status = BankpaymentTable::getStatusFailed();
        }
        
        $user = sfContext::getInstance()->getUser();
        $credentials = $user->getCredentials();
        
        $this->refundCredential = 0;
        if (in_array('bankpayment_refund', $credentials)) {
            $this->refundCredential = 1;
        }
        
        $this->rows = BankpaymentTable::getList($this->dateType, $this->dateFrom, $this->dateTo, $this->type, $this->bank, $this->keyword, $status, $this->staff);
        if ($request->getParameter('excel')) {
            set_time_limit(0);
            ini_set("memory_limit", "1024M");

            $filename = 'callpayment';

            $data = "БАНК;";
            $data .= "ДАНСНЫ ДУГААР;";
            $data .= "БАНК ОГНОО;";
            $data .= "ГҮЙЛГЭЭНИЙ УТГА;";
            $data .= "Төлөлтийн ДҮН ;";
            $data .= "Төлөв;";
            $data .= "Төлөлтийн хариу;";
            $data .= "Утасны дугаар;";
            $data .= "Гэрээний дугаар;";
            $data .= "Гэрээний үлдэгдэл;";
            $data .= "Гэрээний нэр;";
            $data .= "Гэрээний цикл;";
            $data .= "Ажилтан;";
            $data .= "Төлөлтийн огноо;";
            $data .= "Огноо;";
            $data .= "Буцаалтын төрөл;";
            $data .= "Хувилсан гүйлгээ;";
            $data.="\n";
            foreach ($this->rows as $row) {
                $statusName = BankpaymentTable::getStatusName($row['status'], false);
                $data.='"' . $row['bank_name'] . '";';
                $data.='"' . $row['bank_account'] . '";';
                $data.='"' . $row['order_date'] . '";';
                $data.='"' . $row['order_p'] . '";';
                $data.='"' . $row['order_amount'] . '";';
                $data.='"' . $statusName . '(' . $row['try_count'] . ')' . '";';
                $data.='"' . $row['status_comment'] . '";';
                $data.='"' . $row['number'] . '";';
                $data.='"' . $row['contract_number'] . '";';
                $data.='"' . $row['contract_amount'] . '";';
                $data.='"' . $row['contract_name'] . '";';
                $data.='"' . $row['bill_cycle'] . '";';
                $data.='"' . $row['username'] . '";';
                $data.='"' . $row['updated_at'] . '";';
                $data.='"' . $row['created_at'] . '";';
                $data.='"' . $row['pay_type'] . '";';
                $data.='"' . $row['copy_inv'] . '";';
                $data.="\n";
            }

            AppTools::ExportCsv($data, $filename, false);
            die();
        }
        $urlParams = array();
        $urlParams[] = 'dateFrom=' . $this->dateFrom;
        $urlParams[] = 'dateTo=' . $this->dateTo;
        $urlParams[] = 'chargedMobile=' . $this->chargedMobile;
        $urlParams[] = 'orderedMobile=' . $this->orderedMobile;
        $urlParams[] = 'orderId=' . $this->orderId;
        $urlParams[] = 'sta=' . $this->sta;
        $urlParams[] = 'staff=' . $this->staff;
        $this->urlParams = join('&', $urlParams);
        $this->status = $status;

        return sfView::SUCCESS;
    }

    /**
     * Төлбөр оруулах
     * 
     * @param sfWebRequest $request
     */
    public function executeDoPayment(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'mobinetpayment');
        $transactions = $request->getParameter('transaction', 1);
        $cancel = $request->getParameter('cancel', 0);
        $billInfo = $request->getParameter('billInfo', 0);
        $message = " төлөлтийг дахин дуудлаа.";
        $success = 0;
        foreach ($transactions as $transaction) {
            $bankpayment = BankpaymentTable::retrieveByPK($transaction);
            if ($bankpayment) {
                if ($cancel) {
                    if (!in_array($bankpayment->getStatus(), array(BankpaymentTable::STAT_NEW, BankpaymentTable::STAT_PROCESS, BankpaymentTable::STAT_SUCCESS, BankpaymentTable::STAT_REFUND, BankpaymentTable::STAT_SPLITED))) {
                        $bankpayment->setStatus(BankpaymentTable::STAT_IMPOSSIBLE);
                        $bankpayment->setUsername($this->getUser()->getUsername());
                        $bankpayment->save();
                        LogTools::setLogBankpayment($bankpayment);
                    }
                    $message = " боломжгүй төлөв орууллаа.";
                } elseif ($billInfo) {
                    if (in_array($bankpayment->getStatus(), array(BankpaymentTable::STAT_FAILED_BILL_INFO))) {
                        $bankOrderId = $bankpayment->getBankOrderId();
                        $vendor = $bankpayment->getVendorId();
                        $bankTable = BankpaymentTable::getBankTransaction($vendor, $bankOrderId);
                        if ($bankOrderId && $bankTable) {
                            $bankpayment->delete();
                            $bankTable->setStatus(1);
                            $bankTable->save();
                        }
                    }
                    $message = " биллийн мэдээллийг дахин дуудлаа.";
                } else {
                    BankpaymentTable::processPayment($bankpayment);
                    $bankpayment->setUsername($this->getUser()->getUsername());
                    $bankpayment->save();
                    LogTools::setLogBankpayment($bankpayment);
                }
                $success++;
                unset($bankpayment);
            }
            unset($bankpayment);
        }

        if ($success) {
            $this->getUser()->setFlash('success', "$success $message");
        }

        $this->redirect($request->getReferer());
    }

    /**
     * Test 
     * 
     * @param sfWebRequest $request
     */
    public function executeUpdate(sfWebRequest $request)
    {
        $id = $request->getParameter('id', 0);
        $contract = trim($request->getParameter('contract_number', 0));

        $bankpayment = BankpaymentTable::retrieveByPK($id);
        $this->forward404Unless($bankpayment);
        if (BankpaymentTable::getChildCount($id)) {
            return $this->renderText('<div class="error message">Хувилсан гүйлгээг засах боломжгүй');
        }
        if (!in_array($bankpayment->getStatus(), array(BankpaymentTable::STAT_BANKPAYMENT_AMOUNT, BankpaymentTable::STAT_BANKPAYMENT_TRANS_VALUE, BankpaymentTable::STAT_FAILED_CHARGE, BankpaymentTable::STAT_FAILED_BILL_INFO))) {
            $message = '<div class="error message">Энэ гүйлгээг засах боломжгүй. Зөвхөн "' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_CHARGE) . ',' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_BILL_INFO) . '" төлөвтэй гүйлгээ засах боломжтой.';
            return $this->renderText($message);
        }
        if (BlockDateTable::checkBlock(date('Y-m-d', strtotime($bankpayment['updated_at'])))) {
            $block = BlockDateTable::getByType();
            $message = '<div class="warning message">' . $block['block_date'] . ' -ны өдрөөр хаалт хийсэн тул энэ гүйлгээг засах боломжгүй.';
            return $this->renderText($message);
        }
            
        if ($request->isMethod('post')) {
            try {
                $status = BankpaymentTable::STAT_NEW;

                $trans = array();
                $trans['old_bankpayment_id'] = $bankpayment['id'];
                $trans['bankpayment_id'] = $bankpayment['id'];
                $trans['user_name'] = $this->getUser()->getUsername();
                $trans['type'] = 'EDIT';
                $trans['refund_desc'] = 'Гэрээний дугаар засав.';
                $trans['refund_type'] = 'CONTRACT';
                $trans['old_value'] = $bankpayment['contract_number'];
                $trans['new_value'] = $contract;
                
                $bankpayment->setUpdatedUserId($this->getUser()->getId());
                $bankpayment->setUsername($this->getUser()->getUsername());
                $bankpayment->setUpdatedAt(date('Y-m-d H:i:s'));
                $bill = PostGateway::getBillInfo(0, $contract);
                if ($bill && $bill['Code'] === "0") {
                    $contractNumber = $bill['AccountNo'];
                    $billСycle = $bill['BillCycleCode'];
                    $contractAmount = doubleval($bill['CurrentBalance']);
                    $accountInfo = PostGateway::getAccountInfo($contractNumber);
                    $contractName = $accountInfo['AccountName'];
                    $bankTransaction = BankpaymentTable::getBankTransaction($bankpayment->getVendorId(), $bankpayment->getBankOrderId());
                    if (($contractAmount - 1500) <= $bankTransaction['order_amount'] && $bankTransaction['order_amount'] <= ($contractAmount + 1500)) {
                        $status = BankpaymentTable::STAT_NEW;
                    } else {
                        $status = BankpaymentTable::STAT_BANKPAYMENT_AMOUNT;
                    }
                } else {
//                    $status = BankpaymentTable::STAT_FAILED_BILL_INFO;
                    $this->getUser()->setFlash('error', 'Амжилтгүй. '.$contract.' гэрээний дугаараар гэрээ олдсонгүй');
                    $this->redirect($request->getReferer());
                }
                
                $trans['payment_type_id'] = PaymentTypeTable::getTypeByBillCycle($bill['BillCycleCode']);
                
                $bankpayment->setContractNumber($contractNumber);
                $bankpayment->setStatus($status);
                $bankpayment->setBillCycle($billСycle);
                $bankpayment->setContractName($contractName);
                $bankpayment->setContractAmount($contractAmount);
                if ($bankpayment->getType() == BankpaymentTable::TYPE_TOPUP) {
                    $bankpayment->setType(BankpaymentTable::TYPE_CALL_PAYMENT);
                }
                $bankpayment->save();
                LogTools::setLogBankpayment($bankpayment);
                BankpaymentVatRefundTable::insert($trans);
                $this->getUser()->setFlash('success', "Гэрээний дугаарыг амжилттай заслаа.");
            } catch (Exception $exc) {
                $this->getUser()->setFlash('error', "Гэрээний дугаар засах хүсэлт амжилтгүй.");
            }
            $this->redirect($request->getReferer());
        }
        $this->types = PaymentTypeTable::getForSelect();
        $this->transaction = BankpaymentTable::getBankTransaction($bankpayment['vendor_id'], $bankpayment['bank_order_id']);
        $this->bankpayment = $bankpayment;
    }
    
    /**
     * Төлөлт болгох 
     * 
     * @param sfWebRequest $request
     */
    public function executeMakePayment(sfWebRequest $request)
    {
        try {
            $id = $request->getParameter('id', 0);
            $payment = $request->getParameter('payment', 0);
            $info = array();
            $bankpayment = BankpaymentTable::retrieveByPK($id);
            $transaction = BankpaymentTable::getBankTransaction($bankpayment['vendor_id'], $bankpayment['bank_order_id']);
            
            if (!in_array($bankpayment->getStatus(), array(BankpaymentTable::STAT_BANKPAYMENT_AMOUNT, BankpaymentTable::STAT_BANKPAYMENT_TRANS_VALUE, BankpaymentTable::STAT_FAILED_CHARGE, BankpaymentTable::STAT_FAILED_BILL_INFO))) {
                $message = 'Энэ гүйлгээг төлөлт болгох боломжгүй. Зөвхөн "' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_CHARGE) . ',' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_BILL_INFO) . '" төлөвтэй гүйлгээг төлөлт болгох боломжтой.';
                $info = array('code' => 2, 'message' => $message);
                return $this->renderText(json_encode($info));
            }
            
            if (BlockDateTable::checkBlock(date('Y-m-d', strtotime($transaction['order_date']))) || BlockDateTable::checkBlock(date('Y-m-d', strtotime($bankpayment['updated_at'])))) {
                $block = BlockDateTable::getByType();
                $message = $block['block_date'] . ' -ны өдрөөр хаалт хийсэн тул энэ гүйлгээг төлөлт болгох боломжгүй.';
                $info = array('code' => 2, 'message' => $message);
                return $this->renderText(json_encode($info));
            }
            
            $isChild = intval($bankpayment['parent_id']) > 0;
            
            if ($isChild) {
                $tran = TransactionTable::retrieveByBankAndOrderId(BankTable::getBankAndVendorMap($bankpayment['vendor_id']), $transaction['order_id'], $transaction['order_type'], $transaction['order_amount'], $transaction['order_date']);
                if ($tran) {
                    $checkPayment = TransactionPaymentTable::checkPaymentByType($tran['id'], $payment);
                    if ($checkPayment) {
                        $message = 'Энэ гүйлгээг төлөлт болгох боломжгүй. Тухайн салбараар төлөлт оруулсан байна!!! "';
                        $info = array('code' => 2, 'message' => $message);
                        return $this->renderText(json_encode($info));
                    }
                }
            }
            
            $paymentOld = PaymentTable::getTypeId(BankTable::getBankAndVendorMap($bankpayment['vendor_id']), $transaction['order_id'], $transaction['order_date'], $transaction['order_type'], $transaction['order_amount']);
            
            $trans = array();
            $trans['old_bankpayment_id'] = $bankpayment['id'];
            $trans['bankpayment_id'] = $bankpayment['id'];
            $trans['user_name'] = $this->getUser()->getUsername();
            $trans['type'] = 'EDIT';
            $trans['refund_desc'] = 'Төлөлт болгов.';
            $trans['refund_type'] = 'PAYMENTTYPE';
            $trans['old_value'] = $paymentOld['type_id'];
            $trans['new_value'] = $payment;
            $trans['payment_type_id'] = $payment;
            
            $result = TransactionTable::setAssignmentMain($payment, BankTable::getBankAndVendorMap($bankpayment['vendor_id']), $transaction['bank_account'], $transaction['order_id'], $transaction['order_date'], $transaction['order_p'], $transaction['order_type'], $transaction['order_amount'], $transaction['order_s'], "BANKPAYMENT", $isChild, ($isChild ? $bankpayment['paid_amount'] : 0));
            
            if ($result) {
                BankpaymentTable::updateStatus($id, BankpaymentTable::STAT_IMPOSSIBLE, 'Боломжгүй', $this->getUser()->getId(), $this->getUser()->getUsername());
                BankpaymentVatRefundTable::insert($trans);
                $this->getUser()->setFlash('success', "Амжилттай төлөлт болголоо.");
                $info = array('code' => 1);
                return $this->renderText(json_encode($info));
            }
            else {
                $info = array('code' => 0, 'message' => 'Төлөлт болгох боломжгүй байна.');
                return $this->renderText(json_encode($info));
            }
        } catch (Exception $exc) {
            $info = array('code' => 0, 'message' => 'Төлөлт болгох боломжгүй байна.'.$exc->getMessage());
            return $this->renderText(json_encode($info));
        }
        
    }

    /**
     * copy 
     * 
     * @param sfWebRequest $request
     */
    public function executeCopy(sfWebRequest $request)
    {
        $bankpayment = BankpaymentTable::retrieveByPK($request->getParameter('id'));
        $transaction = BankpaymentTable::getBankTransaction($bankpayment['vendor_id'], $bankpayment['bank_order_id']);
        $balance = $request->getParameter('bal');
        if (!$bankpayment) {
            $this->getUser()->setFlash('error', 'Гүйлгээ одсонгүй.'.$request->getParameter('amount[0]'));
            $this->redirect($request->getReferer());
        }
        
        if (!in_array($bankpayment->getStatus(), array(BankpaymentTable::STAT_BANKPAYMENT_AMOUNT, BankpaymentTable::STAT_BANKPAYMENT_TRANS_VALUE, BankpaymentTable::STAT_FAILED_BILL_INFO, BankpaymentTable::STAT_FAILED_CHARGE))) {
            return $this->renderText('<div class="error message">Энэ гүйлгээг хуваах боломжгүй. Зөвхөн ' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_BANKPAYMENT_AMOUNT) . ',' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_BANKPAYMENT_TRANS_VALUE) . ',' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_BILL_INFO) . ',' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_CHARGE). ' төлөвтэй гүйлгээ хувилах боломжтой.</div>');
        }
        
        if (BlockDateTable::checkBlock(date('Y-m-d', strtotime($transaction['order_date']))) || BlockDateTable::checkBlock(date('Y-m-d', strtotime($bankpayment['updated_at'])))) {
            $block = BlockDateTable::getByType();
            $message = '<div class="warning message">' . $block['block_date'] . ' -ны өдрөөр хаалт хийсэн тул энэ гүйлгээг засах боломжгүй.';
            return $this->renderText($message);
        }

        if ($request->isMethod('POST')) {
            $rowCount = $request->getParameter('rowCount');
            $param = array();
            for ($index = 0; $index <= $rowCount; $index++) {
                $param['checkBranch'.$index] = $request->getParameter('checkBranch'.$index);
                $param['amount'.$index] = $request->getParameter('amount'.$index);
                $param['contractNum'.$index] = $request->getParameter('contractNum'.$index);
                $param['contNumber'.$index] = $request->getParameter('contNumber'.$index,0);
                $param['payment'.$index] = $request->getParameter('payment'.$index);
            }
            
            $payment = PaymentTable::getTypeId(BankTable::getBankAndVendorMap($bankpayment['vendor_id']), $transaction['order_id'], $transaction['order_date'], $transaction['order_type'], $transaction['order_amount']);
            $payment_type_id = 0;
            
            if ($payment) {
                $payment_type_id = $payment['type_id'];
            }
            
            $this->copyPayment($bankpayment, $transaction, $rowCount, $balance, $param, $payment_type_id, 'SPLIT');
            $bankpayment->setStatus(BankpaymentTable::STAT_SPLITED);
            $bankpayment->setStatusComment('Хуваагдсан гүйлгээ');
            $bankpayment->setUsername($this->getUser()->getUsername());
            $bankpayment->save();
            LogTools::setLogBankpayment($bankpayment);
            $this->redirect($request->getReferer());
        }
        
        $this->types = PaymentTypeTable::getForSelect();
        $this->transaction = $transaction;
        $this->bankpayment = $bankpayment;
    }

    /**
     * Улуснэт төлбөр 
     * 
     * @param sfWebRequest $request
     */
    public function executeUlusnet(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'ulusnetpayment');
        $this->dateType = $request->getParameter('date_type', 1);
        $this->dateFrom = (AppTools::isDate($request->getParameter('dateFrom', date('Y-m-d')))) ? $request->getParameter('dateFrom', date('Y-m-d')) : date('Y-m-d');
        $this->dateTo = (AppTools::isDate($request->getParameter('dateTo', date('Y-m-d')))) ? $request->getParameter('dateTo', date('Y-m-d')) : date('Y-m-d');

        $status = $request->getParameter('status', 0);
        if (!$status) {
            $status = BankpaymentTable::getStatusFailed();
        }
        $this->type = BankpaymentTable::TYPE_ULUSNET;
        $this->bank = (int) $request->getParameter('bank');
        $this->staff = mysql_escape_string($request->getParameter('staff'));
        $this->keyword = mysql_escape_string($request->getParameter('keyword'));

        $this->rows = BankpaymentTable::getList($this->dateType, $this->dateFrom, $this->dateTo, $this->type, $this->bank, $this->keyword, $this->status, $this->staff);

        $urlParams = array();
        $urlParams[] = 'dateFrom=' . $this->dateFrom;
        $urlParams[] = 'dateTo=' . $this->dateTo;
        $urlParams[] = 'chargedMobile=' . $this->chargedMobile;
        $urlParams[] = 'orderedMobile=' . $this->orderedMobile;
        $urlParams[] = 'orderId=' . $this->orderId;
        $urlParams[] = 'sta=' . $this->sta;
        $urlParams[] = 'staff=' . $this->staff;
        $this->urlParams = join('&', $urlParams);
        $this->status = $status;

        return sfView::SUCCESS;
    }

    /**
     * Ulusnet Төлбөр оруулах
     * 
     * @param sfWebRequest $request
     */
    public function executeUlusnetPayment(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'ulusnetpayment');
        $transactions = $request->getParameter('transaction', 1);
        $cancel = $request->getParameter('cancel', 0);
        $message = " төлөлтийг дахин дуудлаа.";
        $success = 0;
        foreach ($transactions as $transaction) {
            $bankpayment = BankpaymentTable::retrieveByPK($transaction);
            if ($bankpayment) {
                if ($cancel) {
                    if (!in_array($bankpayment->getStatus(), array(BankpaymentTable::STAT_NEW, BankpaymentTable::STAT_PROCESS, BankpaymentTable::STAT_SUCCESS))) {
                        $bankpayment->setStatus(BankpaymentTable::STAT_IMPOSSIBLE);
                        $bankpayment->save();
                        LogTools::setLogBankpayment($bankpayment);
                    }
                    $message = " боломжгүй төлөв орууллаа.";
                } else {
                    BankpaymentTable::processPaymentUlusnet($bankpayment);
                }
                $success++;
                unset($bankpayment);
            }
            unset($bankpayment);
        }

        if ($success) {
            $this->getUser()->setFlash('success', "$success $message");
        }

        $this->redirect($request->getReferer());
    }

    /**
     * Ulusnet product 
     * 
     * @param sfWebRequest $request
     */
    public function executeUlusnetProduct(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'ulusnetconfig');

        $id = $request->getParameter('id', 0);

        $product = BankpaymentProductTable::retrieveByPK($id);

        if ($request->isMethod('POST')) {
            $name = $request->getParameter('name', 0);
            $code = $request->getParameter('code', 0);
            $price = $request->getParameter('price', 0);

            if (!$product) {
                $product = new BankpaymentProduct();
                $product->setCreatedAt(date("Y-m-d H:i:s"));
            }
            $product->setCode($code);
            $product->setName($name);
            $product->setPrice($price);
            $product->save();
            $this->getUser()->setFlash('success', 'Амжилттай хадгаллаа.');
            $this->redirect($request->getReferer());
        }
        if ($product) {
            $this->product = $product;
            $this->id = $id;
        }
        $this->rows = BankpaymentProductTable::getList();
    }

    /**
     * Test 
     * 
     * @param sfWebRequest $request
     */
    public function executeUlusnetUpdate(sfWebRequest $request)
    {
        $id = $request->getParameter('id', 0);
        $number = trim($request->getParameter('number', 0));

        $bankpayment = BankpaymentTable::retrieveByPK($id);
        $this->forward404Unless($bankpayment);
        if (BankpaymentTable::getChildCount($id)) {
            return $this->renderText("Хувилсан гүйлгээг засах боломжгүй");
        }
        if (!in_array($bankpayment->getStatus(), array(BankpaymentTable::STAT_BANKPAYMENT_AMOUNT, BankpaymentTable::STAT_BANKPAYMENT_TRANS_VALUE, BankpaymentTable::STAT_FAILED_CHARGE, BankpaymentTable::STAT_FAILED_BILL_INFO))) {
            $message = 'Энэ гүйлгээг засах боломжгүй. Зөвхөн "' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_CHARGE) . ',' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_BILL_INFO) . '" төлөвтэй гүйлгээ засах боломжтой.';
            return $this->renderText($message);
        }
        if (BlockDateTable::checkBlock(date('Y-m-d', strtotime($bankpayment['updated_at'])))) {
            $block = BlockDateTable::getByType();
            $message = '<div class="warning message">' . $block['block_date'] . ' -ны өдрөөр хаалт хийсэн тул энэ гүйлгээг засах боломжгүй.';
            return $this->renderText($message);
        }
        if ($request->isMethod('post')) {
            try {
                $status = BankpaymentTable::STAT_NEW;

                $bankpayment->setNumber($number);
                $bankpayment->setUpdatedUserId($this->getUser()->getId());
                $bankpayment->setUsername($this->getUser()->getUsername());
                $bankpayment->setUpdatedAt(date('Y-m-d H:i:s'));
                if ($number) {
                    $accountInfo = PostGateway::getPostPhoneInfo($number);
                    if ($accountInfo && $accountInfo['Code'] === "0") {
                        $contractNumber = $accountInfo['AccountNo'];
                        $contractName = $accountInfo['AccountName'];
                        $status = BankpaymentTable::STAT_NEW;
                    }
                }
                $bankpayment->setStatus($status);
                $bankpayment->setContractName($contractName);
                $bankpayment->setContractNumber($contractNumber);
                $bankpayment->save();
                LogTools::setLogBankpayment($bankpayment);
                $this->getUser()->setFlash('success', "Гэрээний дугаарыг амжилттай заслаа.");
            } catch (Exception $exc) {
                $this->getUser()->setFlash('error', "Гэрээний дугаар засах хүсэлт амжилтгүй.");
            }
            $this->redirect($request->getReferer());
        }
        $this->transaction = BankpaymentTable::getBankTransaction($bankpayment['vendor_id'], $bankpayment['bank_order_id']);
        $this->bankpayment = $bankpayment;
    }

    /**
     * Мобинэт төлбөр prepaid
     * 
     * @param sfWebRequest $request
     */
    public function executeMobinetPrepaid(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'mobinet_prepaid');
        $this->dateType = $request->getParameter('date_type', 1);
        $this->dateFrom = (AppTools::isDate($request->getParameter('dateFrom', date('Y-m-d')))) ? $request->getParameter('dateFrom', date('Y-m-d')) : date('Y-m-d');
        $this->dateTo = (AppTools::isDate($request->getParameter('dateTo', date('Y-m-d')))) ? $request->getParameter('dateTo', date('Y-m-d')) : date('Y-m-d');

        $status = $request->getParameter('status', 0);
        if (!$status) {
            $status = BankpaymentTable::getStatusFailed();
        }
        $this->type = BankpaymentTable::TYPE_MOBINET_PREPAID;
        $this->status = $status;
        $this->bank = (int) $request->getParameter('bank');
        $this->staff = mysql_escape_string($request->getParameter('staff'));
        $this->keyword = mysql_escape_string($request->getParameter('keyword'));
        $dateBegin = new DateTime($this->dateFrom);
        $dateEnd = new DateTime($this->dateTo);
        $interval = date_diff($dateBegin, $dateEnd);

        if ($interval->days > 31) {
            $this->getUser()->setFlash('error', 'Уучлаарай 1-н сараас дээш хугацаагаар шүүх боломжгүй!!!');
            $this->redirect($request->getReferer());
        }


        $this->rows = BankpaymentTable::getList($this->dateType, $this->dateFrom, $this->dateTo, $this->type, $this->bank, $this->keyword, $this->status, $this->staff);
        if ($request->getParameter('excel')) {
            set_time_limit(0);
            ini_set("memory_limit", "1024M");

            $filename = 'callpayment';

            $data = "БАНК;";
            $data .= "ДАНСНЫ ДУГААР;";
            $data .= "БАНК ОГНОО;";
            $data .= "ГҮЙЛГЭЭНИЙ УТГА;";
            $data .= "Төлөлтийн ДҮН ;";
            $data .= "Төлөв;";
            $data .= "Төлөлтийн хариу;";
            $data .= "Гэрээний дугаар;";
            $data .= "Гэрээний нэр;";
            $data .= "Username;";
            $data .= "Хурд;";
            $data .= "Bundle;";
            $data .= "Сунгасан сар;";
            $data .= "Ажилтан;";
            $data .= "Төлөлтийн огноо;";
            $data .= "Огноо;";
            $data.="\n";
            foreach ($this->rows as $row) {
                $statusName = BankpaymentTable::getStatusName($row['status'], false);
                $data.='"' . $row['bank_name'] . '";';
                $data.='"' . $row['bank_account'] . '";';
                $data.='"' . $row['order_date'] . '";';
                $data.='"' . $row['order_p'] . '";';
                $data.='"' . $row['order_amount'] . '";';
                $data.='"' . $statusName . '(' . $row['try_count'] . ')' . '";';
                $data.='"' . $row['status_comment'] . '";';
                $data.='"' . $row['contract_number'] . '";';
                $data.='"' . $row['contract_name'] . '";';
                $data.='"' . $row['number'] . '";';
                $data.='"' . $row['speed'] . '";';
                $data.='"' . $row['bundle_name'] . '";';
                $data.='"' . $row['extent_month'] . '";';
                $data.='"' . $row['username'] . '";';
                $data.='"' . $row['updated_at'] . '";';
                $data.='"' . $row['created_at'] . '";';
                $data.="\n";
            }

            AppTools::ExportCsv($data, $filename, false);
            die();
        }
        $urlParams = array();
        $urlParams[] = 'dateFrom=' . $this->dateFrom;
        $urlParams[] = 'dateTo=' . $this->dateTo;
        $urlParams[] = 'chargedMobile=' . $this->chargedMobile;
        $urlParams[] = 'orderedMobile=' . $this->orderedMobile;
        $urlParams[] = 'orderId=' . $this->orderId;
        $urlParams[] = 'sta=' . $this->sta;
        $urlParams[] = 'staff=' . $this->staff;
        $this->urlParams = join('&', $urlParams);
    }

    /**
     * MObinet Төлбөр оруулах
     * 
     * @param sfWebRequest $request
     */
    public function executeMobinetPrepaidPayment(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'mobinet_prepaid');
        $transactions = $request->getParameter('transaction', 1);
        $cancel = $request->getParameter('cancel', 0);
        $message = " төлөлтийг дахин дуудлаа.";
        $success = 0;
        foreach ($transactions as $transaction) {
            $bankpayment = BankpaymentTable::retrieveByPK($transaction);
            if ($bankpayment) {
                if ($cancel) {
                    if (!in_array($bankpayment->getStatus(), array(BankpaymentTable::STAT_NEW, BankpaymentTable::STAT_PROCESS, BankpaymentTable::STAT_SUCCESS))) {
                        $bankpayment->setStatus(BankpaymentTable::STAT_IMPOSSIBLE);
                        $bankpayment->save();
                        LogTools::setLogBankpayment($bankpayment);
                    }
                    $message = " боломжгүй төлөв орууллаа.";
                } else {
                    BankpaymentTable::processPaymentMobinet($bankpayment);
                }
                $success++;
                unset($bankpayment);
            }
            unset($bankpayment);
        }

        if ($success) {
            $this->getUser()->setFlash('success', "$success $message");
        }

        $this->redirect($request->getReferer());
    }

    /**
     * Test 
     * 
     * @param sfWebRequest $request
     */
    public function executeMobinetPrepaidUpdate(sfWebRequest $request)
    {
        $id = $request->getParameter('id', 0);
        $contract = trim($request->getParameter('contract_number', 0));

        $bankpayment = BankpaymentTable::retrieveByPK($id);
        $this->forward404Unless($bankpayment);
        if (BankpaymentTable::getChildCount($id)) {
            return $this->renderText("Хувилсан гүйлгээг засах боломжгүй");
        }
        if (!in_array($bankpayment->getStatus(), array(BankpaymentTable::STAT_BANKPAYMENT_AMOUNT, BankpaymentTable::STAT_BANKPAYMENT_TRANS_VALUE, BankpaymentTable::STAT_FAILED_CHARGE, BankpaymentTable::STAT_FAILED_BILL_INFO))) {
            $message = 'Энэ гүйлгээг засах боломжгүй. Зөвхөн "' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_CHARGE) . ',' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_BILL_INFO) . '" төлөвтэй гүйлгээ засах боломжтой.';
            return $this->renderText($message);
        }
        if (BlockDateTable::checkBlock(date('Y-m-d', strtotime($bankpayment['updated_at'])))) {
            $block = BlockDateTable::getByType();
            $message = '<div class="warning message">' . $block['block_date'] . ' -ны өдрөөр хаалт хийсэн тул энэ гүйлгээг засах боломжгүй.';
            return $this->renderText($message);
        }
        if ($request->isMethod('post')) {
            try {
                $status = $bankpayment->getStatus();

                $bankpayment->setContractNumber($contract);
                $bankpayment->setUpdatedUserId($this->getUser()->getId());
                $bankpayment->setUsername($this->getUser()->getUsername());
                $bankpayment->setUpdatedAt(date('Y-m-d H:i:s'));
                $accountInfo = MobinetGateway::contractInfo($contract);
                if (isset($accountInfo['id']) && $accountInfo['id']) {
                    $username = $accountInfo['username'];
                    $speed = $accountInfo['speed'];
                    $contractName = $accountInfo['lastname'] . ' ' . $accountInfo['firstname'];
                    $status = BankpaymentTable::STAT_NEW;
                    BankpaymentMobinetTable::insert($bankpayment->getId(), $speed);
                    $bankpayment->setStatus($status);
                    $bankpayment->setNumber($username);
                    $bankpayment->setContractName($contractName);
                    $bankpayment->save();
                    LogTools::setLogBankpayment($bankpayment);
                    $this->getUser()->setFlash('success', "Гэрээний дугаарыг амжилттай заслаа.");
                } else {
                    $this->getUser()->setFlash('error', "$contract  гэрээний дугаар дээр мэдээлэл олдсонгүй.");
                }
            } catch (Exception $exc) {
                $this->getUser()->setFlash('error', "Гэрээний дугаар засах хүсэлт амжилтгүй.");
            }
            $this->redirect($request->getReferer());
        }
        $this->transaction = BankpaymentTable::getBankTransaction($bankpayment['vendor_id'], $bankpayment['bank_order_id']);
        $this->bankpayment = $bankpayment;
    }

    /**
     * Topup цэнэглэлт, DATA sapc , WIFI Card
     * 
     * @param sfWebRequest $request
     */
    public function executeUssdCharge(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'ussd');
//        $this->getRequest()->setParameter('account_type', BankAccountTable::TYPE_CALLPAYMET);
        $this->dateType = $request->getParameter('date_type', 1);
        $this->ussd = $request->getParameter('ussd', 0);
        $this->dateFrom = (AppTools::isDate($request->getParameter('dateFrom', date('Y-m-d')))) ? $request->getParameter('dateFrom', date('Y-m-d')) : date('Y-m-d');
        $this->dateTo = (AppTools::isDate($request->getParameter('dateTo', date('Y-m-d')))) ? $request->getParameter('dateTo', date('Y-m-d')) : date('Y-m-d');

        $this->bank = (int) $request->getParameter('bank');
        if ($request->getParameter('staff')) {
            $this->staff = mysql_escape_string($request->getParameter('staff'));
            //echo $request->getParameter('staff') ;die();
        }
        $this->staff = mysql_escape_string($request->getParameter('staff'));
        $this->keyword = mysql_escape_string($request->getParameter('keyword'));
        $this->type = array(BankpaymentTable::TYPE_CALL_PAYMENT,BankpaymentTable::TYPE_TOPUP, BankpaymentTable::TYPE_SAPC, BankpaymentTable::TYPE_WIFI,);
        if ($this->ussd) {
            $this->type = $this->ussd;
        }

        $status = $request->getParameter('status', 0);
        if (!$status) {
            $status = BankpaymentTable::getStatusFailed();
        }

        $this->rows = BankpaymentTable::getList($this->dateType, $this->dateFrom, $this->dateTo, $this->type, $this->bank, $this->keyword, $status, $this->staff);
        $urlParams = array();
        $urlParams[] = 'dateFrom=' . $this->dateFrom;
        $urlParams[] = 'dateTo=' . $this->dateTo;
        $urlParams[] = 'chargedMobile=' . $this->chargedMobile;
        $urlParams[] = 'orderedMobile=' . $this->orderedMobile;
        $urlParams[] = 'orderId=' . $this->orderId;
        $urlParams[] = 'sta=' . $this->sta;
        $urlParams[] = 'staff=' . $this->staff;
        $this->urlParams = join('&', $urlParams);
        $this->status = $status;

        if ($request->getParameter('excel')) {
            set_time_limit(0);
            ini_set("memory_limit", "1024M");

            $filename = 'callpayment';

            $data = "БАНК;";
            $data .= "ДАНСНЫ ДУГААР;";
            $data .= "БАНК ОГНОО;";
            $data .= "ГҮЙЛГЭЭНИЙ УТГА;";
            $data .= "Төлөлтийн ДҮН ;";
            $data .= "Төлөв;";
            $data .= "Төлөлтийн хариу;";
            $data .= "Утасны дугаар;";
            $data .= "Гэрээний дугаар;";
            $data .= "Гэрээний нэр;";
            $data .= "Ажилтан;";
            $data .= "Төлөлтийн огноо;";
            $data .= "Огноо;";
            $data.="\n";
            foreach ($this->rows as $row) {
                $statusName = BankpaymentTable::getStatusName($row['status'], false);
                $data.='"' . $row['bank_name'] . '";';
                $data.='"' . $row['bank_account'] . '";';
                $data.='"' . $row['order_date'] . '";';
                $data.='"' . $row['order_p'] . '";';
                $data.='"' . $row['order_amount'] . '";';
                $data.='"' . $statusName . '(' . $row['try_count'] . ')' . '";';
                $data.='"' . $row['status_comment'] . '";';
                $data.='"' . $row['number'] . '";';
                $data.='"' . $row['contract_number'] . '";';
                $data.='"' . $row['contract_name'] . '";';
                $data.='"' . $row['username'] . '";';
                $data.='"' . $row['updated_at'] . '";';
                $data.='"' . $row['created_at'] . '";';
                $data.="\n";
            }

            AppTools::ExportCsv($data, $filename, false);
            die();
        }
    }


     /**
     * Topup цэнэглэлт
     * 
     * @param sfWebRequest $request
     */
    public function executeChargeData(sfWebRequest $request)
    {   
        if($request->isMethod('POST')){
          /*  @param String $number утасны дугаар
          * @param String $card profile code
          * @param integer $userId төлөх дүн
          * @param integer $chargerId төлөлт хийж буй огноо
          * @return array Цэнэгэлэлт орсон тухай*/
            $bankpayment = BankpaymentTable::retrieveByPK($id);
            $number = $request->getParameter('number');
            $profile = $request->getParameter('card', 0);
            $result= SapcGateway::chargeFreePackage($msisdn, $rofile, $logger, $bankName);
            if(isset($result)){
                $code==1;
            }else{$code==0;}
        }
    }
                  
         /**
     * Topup цэнэглэлт
     * 
     * @param sfWebRequest $request
     */
    public function executeChargeUnit(sfWebRequest $request)
    {   
        $bankpayment->setUpdatedUserId($this->getUser()->getId());
        $id = $request->getParameter('id', 0);
        $number= $request -> getParameter('number');
        $card= $request -> getParameter('card');
        $result=RtcgwGateway::chargeTopup($number, $card, "bankgw_khan1");
        if(isset($result)){
            $code=1;

            alert("1");
        }else {$code=0;
            alert("2");}
        
    
    return sfView::NONE;
    
}
    

    /**
     * Topup цэнэглэлт , DATA sapc , WIFI Card
     * 
     * @param sfWebRequest $request
     */
    public function executeUssdUpdate(sfWebRequest $request)
    {
            $id = $request->getParameter('id', 0);
            $number = $request->getParameter('number', 0);
            $cart = $request->getParameter('cart', 0);

        $bankpayment = BankpaymentTable::retrieveByPK($id);
        $this->forward404Unless($bankpayment);
        if (BankpaymentTable::getChildCount($id)) {
            return $this->renderText("Хувилсан гүйлгээг засах боломжгүй");
        }
        if (!in_array($bankpayment->getStatus(), array(BankpaymentTable::STAT_BANKPAYMENT_AMOUNT, BankpaymentTable::STAT_BANKPAYMENT_TRANS_VALUE, BankpaymentTable::STAT_FAILED_CHARGE, BankpaymentTable::STAT_FAILED_BILL_INFO))) {
            $message = 'Энэ гүйлгээг засах боломжгүй. Зөвхөн "' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_CHARGE) . ',' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_FAILED_BILL_INFO) . '" төлөвтэй гүйлгээ засах боломжтой.';
            return $this->renderText($message);
        }
        if (BlockDateTable::checkBlock(date('Y-m-d', strtotime($bankpayment['updated_at'])))) {
            $block = BlockDateTable::getByType();
            $message = '<div class="warning message">' . $block['block_date'] . ' -ны өдрөөр хаалт хийсэн тул энэ гүйлгээг засах боломжгүй.';
            return $this->renderText($message);
        }

        if ($request->isMethod('post')) {
            try {
                $status = BankpaymentTable::STAT_NEW;
                $trans = array();
                $trans['old_bankpayment_id'] = $bankpayment['id'];
                $trans['bankpayment_id'] = $bankpayment['id'];
                $trans['user_name'] = $this->getUser()->getUsername();
                $trans['type'] = 'EDIT';
            
                $bankTransaction = BankpaymentTable::getBankTransaction($bankpayment->getVendorId(), $bankpayment->getBankOrderId());
             
                $bankpayment->setUpdatedUserId($this->getUser()->getId());
                $bankpayment->setUsername($this->getUser()->getUsername());
                $bankpayment->setUpdatedAt(date('Y-m-d H:i:s'));
                $bankpayment->setStatus(BankpaymentTable::STAT_NEW);
                $bankpayment->setNumber($number);
                $bankpayment->save();
                LogTools::setLogBankpayment($bankpayment);
                $this->getUser()->setFlash('success', "Утасны дугаарыг амжилттай заслаа.");
            } catch (Exception $exc) {
                $this->getUser()->setFlash('error', "aldaa");
            }
            $this->redirect($request->getReferer());
        }
        $this->types = PaymentTypeTable::getForSelect();
        $this->transaction = BankpaymentTable::getBankTransaction($bankpayment['vendor_id'], $bankpayment['bank_order_id']);
        $this->bankpayment = $bankpayment;
        
    }

    public function executeReturn(sfWebRequest $request)
    {
        $bankpayment = BankpaymentTable::retrieveByPK($request->getParameter('id'));
        $transaction = BankpaymentTable::getBankTransaction($bankpayment['vendor_id'], $bankpayment['bank_order_id']);
        $this->forward404Unless($bankpayment);
        $user = sfContext::getInstance()->getUser();
        $credentials = $user->getCredentials();
        if (!$bankpayment) {
            $this->getUser()->setFlash('error', 'Гүйлгээ одсонгүй.');
            $this->redirect($request->getReferer());
        }
        
        if (!in_array($bankpayment->getStatus(), array(BankpaymentTable::STAT_SUCCESS, BankpaymentTable::STAT_IMPOSSIBLE)) || $bankpayment->getParentId() > 0) {
            return $this->renderText('<div class="error message">Энэ гүйлгээг буцаах боломжгүй. Зөвхөн ' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_SUCCESS) . ' болон ' . BankpaymentTable::getStatusName(BankpaymentTable::STAT_IMPOSSIBLE) . ' төлөвтэй, хуваалт хийгдээгүй гүйлгээг буцаах боломжтой.</div>');
        }
        
        if (BlockDateTable::checkBlock(date('Y-m-d', strtotime($transaction['order_date']))) || BlockDateTable::checkBlock(date('Y-m-d', strtotime($bankpayment['updated_at'])))) {
            $block = BlockDateTable::getByType();
            $message = '<div class="warning message">' . $block['block_date'] . ' -ны өдрөөр хаалт хийсэн тул энэ гүйлгээг буцаах боломжгүй.';
            return $this->renderText($message);
        }
        
        if (!in_array('bankpayment_refund', $credentials)) {
            $message = '<div class="warning message">Уучлаарай таньд буцаалт хийх эрх байхгүй байна.';
            return $this->renderText($message);
        }
        
        $start = date('Y-m-d H:i:s', strtotime(date('Y-m-1')." -1 month"));
        $end = date('Y-m-d');
        $end = date('Y-m-d H:i:s', strtotime($end. ' + 1 days'));
        $limit = 10; 
        $offset = 0;
//        $this->rows = BlockDateTable::getList();
//        $blockDate = $rows[0]['created_at'];
//        $paymentDate = $bankpayment['updated_at'];
//
//        $isReturnable = false;
//        $isUpdatedBlock = false;
//
//        $currentDate = date('Y-m-d H:i:s');
//        $currentMonth = (int) date('m');
//        $blockDateMonth = (int) date('m', strtotime($blockDate));
//        $paymentDateMonth = (int) date('m', strtotime($paymentDate));
//
//        if($currentMonth <= $blockDateMonth && $currentDate < $blockDate){
//            $isUpdatedBlock = true;
//        }
//        if((($paymentDate < $blockDate) && $isUpdatedBlock) || (!$isUpdatedBlock && ($paymentDate > $blockDate)) || $paymentDate > $blockDate){
//            $isReturnable = true;
//        }

        $this->returnBillList = [];
        if($bankpayment['contract_number'] && AppTools::isContractNumber($bankpayment['contract_number'])){
            $this->returnBillList = BasicVatSenderNew::getVatBillList($start, $end, $bankpayment['contract_number'], $user->getUsername(), $limit, $offset);   
        } 
        $this->isSuccess = $bankpayment['status'] == 3;
        
        if ($request->isMethod('POST')) {
            $returnType = $request->getParameter('returnType');
            $billId = $request->getParameter('returnBill');
            $desc = $request->getParameter('description');
        
            if (!$billId && $bankpayment['status'] == 3) {
                return $this->renderText('<div class="error message">Буцаах билл сонгоно уу.</div>');
            }
        
            $trans = array();
            $trans['old_bankpayment_id'] = $bankpayment['id'];
            $trans['user_name'] = $this->getUser()->getUsername();
            $trans['type'] = 'REFUND';
            $trans['refund_desc'] = $desc;

            $returnVat = array();
            if ($bankpayment['status'] == 3) {
//                $returnBill = DbClone4::getVatReturnBill($billId);
//                $returnVat = VatReturnBill::returnBill($bankpayment['number'] ? $bankpayment['number'] : $returnBill[0]['c_isdn'], $billId, date('Y-m-d H:i:s',strtotime($returnBill[0]['c_posresdate'])), 'BankPayment Refund', $returnBill[0]['c_amount'], $returnBill[0]['lotteryId']);
                $returnBill = BasicVatSenderNew::getVatReturnBillListFormatter($billId, $this->returnBillList);
                $returnVat = BasicVatSenderNew::returnVat($bankpayment['number'] ? $bankpayment['number'] : $returnBill['ebarimt']['isdnA'], $billId, date('Y-m-d H:i:s',strtotime($returnBill['ebarimt']['vatDate'])), 'BankPayment Refund', $returnBill['ebarimt']['amount'], $returnBill['ebarimt']['lottery'], $returnBill['ebarimt']['vat'], null, $user->getUsername()? $user->getUsername() : $bankpayment['number']);
                $trans['refund_bill_id'] = $billId;
            }
            else {
                $returnVat['success'] = true;
            }
            
            if ($returnVat['success']) {
                $amount = $transaction['order_amount'];
                $payment = PaymentTable::getTypeId(BankTable::getBankAndVendorMap($bankpayment['vendor_id']), $transaction['order_id'], $transaction['order_date'], $transaction['order_type'], $amount);

                $payment_type_id = 0;

                if ($payment) {
                    $payment_type_id = $payment['type_id'];
                }

                if ($returnType == 'notBill') {
                    $paymentType = $request->getParameter('paymentType');

                    $trans['bankpayment_id'] = $bankpayment['id'];
                    $trans['refund_type'] = 'PAYMENTTYPE';
                    $trans['old_value'] = $payment_type_id;
                    $trans['new_value'] = $paymentType;
                    $trans['payment_type_id'] = $paymentType;

                    PaymentTable::updateTypeId(BankTable::getBankAndVendorMap($bankpayment['vendor_id']), $transaction['order_id'], $transaction['order_date'], $transaction['order_type'], $amount, $paymentType);
                    BankpaymentVatRefundTable::insert($trans);
                    $bankpayment->setUsername($this->getUser()->getUsername());
                }
                else if ($returnType == 'notValidContract') {
                    $trans['refund_type'] = 'CONTRACT';
                    $trans['old_value'] = $bankpayment['contract_number'];
                    $trans['new_value'] = $request->getParameter('contract');

                    $bill = PostGateway::getBillInfo(0, $request->getParameter('contract'));

                    $new_pay_type = PaymentTypeTable::getTypeByBillCycle($bill['BillCycleCode']);

                    $insertData = new Bankpayment();
                    $insertData['child_num'] = 1;
                    $insertData['vendor_id'] = $bankpayment['vendor_id'];
                    $insertData['type'] = $bankpayment['type'];
                    $insertData['bank_order_id'] = $bankpayment['bank_order_id'];
                    $insertData['status'] = BankpaymentTable::STAT_NEW;
                    $insertData['status_comment'] = '';
                    $insertData['number'] = $bankpayment['number'];
                    $insertData['bill_cycle'] = $bill['BillCycleCode'];
                    $insertData['paid_amount'] = $bankpayment['paid_amount'];
                    $insertData['contract_amount'] = doubleval($bill['CurrentBalance']);
                    $insertData['contract_number'] = $request->getParameter('contract');
                    $accountInfo = PostGateway::getAccountInfo($request->getParameter('contract'));
                    $insertData['contract_name'] = $accountInfo['AccountName'];
                    $insertData['credit_control'] = $bankpayment['credit_control'];
                    $insertData['insurance_date'] = $bankpayment['insurance_date'];
                    $insertData['insurance_amount'] = $bankpayment['insurance_amount'];
                    $insertData['username'] = $this->getUser()->getUsername();
                    $trans['payment_type_id'] = $new_pay_type;
                    $newBankpayment = BankpaymentTable::insert($insertData);
                    if (!$newBankpayment) {
                        $this->getUser()->setFlash('error', 'Гэрээний дугаар солих амжилтгүй боллоо.');
                        $this->redirect($request->getReferer());
                    }
                    $trans['bankpayment_id'] = $newBankpayment['id'];
                    if ($new_pay_type != $payment_type_id) {
                        PaymentTable::updateTypeId(BankTable::getBankAndVendorMap($bankpayment['vendor_id']), $transaction['order_id'], $transaction['order_date'], $transaction['order_type'], $amount, $new_pay_type);
                    }
                    BankpaymentVatRefundTable::insert($trans);
                }
                else if ($returnType == 'copyContract') {
                    $rowCount = $request->getParameter('rowCount');
                    $balance = $request->getParameter('bal');
                    $param = array();
                    for ($index = 0; $index <= $rowCount; $index++) {
                        $param['checkBranch'.$index] = $request->getParameter('checkBranch'.$index);
                        $param['amount'.$index] = $request->getParameter('amount'.$index);
                        $param['contractNum'.$index] = $request->getParameter('contractNum'.$index,0);
                        $param['payment'.$index] = $request->getParameter('payment'.$index);
                        $param['contNumber'.$index] = $request->getParameter('contNumber'.$index,0);
                    }
                    $this->copyPayment($bankpayment, $transaction, $rowCount, $balance, $param, $payment_type_id, 'REFUND', $desc, $billId);
                }
                $bankpayment->setUsername($this->getUser()->getUsername());
                $bankpayment->setStatus(BankpaymentTable::STAT_REFUND);
                $bankpayment->setStatusComment('Буцаалт');
                $bankpayment->save();
                LogTools::setLogBankpayment($bankpayment);

                $this->getUser()->setFlash('success', "Амжилттай буцаалт хийгдлээ.");

            }
            else {
                $this->getUser()->setFlash('error', "Буцаалт амжилтгүй. ".$returnVat['message']);
            }
            $this->redirect($request->getReferer());
        }
        
//        $this->returnBillList = DbClone4::getVatReturnBillList($bankpayment['number'], $bankpayment['contract_number'], $bankpayment['updated_at']);
        $this->bankpayment = $bankpayment;
        $this->transaction = $transaction;
        $this->types = PaymentTypeTable::getForSelect();
    }
    
    public function copyPayment($bankpayment, $bankTransaction, $rowCount, $balance, $param, $old_type_id, $is_refund, $description = '', $billId = null)
    {            
        if (!$bankTransaction) {
            $this->getUser()->setFlash('error', 'Банкны гүйлгээний мэдээлэл олдсонгүй.');
            $this->redirect(sfContext::getInstance()->getRequest()->getReferer());
        }
        if (doubleval($balance) != 0) {
            $this->getUser()->setFlash('error', 'Амжилтгүй. Хуваах үнийн дүн буруу байна.');
            $this->redirect(sfContext::getInstance()->getRequest()->getReferer());
        }
        
        $bills = array();
        for ($index = 0; $index <= $rowCount; $index++) {
            $check = $param['checkBranch'.$index];
            $amt = $param['amount'.$index];
            if ($amt == 0 || $amt == "") {
                continue;
            }
            if ($check != 1 ) {
                $contract = $param['contractNum'.$index];
                $number = $param['contNumber'.$index];
                $postGw = PostGateway::getBillInfo($number, $contract);
                
                if ($postGw && $postGw['Code'] == "0") {
                    $bills[$index] = $postGw;
                }
                else {
                    $text = "";
                    if ($contract) {
                        $text = $contract.'гэрээний дугаараар';
                        if ($number) {
                            $text = $contract.' гэрээ болон '.$number.' утасны дугаараар';
                        }
                    }
                    else {
                        $text = $number.' утасны дугаараар';
                    }
                    $this->getUser()->setFlash('error', 'Амжилтгүй. '.$text.' гэрээ олдсонгүй');
                    $this->redirect(sfContext::getInstance()->getRequest()->getReferer());
                }
            }
            else {
                $paymentCode = $param['payment'.$index];
                if ($paymentCode == 0) {
                    $this->getUser()->setFlash('error', 'Амжилтгүй. Салбар сонгогдоогүй байна.');
                    $this->redirect(sfContext::getInstance()->getRequest()->getReferer());
                }
            }
        }

        $tran = TransactionTable::retrieveByBankAndOrderId(BankTable::getBankAndVendorMap($bankpayment['vendor_id']), $bankTransaction['order_id'], $bankTransaction['order_type'], $bankTransaction['order_amount'], $bankTransaction['order_date']);
        if ($tran) {
            PaymentTable::deletePayment($tran['id'], $this->getUser()->getId());
        }

        for ($index = 0; $index <= $rowCount; $index++) {
            $check = $param['checkBranch'.$index];
            $payment = $param['payment'.$index];
            $amount = $param['amount'.$index];

            if ($amount == 0 || $amount == "") {
                continue;
            }
            
            $refund = array();
            $refund['old_bankpayment_id'] = $bankpayment['id'];
            $refund['user_name'] = $this->getUser()->getUsername();
            $refund['refund_desc'] = $description;
            $refund['refund_bill_id'] = $billId;
            
            $childCount = (int) BankpaymentTable::getChildCount($bankpayment['id']);

            $values = array();
            $values['parent_id'] = $bankpayment['id'];
            $values['vendor_id'] = $bankpayment['vendor_id'];
            $values['type'] = $bankpayment['type'];
            $values['bank_order_id'] = $bankpayment['bank_order_id'];
            $values['child_num'] = ++$childCount;
            $values['paid_amount'] = $amount;
            $values['username'] = $this->getUser()->getUsername();

            if ($check != 1 ) {
                $bill = $bills[$index];
                
                $contractNumber = $bill['AccountNo'];
                $billСycle = $bill['BillCycleCode'];
                $contractAmount = doubleval($bill['CurrentBalance']);
                $accountInfo = PostGateway::getAccountInfo($contractNumber);
                $contractName = $accountInfo['AccountName'];

                $values['contract_number'] = $contractNumber;
                $values['contract_name'] = $contractName;
                $values['bill_cycle'] = $billСycle;
                $values['contract_amount'] = $contractAmount;
                $values['number'] = $bill['AccessNo'];
            }

            $paymentCode = BankpaymentTable::getPaymentCode($bankpayment['vendor_id'], $bankTransaction->getBankAccount());
            if ($paymentCode) {
                $values['bank_payment_code'] = $paymentCode;
                $values['status'] = $check == 1 && $payment > 0 ? BankpaymentTable::STAT_IMPOSSIBLE : BankpaymentTable::STAT_NEW;
                $values['status_comment'] = $check == 1 && $payment > 0 ? "Боломжгүй" : "";
                $bankpaymentChild = BankpaymentTable::insert($values);
                if ($bankpaymentChild) {
                    $refund['bankpayment_id'] = $bankpaymentChild['id'];
                    $refund['type'] = $is_refund;
                    $refund['refund_type'] = $check == 1 ? 'SPLITEDPAYMENT' : 'SPLITEDCONTRACT';
                    $refund['old_value'] = $check == 1 ? $old_type_id : $bankpayment['contract_number'];
                    $refund['new_value'] = $check == 1 ? $payment : $bills[$index]['AccountNo'];
                    $refund['payment_type_id'] = $check == 1 ? $payment : PaymentTypeTable::getTypeByBillCycle($bills[$index]['BillCycleCode']);
                    BankpaymentVatRefundTable::insert($refund);
//                    if ($check == 1 && $payment > 0) {
                        TransactionTable::setAssignmentCopy($refund['payment_type_id'], BankTable::getBankAndVendorMap($bankpayment['vendor_id']), $bankTransaction['bank_account'], 
                                $bankTransaction['order_id'], $bankTransaction['order_date'], $bankTransaction['order_p'], $bankTransaction['order_type'], 
                                $bankTransaction['order_amount'], $bankTransaction['order_s'], "BANKPAYMENT", true, $amount);
//                    }
                }
                $this->getUser()->setFlash('success', 'Амжилттай. Хуулбарийг төлөлтийн дараалалд орууллаа. ');
            } else {
                $this->getUser()->setFlash('error', 'Амжилтгүй. Төлбөрийн код олдсонгүй.');
                break;
            }
        }
    }
    
    public function executeBlockDate(sfWebRequest $request)
    {
        $block_type = $request->getParameter('blockType', BlockDateTable::BLOCK_PAYMENT);
        $block_date = $request->getParameter('block_date', date('Y-m-d'));
        $is_active = $request->getParameter('is_active', BlockDateTable::NOT_ACTIVE);
        $id = (int) $request->getParameter('id', 0);

        $this->blockTypes = BlockDateTable::getBlockTypes();
        $this->block_date = $block_date;
        $this->rows = BlockDateTable::getList();

        if ($request->isMethod('POST')) {
            $block = BlockDateTable::getByType($block_type);
            if ($block && !$id) {
                $this->getUser()->setFlash('warning', 'Тухайн хаалтын төрлийг зөвхөн нэг л удаа оруулах ёстой.');
                $this->redirect('@bankpayment_block_date');
            }
            BlockDateTable::update($id, $block_date, $block_type, $is_active, $this->getUser()->getId());
            $this->getUser()->setFlash('success', 'Амжилттай хадгаллаа.');
            $this->redirect('@bankpayment_block_date');
        }

        if ($id) {
            $row = BlockDateTable::getInstance()->find($id);
            if ($row) {
                $this->id = $id;
                $this->blockType = $row['block_type'];
                $this->block_date = $row['block_date'];
                $this->is_active = $row['is_active'];
            }
        }
    }
    
    public function executePaymentReport(sfWebRequest $request)
    {
        set_time_limit(0);
        ini_set("memory_limit", "1024M");
        $this->dateType = $request->getParameter('date_type', 1);
        $this->dateFrom = (AppTools::isDate($request->getParameter('dateFrom', date('Y-m-d')))) ? $request->getParameter('dateFrom', date('Y-m-d')) : date('Y-m-d');
        $this->dateTo = (AppTools::isDate($request->getParameter('dateTo', date('Y-m-d')))) ? $request->getParameter('dateTo', date('Y-m-d')) : date('Y-m-d');
        $this->bank = (int) $request->getParameter('bank');
        if ($request->getParameter('staff')) {
            $this->staff = mysql_escape_string($request->getParameter('staff'));
        }
        $this->staff = $request->getParameter('staff');
        $this->keyword = $request->getParameter('keyword');
        $this->accountNumber = $request->getParameter('bank_account');
        $this->type = $request->getParameter('payment_type', 4);
        $this->accountNumbers = BankAccountTable::getForSelectWithType(BankTable::getBankAndVendorMap($this->bank));
        $status = $request->getParameter('status', 0);
        if (!$status) {
            $status = BankpaymentTable::getStatusFailed();
        }

        if ($this->staff == 0) {
            $this->staff = '';
        }
        
        $this->rows = BankpaymentTable::getPaymentList($this->dateType, $this->dateFrom, $this->dateTo, $this->type, $this->bank, $this->keyword, $status, $this->staff, $this->accountNumber);
        $urlParams = array();
        $urlParams[] = 'dateFrom=' . $this->dateFrom;
        $urlParams[] = 'dateTo=' . $this->dateTo;
        $urlParams[] = 'orderId=' . $this->orderId;
        $urlParams[] = 'staff=' . $this->staff;
        $this->urlParams = join('&', $urlParams);
        $this->status = $status;

        if ($request->getParameter('excel')) {
            set_time_limit(0);
            ini_set("memory_limit", "1024M");

            $filename = 'callpayment';

            $data = "БАНК;";
            $data .= "ДАНСНЫ ДУГААР;";
            $data .= "БАНК ОГНОО;";
            $data .= "ГҮЙЛГЭЭНИЙ УТГА;";
            $data .= "Төлөлтийн ДҮН ;";
            $data .= "Төлөв;";
            $data .= "Төлөлтийн хариу;";
            $data .= "Утасны дугаар;";
            $data .= "Гэрээний дугаар;";
            $data .= "Гэрээний үлдэгдэл;";
            $data .= "Гэрээний нэр;";
            $data .= "Гэрээний цикл;";
            $data .= "Ажилтан;";
            $data .= "Төлөлтийн огноо;";
            $data .= "Огноо;";
            $data .= "Салбар;";
            $data .= "Буцаалтын төрөл;";
            $data .= "Хувилсан гүйлгээ;";
            $data.="\n";
            foreach ($this->rows as $row) {
                $statusName = BankpaymentTable::getStatusName($row['status'], false);
                $data.='"' . $row['bank_name'] . '";';
                $data.='"' . $row['bank_account'] . '";';
                $data.='"' . $row['order_date'] . '";';
                $data.='"' . $row['order_p'] . '";';
                $data.='"' . $row['order_amount'] . '";';
                $data.='"' . $statusName . '(' . $row['try_count'] . ')' . '";';
                $data.='"' . $row['status_comment'] . '";';
                $data.='"' . $row['number'] . '";';
                $data.='"' . $row['contract_number'] . '";';
                $data.='"' . $row['contract_amount'] . '";';
                $data.='"' . $row['contract_name'] . '";';
                $data.='"' . $row['bill_cycle'] . '";';
                $data.='"' . $row['username'] . '";';
                $data.='"' . $row['updated_at'] . '";';
                $data.='"' . $row['created_at'] . '";';
                $data.='"' . $row['payment_type'] . '";';
                $data.='"' . $row['pay_type'] . '";';
                $data.='"' . $row['copy_inv'] . '";';
                $data.="\n";
            }

            AppTools::ExportCsv($data, $filename, false);
            die();
        }

        return sfView::SUCCESS;
    }
}
