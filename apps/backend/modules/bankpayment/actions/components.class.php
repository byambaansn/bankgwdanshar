<?php

/**
 * bankpayment actions.
 *
 * @package    bankgw
 * @subpackage bankpayment
 * @author     Belbayar
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class bankpaymentComponents extends sfComponents
{

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeSearchForm(sfWebRequest $request)
    {
        $this->hasExcelCredential = in_array('bankpayment_excel', $this->getUser()->getCredentials());
        $this->dateType = $request->getParameter('date_type', 1);
        $this->dateFrom = (AppTools::isDate($request->getParameter('dateFrom', date('Y-m-d')))) ? $request->getParameter('dateFrom', date('Y-m-d')) : date('Y-m-d');
        $this->dateTo = (AppTools::isDate($request->getParameter('dateTo', date('Y-m-d')))) ? $request->getParameter('dateTo', date('Y-m-d')) : date('Y-m-d');

        $this->bank = (int) $request->getParameter('bank');
        $this->staff = $request->getParameter('staff');
        $this->keyword = $request->getParameter('keyword');

        $this->statuses = BankpaymentTable::getForSelectStatus();
        $this->staffs = BankpaymentTable::getForSelectStaff($this->type);
        $this->banks = VendorTable::getForSelect();
        $this->ussdTypes = BankpaymentTable::getForSelectUssdTypes();
        $accountType = $request->getParameter('account_type', 0);
        if ($accountType) {
            $this->accounts = BankAccountTable::getForSelectWithBank($this->bank, $accountType);
        }
    }

}

