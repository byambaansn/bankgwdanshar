<?php

class AppTools
{

    public static function utime()
    {
        $time = explode(" ", microtime());
        $usec = (int) ($time[0] * 1000);
        $sec = (int) ($time[1]);
        return $sec . $usec;
    }

    public static function utf8_substr($str, $from, $len)
    {
        # utf8 substr
        return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' .
                '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str);
    }

    public static function mb_strlen($t, $encoding = 'UTF-8')
    {
        /* --enable-mbstring */
        if (function_exists('mb_strlen')) {
            return mb_strlen($t, $encoding);
        } else {
            return strlen(utf8_decode($t));
        }
    }

    public static function decryptRJ128($key, $iv, $string_to_decrypt)
    {
        $string_to_decrypt = base64_decode($string_to_decrypt);
        $rtn = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $string_to_decrypt, MCRYPT_MODE_CBC, $iv);
        $rtn = rtrim($rtn, "\4");
        return($rtn);
    }

    public static function encryptRJ128($key, $iv, $string_to_encrypt)
    {
        $rtn = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string_to_encrypt, MCRYPT_MODE_CBC, $iv);
        $rtn = base64_encode($rtn);
        return($rtn);
    }

    public static function cp1251_utf8($word)
    {

        $cyr_lower_chars = array(
            'е', 'щ', 'ф', 'ц', 'у', 'ж', 'э',
            'н', 'г', 'ш', 'ү', 'з', 'к', 'ъ',
            'й', 'ы', 'б', 'ө', 'а', 'х', 'р',
            'о', 'л', 'д', 'п', 'я', 'ч', 'ё',
            'с', 'м', 'и', 'т', 'ь', 'в', 'ю',);

        $latin_lower_chars = array(
            'å', 'ù', 'ô', 'ö', 'ó', 'æ', 'ý',
            'í', 'ã', 'ø', '¿', 'ç', 'ê', 'ú',
            'é', 'û', 'á', 'º', 'à', 'õ', 'ð',
            'î', 'ë', 'ä', 'ï', 'ÿ', '÷', '¸',
            'ñ', 'ì', 'è', 'ò', 'ü', 'â', 'þ',);

        $cyr_upper_chars = array(
            'Е', 'Щ', 'Ф', 'Ц', 'У', 'Ж', 'Э',
            'Н', 'Г', 'Ш', 'Ү', 'З', 'К', 'Ъ',
            'Й', 'Ы', 'Б', 'Ө', 'А', 'Х', 'Р',
            'О', 'Л', 'Д', 'П', 'Я', 'Ч', 'Ё',
            'С', 'М', 'И', 'Т', 'Ь', 'В', 'Ю',);

        $latin_upper_chars = array(
            'Å', 'Ù', 'Ô', 'Ö', 'Ó', 'Æ', 'Ý',
            'Í', 'Ã', 'Ø', '¯', 'Ç', 'Ê', 'Ú',
            'É', 'Û', 'Á', 'ª', 'À', 'Õ', 'Ð',
            'Î', 'Ë', 'Ä', 'Ï', 'ß', '×', '¨',
            'Ñ', 'Ì', 'È', 'Ò', 'Ü', 'Â', 'Þ',);

        //replacing lower cyrillic
        $word = str_replace($latin_lower_chars, $cyr_lower_chars, $word);
        //replacing upper cyrillic
        $word = str_replace($latin_upper_chars, $cyr_upper_chars, $word);

        return $word;
    }

    /**
     * Converts an XML string into php Array
     * returns null if input string is empty
     * 
     * JSON wins!
     * 
     * @author Atu
     * @param string $xmlStr
     * @return Array
     */
    public static function xmlToArray($xmlStr, $isUrl = false)
    {
        if (!$xmlStr) {
            return null;
        }

        if ($isUrl === true) {
            $xml = simplexml_load_file($xmlStr, "SimpleXMLElement", LIBXML_NOCDATA);
        } else {
            $xml = simplexml_load_string($xmlStr);
        }

        $json = json_encode($xml);
        return json_decode($json, TRUE);
    }

    /**
     * Converts an Array into XML string
     * returns null if input array is empty
     * 
     * @author Duurenbayar
     * @param array $array
     * @return SimpleXMLElement
     */
    public static function arrayToXml($array, $xml = null)
    {
        $array = (array) $array;

        if ($xml == null) {
            $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><response></response>");
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild("$key");
                    self::arrayToXml($value, $subnode);
                } else {
                    self::arrayToXml($value, $xml);
                }
            } else {
                $xml->addChild("$key", "$value");
            }
        }

        return $xml;
    }

    public static function getCurrentUrl()
    {
        $pageURL = 'http';
        if (!empty($_SERVER["HTTPS"])) {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public static function getQueryString($url, $deny = null)
    {
        $deny = (array) $deny;

        $params = (array) $_REQUEST;

        foreach ($deny as $d) {
            if (array_key_exists($d, $params)) {
                unset($params[$d]);
            }
        }

        if (array_key_exists('symfony', $params)) {
            unset($params['symfony']);
        }

        $q = http_build_query($params);

        return $url . ($q ? (preg_match('/\?/', $url) ? '&' : '?') . $q : '');
    }

    public static function ExportCsv($data, $fileName = '', $canUnLink = false, $writeOnly = false, $utf8 = true)
    {
        $exportName = 'report';
        if (strlen($fileName)) {
            $exportName = $fileName;
        }
        $filename = sfConfig::get('sf_upload_dir') . '/' . $exportName . '.csv';

        $fp = fopen($filename, "w"); // $fp is now the file pointer to file $filename
        if ($fp) {
            if ($utf8) {
                fwrite($fp, pack("CCC", 0xef, 0xbb, 0xbf)); //save UTF-8
            }

            fwrite($fp, $data);                        //Write information to the file
            fclose($fp);
            if ($writeOnly)
                return;
            //echo "File saved successfully";
            header('Content-Description: File Transfer');
            header("content-type:application/csv;charset=UTF-8");

//      header("Content-Type: application/vnd.ms-excel; charset=UTF-16LE");
            //header('Content-Type: application/octet-stream');// charset=utf-16
            header('Content-Disposition: attachment; filename=' . basename($filename));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0,pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            if (ob_get_length() > 0) {
                ob_clean();
            }
            flush();
            readfile($filename);
            # delete file
            if (!$canUnLink) {
                @unlink($filename);
            }
        } else {
            echo "Error saving file!";
        }
    }

    /**
     * Array fix
     * 
     * @param array $array
     * @param bool $identifyNumbers unset if not number
     * @author Atu
     * @return array
     */
    public static function arrayFixOneValue($array)
    {
        if (isset($array[0]) && is_array($array)) {
            $fixedArray = $array;
        } else {
            $fixedArray[0] = $array;
        }
        return $fixedArray;
    }

    public static function isDate($date, $format = 'Y-m-d')
    {
        $year = 0;
        $month = 0;
        $day = 0;

        switch ($format) {
            case 'Y-m-d':
                $arr = explode('-', $date);
                break;
            case 'Y/m/d':
                $arr = explode('/', $date);
                break;
            default:
                $arr = explode('-', $date);
                break;
        }

        if (count($arr) != 3)
            return false;

        $year = (int) $arr[0];
        $month = (int) $arr[1];
        $day = (int) $arr[2];

        return checkdate($month, $day, $year);
    }

    public static function isContractNumber($number)
    {
        $regex = '/(^[12346][0-9]{7}$)/';
        $matches = array();
        preg_match($regex, $number, $matches);
        if ($matches) {
            return true;
        }

        return false;
    }

    public static function isNumber($number)
    {
        $regex = '/(^[9][954][0-9]{6}$)|(^85[0-9]{6}$)/';
        $matches = array();
        preg_match($regex, $number, $matches);
        if ($matches) {
            return true;
        }

        return false;
    }

    public static function isNumberMobinet($number)
    {
        $regex = '/(^[7][75][0-9]{6}$)|(^591[0-9]{5}$)/';
        $matches = array();
        preg_match($regex, $number, $matches);
        if ($matches) {
            return true;
        }

        return false;
    }

    public static function isMobinetHHB($number)
    {
        $regex = '/(^591[0-9]{5}$)/';
        $matches = array();
        preg_match($regex, $number, $matches);
        if ($matches) {
            return true;
        }

        return false;
    }
    
    public static function isNumberVoo($number)
    {
        $regex = '/(^491[0-9]{5}$)/';
        $matches = array();
        preg_match($regex, $number, $matches);
        if ($matches) {
            return true;
        }

        return false;
    }

    public static function isPostHbbContract($contract)
    {
        $regex = '/(^BSCS-[0-9]+$)/';
        $matches = array();
        preg_match($regex, $contract, $matches);
        if ($matches) {
            return true;
        }

        return false;
    }

    public static function isTriniti($bundle)
    {
        $regex = '/(^4-[0-9]{1})/';
        $matches = array();
        preg_match($regex, $bundle, $matches);
        if ($matches) {
            return true;
        }

        return false;
    }

    public static function getDays($date_from = null, $date_to = null)
    {
        $date_from = !$date_from ? date('Y-m-d') : $date_from;
        $date_to = !$date_to ? date('Y-m-d') : $date_to;

        $t1 = strtotime($date_from);
        $t2 = strtotime($date_to);

        $y1 = date('Y', $t1);
        $m1 = date('m', $t1);
        $d1 = date('d', $t1);

        $y2 = date('Y', $t2);
        $m2 = date('m', $t2);
        $d2 = date('d', $t2);

        $d1 = mktime(0, 0, 0, $m1, $d1, $y1);
        $d2 = mktime(0, 0, 0, $m2, $d2, $y2);

        return floor(($d2 - $d1) / 86400);
    }

    public static function xml2array($xmlObject, $out = array())
    {
        foreach ((array) $xmlObject as $index => $node)
            $out[$index] = ( is_object($node) || is_array($node) ) ? self::xml2array($node) : $node;

        return $out;
    }

    public static function passEncrypt($textToencrypt)
    {
        $key_pass = 'da0834064511f5aeda3ae309e082cfbba0b0dac67a45f1653318788747eb9f2b';
        $key = pack('H*', $key_pass);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $textToencrypt, MCRYPT_MODE_CBC, $iv);
        $ciphertext = $iv . $ciphertext;
        return base64_encode($ciphertext);
    }

    public static function passDecrypt($textTodecrypt)
    {
        $key_pass = 'da0834064511f5aeda3ae309e082cfbba0b0dac67a45f1653318788747eb9f2b';
        $key = pack('H*', $key_pass);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $decryptText = base64_decode($textTodecrypt);
        $iv_decrypt = substr($decryptText, 0, $iv_size);
        $decryptText = substr($decryptText, $iv_size);
        return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $decryptText, MCRYPT_MODE_CBC, $iv_decrypt);
    }

}

?>