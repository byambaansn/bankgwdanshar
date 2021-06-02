<?php

/**
 * dealer actions.
 *
 * @package    bankgw
 * @subpackage dealer
 * @author     Belbayar
 * @version    1.0
 */
class dealerActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'dealer');
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

        $bank = $request->getParameter('bank', 0);

        $status = $request->getParameter('status', 0);
        if (!$status) {
            $status = DealerCore::getStatusesFailed();
        }

        if (count($status) == 1 && in_array(BankKhaanTable::STAT_FAILED_MIN_AMOUNT, $status) && $bank) {
            $this->allowMerge = true;
        }
        $this->statuses = DealerCore::getForSelectStatus();
        $this->banks = VendorTable::getForSelect();

        $this->rows = DealerCore::getList($this->dateFrom, $this->dateTo, $status, $bank, $this->orderId, $this->chargedMobile, $this->orderedMobile, $this->allowMerge);

        $this->status = $status;
        $this->bank = $bank;
        if ($request->getParameter('excel', 0)) {
            set_time_limit(180);
            ini_set("memory_limit", "1024M");
            $data = "БАНК;";
            $data .= "№ ГҮЙЛГЭЭ;";
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
            foreach ($this->rows as $dealer) {
                $data .= '"' . $dealer['bank_name'] . '";';
                $data .= '"' . $dealer['order_id'] . '";';
                $data .= '"' . $dealer['bank_account'] . '";';
                $data .= '"' . $dealer['charge_mobile'] . '";';
                $data .= '"' . $dealer['order_mobile'] . '";';
                $data .= '"' . $dealer['order_type'] . '";';
                $data .= '"' . $dealer['charge_amount'] . '";';
                $data .= '"' . $dealer['order_amount'] . '";';
                $data .= '"' . ($dealer['charge_amount'] - $dealer['order_amount']) . '";';
                $data .= '"' . DealerCore::getStatusName($dealer['status']) . '";';
                $data .= '"' . $dealer['created_at'] . '";';
                $data .= '"' . $dealer['updated_at'] . '";';

                $data .= "\n";
            }
            $filename = 'dealerBank';
            AppTools::ExportCsv($data, $filename, false);
            die();
        }
    }

    /*     * *
     * Бага дүнтэй гүйлгээг нэгтгэж цэнэглэлт хийх;
     * 
     * */

    public function executeMergeAmount(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('sub_tab', 'dealer');

        if ($request->isMethod('GET')) {
            $transactions = $request->getParameter('transaction');
            $vendorId = $request->getParameter('vendorId');

            $mergeAmount = 0;
            $errors = array();

            if (!count($transactions)) {
                $text = 'Гүйлгээ сонгоно уу';
                return $this->renderText($text);
            }
            foreach ($transactions as $bankOrderId) {
                $bankOrder = BankpaymentTable::getBankTransaction($vendorId, $bankOrderId);
                if ($bankOrder) {
                    # status Shalgah 
                    if ($bankOrder['status'] != BankKhaanTable::STAT_FAILED_MIN_AMOUNT) {
                        $errors[] = '<b>' . DealerCore::getStatusName($bankOrder['status']) . '</b> төлөвтэй гүйлгээг нэгтгэх боломжгүй. (id:' . $bankOrderId . ',банк:' . VendorTable::getNameById($vendor) . ',дүн:' . $bankOrder['order_amount'] . ')';
                        continue;
                    }
                    $mergeAmount += $bankOrder['order_amount'];
                }
            }
            if (count($errors)) {
                $text = implode('<br>', $errors);
                return $this->renderText($text);
            }

//            if ($mergeAmount < BankKhaanTable::MIN_AMOUNT_LIMIT) {
//                $text = 'Нийт дүн ' . BankKhaanTable::MIN_AMOUNT_LIMIT . ' -с бага байна.';
//                return $this->renderText($text);
//            }
            $this->mergeAmount = $mergeAmount;
            $this->vendorId = $vendorId;
            $this->getUser()->setAttribute('transaction', array($vendorId => $transactions));
        }
        if ($request->isMethod('POST')) {
            $dealerMobile = $request->getParameter('mobile', 0);
            $dealerType = $request->getParameter('type', 0);
            $vendorId = $request->getParameter('vendorId', 0);
            $sessionData = $this->getUser()->getAttribute('transaction');
            $transactions = $sessionData[$vendorId];

            $mergeAmount = 0;
            $errors = array();

            if (!$transactions) {
                $text = 'Гүйлгээ сонгоно уу';
                $this->getUser()->setFlash('warning', $text);
                return $this->redirect($request->getReferer());
            }
            foreach ($transactions as $bankOrderId) {
                $bankOrder = BankpaymentTable::getBankTransaction($vendorId, $bankOrderId);
                if ($bankOrder) {
                    # status Shalgah 
                    if ($bankOrder['status'] != BankKhaanTable::STAT_FAILED_MIN_AMOUNT) {
                        $errors[] = '<b>' . DealerCore::getStatusName($bankOrder['status']) . '</b> төлөвтэй гүйлгээг нэгтгэх боломжгүй. (id:' . $bankOrderId . ',банк:' . VendorTable::getNameById($vendor) . ',дүн:' . $bankOrder['order_amount'] . ')';
                        continue;
                    }
                    $mergeAmount += $bankOrder['order_amount'];
                }
            }
            if (count($errors)) {
                $text = implode('<br>', $errors);
                $this->getUser()->setFlash('warning', $text);
                return $this->redirect($request->getReferer());
            }

//            if ($mergeAmount < BankKhaanTable::MIN_AMOUNT_LIMIT) {
//                $text = 'Нийт дүн ' . BankKhaanTable::MIN_AMOUNT_LIMIT . ' -с бага байна.';
//                $this->getUser()->setFlash('warning', $text);
//                return $this->redirect($request->getReferer());
//            }
            $amount = $request->getParameter('amount');
            if ($mergeAmount != $amount) {
                $text = 'Анх сонгосон гүйлгээнүүдийн үнийн дүн зөрлөө, дахин оролдоно уу';
                $this->getUser()->setFlash('warning', $text);
                return $this->redirect($request->getReferer());
            }

            # Цэнэглэлт хийх
            if ($dealerType) {
                $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/dealer/process-' . date("Ymd") . '.log'));
                $dealerAgent = DealerGateway::findDealerByMobile($dealerMobile, $logger);
                if (!$dealerAgent) {
                    $text = $dealerMobile . ' дугаартай АГЕНТ дилер олдсонгүй.';
                    $this->getUser()->setFlash('warning', $text);
                    return $this->redirect($request->getReferer());
                }
                $chargeResult = DealerGateway::charge($dealerAgent['dealerId'], $amount, VendorTable::getNameById($vendorId), $logger);
                $orderMobile = $dealerAgent['dealerCode'];
            } else {
                $dealer = DealerCharge::getDealer($dealerMobile);
                if (!$dealer) {
                    $text = $dealerMobile . ' дугаартай дилер олдсонгүй.';
                    $this->getUser()->setFlash('warning', $text);
                    return $this->redirect($request->getReferer());
                }
                $chargeResult = DealerCharge::charge($dealerMobile, $amount);
            }

            if ($chargeResult['success'] == TRUE) {
                $bankMerge = BankMergeTable::insert(BankMergeTable::TYPE_CHARGE, $this->getUser()->getUsername());
                foreach ($transactions as $bankOrderId) {
                    $bankOrder = BankpaymentTable::getBankTransaction($vendorId, $bankOrderId);
                    if ($bankOrder) {
                        # Цэнэглэгдсэн мөнгөн дүн болон хувийг хадгалах
                        $bankOrder->charge_amount = $bankOrder->order_amount;
                        $bankOrder->percent = $chargeResult['percent'];
                        if ($orderMobile) {
                            $bankOrder->order_mobile = $orderMobile;
                        }
                        $bankOrder->save();
                        TransactionTable::setRechargeAssignment(PaymentTypeTable::DEALER, BankTable::getBankAndVendorMap($vendorId), $bankOrder->bank_account, $bankOrder->order_id, $bankOrder->order_date, $bankOrder->order_p, $bankOrder->order_type, $bankOrder->order_amount, $bankOrder->order_s);                        
                        # Зарлага үүсгэх
                        $outcomeOrderId = DealerCore::reoutcome($bankOrder, $dealer, null, $dealerAgent, $chargeResult['percent']);
                        if ($outcomeOrderId) {
                            $bankOrder->status = BankKhaanTable::STAT_SUCCESS;
                            $bankOrder->sales_order_id = $outcomeOrderId;
                            $bankOrder->save();
                        } else {
                            $bankOrder->status = BankKhaanTable::STAT_FAILED_OUTCOME;
                            $bankOrder->save();
                        }
                    }
                    #
                    BankMergeOrderTable::insert($bankOrderId, $bankMerge['id'], $vendorId, $amount);
                }
                $this->getUser()->setFlash('success', 'Амжилттай нэгтгэж цэнэглэлээ');
            } else {
                $this->getUser()->setFlash('warning', 'Цэнэглэлт амжилтгүй боллоо' . $chargeResult['log_response']);
            }
            $this->redirect($request->getReferer());
        }
    }

    public function executeMergeReport(sfWebRequest $request)
    {
        $this->hasExcelCredential = in_array('bankpayment_excel', $this->getUser()->getCredentials());
        $this->getRequest()->setParameter('sub_tab', 'dealer_merge');
        $this->dateFrom = $request->getParameter('dateFrom', date('Y-m-d'));
        $this->dateTo = $request->getParameter('dateTo', date('Y-m-d'));

        $this->chargedMobile = $request->getParameter('chargedMobile');
        $this->orderedMobile = $request->getParameter('orderedMobile');
        $this->orderId = $request->getParameter('orderId');

        $bank = $request->getParameter('bank', 0);

        $status = $request->getParameter('status', 0);
        if (!$status) {
            $status = DealerCore::getStatusesFailed();
        }

        if (count($status) == 1 && in_array(BankKhaanTable::STAT_FAILED_MIN_AMOUNT, $status) && $bank) {
            $this->allowMerge = true;
        }
        $this->statuses = DealerCore::getForSelectStatus();
        $this->banks = VendorTable::getForSelect();

        $this->rows = BankMergeOrderTable::getList($this->dateFrom, $this->dateTo, $status, $bank, $this->orderId, $this->chargedMobile, $this->orderedMobile);

        $this->status = $status;
        $this->bank = $bank;
    }

}