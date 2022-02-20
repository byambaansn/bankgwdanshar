<?php

function getMenu()
{
    // current host
    $host = !empty($_SERVER['HTTPS']) ? "https://" . $_SERVER['SERVER_NAME'] : "http://" . $_SERVER['SERVER_NAME'];
    $host .= "/manage";

    // current user
    $user = sfContext::getInstance()->getUser();

    // user credentials
    $credentials = $user->getCredentials();
    // menus
    $menus = array();

    // homepage
    $menus['user']['main'] = array('Мэдээлэл', $host);
    $menus['user']['sub'] = array();

    // observ
    if (in_array('observ', $credentials) || in_array('transaction_view', $credentials)) {
        // savings
        $menus['savings']['main'] = array('Төрийн банк', '@bank_savings_list');
        $menus['savings']['sub']['dealer'] = array('Дилер цэнэглэлт', '@bank_savings_list', 'list');
        // savings
        $menus['dealer']['main'] = array('DEALER', '@bank_dealer_list');
        $menus['dealer']['sub']['dealer'] = array('Дилер цэнэглэлт', '@bank_dealer_list', 'list');
        $menus['dealer']['sub']['dealer_merge'] = array('Нэгтгэсэн тайлан', '@bank_dealer_merge_report', 'list');
        // Khaan
        $menus['khaan']['main'] = array('Хаан', '@bank_khaan_list');
        $menus['khaan']['sub']['dealer'] = array('Дилер цэнэглэлт', '@bank_khaan_list', 'list');
        // Golomt
        $menus['golomt']['main'] = array('Голомт', '@bank_golomt_list');
        $menus['golomt']['sub']['dealer'] = array('Дилер цэнэглэлт', '@bank_golomt_list', 'list');
        // Xac
        $menus['xac']['main'] = array('Хас', '@bank_xac_list');
        $menus['xac']['sub']['dealer'] = array('Дилер цэнэглэлт', '@bank_xac_list', 'list');
        // TDB
        $menus['tdb']['main'] = array('TDB', '@bank_tdb_list');
        $menus['tdb']['sub']['dealer'] = array('Дилер цэнэглэлт', '@bank_tdb_list', 'list');
        // candy
        $menus['candy']['main'] = array('MONPAY', '@bank_candy_dealer');
        $menus['candy']['sub']['dealer'] = array('Дилер цэнэглэлт', '@bank_candy_dealer', 'list');
    }

    // callpayment observ
    if (in_array('call_payment_observ', $credentials)) {
        if (!isset($menus['savings']['main'])) {
            $menus['savings']['main'] = array('Төрийн банк', '@bank_savings_call_payment_list');
        }
        $menus['savings']['sub']['callpayment'] = array('Төлбөр төлөлт', '@bank_savings_call_payment_list', 'list');
        if (!isset($menus['khaan']['main'])) {
            $menus['khaan']['main'] = array('Хаан', '@bank_khaan_call_payment_list');
        }
        $menus['khaan']['sub']['callpayment'] = array('Төлбөр төлөлт', '@bank_khaan_call_payment_list', 'list');
        if (!isset($menus['golomt']['main'])) {
            $menus['golomt']['main'] = array('Голомт', '@bank_golomt_call_payment_list');
        }
        $menus['golomt']['sub']['callpayment'] = array('Төлбөр төлөлт', '@bank_golomt_call_payment_list', 'list');
        if (!isset($menus['xac']['main'])) {
            $menus['xac']['main'] = array('Хас', '@bank_xac_call_payment_list');
        }
        $menus['xac']['sub']['callpayment'] = array('Төлбөр төлөлт', '@bank_xac_call_payment_list', 'list');
        #
        if (!isset($menus['capital']['main'])) {
            $menus['capital']['main'] = array('Капитал', '@bank_capital_call_payment_list');
        }
        $menus['capital']['sub']['callpayment'] = array('Төлбөр төлөлт', '@bank_capital_call_payment_list', 'list');
        if (!isset($menus['tdb']['main'])) {
            $menus['tdb']['main'] = array('TDB', '@bank_tdb_call_payment_list');
        }
        $menus['tdb']['sub']['callpayment'] = array('Төлбөр төлөлт', '@bank_tdb_call_payment_list', 'list');
    }
    // MX observ
    if (in_array('mx_observ', $credentials)) {
        if (!isset($menus['savings']['main'])) {
            $menus['savings']['main'] = array('Төрийн банк', '@bank_savings_mx_list');
        }
        $menus['savings']['sub']['mx'] = array('MX гүйлгээ', '@bank_savings_mx_list', 'list');
        // mobixpress
        $menus['mobixpress']['main'] = array('Mobixpress', '@bank_mobixpress_list');
        $menus['mobixpress']['sub']['dealer'] = array('Дилер цэнэглэлт', '@bank_mobixpress_list', 'list');
    }
    // transaction_payment
    if (in_array('transaction_payment', $credentials)) {
        $menus['transaction']['main'] = array('Банк хуулга', '@transaction_list?bank_date=on');
        $menus['transaction']['sub']['trans'] = array('Татагдсан хуулга', '@transaction_list?bank_date=on', 'list');
        $menus['transaction']['sub']['payment'] = array('Төлбөр болсон', '@transaction_payment', 'list');
        $menus['transaction']['sub']['uo'] = array('УО', '@transaction_uo', 'list');
        $menus['transaction']['sub']['type'] = array('Төлбөрийн төрөл', '@transaction_type', 'list');
        $menus['transaction']['sub']['bank_account'] = array('Данс', '@transaction_bank_account', 'list');
        $menus['transaction']['sub']['config_assignment'] = array('Төлөлт шүүх тохиргоо', '@transaction_config_assignment', 'list');
    }
    // MX observ
    if (in_array('transaction_view', $credentials)) {
        // Khaan
        $menus['khaan']['main'] = array('Хаан', '@bank_khaan_list');
        $menus['khaan']['sub']['dealer'] = array('Дилер цэнэглэлт', '@bank_khaan_list', 'list');
    }
    // Bankpayment
    if (in_array('bankpayment_cx', $credentials)) {
        #Bankpayment
        $menus['bankpayment']['main'] = array('Bankpayment', '@bankpayment_call_payment');
        $menus['bankpayment']['sub']['call_payment'] = array('Яриа', '@bankpayment_call_payment', 'list');
        $menus['bankpayment']['sub']['mobinetpayment'] = array('Мобинэт', '@bankpayment_mobinet', 'list');
    }
    if (in_array('bankpayment_ulusnet', $credentials)) {
        #Bankpayment
        if (!isset($menus['bankpayment']['main'])) {
            $menus['bankpayment']['main'] = array('Bankpayment', '@bankpayment_ulusnet');
        }
        $menus['bankpayment']['sub']['ulusnetpayment'] = array('Улуснэт', '@bankpayment_ulusnet', 'list');
        $menus['bankpayment']['sub']['ulusnetconfig'] = array('Улуснэт тохиргоо', '@bankpayment_ulusnet_card', 'list');
    }
    if (in_array('bankpayment_mobinet', $credentials)) {
        #Bankpayment
        if (!isset($menus['bankpayment']['main'])) {
            $menus['bankpayment']['main'] = array('Bankpayment', '@bankpayment_ulusnet');
        }
        $menus['bankpayment']['sub']['mobinet_prepaid'] = array('Мобинэт Prepaid', '@bankpayment_mobinet_prepaid', 'list');
    }
    if (in_array('bankpayment_ussd', $credentials)) {
        if (!isset($menus['bankpayment']['main'])) {
            $menus['bankpayment']['main'] = array('Bankpayment', '@bankpayment_ussd');
        }
        $menus['bankpayment']['sub']['ussd'] = array('USSD', '@bankpayment_ussd', 'list');
    }
    if (in_array('candy_loan', $credentials)) {
        $menus['candy']['main'] = array('MONPAY', '@bank_candy_list');
        $menus['candy']['sub']['candy'] = array('MONPAY цэнэглэлт', '@bank_candy_list', 'list');
    }
    if (in_array('transaction_payment_mobifinance', $credentials)) {
        $menus['transaction']['main'] = array('Банк хуулга', '@transaction_list?bank_date=on');
        $menus['transaction']['sub']['trans'] = array('Татагдсан хуулга', '@transaction_list?bank_date=on', 'list');
    }
    if (in_array('bankpayment_refund_report', $credentials)) {
        $menus['refund']['main'] = array('Гүйлгээний буцаалт', '@refund_list');
//        $menus['refund']['sub']['refund'] = array('Буцаалтын тайлан', '@refund_list', 'list');
    }
    if (in_array('bankpayment_block_date', $credentials)) {
        if (!isset($menus['bankpayment']['main'])) {
            $menus['bankpayment']['main'] = array('Bankpayment', '@bankpayment_block_date');
        }
        $menus['bankpayment']['sub']['block_date'] = array('Хаалт', '@bankpayment_block_date', 'list');
    }
    if (in_array('bankpayment_payment_report', $credentials)) {
        if (!isset($menus['bankpayment']['main'])) {
            $menus['bankpayment']['main'] = array('Bankpayment', '@bankpayment_payment_report');
        }
        $menus['bankpayment']['sub']['payment_report1'] = array('Төлбөрийн тайлан', '@bankpayment_payment_report', 'list');
    }
    return $menus;
}
