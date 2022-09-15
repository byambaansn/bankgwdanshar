<?php

class cronActions extends sfActions
{

    /**
     * Банкны хуулга татах Төрийн банк
     *
     * @param sfWebRequest $request
     * @return void
     */
    public function executeSavingsBank(sfWebRequest $request)
    {
        set_time_limit(3600);
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeSavingsBank.log'));
        $logger->log('--start--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');

        $url = $yml['all']['statebank']['api'];
        $username = $yml['all']['statebank']['username'];
        $passwordToDec = $yml['all']['statebank']['password'];
        $password = AppTools::passDecrypt($passwordToDec);


        $logger->log('--username--' . $username, sfFileLogger::INFO);
        $accounts = BankSavingsAccountTable::getForSelectAccounts();
        $logger->log('--$accounts count--' . count($accounts), sfFileLogger::INFO);
        if (!count($accounts)) {
            $logger->log('--$accounts reset--' . count($accounts), sfFileLogger::INFO);
            BankSavingsAccountTable::resetAccounts($url, $username, $password);
            $logger->log('--$accounts reset--' . $username . $password, sfFileLogger::INFO);
            $accounts = BankSavingsAccountTable::getForSelectAccounts();
        }
        for ($i = 1; $i <= 1; $i++) {
            foreach ($accounts as $account) {
                $logger->log('--start account--' . $account['account'], sfFileLogger::INFO);
                BankSavingsTable::init($url, $username, $password, $account, date("Y-m-d"));
                $logger->log('--end account--' . $account['account'], sfFileLogger::INFO);
            }
        }

        die('DONE');
    }

    /**
     * Банкны хуулгаар цэнэглэлт хийх
     * 
     * @param sfWebRequest $request
     * @return type
     */
    public function executeSavingsBankCharge(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        set_time_limit(3600);
        echo 'Started recharge: ' . date('Y-m-d H:i:s') .', IP: '.$_SERVER['REMOTE_ADDR']. '<br/><br/>';
        BankSavingsTable::recharge(null, $limit);
        echo 'Started : ' . date('Y-m-d H:i:s') . '<br/> MX<br/>';
        # Цэнэглэлт MX
        BankSavingsTable::mxCharge(null, $limit);
        # Цэнэглэлт PRODUCT
        BankSavingsTable::setAssignment($limit);
        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        return sfView::NONE;
    }

    /**
     * Банкны хуулгаар цэнэглэлт хийх
     * 
     * @param sfWebRequest $request
     * @return type
     */
    public function executeSavingsBankCallCharge(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        set_time_limit(3600);
        echo 'Started : ' . date('Y-m-d H:i:s') . '<br/><br/>';
        # Цэнэглэлт CALLPAYMENT
//        BankSavingsTable::callPayment();
        BankSavingsTable::bankPayment(null, null, $limit);
        BankSavingsTable::bankPaymentHBB(null, null, $limit);

        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        return sfView::NONE;
    }

