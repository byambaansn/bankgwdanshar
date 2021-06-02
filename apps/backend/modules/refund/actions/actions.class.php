<?php

/**
 * refund actions.
 *
 * @package    sf_sandbox
 * @subpackage refund
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class refundActions extends sfActions
{

    public function preExecute()
    {
        $this->getRequest()->setParameter('tab', 'refund');
    }

    public function executeList(sfWebRequest $request)
    {
        $this->hasExcelCredential = in_array('bankpayment_excel', $this->getUser()->getCredentials());
        $this->getRequest()->setParameter('sub_tab', 'dealer');
        $this->dateFrom = $request->getParameter('dateFrom', date('Y-m-d'));
        $this->dateTo = $request->getParameter('dateTo', date('Y-m-d'));
        $this->orderDesc = $request->getParameter('orderDesc');
        $this->number = $request->getParameter('number');
        $this->contract = $request->getParameter('contract');
        $this->type = $request->getParameter('payment_type', 4);
        $this->refundType = $request->getParameter('refund_type', 1);
        
        $this->refundResutlt = BankpaymentVatRefundTable::getList($request);

        $urlParams = array();
        $urlParams[] = 'dateFrom=' . $this->dateFrom;
        $urlParams[] = 'dateTo=' . $this->dateTo;
        $urlParams[] = 'number=' . $this->number;
        $urlParams[] = 'contract=' . $this->contract;
        $urlParams[] = 'orderDesc=' . $this->orderDesc;
        $urlParams[] = 'payment_type=' . $this->type;
        $urlParams[] = 'refund_type=' . $this->refundType;
        $this->urlParams = join('&', $urlParams);
        
        return sfView::SUCCESS;
    }
    
    public function executeListExcel(sfWebRequest $request)
    {

        set_time_limit(180);
        ini_set("memory_limit", "1024M");
        $refundType = $request->getParameter('refund_type', 1);
        $filename = 'VatRefundList';
        $result = BankpaymentVatRefundTable::getList($request);

        $data = "Төлсөн суваг;";
        $data .= "Дансны дугаар;";
        $data .= "Банк огноо;";
        $data .= "Төлбөрийн төрөл;";
        $data .= "Гүйлгээний утга;";
        $data .= "Төлөлтийн дүн;";
        $data .= "НӨАТ илгээгч компани;";
        $data .= "Утасны дугаар;";
        $data .= "Гэрээний дугаар;";
        $data .= "Хуучин төлөлт оруулсан ажилтан;";
        $data .= "Хуучин төлөлт оруулсан огноо;";
        $data .= "Шинэ төлөлтийн дүн;";
        $data .= ($refundType == 1 ? "Буцаалтын төрөл;" : "Засварын төрөл;");
        $data .= "Өмнөх утга;";
        $data .= "Шинэ утга;";
        $data .= ($refundType == 1 ? "Буцаалт хийсэн ажилтан;" : "Засвар хийсэн ажилтан;");
        $data .= ($refundType == 1 ? "Буцаалт хийсэн огноо;" : "Засвар хийсэн огноо;");
        $data .= "Төлөлт оруулсан ажилтан;";
        $data .= "Төлөлт оруулсан огноо;";
        $data .= "Салбар;";
        $data .= "Тайлбар;";
        $data .= "Тайлангийн төрөл\n";
        foreach ($result as $refund) {
            $data .= '"' . $refund['bank_name'] . '";';
            $data .= '"' . $refund['bank_account'] . '";';
            $data .= '"' . $refund['order_date'] . '";';
            $data .= '"' . $refund['item_type'] . '";';
            $data .= '"' . $refund['order_desc'] . '";';
            $data .= '"' . $refund['order_amount'] . '";';
            $data .= '"' . $refund['vat_company'] . '";';
            $data .= '"' . $refund['number'] . '";';
            $data .= '"' . $refund['contract_number'] . '";';
            $data .= '"' . $refund['username'] . '";';
            $data .= '"' . $refund['updated_at'] . '";';
            $data .= '"' . $refund['paid_amount'] . '";';
            $data .= '"' . $refund['refund_type'] . '";';
            $data .= '"' . $refund['old_value'] . '";';
            $data .= '"' . $refund['new_value'] . '";';
            $data .= '"' . $refund['refund_user'] . '";';
            $data .= '"' . $refund['refund_date'] . '";';
            $data .= '"' . $refund['new_username'] . '";';
            $data .= '"' . $refund['new_updated_at'] . '";';
            $data .= '"' . $refund['payment_type'] . '";';
            $data .= '"' . $refund['refund_desc'] . '";';
            $data .= '"' . $refund['type'] . '";';
            $data .= "\n";
        }

        AppTools::ExportCsv($data, $filename, false);
        die();
    }
}