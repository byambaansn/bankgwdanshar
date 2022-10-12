<?php

/**
 * bankTransaction actions.
 *
 * @package    sf_sandbox
 * @subpackage khaan
 * @author     Belbayar
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class bankTransactionActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'transaction');
    }

    /**
     * Төлөлт болгох
     * 
     * @param sfWebRequest $request
     */
    public function executeUpdate(sfWebRequest $request)
    {
        if ($request->isMethod('post')) {
            $action = (int) $request->getParameter('action_type', 0);
            $paymentType = $request->getParameter('payment', 0);
            $transactions = $request->getParameter('transaction', 0);
            $amounts = $request->getParameter('amount', 0);
            $comment = $request->getParameter('comment', 0);

            if (!$paymentType) {
                $this->getUser()->setFlash('error', 'Гүйлгээний төрөл сонгоогүй байна');
                $this->redirect($request->getReferer());
            }

            if (!$transactions || $transactions == 0 || count($transactions) == 0) {
                $this->getUser()->setFlash('error', 'Гүйлгээ сонгоно уу');
                $this->redirect($request->getReferer());
            }
            $totalRow = 0;
            switch ($action) {
                case 1:// one shot
                    foreach ($transactions as $tran) {
                        $transaction = TransactionTable::retrieveByPK($tran);
                        if ($transaction) {
                            if ($transaction['status'] == TransactionTable::STATUS_PAYMENT) {
                                $this->getUser()->setFlash('error', 'Гүйлгээг төлөлт болгосон байна');
                                $this->redirect($request->getReferer());
                            }
                            $payment = PaymentTable::insert($paymentType, $comment, $transaction['order_amount'], $this->getUser()->getId(), $this->getUser()->getUsername());
                            if ($payment) {
                                TransactionPaymentTable::insert($transaction['id'], $payment->getId());
                                $result['total'] = $result['total'] + 1;
                                $totalRow = 1;
                            }
                            $transaction->setStatus(TransactionTable::STATUS_PAYMENT);
                            $transaction->save();
                        }
                    }
                    break;
                case 2://split
                    if (count($transactions) != 1) {
                        $this->getUser()->setFlash('error', 'Хуваах үйлдэл хийхэд нэг гүйлгээ сонгох боломжтой.');
                        $this->redirect($request->getReferer());
                    }
                    if (count($amounts) != count($paymentType)) {
                        $this->getUser()->setFlash('error', 'Хуваасан дүнгийн тоо, төрөлийн тоо 2 зөрүүтэй байна.');
                        $this->redirect($request->getReferer());
                    }
                    $total = 0;
                    foreach ($amounts as $index => $amount) {
                        if (!$amount) {
                            $this->getUser()->setFlash('error', 'Хувааж буй мөнгөн дүн дутуу байна.');
                            $this->redirect($request->getReferer());
                        }
                        if (!isset($paymentType[$index]) || $paymentType[$index] <= 0) {
                            $this->getUser()->setFlash('error', 'Хувааж буй төрөл дутуу сонгосон байна');
                            $this->redirect($request->getReferer());
                        }
                        $total+=$amount;
                    }
                    $transaction = TransactionTable::retrieveByPK($transactions[0]);
                    if ($transaction['status'] == TransactionTable::STATUS_PAYMENT) {
                        $this->getUser()->setFlash('error', 'Гүйлгээг төлөлт болгосон байна');
                        $this->redirect($request->getReferer());
                    }
                    if ($total == $transaction['order_amount']) {
                        foreach ($amounts as $index => $amount) {
                            $payment = PaymentTable::insert($paymentType[$index], $comment, $amount, $this->getUser()->getId(), $this->getUser()->getUsername());
                            TransactionPaymentTable::insert($transaction['id'], $payment->getId());
                            $result['total'] = $result['total'] + 1;
                            $totalRow++;
                        }
                        $transaction->setStatus(TransactionTable::STATUS_PAYMENT);
                        $transaction->save();
                    } else {
                        $this->getUser()->setFlash('error', 'Хувааж буй мөнгөн дүн үндсэн гүйлгээний дүнтэй таарахгүй байна.');
                        $this->redirect($request->getReferer());
                    }
                    break;
                case 3: //merge
                    $total = 0;
                    foreach ($transactions as $tran) {
                        $transaction = TransactionTable::retrieveByPK($tran);
                        if ($transaction['status'] == TransactionTable::STATUS_PAYMENT) {
                            $this->getUser()->setFlash('error', 'Гүйлгээг төлөлт болгосон байна');
                            $this->redirect($request->getReferer());
                            break;
                        }
                    }


                    $payment = PaymentTable::insert($paymentType, $comment, $total, $this->getUser()->getId(), $this->getUser()->getUsername());
                    foreach ($transactions as $tran) {
                        $transaction = TransactionTable::retrieveByPK($tran);
                        $total+=$transaction->getOrderAmount();
                        TransactionPaymentTable::insert($transaction->getId(), $payment->getId());
                        $transaction->setStatus(TransactionTable::STATUS_PAYMENT);
                        $transaction->save();
                    }
                    $payment->setAmount($total);
                    $payment->save();
                    $totalRow = 1;
                    break;
                default:
                    $this->getUser()->setFlash('error', 'Үйлдэл буруу сонгосон байна.');
                    $this->redirect($request->getReferer());
                    break;
            }

            $this->getUser()->setFlash('success', $totalRow . ' -н гүйлгээ амжилттай хадгаллаа');
        }
        return $this->redirect($request->getReferer());
    }

    public function executeTransaction(sfWebRequest $request)
    {
        $this->hasExcelCredential = in_array('bankpayment_excel', $this->getUser()->getCredentials());
        $this->getRequest()->setParameter('sub_tab', 'trans');
        $this->dateFrom = $request->getParameter('date_from', date('Y-m-d'));
        $this->dateTo = $request->getParameter('date_to', date('Y-m-d'));

        $this->status = (int) $request->getParameter('status', 1);
        $this->orderType = (int) $request->getParameter('type', 1);
        $bankDate = $request->getParameter('bank_date', '0');
        $this->excel = $request->getParameter('excel', 0);
        $this->page = (int) $request->getParameter('page', 0);
        $this->bank = (int) $request->getParameter('bank', 0);
        $this->orderId = $request->getParameter('order_id');
        $this->orderAmount = $request->getParameter('order_amount');
        $this->orderValue = $request->getParameter('order_value');
        $this->accountNumber = $request->getParameter('account_number');
        //$this->relatedAccount = $request->getParameter('related_account');
        $this->statuses = TransactionTable::getForSelectStatus();
        $this->banks = BankTable::getForSelect();
        $this->types = PaymentTypeTable::getForSelect();
        $this->accountNumbers = BankAccountTable::getForSelectWithType($this->bank);

        if ($bankDate === "on") {
            $this->bankDate = 1;
        } else {
            $this->bankDate = 0;
        }

        $daysDiff = AppTools::getDays($this->dateFrom, $this->dateTo);
        if (!$request->getParameter('date_from')) {

        } else if ($daysDiff > 31) {
            $results = array();
            $this->getUser()->setFlash('info', 'Та 1 сараас илүүтэйгээр шүүх боломжгүй');
        } else {
            $results = TransactionTable::getList($this->dateFrom, $this->dateTo, $this->bank, $this->orderId, $this->orderType, $this->orderAmount, $this->orderValue, $this->accountNumber, $this->status, $this->bankDate, $this->page);
            $this->rows = $results->getResults();
            $this->count = $results->getNbResults();
            //$this->totalAmount = TransactionTable::getListTotalAmount($this->dateFrom, $this->dateTo, $this->bank, $this->orderId, $this->orderAmount, $this->orderValue, $this->accountNumber, $this->status, $this->bankDate);
            $this->pager = $results;
        }

//print_r($results->getResults()[0]->BankAccount->type);
//die();
        if ($this->excel) {
            $results = TransactionTable::getListCustom($this->dateFrom, $this->dateTo, $this->bank, $this->orderId, $this->orderType, $this->orderAmount, $this->orderValue, $this->accountNumber, $this->relatedAccount, $this->status, $this->bankDate);

            set_time_limit(0);
            ini_set("memory_limit", "1024M");

            $filename = 'transaction';

            $data = "БАНК;";
            $data .= "№ ГҮЙЛГЭЭ;";
            $data .= "ДАНСНЫ ДУГААР;";
            $data .= "ХАРЬЦСАН ДАНС;";
            $data .= "ТӨЛӨВ;";
            $data .= "ГҮЙЛГЭЭНИЙ ТӨРӨЛ;";
            $data .= "ГҮЙЛГЭЭНИЙ УТГА;";
            $data .= "ГҮЙЛГЭЭНИЙ ДҮН ;";
            $data .= "ГҮЙЛГЭЭНИЙ ОГНОО;";
            $data .= "ГҮЙЛГЭЭНИЙ САЛБАР;";
            $data .= "ҮҮССЭН;";
            $data.="\n";
            foreach ($results as $row) {
                $status = ($row['status'] == 1) ? 'N' : (($row['status'] == 2) ? 'D' : '' );
                $data.='"' . $row['bank_name'] . '";';
                $data.='"' . $row['order_id'] . '";';
                $data.='"' . $row['bank_account'] . '";';
                $data.='"' . $row['related_account'] . '";';
                $data.='"' . $status . '";';
                $data.='"' . $row['order_type'] . '";';
                $data.='"' . $row['order_p'] . '";';
                $data.='"' . $row['order_amount'] . '";';
                $data.='"' . $row['order_date'] . '";';
                $data.='"' . $row['order_branch'] . '";';
                $data.='"' . $row['created_at'] . '";';
                $data.="\n";
            }

            AppTools::ExportCsv($data, $filename, false);
            die();
        }
    }

    /**
     * Төлбөрийн төрөл
     * 
     * @param sfWebRequest $request
     */
    public function executePaymentType(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'type');
        $name = $request->getParameter('name', 0);
        $status = $request->getParameter('status', 1);
        $id = $request->getParameter('id', 0);
        if ($id) {
            $paymentType = PaymentTypeTable::retrieveByPK($id);
            $this->name = $paymentType['name'];
            $this->status = $paymentType['status'];
            if (in_array($id, PaymentTypeTable::getSecretIds())) {
                $this->getUser()->setFlash('warning', 'Засах боломжгүй төрөл байна');
            }
        }

        $this->statuses = PaymentTypeTable::getForSelectStatus();
        $this->rows = PaymentTypeTable::getList();
        if ($request->isMethod('POST')) {

            if (!$paymentType) {
                $paymentType = new PaymentType();
                $paymentType->setCreatedAt(date('Y-m-d H:i:s'));
            }
            $paymentType->setName($name);
            $paymentType->setStatus($status);
            $paymentType->save();
            $this->getUser()->setFlash('success', 'Амжилттай хадгаллаа.');
        }
    }

    /**
     *  Төлбөрийн төрөл жагсаалт авах 
     * 
     * @param sfWebRequest $request
     */
    public function executePaymentTypeList(sfWebRequest $request)
    {
        $jTableResult = array();
        try {
            $jTableResult['Result'] = "OK";
            $jTableResult['Options'] = PaymentTypeTable::getForSelectJTable();
        } catch (Exception $exc) {
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = $exc->getTraceAsString();
        }
        $json = json_encode($jTableResult);
        return $this->renderText($json);
    }

    /**
     * Төлөлт шүүх тохиргоо
     * 
     * @param sfWebRequest $request
     */
    public function executeConfigAssignment(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'config_assignment');
        $priority = (int) $request->getParameter('priority', 0);
        $filter = $request->getParameter('filter', '');
        $accType = (int) $request->getParameter('accType', TransactionTable::TYPE_ALL);
        $paymentType = (int) $request->getParameter('paymentType', PaymentTypeTable::AUTO);
        $filterType = (int) $request->getParameter('filterType', ConfigAssignmentTable::FILTER_WORD);
        $filterDay = (int) $request->getParameter('filterDay', ConfigAssignmentTable::FILTER_EVERY_DAY);
        $status = (int) $request->getParameter('status', ConfigAssignmentTable::STATUS_ACTIVE);
        $id = (int) $request->getParameter('id', 0);

        $this->accTypes = ConfigAssignmentTable::getAccTypes();
        $this->paymentTypes = PaymentTypeTable::getForSelect();
        $this->filterTypes = ConfigAssignmentTable::getFilterTypes();
        $this->filterDays = ConfigAssignmentTable::getFilterDays();
        $this->statuses = ConfigAssignmentTable::getStatuses();
        $this->rows = ConfigAssignmentTable::getList(FALSE);

        if ($request->isMethod('POST')) {
            if ($filterType != ConfigAssignmentTable::FILTER_REST and ! $filter) {
                $this->getUser()->setFlash('error', 'Шүүлт хийх үгээ оруулна уу.');
                $this->redirect($request->getReferer());
            }
            if (!array_key_exists($accType, $this->accTypes)) {
                $this->getUser()->setFlash('error', 'Дансны төрөл сонгоно уу.');
                $this->redirect($request->getReferer());
            }
            if (!PaymentTypeTable::retrieveByPK($paymentType)) {
                $this->getUser()->setFlash('error', 'Төлбөрийн төрөл сонгоно уу.');
                $this->redirect($request->getReferer());
            }
            if (!array_key_exists($filterType, $this->filterTypes)) {
                $this->getUser()->setFlash('error', 'Шүүлт хийх төрөл сонгоно уу.');
                $this->redirect($request->getReferer());
            }
            if (!array_key_exists($filterDay, $this->filterDays)) {
                $this->getUser()->setFlash('error', 'Шүүлт ажиллах өдөр сонгоно уу.');
                $this->redirect($request->getReferer());
            }
            if (!array_key_exists($status, $this->statuses)) {
                $this->getUser()->setFlash('error', 'Шүүлтийн төлөв сонгоно уу.');
                $this->redirect($request->getReferer());
            }
            ConfigAssignmentTable::update($id, $priority, $filter, $accType, $paymentType, $filterType, $filterDay, $status, $this->getUser()->getId(), $this->getUser()->getUsername());
            $this->getUser()->setFlash('success', 'Амжилттай хадгаллаа.');
            $this->redirect('@transaction_config_assignment');
        }

        if ($id) {
            $row = ConfigAssignmentTable::getInstance()->find($id);
            if ($row) {
                $this->id = $id;
                $this->priority = $row->getPriority();
                $this->filter = $row->getFilter();
                $this->accType = $row->getAccType();
                $this->paymentType = $row->getTypeId();
                $this->filterType = $row->getFilterType();
                $this->filterDay = $row->getFilterDay();
                $this->status = $row->getStatus();
            }
        }
    }
    
    
    /**
     * Төлбөр болгох
     * 
     * @param sfWebRequest $request
     */
    public function executeToBankpayment(sfWebRequest $request)
    {
        $type = $request->getParameter('type', BankpaymentTable::TYPE_CALL_PAYMENT);
        $id = $request->getParameter('id', 0);

        $transaction = TransactionTable::retrieveByPK($id);
        
        if ($transaction) {
            
            $bankAccount = BankAccountTable::getByAccount($transaction['bank_account']);
            
            if ($bankAccount) {
                if ($bankAccount['type'] == BankAccountTable::TYPE_CALLPAYMET) {
                    $message = '<div class="warning message">Ярианы данс тул төлбөр болгох боломжгүй!!!';
                    return $this->renderText($message);
                }
            }
            
            $success = false;
            $bankPayment = null;
            $bankOrder = null;
            
            switch ((int) $transaction['bank_id']) {
                case BankTable::KHAAN:
                    $bankOrder = BankKhaanTable::retrieveByTran($transaction['order_id'], $transaction['order_type'], $transaction['order_amount'], $transaction['order_date']);
                    if (!$bankOrder) {
                        $this->getUser()->setFlash('error', 'Гүйлгээ олдоогүй тул төлбөр болгох боломжгүй!!!');
                        break;
                    }
                    
                    $bankPayment = BankpaymentTable::retrieveByBankOrderId($bankOrder['id'], $bankOrder['vendor_id']);
                    if (!$bankPayment) {
                        $success = BankKhaanTable::bankPaymentFromTransaction($bankOrder);
                    }
                    break;
                case BankTable::GOLOMT:
                    $bankOrder = BankGolomtTable::retrieveByTran($transaction['order_id'], $transaction['order_type'], $transaction['order_amount'], $transaction['order_date']);
                    if (!$bankOrder) {
                        $this->getUser()->setFlash('error', 'Гүйлгээ олдоогүй тул төлбөр болгох боломжгүй!!!');
                        break;
                    }
                    
                    $bankPayment = BankpaymentTable::retrieveByBankOrderId($bankOrder['id'], $bankOrder['vendor_id']);
                    if (!$bankPayment) {
                        $success = BankGolomtTable::bankPaymentFromTransaction($bankOrder);
                    }
                    break;
                case BankTable::XAC:
                    $bankOrder = BankXacTable::retrieveByTran($transaction['order_id'], $transaction['order_type'], $transaction['order_amount'], $transaction['order_date']);
                    if (!$bankOrder) {
                        $this->getUser()->setFlash('error', 'Гүйлгээ олдоогүй тул төлбөр болгох боломжгүй!!!');
                        break;
                    }
                    
                    $bankPayment = BankpaymentTable::retrieveByBankOrderId($bankOrder['id'], $bankOrder['vendor_id']);
                    if (!$bankPayment) {
                        $success = BankXacTable::bankPaymentFromTransaction($bankOrder);
                    }
                    break;
                case BankTable::TDB:
                    $bankOrder = BankTdbTable::retrieveByTran($transaction['order_id'], $transaction['order_type'], $transaction['order_amount'], $transaction['order_date']);
                    if (!$bankOrder) {
                        $this->getUser()->setFlash('error', 'Гүйлгээ олдоогүй тул төлбөр болгох боломжгүй!!!');
                        break;
                    }
                    
                    $bankPayment = BankpaymentTable::retrieveByBankOrderId($bankOrder['id'], $bankOrder['vendor_id']);
                    if (!$bankPayment) {
                        $success = BankTdbTable::bankPaymentFromTransaction($bankOrder);
                    }
                    break;
                case BankTable::CAPITAL:
                    $bankOrder = BankCapitalTable::retrieveByTran($transaction['order_id'], $transaction['order_type'], $transaction['order_amount'], $transaction['order_date']);
                    if (!$bankOrder) {
                        $this->getUser()->setFlash('error', 'Гүйлгээ олдоогүй тул төлбөр болгох боломжгүй!!!');
                        break;
                    }
                    
                    $bankPayment = BankpaymentTable::retrieveByBankOrderId($bankOrder['id'], $bankOrder['vendor_id']);
                    if (!$bankPayment) {
                        $success = BankCapitalTable::bankPaymentFromTransaction($bankOrder);
                    }
                    break;
                case BankTable::SAVINGS:
                    $bankOrder = BankSavingsTable::retrieveByTran($transaction['order_id'], $transaction['order_type'], $transaction['order_amount'], $transaction['order_date']);
                    if (!$bankOrder) {
                        $this->getUser()->setFlash('error', 'Гүйлгээ олдоогүй тул төлбөр болгох боломжгүй!!!');
                        break;
                    }
                    
                    $bankPayment = BankpaymentTable::retrieveByBankOrderId($bankOrder['id'], $bankOrder['vendor_id']);
                    if (!$bankPayment) {
                        $success = BankSavingsTable::bankPaymentFromTransaction($bankOrder);
                    }
                    break;
                case BankTable::CAPITRON:
                    $bankOrder = BankCapitronTable::retrieveByTran($transaction['order_id'], $transaction['order_type'], $transaction['order_amount'], $transaction['order_date']);
                    if (!$bankOrder) {
                        $this->getUser()->setFlash('error', 'Гүйлгээ олдоогүй тул төлбөр болгох боломжгүй!!!');
                        break;
                    }
                    
                    $bankPayment = BankpaymentTable::retrieveByBankOrderId($bankOrder['id'], $bankOrder['vendor_id']);
                    if (!$bankPayment) {
                        $success = BankCapitronTable::bankPaymentFromTransaction($bankOrder);
                    }
                    break;
                default:
                    $bankOrder = null;
                    $success = false;
                    break;
            }
        }
        
        if ($success) {
            $message = '<div class="success message">Тухайн хуулгыг амжилттай төлбөр болголоо.';
            return $this->renderText($message);
        }

        if ($bankPayment) {
            if ($bankPayment['type'] != 5 && $bankPayment['type'] != 6 && in_array($bankPayment['status'], array(BankpaymentTable::STAT_SUCCESS, BankpaymentTable::STAT_IMPOSSIBLE, BankpaymentTable::STAT_REFUND, BankpaymentTable::STAT_SPLITED))) {
                $message = '<div class="warning message">Тухайн гүйлгээ нь Bankpayment-д /'.BankpaymentTable::getStatusName(BankpaymentTable::STAT_SUCCESS).', '.BankpaymentTable::getStatusName(BankpaymentTable::STAT_IMPOSSIBLE).', '.BankpaymentTable::getStatusName(BankpaymentTable::STAT_REFUND).' болон '.BankpaymentTable::getStatusName(BankpaymentTable::STAT_SPLITED).'/ төрөлтэй байгаа тул төлбөр болгох боломжгүй!!!';
                return $this->renderText($message);
            }
            else {
                if ($request->isMethod('POST')) {
                    $bankPayment->type = $type;
                    $bankPayment->username = $this->getUser()->getUsername();
                    $bankPayment->save();
                    $this->getUser()->setFlash('success', 'Тухайн хуулгыг амжилттай төлбөр болголоо.');
                    return $this->redirect('@transaction_list');
                }
            }
        }
        
        $this->transaction_id = $id;
        $this->types = BankpaymentTable::getForSelectTypes();
    }
    
    /**
     * Банкны данс
     * 
     * @param sfWebRequest $request
     */
    public function executeBankAccount(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'bank_account');
        $accountNumber = $request->getParameter('account', 0);
        $accountAlias = $request->getParameter('account_alias', 0);
        $sapAccount = $request->getParameter('sap_account', 0);
        $sapGlAccount = $request->getParameter('sap_gl_account', 0);
        $bankCode = $request->getParameter('bank_code', '');
        $bank = (int) $request->getParameter('bank', 0);
        $company = (int) $request->getParameter('company', 0);
        $type = (int) $request->getParameter('type', 0);
        $id = $request->getParameter('id', 0);

        if ($request->isMethod('POST')) {
            if (!$bank) {
                $this->getUser()->setFlash('error', 'Банк сонгоно уу.');
                $this->redirect($request->getReferer());
            }
            if (!$company) {
                $this->getUser()->setFlash('error', 'Компани сонгоно уу.');
                $this->redirect($request->getReferer());
            }
            if (!$type) {
                $this->getUser()->setFlash('error', 'Дансын төрөл сонгоно уу.');
                $this->redirect($request->getReferer());
            }
            $account = BankAccountTable::retrieveByPK($id);

            if (!$account) {
                $account = new BankAccount();
            }
            $account->setBankId($bank);
            $account->setCompanyId($company);
            $account->setType($type);
            $account->setAccount($accountNumber);
            $account->setAccountAlias($accountAlias);
            $account->setSapAccount($sapAccount);
            $account->setSapGlAccount($sapGlAccount);
            $account->setBankCode($bankCode);
            $account->save();
            $this->getUser()->setFlash('success', 'Амжилттай хадгаллаа.');
            $this->redirect('@transaction_bank_account');
        }
        if ($id) {
            $account = BankAccountTable::retrieveByPK($id);
            $this->bank = $account['bank_id'];
            $this->company = $account['company_id'];
            $this->id = $account['id'];
            $this->type = $account['type'];
            $this->account = $account['account'];
            $this->accountAlias = $account['account_alias'];
            $this->sapGlAccount = $account['sap_gl_account'];
            $this->sapAccount = $account['sap_account'];
            $this->bankCode = $account['bank_code'];
        }
        $this->rows = BankAccountTable::getList();
        $this->banks = BankTable::getForSelect();
        $this->companies = CompanyTable::getForSelect();
        $this->types = BankAccountTable::getTypes();
    }

    /**
     * Төлөлт болгосон гүйлгээнүүд
     * 
     * @param sfWebRequest $request
     */
    public function executePayment(sfWebRequest $request)
    {
        $this->hasExcelCredential = in_array('bankpayment_excel', $this->getUser()->getCredentials());
        $this->getRequest()->setParameter('sub_tab', 'payment');
        $this->dateFrom = $request->getParameter('date_from', date('Y-m-01'));
        $this->dateTo = $request->getParameter('date_to', date('Y-m-d'));
        $bankDate = $request->getParameter('bank_date', 0);
        $this->status = (int) $request->getParameter('status', 1);
        $excel = $request->getParameter('excel', 0);
        $sap = $request->getParameter('sap', 0);
        $this->bank = (int) $request->getParameter('bank', 0);
        $this->orderId = $request->getParameter('order_id');
        $this->account = $request->getParameter('account');
        $this->orderAmount = $request->getParameter('order_amount');
        $this->orderValue = $request->getParameter('order_value');
        $this->type = (int) $request->getParameter('type');

        $this->statuses = PaymentTable::getForSelectStatus();
        $this->banks = BankTable::getForSelect();
        $this->accountNumbers = TransactionTable::getAccountNumbers();
        $this->types = PaymentTypeTable::getForSelect();
        if ($bankDate === "on") {
            $this->bankDate = 1;
        } else {
            $this->bankDate = 0;
        }

        if ($excel) {
            $results = PaymentTable::getListCustom(1, $this->dateFrom, $this->dateTo, $this->bank, $this->account, $this->orderId, $this->orderAmount, $this->orderValue, $this->status, $this->type);
            set_time_limit(0);
            ini_set("memory_limit", "1024M");

            $filename = 'transaction_payment';


            $data = "БАНК;";
            $data .= "№ ГҮЙЛГЭЭ;";
            $data .= "ДАНСНЫ ДУГААР;";
        
            $data .= "ТӨЛӨВ;";
            $data .= "ГҮЙЛГЭЭНИЙ УТГА;";
            $data .= "ГҮЙЛГЭЭНИЙ ДҮН ;";
            $data .= "ГҮЙЛГЭЭНИЙ ОГНОО;";
            $data .= "ГҮЙЛГЭЭНИЙ САЛБАР;";
            $data .= "САЛБАР;";
            $data .= "АЖИЛТАН;";
            $data .= "ТАЙЛБАР;";
            $data .= "ҮҮССЭН;";
            $data.="\n";

            foreach ($results as $row) {
                $status = ($row['status'] == PaymentTable::STATUS_PAYMENT) ? 'D' : (($row['status'] == PaymentTable::STATUS_SAP) ? 'SAP' : '' );
                $data.='"' . $row['bank_name'] . '";';
                $data.='"' . $row['order_id'] . '";';
                $data.='"' . $row['bank_account'] . '";';
              
                $data.='"' . $row['status'] . '";';
                $data.='"' . $row['order_p'] . '";';
                if ($row['order_type'] == 'SUB') {
                    $data.='"-' . $row['amount'] . '";';
                } else {
                    $data.='"' . $row['amount'] . '";';
                }
                $data.='"' . $row['order_date'] . '";';
                $data.='"' . $row['order_branch'] . '";';
                $data.='"' . $row['payment_type'] . '";';
                $data.='"' . $row['username'] . '";';
                $data.='"' . $row['description'] . '";';
                $data.='"' . $row['created_at'] . '";';
                $data.="\n";
            }

            AppTools::ExportCsv($data, $filename, false);
            die();
        } elseif ($sap && $this->getUser()->hasCredential('transaction_admin')) {
            $results = PaymentTable::getListCustom(2, $this->dateFrom, $this->dateTo, $this->bank, $this->account, $this->relatedAccount, $this->orderId, $this->orderAmount, $this->orderValue, $this->status, $this->type, $this->bankDate);
            set_time_limit(0);
            ini_set("memory_limit", "1024M");

            $transactions = array();
            $paymentIds = array();
            foreach ($results as $row) {
//                $transactions[date('Ymd', strtotime($row['order_date']))][] = $row;
                $transactions[$row['company_id']][date('Ymd', strtotime($row['order_date']))][] = $row;
                $paymentIds[] = $row['p_id'];
            }
            unset($results);


            foreach ($transactions as $company => $comTransactions) {
                foreach ($comTransactions as $date => $trans) {
                    $fiDoc = new libSAPFI();

                    $params = array();
                    $params['company_code'] = $company;
                    $params['doc_type'] = 'ZR';
                    $params['doc_date'] = $date;
                    $params['ref_num'] = 'test document';
                    $params['currency'] = 'MNT';
                    $params['posting_date'] = $date;
                    $params['doc_header_txt'] = 'test document header';
                    $params['translation_date'] = null;
                    $fiDoc->createHeader($params);
                    $i = 1;
                    foreach ($trans as $tran) {
                        $accInd = "G";
                        if (!$tran['sap_account'] || !$tran['sap_gl_account']) {
                            $this->getUser()->setFlash('error', 'Дансын тохиргоо олдсонгүй, данс тохируулаад дахин үзнэ үү.' . $tran['bank_account']);
                            $this->redirect('@transaction_payment');
                        }
                        $lineItem = array();
                        $lineItem['pkey'] = ($tran['order_type'] == 'SUB') ? '50' : '40';
                        $lineItem['amount'] = $tran['amount'];
                        $lineItem['acc_ind'] = $accInd;
                        $lineItem['account'] = $tran['sap_account'];
                        $lineItem['clearing'] = $tran['sap_gl_account'];
                        $lineItem['vat_code'] = null;
                        $lineItem['assignment'] = $tran['payment_type'];
                        $lineItem['text'] = $tran['order_p'];
                        $fiDoc->addStatement($lineItem);
                        if ($i % 499 == 0) {
                            $fiDoc->createHeader($params);
                        }
                        $i++;
                    }
                    $content = $fiDoc->getStringOfDocument();
                    $filename = 'FI' . date($params['company_code'] . $params['posting_date']) . date("His") . '.txt';
                    $filepath = sfConfig::get('sf_upload_dir') . '/sap/' . $filename;
                    $fileNames[] = $filename;
                    $file = fopen($filepath, 'w+');
                    fwrite($file, pack("CCC", 0xef, 0xbb, 0xbf)); //save UTF-8

                    $len = strlen($content);
                    fputs($file, $content, $len);
                    fclose($file);
                    //  ServiceCore::uploadFileToFTP(ErpCore::FTP_USER, ErpCore::FTP_PASS, $filepath, $filename);
                    //  $files[$filepath] = $filename;
                }
            }

            $zipFileName = date('YmdHis') . '.zip';
            $zipFilepath = sfConfig::get('sf_upload_dir') . '/sap/download/';
            $filepath = sfConfig::get('sf_upload_dir') . '/sap/';
            $zip = new ZipArchive();
            if ($zip->open($zipFilepath . $zipFileName, ZIPARCHIVE::CREATE) !== TRUE) {
                exit("cannot open <$zipFileName>\n");
                die();
            }
            foreach ($fileNames as $files) {
                if (!file_exists($filepath . $files)) {
                    die($filepath . $files . ' does not exist');
                }
                if (!is_readable($filepath . $files)) {
                    die($filepath . $files . ' not readable');
                }
                $zip->addFile($filepath . $files, $files);
            }
            $zip->close();

            # SAP temdeglegee
            PaymentTable::setSapDataExport($paymentIds);

            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=$zipFileName");
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($zipFilepath . $zipFileName);
            exit;
            die();
            /* echo $fiDoc->__toString(); */
        }
    }

    /**
     * Засах payment
     * 
     * @param sfWebRequest $request
     */
    public function executePaymentUpdate(sfWebRequest $request)
    {
        $transPayment = TransactionPaymentTable::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($transPayment);

        $paymentType = PaymentTypeTable::retrieveByPK($request->getParameter('payment_id'));
        $this->forward404Unless($paymentType);

        $payment = PaymentTable::retrieveByPK($transPayment['payment_id']);
        $this->forward404Unless($payment);
        $jTableResult = array();

        try {
            $payment->setTypeId($paymentType['id']);
            $payment->setCreatedUserId($this->getUser()->getId());
            $payment->setUsername($this->getUser()->getUsername());
            $payment->save();

            $jTableResult['Result'] = "OK";
        } catch (Exception $exc) {
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = $exc->getTraceAsString();
        }
        $json = json_encode($jTableResult);
        return $this->renderText($json);
    }

    public function executePaymentList(sfWebRequest $request)
    {
        $temp = explode("payment/", $request->getReferer());
        $tempArray = $temp[1];
        if (!empty($tempArray)) {
            $dateFrom = $request->getParameter('date_from', date('Y-m-01'));
            $dateTo = $request->getParameter('date_to', date('Y-m-d'));
            $pageSize = (int)$request->getParameter('jtPageSize', 10);
            $startIndex = (int)$request->getParameter('jtStartIndex', 0);
            $orderBy = $request->getParameter('jtSorting', 0);
            $bank = (int)$request->getParameter('bank', 0);
            $account = $request->getParameter('account', 0);
            $orderId = $request->getParameter('order_id', 0);
            $orderAmount = $request->getParameter('order_amount', 0);
            $orderValue = $request->getParameter('order_value', 0);
            $status = (int)$request->getParameter('status', 0);
            $type = (int)$request->getParameter('type', 0);
//Return result to jTable
            $jTableResult = array();
            $jTableResult['Result'] = "OK";
            $jTableResult['TotalRecordCount'] = PaymentTable::getListCustomCount($dateFrom, $dateTo, $bank, $account, $orderId, $orderAmount, $orderValue, $status, $type);
            $jTableResult['TotalAmount'] = PaymentTable::getListCustomFooter($dateFrom, $dateTo, $bank, $account, $orderId, $orderAmount, $orderValue, $status, $type);
            $jTableResult['Records'] = PaymentTable::getListCustom(0, $dateFrom, $dateTo, $bank, $account, $orderId, $orderAmount, $orderValue, $status, $type, $startIndex, $pageSize, $orderBy);

            $json = json_encode($jTableResult);
            return $this->renderText($json);
        } else {
            $jTableResult = array();
            $jTableResult['Result'] = 0;
            $jTableResult['TotalRecordCount'] = 0;
            $jTableResult['TotalAmount'] = 0;
            $jTableResult['Records'] = 0;
            $json = json_encode($jTableResult);
            return $this->renderText("");
        }
    }

    /**
     * Урьдчилж олсон орлого 
     * 
     * @param sfWebRequest $request
     */
    public function executeUo(sfWebRequest $request)
    {
        $this->hasExcelCredential = in_array('bankpayment_excel', $this->getUser()->getCredentials());
        $this->getRequest()->setParameter('sub_tab', 'uo');
        $this->dateFrom = $request->getParameter('date_from', date('Y-m-01'));
        $this->dateTo = $request->getParameter('date_to', date('Y-m-d'));

        $this->status = 0;
        $this->bank = (int) $request->getParameter('bank', 0);
        $this->orderId = $request->getParameter('order_id');
        $this->account = $request->getParameter('account');
        $this->type = PaymentTypeTable::UO;

        $this->statuses = PaymentTable::getForSelectStatus();
        $this->banks = BankTable::getForSelect();
        $this->types = PaymentTypeTable::getForSelect();
    }

    /**
     * Урьдчилж олсон орлого  засах
     * 
     * @param sfWebRequest $request
     */
    public function executeUoUpdate(sfWebRequest $request)
    {
        $jTableResult['Result'] = "OK";

        try {
            $this->getRequest()->setParameter('sub_tab', 'uo');
            $id = (int) $request->getParameter('id', 0);
            $paymentType = (int) $request->getParameter('payment_id');
            $transactionPayment = TransactionPaymentTable::retrieveByPK($id);
            $payment = PaymentTable::retrieveByPK($transactionPayment->getPaymentId());
            $payment->setTypeId($paymentType);
            $payment->save();
        } catch (Exception $exc) {
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = "Алдаа гарлаа. Дахин оролдоно уу";
        }

        $json = json_encode($jTableResult);
        return $this->renderText($json);
    }

    /**
     * Дансны дугаар авах
     * 
     * @param sfWebRequest $request
     */
    public function executeAjaxAccountNumbers(sfWebRequest $request)
    {
        $bankId = $request->getParameter('bank_id', 0);
        $this->accountNumber = $request->getParameter('account', 0);
        $this->accountNumbers = BankAccountTable::getForSelectWithType($bankId);
//        print_r($this->accountNumbers );die();
    }

}
