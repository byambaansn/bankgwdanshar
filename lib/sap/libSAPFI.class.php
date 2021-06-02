<?php

class libSAPFI
{

    private $pkeys = array('01' => array('C', 'D', 'Invoice'), '02' => array('C', 'D', 'Reverse credit memo'), '03' => array('C', 'D', 'Expenses'), '04' => array('C', 'D', 'Other receivables'), '05' => array('C', 'D', 'Outgoing payment'), '06' => array('C', 'D', 'Payment difference'), '07' => array('C', 'D', 'Other clearing'), '08' => array('C', 'D', 'Payment clearing'), '09' => array('C', 'D', 'Special G/L debit'), '0A' => array('C', 'D', 'CH Bill.doc. Deb'), '0B' => array('C', 'D', 'CH Cancel.Cred.memoD'), '0C' => array('C', 'D', 'CH Clearing Deb'), '0X' => array('C', 'D', 'CH Clearing Cred'), '0Y' => array('C', 'D', 'CH Credit memo Cred'), '0Z' => array('C', 'D', 'CH Cancel.BillDocDeb'), '11' => array('C', 'C', 'Credit memo'), '12' => array('C', 'C', 'Reverse invoice'), '13' => array('C', 'C', 'Reverse charges'), '14' => array('C', 'C', 'Other payables'), '15' => array('C', 'C', 'Incoming payment'), '16' => array('C', 'C', 'Payment difference'), '17' => array('C', 'C', 'Other clearing'), '18' => array('C', 'C', 'Payment clearing'), '19' => array('C', 'C', 'Special G/L credit'), '1A' => array('C', 'C', 'CH Cancel.Bill.docDe'), '1B' => array('C', 'C', 'CH Credit memo Deb'), '1C' => array('C', 'C', 'CH Credit memo Deb'), '1X' => array('C', 'C', 'CH Clearing Cred'), '1Y' => array('C', 'C', 'CH Cancel.Cr.memo C'), '1Z' => array('C', 'C', 'CH Bill.doc. Cred'), '21' => array('V', 'D', 'Credit memo'), '22' => array('V', 'D', 'Reverse invoice'), '24' => array('V', 'D', 'Other receivables'), '25' => array('V', 'D', 'Outgoing payment'), '26' => array('V', 'D', 'Payment difference'), '27' => array('V', 'D', 'Clearing'), '28' => array('V', 'D', 'Payment clearing'), '29' => array('V', 'D', 'Special G/L debit'), '31' => array('V', 'C', 'Invoice'), '32' => array('V', 'C', 'Reverse credit memo'), '34' => array('V', 'C', 'Other payables'), '35' => array('V', 'C', 'Incoming payment'), '36' => array('V', 'C', 'Payment difference'), '37' => array('V', 'C', 'Other clearing'), '38' => array('V', 'C', 'Payment clearing'), '39' => array('V', 'C', 'Special G/L credit'), '40' => array('G', 'D', 'Debit entry'), '50' => array('G', 'C', 'Credit entry'), '70' => array('A', 'D', 'Debit asset'), '75' => array('A', 'C', 'Credit asset'), '80' => array('G', 'D', 'Stock initial entry'), '81' => array('G', 'D', 'Costs'), '83' => array('G', 'D', 'Price difference'), '84' => array('G', 'D', 'Consumption'), '85' => array('G', 'D', 'Change in stock'), '86' => array('G', 'D', 'GR/IR debit'), '89' => array('M', 'D', 'Stock inwrd movement'), '90' => array('G', 'C', 'Stock initial entry'), '91' => array('G', 'C', 'Costs'), '93' => array('G', 'C', 'Price difference'), '94' => array('G', 'C', 'Consumption'), '95' => array('G', 'C', 'Change in stock'), '96' => array('G', 'C', 'GR/IR credit'), '99' => array('M', 'C', 'Stock outwd movement'));
    public $fiDoc = array();
    private $_hasHeader = false;
    private $_hasItem = false;
    private $_currency = 'MNT';

    public function __construct()
    {
        echo 'SAPFI';
    }

    /**
     * create header for document.
     * file header uusgesenii daraa dotorhi guilgeenuudiig ni nemj uguh shaardlagatai.
     * '{"header":["company_code","doc_type","ref_num","doc_date","currency","doc_header_txt","posting_date","translation_date"],
     */
    public function createHeader($params)
    {
        if ($this->_hasHeader) {
            //die('Document already have header.');
        }
        if (isset($params['doc_date']) == FALSE) {
            $params['doc_date'] = date('Ymd');
        }
        $this->fiDoc[] = array('H', $params['company_code'], $params['doc_type'], $params['ref_num'], $params['doc_date'], $params['currency'], substr($params['posting_date'], 4, 2), $params['doc_header_txt'], $params['posting_date'], $params['translation_date']);
        $this->_currency = $params['currency'];
        $this->_hasHeader = TRUE;
    }

    /**
     * add line of statement to document
     * ene ni undsen dans, customer, vendor deerh bichiltiig clearing dansandah bichiltiin hamt uusgene.
     * @param array of statement line.
     * keys:
     * 	pkey: - char(2) {##} posting kay, credit or debit code in SAP
     * 	amount: double {#.##} statement value
     * 	acc_ind: char(1) account indicator. D - customer, G - account
     * 	account: string(8) main account or customer(SAP)
     * 	clearing: string(8) clearing account (SAP)
     *  vat_code: VAT code, (A0, A1, A2)
     * 	assignment: string(16) optional
     * 	text: string. Statement value optional
     */
    public function addStatement($lineItem)
    {
        if (!$this->_hasHeader) {
            die('Document hasn\'t header line!');
        }

        if (!isset($lineItem['pkey'])) {
            die('"pkey" missed.');
        }
        if (!isset($lineItem['amount'])) {
            die('"amount" missed.');
        }
        if (!isset($lineItem['account'])) {
            die('"account" missed.');
        }
        if (!isset($lineItem['clearing'])) {
            die('"clearing" missed.');
        }
        $this->fiDoc[] = array('I', $lineItem['acc_ind'], $lineItem['pkey'], $lineItem['account'], $this->_currency == 'MNT' ? $lineItem['amount'] : null, $this->_currency == 'MNT' ? null : $lineItem['amount'], $lineItem['vat_code'], null, $lineItem['assignment'], $lineItem['text']);
        $this->fiDoc[] = array('I', $lineItem['acc_ind'], $this->getClearingPkey($lineItem['pkey']), $lineItem['clearing'], $this->_currency == 'MNT' ? $lineItem['amount'] : null, $this->_currency == 'MNT' ? null : $lineItem['amount'], $lineItem['vat_code'], null, $lineItem['assignment'], $lineItem['text']);
        $this->_hasItem = TRUE;
    }

    private function getClearingPkey($pkey)
    {
        if (isset($this->pkeys[$pkey])) {
            return $this->pkeys[$pkey][1] == 'C' ? '40' : '50';
        } else
            die('Posting key doesn\'t exist.');
    }

    public function __toString()
    {
        return var_dump($this->fiDoc);
    }

    /**
     * FI documentiin string utga butsaana. uuniig UTF-8-r filed bichij
     * SAP uruu upload hiihed belen bolno.
     */
    public function getStringOfDocument()
    {
        if (!$this->_hasItem) {
            die("Document hasn\t items! first use function addStatement");
        }
        $result = '';
        foreach ($this->fiDoc as $line) {
            foreach ($line as $item) {
                $result .= $item . "\t";
            }
            $result .= "\n";
        }
        return $result;
    }

}

?>