    /**
     * Банкны хуулга татах хаан
     *
     * @param sfWebRequest $request
     * @return void
     * @throws Doctrine_Manager_Exception
     * @throws Doctrine_Query_Exception
     */
    public function executeKhaanBank(sfWebRequest $request)
    {
        $limit = sfConfig::get('app_bankpayment_khan_charge_limit');
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/executeKhaanBank.log'));
        $logger->log('--INIT--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        BankKhaanTable::init();
        $logger->log('--bankPaymentTopupSapc--=' . date('Y-m-d H:i:s'), sfFileLogger::INFO);
        BankKhaanTable::bankPaymentTopupSapc(null, $limit);
        $logger->log('--end--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        die('DONE');
    }


    /**
     * Банкны хуулгаар dealer цэнэгэлэлт хийх
     *
     * @param sfWebRequest $request
     * @return type
     */
    public function executeKhaanBankCharge(sfWebRequest $request)
    {
        $limit = sfConfig::get('app_bankpayment_khan_charge_limit');
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        set_time_limit(3600);  
        echo 'Started : ' . date('Y-m-d H:i:s') .', IP: '.$_SERVER['REMOTE_ADDR']. '<br/><br/>';
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeKhaanBankCharge.log'));
        $logger->log('--start--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        try {
            $logger->log('--bankPaymentTopupSapc--=' . date('Y-m-d H:i:s'), sfFileLogger::INFO);
            BankKhaanTable::bankPaymentTopupSapc(null, $limit);
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
        }
        try {
            $logger->log('--bankPayment--', sfFileLogger::INFO);
            BankKhaanTable::bankPayment(null, null, $limit);
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
        }
       try {
           $logger->log('--bankPaymentHBB--', sfFileLogger::INFO);
           BankKhaanTable::bankPaymentHBB(null, null, $limit);
       } catch (Exception $exc) {
           error_log($exc->getTraceAsString());
       }
        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        return sfView::NONE;
    }
    
    public function executeKhaanSetAssignment(sfWebRequest $request) {
        $limit = sfConfig::get('app_bankpayment_khan_assignment_limit');
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        set_time_limit(3600);
        echo 'Started : ' . date('Y-m-d H:i:s') .', IP: '.$_SERVER['REMOTE_ADDR']. '<br/><br/>';
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeKhaanBankDealerCharge.log'));
        $logger->log('--start--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        try {
            $logger->log('--recharge--', sfFileLogger::INFO);
            BankKhaanTable::recharge(null, null, $limit);
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
        }
        try {
            $logger->log('--setAssignment--', sfFileLogger::INFO);
            BankKhaanTable::setAssignment($limit);
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
        }
        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        return sfView::NONE;
    }


    /**
     * Банкны хуулгаар  цэнэгэлэлт хийх 
     * 
     * @param sfWebRequest $request
     * @return type
     */
    public function executeGolomtBankCharge(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        set_time_limit(3600);
        echo 'Started : ' . date('Y-m-d H:i:s') .', IP: '.$_SERVER['REMOTE_ADDR']. '<br/><br/>';
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeGolomtBankCharge.log'));
        $logger->log('--start--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        $logger->log('--bankPayment--', sfFileLogger::INFO);
        BankGolomtTable::bankPayment(null, null, $limit);
        $logger->log('--recharge--', sfFileLogger::INFO);
        BankGolomtTable::recharge(null, $limit);
        $logger->log('--bankPaymentUlusnet--', sfFileLogger::INFO);
        BankGolomtTable::bankPaymentUlusnet(null, $limit);
        $logger->log('--bankPaymentHBB--', sfFileLogger::INFO);
        BankGolomtTable::bankPaymentHBB(null, null, $limit);
        $logger->log('--setAssignment--', sfFileLogger::INFO);
        BankGolomtTable::setAssignment($limit);
        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        $logger->log('--end--', sfFileLogger::INFO);
        return sfView::NONE;
    }

    /**
     * Mobixpress хуулгаар цэнэглэлт хийх
     * 
     * @param sfWebRequest $request
     * @return type
     */
    public function executeMobixpressCharge(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        set_time_limit(3600);
        echo 'Started : ' . date('Y-m-d H:i:s') . '<br/><br/>';
        # Цэнэглэлт 
        BankMobixpressTable::recharge(null, $limit);
        BankCandyTable::recharge(null, $limit);
        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        return sfView::NONE;
    }

    /**
     * Банкны хуулга татах хас
     *
     * @param sfWebRequest $request
     * @return void
     */
    public function executeXacBank(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/executeXacBank.log'));
        $logger->log('--start--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        BankXacTable::init();
        $logger->log('--end init--=' . date('Y-m-d H:i:s'), sfFileLogger::INFO);
        $logger->log('--start bankPaymentTopupSapc--=' . date('Y-m-d H:i:s'), sfFileLogger::INFO);
        BankXacTable::bankPaymentTopupSapc(null, $limit);
        $logger->log('--end bankPaymentTopupSapc--=' . date('Y-m-d H:i:s'), sfFileLogger::INFO);
        die('DONE');
    }

    /**
     * Банкны хуулга татах хас
     * 
     * @param sfWebRequest $request
     * @return type
     */
    public function executeXacBankCharge(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        set_time_limit(3600);
        echo 'Started : ' . date('Y-m-d H:i:s') .', IP: '.$_SERVER['REMOTE_ADDR']. '<br/><br/>';
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeXacBankCharge.log'));
        $logger->log('--start--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        $logger->log('--bankPaymentTopupSapc--=', sfFileLogger::INFO);
        BankXacTable::bankPaymentTopupSapc(null, $limit);
        $logger->log('--recharge--=', sfFileLogger::INFO);
        BankXacTable::recharge(null, $limit);
        $logger->log('--bankPayment--=', sfFileLogger::INFO);
        BankXacTable::bankPayment(null, null, $limit);
        $logger->log('--setAssignment--=', sfFileLogger::INFO);
        BankXacTable::setAssignment($limit);
        $logger->log('--end--=', sfFileLogger::INFO);
        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        return sfView::NONE;
    }

    /**
     * Банкны хуулга татах TDB
     *
     * @param sfWebRequest $request
     * @return void
     * @throws Doctrine_Manager_Exception
     * @throws Doctrine_Query_Exception
     */
    public function executeTDBBank(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        echo 'Started : ' . date('Y-m-d H:i:s') . '<br/><br/>';
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeTDBBank.log'));
        $logger->log('--start--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        BankTdbTable::bankPayment(null, null, $limit);
        BankTdbTable::bankPaymentUlusnet(null, $limit);
        BankTdbTable::bankPaymentHBB(null, null, $limit);
        $logger->log('--end--=', sfFileLogger::INFO);
        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        die('DONE');
    }

    /**
     * Банкны хуулга цэнэглэлт КАПИТАЛ
     * 
     * @param sfWebRequest $request
     * @return type
     */
    public function executeTDBBankCharge(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        set_time_limit(3600);
        echo 'Started : ' . date('Y-m-d H:i:s') . '<br/><br/>';
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeTDBBankCharge.log'));
        $logger->log('--start--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        $logger->log('--recharge--=', sfFileLogger::INFO);
        BankTdbTable::recharge(null, $limit);
        $logger->log('--setAssignment--=', sfFileLogger::INFO);
        BankTdbTable::setAssignment($limit);
        $logger->log('--end--=', sfFileLogger::INFO);
        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        return sfView::NONE;
    }

    /**
     * Банкны хуулга татах КАПИТАЛ
     *
     * @param sfWebRequest $request
     * @return void
     */
    public function executeCapitalBank(sfWebRequest $request)
    {
        echo 'Started : ' . date('Y-m-d H:i:s') . '<br/><br/>';
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeCapitalBank.log'));
        $logger->log('--start--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        $logger->log('--initTranfer--=', sfFileLogger::INFO);
        BankCapitalTable::initTranfer();
        $logger->log('--init--=', sfFileLogger::INFO);
        BankCapitalTable::init();
        $logger->log('--end--=', sfFileLogger::INFO);
        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        die('DONE');
    }

    /**
     * Банкны хуулга цэнэглэлт КАПИТАЛ
     * 
     * @param sfWebRequest $request
     * @return type
     */
    public function executeCapitalBankCharge(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        set_time_limit(3600);
        echo 'Started : ' . date('Y-m-d H:i:s') . '<br/><br/>';
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeCapitalBankCharge.log'));
        $logger->log('--start--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        $logger->log('--bankPayment--=', sfFileLogger::INFO);
        BankCapitalTable::bankPayment(null, null, $limit);
        $logger->log('--setAssignment--=', sfFileLogger::INFO);
        BankCapitalTable::setAssignment($limit);
        $logger->log('--end--=', sfFileLogger::INFO);
        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        return sfView::NONE;
    }

    /**
     * @param sfWebRequest $request
     * @throws Doctrine_Manager_Exception
     */
    public function executeBankpayment(sfWebRequest $request)
    {
        $limit = sfConfig::get('app_bankpayment_khan_payment_limit');
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeBankpayment.log'));
        $logger->log('--start--=' . date('Y-m-d H:i:s').', IP: '.$_SERVER['REMOTE_ADDR'], sfFileLogger::INFO);
        $logger->log('--processPayment--=', sfFileLogger::INFO);
        BankpaymentTable::processPayment(null, $limit);
        $logger->log('--processPaymentUlusnet--=', sfFileLogger::INFO);
        BankpaymentTable::processPaymentUlusnet(null, $limit);
        $logger->log('--processPaymentMobinet--=', sfFileLogger::INFO);
        BankpaymentTable::processPaymentMobinet(null, $limit);
        $logger->log('--processVatNopayer--=', sfFileLogger::INFO);
        BankpaymentTable::processVatNopayer(null, $limit);
        $logger->log('--chargeLoyaltyApi--=', sfFileLogger::INFO);
        BankpaymentTable::chargeLoyaltyApi(null, null, $limit);
        if (date("H") == "07" && date("i") < "10") {
            $logger->log('--rolebackTemp--=', sfFileLogger::INFO);
            TransactionTable::rolebackTemp();
        }
        $logger->log('--end--=', sfFileLogger::INFO);
        die('done');
    }

    /**
     * Төлөлт шүүж хөрвүүлэх
     * 
     * @param sfWebRequest $request
     * @return sfView::NONE;
     */
    public function executeSetAssignment(sfWebRequest $request)
    {
        $limit = 50;
        $days = 3;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        if ($request->getParameter('days')) {
            $days = $request->getParameter('days');
        }
        set_time_limit(3600);
        echo nl2br("Started : " . date('Y-m-d H:i:s') .', IP: '.$_SERVER['REMOTE_ADDR']. "\n\n");
        echo TransactionTable::setAssignment($limit, $days);
        echo nl2br("\n\nDone : " . date('Y-m-d H:i:s'));
        return sfView::NONE;
    }

    /**
     * SMS рүү зарлага хийгдээгүй дилер цэнэглэлтүүдийг дахин дуудах
     *
     * @param sfWebRequest $request
     * @return void ::NONE;
     * @throws Doctrine_Query_Exception
     * @throws sfException
     * @throws sfStopException
     */
    public function executeReoutcome(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        header("Content-type: text/plain;charset=utf-8");
        set_time_limit(3600);
        echo nl2br("Started : " . date('Y-m-d H:i:s') . "\n\n");
        if (!$request->getParameter('dateFrom')) {
            $this->getRequest()->setParameter('dateFrom', date("Y-m-01"));
        }
        $this->getRequest()->setParameter('status', BankKhaanTable::STAT_FAILED_OUTCOME);
        $this->getRequest()->setParameter('page', 1);
        $rows = BankKhaanTable::getList(array(BankKhaanAccountTable::ACCOUNT_DEALER, BankKhaanAccountTable::ACCOUNT_DEALER_MOBICOM));
        foreach ($rows as $bankKhaan) {
            echo '<br>' . $bankKhaan->getStatus();
            if ($bankKhaan->canReOutcome()) {
                $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/dealer/process-' . date("Ymd") . '.log'));
                $dealerAgent = DealerGateway::findDealerByMobile($bankKhaan->charge_mobile, $logger);
                if (!$dealerAgent) {
                    $dealer = DealerCharge::getDealer($bankKhaan->charge_mobile);
                }
                if (!$dealer && !$dealerAgent) {
                    echo '<br>' . '[' . $bankKhaan->charge_mobile . '] ' . 'Уучлаарай, дилер олдсонгүй';
                    continue;
                }
                $outcomeOrderId = BankKhaanTable::reoutcome($bankKhaan, $dealer, date('Y-m-d', strtotime($bankKhaan->getCreatedAt())), $dealerAgent);
                if ($outcomeOrderId) {
                    $bankKhaan->status = BankKhaanTable::STAT_SUCCESS;
                    $bankKhaan->transfer_sap = 0;
                    $bankKhaan->sales_order_id = $outcomeOrderId;
                    $bankKhaan->save();
                    sleep(1);
                    echo '<br>' . '[' . $bankKhaan->id . '] ' . 'Зарлагыг амжилттай үүсгэлээ. ' . BankKhaanTable::getStatusName($bankKhaan->status, BankKhaanTable::TYPE_DEALER) . ' төлөвт орууллаа';
                } else {
                    echo '<br>' . '[' . $bankKhaan->id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![1]';
                }
            } else {
                echo '<br>' . '[' . $bankKhaan->id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![2]';
            }
        }
        $rows = BankTdbTable::getList(array(BankTdbTable::ACCOUNT_DEALER, BankTdbTable::ACCOUNT_DEALER_MOBICOM));
        foreach ($rows as $bankTDB) {
            echo '<br>' . $bankTDB->getStatus();
            if ($bankTDB->canReOutcome()) {
                $dealer = DealerCharge::getDealer($bankTDB->charge_mobile);

                $outcomeOrderId = BankTdbTable::reoutcome($bankTDB, $dealer, date('Y-m-d', strtotime($bankTDB->getCreatedAt())));
                if ($outcomeOrderId) {
                    $bankTDB->status = BankTdbTable::STAT_SUCCESS;
                    $bankTDB->sales_order_id = $outcomeOrderId;
                    $bankTDB->save();
                    sleep(1);
                    echo '<br>' . '[' . $bankTDB->id . '] ' . 'Зарлагыг амжилттай үүсгэлээ. ' . BankTdbTable::getStatusName($bankTDB->status, BankTdbTable::TYPE_DEALER) . ' төлөвт орууллаа';
                } else {
                    echo '<br>' . '[' . $bankTDB->id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![1]';
                }
            } else {
                echo '<br>' . '[' . $bankTDB->id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![2]';
            }
        }
        $rows = BankGolomtTable::getList(array(BankGolomtTable::ACCOUNT_DEALER, BankGolomtTable::ACCOUNT_DEALER_MOBICOM));
        foreach ($rows as $bankGolomt) {
            echo '<br>' . $bankGolomt->getStatus();
            if ($bankGolomt->canReOutcome()) {
                $dealer = DealerCharge::getDealer($bankGolomt->charge_mobile);

                $outcomeOrderId = BankGolomtTable::reoutcome($bankGolomt, $dealer, date('Y-m-d', strtotime($bankGolomt->getCreatedAt())));
                if ($outcomeOrderId) {
                    $bankGolomt->status = BankGolomtTable::STAT_SUCCESS;
                    $bankGolomt->sales_order_id = $outcomeOrderId;
                    $bankGolomt->transfer_sap = 0;
                    $bankGolomt->save();
                    sleep(1);
                    echo '<br>' . '[' . $bankGolomt->id . '] ' . 'Зарлагыг амжилттай үүсгэлээ. ' . BankGolomtTable::getStatusName($bankGolomt->status, BankGolomtTable::TYPE_DEALER) . ' төлөвт орууллаа';
                } else {
                    echo '<br>' . '[' . $bankGolomt->id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![1]';
                }
            } else {
                echo '<br>' . '[' . $bankGolomt->id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![2]';
            }
        }
        $rows = BankXacTable::getList(array(BankXacAccountTable::ACCOUNT_DEALER, BankXacAccountTable::ACCOUNT_DEALER_MOBICOM));
        foreach ($rows as $bank) {
            echo '<br>' . $bank->getStatus();
            if ($bank->canReOutcome()) {
                $dealer = DealerCharge::getDealer($bank->charge_mobile);

                $outcomeOrderId = BankXacTable::reoutcome($bank, $dealer, date('Y-m-d', strtotime($bank->getCreatedAt())));
                if ($outcomeOrderId) {
                    $bank->status = BankXacTable::STAT_SUCCESS;
                    $bank->sales_order_id = $outcomeOrderId;
                    $bank->transfer_sap = 0;
                    $bank->save();
                    sleep(1);
                    echo '<br>' . '[' . $bank->id . '] ' . 'Зарлагыг амжилттай үүсгэлээ. ' . BankXacTable::getStatusName($bank->status, BankXacTable::TYPE_DEALER) . ' төлөвт орууллаа';
                } else {
                    echo '<br>' . '[' . $bank->id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![1]';
                }
            } else {
                echo '<br>' . '[' . $bank->id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![2]';
            }
        }
        $rows = BankSavingsTable::getList(0, array(BankSavingsAccountTable::ACCOUNT_DEALER));
        foreach ($rows as $bankSavings) {
            echo '<br>' . $bankSavings->getStatus();
            if ($bankSavings->canReOutcome()) {
                $dealer = DealerCharge::getDealer($bankSavings->charge_mobile);

                $outcomeOrderId = BankSavingsTable::reoutcome($bankSavings, $dealer, date('Y-m-d', strtotime($bankSavings->getCreatedAt())));
                if ($outcomeOrderId) {
                    $bankSavings->status = BankSavingsTable::STAT_SUCCESS;
                    $bankSavings->sales_order_id = $outcomeOrderId;
                    $bankSavings->transfer_sap = 0;
                    $bankSavings->save();
                    sleep(1);
                    echo '<br>' . '[' . $bankSavings->id . '] ' . 'Зарлагыг амжилттай үүсгэлээ. ' . BankSavingsTable::getStatusName($bankSavings->status, BankSavingsAccountTable::ACCOUNT_DEALER) . ' төлөвт орууллаа';
                } else {
                    echo '<br>' . '[' . $bankSavings->id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![1]';
                }
            } else {
                echo '<br>' . '[' . $bankSavings->id . '] ' . 'Уучлаарай, дахин зарлага хийх боломжгүй байна![2]';
            }
        }
        echo nl2br("\n\nDone : " . date('Y-m-d H:i:s'));
        if ($request->getReferer()) {
            $this->redirect($request->getReferer());
        }
        die();
    }

    /**
     * Candy CASHIN цэнэглэлт
     * Candy LOAN цэнэглэлт
     * 
     * @param sfWebRequest $request
     * @return sfView::NONE;
     */
    public function executeCandyLoan(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        set_time_limit(3600);
        echo 'Started : ' . date('Y-m-d H:i:s') . '<br/><br/>';
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeCandyLoan.log'));
        $logger->log('--start--', sfFileLogger::INFO);
        $logger->log('--BankKhaanTable::loyaltyBankpayment--', sfFileLogger::INFO);
        BankKhaanTable::loyaltyBankpayment(null, $limit);
        $logger->log('--BankTdbTable::loyaltyBankpayment--', sfFileLogger::INFO);
        BankTdbTable::loyaltyBankpayment(null, $limit);
        $logger->log('--BankGolomtTable::loyaltyBankpayment--', sfFileLogger::INFO);
        BankGolomtTable::loyaltyBankpayment(null, $limit);
        $logger->log('--BankXacTable::loyaltyBankpayment--', sfFileLogger::INFO);
        BankXacTable::loyaltyBankpayment(null, $limit);
        # belen bish
        $logger->log('--BankSavingsTable::loyaltyBankpayment--', sfFileLogger::INFO);
        BankSavingsTable::loyaltyBankpayment(null, $limit);
//        $logger->log('--BankCapitalTable::loyaltyBankpayment--', sfFileLogger::INFO);
//        BankCapitalTable::loyaltyBankpayment();

        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        return sfView::NONE;
    }

    /**
     * Candy CASHIN цэнэглэлт
     * 
     * @param sfWebRequest $request
     * @return sfView::NONE;
     */
    public function executeCandyCashin(sfWebRequest $request)
    {
        set_time_limit(3600);
//        echo 'Started : ' . date('Y-m-d H:i:s') . '<br/><br/>';
//        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeCandyCashin.log'));
//        $logger->log('--start--', sfFileLogger::INFO);
//        $logger->log('--BankKhaanTable::chargeLoyaltyCashin--', sfFileLogger::INFO);
//        BankKhaanTable::chargeLoyaltyCashin();
//        $logger->log('--BankTdbTable::chargeLoyaltyCashin--', sfFileLogger::INFO);
//        BankTdbTable::chargeLoyaltyCashin();
//        $logger->log('--BankGolomtTable::chargeLoyaltyCashin--', sfFileLogger::INFO);
//        BankGolomtTable::chargeLoyaltyCashin();
//        $logger->log('--BankXacTable::chargeLoyaltyCashin--', sfFileLogger::INFO);
//        BankXacTable::chargeLoyaltyCashin();


        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        return sfView::NONE;
    }

    /**
     * Банкны хуулга татах капитрон
     *
     * @param sfWebRequest $request
     * @return void
     */
    public function executeCapitron(sfWebRequest $request)
    {
        if ($request->getParameter('date')) {
            $date = $request->getParameter('date');
        }
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/my-khaan.log'));
        $logger->log('--INIT--=' . date('Y-m-d H:i:s'), sfFileLogger::INFO);
        BankCapitronTable::init($date);
        die('DONE');
    }

    /**
     * Банкны хуулгаар callpayment цэнэгэлэлт хийх 
     * 
     * @param sfWebRequest $request
     * @return type
     */
    public function executeCapitronBankCharge(sfWebRequest $request)
    {
        $limit = 50;
        if ($request->getParameter('limit')) {
            $limit = $request->getParameter('limit');
        }
        set_time_limit(3600);
        echo 'Started : ' . date('Y-m-d H:i:s') . '<br/><br/>';
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/executeCapitronCharge.log'));
        $logger->log('--start--', sfFileLogger::INFO);
        $logger->log('--bankPayment--', sfFileLogger::INFO);
        BankCapitronTable::bankPayment(null, null, $limit);
        $logger->log('--setAssignment--', sfFileLogger::INFO);
        BankCapitronTable::setAssignment($limit);
        echo '<br/><br/>Done : ' . date('Y-m-d H:i:s');
        return sfView::NONE;
    }

    /**
     * @param sfWebRequest $request
     */
    public function executeTestCron(sfWebRequest $request)
    {
        BankKhaanTable::bankPayment(null, null, 50);
        die();
    }

    /**
     * @param sfWebRequest $request
     */
    public function executeTestPostgw(sfWebRequest $request)
    {
        var_dump(PostGateway::getPostPhoneInfo($request->getParameter('isdn')));
        die();
    }

    public function executeKhaanRecordUpdate() {
        BankKhaanTable::recordUpdater();
    }

    public function executeKhaanInitStandalone(sfWebRequest $request) {
        ini_set('memory_limit','1G');
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        BankKhaanTable::init();
        die();
    }

    public function executeKhaanInitByDate(sfWebRequest $request) {
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        ini_set('memory_limit','-1');
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/cron/khaanDailyTransactions.log'));

        $date = $request->getParameter('date');
        $accountList = KhaanCorpgw::getAccountList();
        $accountListWithDate = array();
        foreach ($accountList as $account) {
            $account['date'] = $date;
            $accountListWithDate[] = $account;
        }
        BankKhaanTable::initByDate($accountListWithDate);
        die();
    }

    /**
     * @param sfWebRequest $request
     * @throws Doctrine_Manager_Exception
     * @throws Doctrine_Query_Exception
     */
    public function executeTestChargeTable(sfWebRequest $request)
    {
        $bankOrderId = (float) $request->getParameter('bank_id');
        $vendorId = (float) $request->getParameter('vendor_id');
        $number = (float) $request->getParameter('number');
        switch ($vendorId) {
            case VendorTable::BANK_KHAAN:
                $bank = BankKhaanTable::retrieveByPK($bankOrderId);
                $result = BankKhaanTable::loyaltyBankpayment($bank, $number);
                break;
            case VendorTable::BANK_SAVINGS:
                $bank = BankSavingsTable::retrieveByPK($bankOrderId);
                $result = BankSavingsTable::loyaltyBankpayment($bank, $number);
                break;
            case VendorTable::BANK_XAC:
                $bank = BankXacTable::retrieveByPK($bankOrderId);
                $result = BankXacTable::loyaltyBankpayment($bank, $number);
                break;
            case VendorTable::GOLOMT:
                $bank = BankGolomtTable::retrieveByPK($bankOrderId);
                $result = BankGolomtTable::loyaltyBankpayment($bank, $number);
                break;
            case VendorTable::BANK_TDB:
                $bank = BankTdbTable::retrieveByPK($bankOrderId);
                $result = BankTdbTable::loyaltyBankpayment($bank, $number);
                break;
            case VendorTable::BANK_CAPITAL:
                $bank = BankCapitalTable::retrieveByPK($bankOrderId);
                $result = BankCapitalTable::loyaltyBankpayment($bank, $number);
                break;
            default:
                $result = false;
                break;
        }
        print_r($result);
        die();
    }

    /**
     * @param sfWebRequest $request
     * @throws Doctrine_Manager_Exception
     * @throws Doctrine_Query_Exception
     */
    public function executeTestDealer(sfWebRequest $request)
    {
        $rows = BankKhaanTable::updateForTransactions(50);
        foreach ($rows as $row) {
            echo $row['id'] . '-' . $row['order_id'] . '<br>';
        }
        die('done');
        die('done');

        $amount = (int) $request->getParameter('amount');
        if (!$amount) {
            die('DUN oruulna uu');
        }
        echo 'OD';
        $pdo = Doctrine_Manager::connection()->getDbh();
        $sql = "UPDATE `bankgw`.`bank_khaan` SET `order_amount` = '$amount',`status` = '1' WHERE `bank_khaan`.`id` =82512;";
        $affectedRows = $pdo->exec($sql);
        if ($affectedRows == 0) {
            echo 'FAILED<br>';
        } else {
            echo 'SUCCESS<br>';
        }
        echo date('Y-m-d H:i:s');
        die();
    }

    //$randBinary = bin2hex(openssl_random_pseudo_bytes(32));

    public function executePasswordEncrypt(sfWebRequest $request)
    {
        //SET PASSWORD VARIABLE VALUE TO ENCRYPT
        echo "PASTE PASSWORD AND CLICK ENCRYPT!";
        echo '<form name="form" action="" method="post">
                 <h2>Password: 
                 <input type="text" name="password" id="password">
                 <input type="submit" value="Encrypt" name="encrypt"><br>
                 </h2>
              </form>';
        if (isset($_REQUEST['encrypt'])) {
            $password = $_POST['password'];
            if ($password != "") {
                echo "Encrypted value: " . "<br>";
                echo AppTools::passEncrypt($password) . "<br><br>";
            } else {
                echo "Password field required.";
            }
            unset($_POST);
            unset($_REQUEST);
        } else {
            echo "Fill this form and click on Encrypt.";
        }
        die();
    }

}
