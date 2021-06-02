<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CallPayment
 *
 * @author Belbayar
 */
class CallPayment
{

    const CHARGE_URL = 'http://bankgw/ora/bankgw/callpayment.php';
    //?order_date=2014-05-28&order_amount=1000&order_p=qwerruygdfdzxlkjdg&bank_account=123456798&order_type=ADD&order_id=123
    const key = 'PyAh&3u6ta'; // '{0x50, 0x79, 0x41, 0x01, 0x05, 0x68, 0x26, 0x11, 0x33, 0x75, 0x11, 0x01, 0x36, 0x01, 0x74, 0x61}';
    const iv = '@	T)1R8`51H#'; //'{0x40, 0x09, 0x54, 0x01, 0x29, 0x14, 0x31, 0x01, 0x52, 0x38, 0x60, 0x35, 0x31, 0x07, 0x48, 0x23}';

    public static function charge($bank)
    {
        $transDate = $bank['transDate'];
        $amount = (double) $bank['amount'];
        $transValue = urlencode(self::encryptData($bank['transValue']));
        $transAccount = $bank['transAccount'];
        $transType = $bank['transType'];
        $transNumber = $bank['transNumber'];
        $bankType = $bank['bankType'];

        // буцаах утга
        $return = array(
            'success' => FALSE,
            'log_request' => self::CHARGE_URL . '?order_date=' . $transDate . '&order_amount=' . $amount . '&order_p=' . $transValue . '&bank_account=' . $transAccount . '&order_type=' . $transType . '&order_id=' . $transNumber . '&bank=' . $bankType,
            'log_response' => '',
            'error_code' => 0
        );

        // цэнэглэх
        $b = new sfWebBrowser();
        $b->get($return['log_request']);
        $responseText = (string) $b->getResponseText();
        if (!$responseText) {
            $return['success'] = FALSE;
            $return['log_response'] = print_r(libxml_get_errors(), TRUE);

            return $return;
        }
        $result = json_decode($responseText, true);


        if ($result['res'] == 'success') {
            $return['success'] = TRUE;
            $return['log_response'] = 'success';
        } else {
            $return['success'] = FALSE;
            $return['log_response'] = $responseText;
            return $return;
        }
        return $return;
    }

    /**
     * encryptData
     * 
     * @return array
     */
    public static function encryptData($data)
    {
        //apply pkcs7 padding
        $value_length = strlen($data);
        $padding = 16 - ($value_length % 16);
        $data .= str_repeat(chr($padding), $padding);

        return AppTools::encryptRJ128(self::key, self::iv, $data);
    }

    private static function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * decryptAllData
     * 
     * @return array
     */
    public static function decryptData($data)
    {
        return AppTools::cp1251_utf8(AppTools::decryptRJ128(self::key, self::iv, $data));
    }

    public static function decrypt($sStr, $sKey = '8h26M$7+itnVC~b$')
    {
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $sKey, base64_decode($sStr), MCRYPT_MODE_CBC);
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);

        return $decrypted;
    }

}

?>
