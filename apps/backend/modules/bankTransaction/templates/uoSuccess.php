<style>
    tbody tr:hover {
        background-color: #ffff99;
    }   
    .jtable-title{
        display: none;
    }
</style>
<form id="form" action="" autocomplete="off">
    <fieldset>
        <legend>ХАЙЛТ</legend>
        <div style="width: 500px;float: left">
            <label for="date_from">Огноо:</label>
            <input type="text" id="date_from" name="date_from" value="<?php echo $dateFrom ?>" readonly />-c
            <input type="text" id="date_to" name="date_to" value="<?php echo $dateTo ?>" readonly /> хүртэл
            <br />
            <label for="sta">Банк:</label>
            <select id="bank" name="bank">
                <option value="0">[бүгд]</option>
                <?php foreach ($banks as $i => $v): ?>
                    <option value="<?php echo $i ?>" <?php echo $i == $bank ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                <?php endforeach; ?>
            </select>  
        </div>
        <div style="width: 350px;float: left">
            <label for="order_id">Гүйлгээ №:</label>
            <input type="text" name="order_id" value="<?php echo $orderId ?>" />
            <br />
            <label for="account">Дансны дугаар:</label>
            <input type="text" name="account" value="<?php echo $account ?>" />
        </div>

        <label class="clearfix">&nbsp;</label>
        <input type="submit" value="Хайх" />
        <?php if ($hasExcelCredential): ?>
            <input type="submit" value="Excel" name="excel" />
        <?php endif; ?>
    </fieldset>
</form>

<div id="PersonTableContainer"></div>

<script type="text/javascript">
    $('#date_from').datepicker();
    $('#date_to').datepicker();
    $(document).ready(function () {
        $('#PersonTableContainer').jtable({
            title: 'Гүйлгээ',
            paging: true, //Enable paging
            footer: true,
            pageSize: 10, //Set page size (default: 10)
            sorting: true, //Enable sorting
            defaultSorting: 'Name ASC', //Set default sorting
            actions: {
                listAction: '<?php echo url_for('@transaction_payment_list?bank=' . $bank . '&date_from=' . $dateFrom . '&date_to=' . $dateTo . '&account=' . $account . '&order_id=' . $orderId . '&status=' . $status . '&type=' . $type) ?>',
                updateAction: '<?php echo url_for('@transaction_uo_update') ?>',
            },
            fields: {
                id: {
                    key: true,
                    list: false
                },
                bank_name: {
                    title: 'Банк',
                    edit: false
                },
                order_id: {
                    title: 'Гүйлгээ №',
                    edit: false
                },
                bank_account: {
                    title: 'Данс',
                    edit: false
                },
                status: {
                    title: 'Төлөв',
                    width: '7%',
                    edit: false
                },
                order_p: {
                    title: 'Утга',
                    width: '20%',
                    edit: false
                },
                amount: {
                    title: 'Дүн',
                    width: '7%',
                    edit: false,
                    footer: function (data) {
                        var total = Number(data.TotalAmount);
                        return total.toFixed(2);
                    }
                },
                order_date: {
                    title: 'Огноо',
//                    type: 'date',
                    create: false,
                    edit: false
                },
                order_branch: {
                    title: 'Салбар',
                    width: '30%',
//                    type: 'date',
                    create: false,
                    edit: false
                },
                payment_type: {
                    title: 'ТӨРӨЛ',
                    width: '30%',
                    edit: false
                },
                payment_id: {
                    title: 'ТӨРӨЛ',
                    width: '30%',
                    list: false,
                    options: [
<?php
foreach ($types as $name => $paymentTypes) {
    // echo "{DisplayText:'$name',";
    //  echo "Value:'$name',";
    // echo "Children: [";
    foreach ($paymentTypes as $id => $paymentType) {
        echo "{Value: '" . $id . "', DisplayText: '" . $paymentType . "'},";
    }
    // echo "]},";
}
?>
                    ]
                },
                created_at: {
                    title: 'Үүссэн',
                    width: '30%',
//                    type: 'date',
                    create: false,
                    edit: false
                }
            }
        });
        $('#PersonTableContainer').jtable('load');
    });

</script>
