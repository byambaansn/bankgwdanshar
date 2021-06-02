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
            <label for="order_id">Гүйлгээ №:</label>
            <input type="text" name="order_id" value="<?php echo $orderId ?>" />
            <br />


            <label for="account">Дансны дугаар:</label>
            <input type="text" name="account" value="<?php echo $account ?>" />
            <br />
        </div>
        <div style="width: 350px;float: left">
            <label for="sta">Банк:</label>
            <select id="bank" name="bank">
                <option value="0">[бүгд]</option>
                <?php foreach ($banks as $i => $v): ?>
                    <option value="<?php echo $i ?>" <?php echo $i == $bank ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                <?php endforeach; ?>
            </select>    <br />
            <label for="sta">Төлөв:</label>
            <select id="status" name="status">
                <option value="0">[бүгд]</option>
                <?php foreach ($statuses as $i => $v): ?>
                    <option value="<?php echo $i ?>" <?php echo $i == $status ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                <?php endforeach; ?>
            </select>
            <br />
            <label for="sta">Төлбөрийн төрөл:</label>
            <select id="type" name="type">
                <option value="0">[бүгд]</option>
                <?php foreach ($types as $i => $v): ?>
                    <option value="<?php echo $i ?>" <?php echo $i == $type ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                <?php endforeach; ?>
            </select>
            <br />
        </div>

        <label class="clearfix">&nbsp;</label>
        <input type="submit" value="Хайх" />
        <input name="excel" type="submit" value="Excel"/>
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
            pageSize: 10, //Set page size (default: 10)
            sorting: true, //Enable sorting
            defaultSorting: 'Name ASC', //Set default sorting
            actions: {
                listAction: '<?php echo url_for('@transaction_payment_list?bank=' . $bank . '&date_from=' . $dateFrom . '&date_to=' . $dateTo . '&account=' . $account . '&order_id=' . $orderId . '&status=' . $status . '&type=' . $type) ?>',
            },
            fields: {
                id: {
                    key: true,
                    list: false
                },
                bank_name: {
                    title: 'Банк',
                },
                order_id: {
                    title: 'Гүйлгээ №',
                },
                bank_account: {
                    title: 'Данс',
                },
                status: {
                    title: 'Төлөв',
                    width: '7%'
                },
                order_p: {
                    title: 'Утга',
                    width: '20%'
                },
                amount: {
                    title: 'Дүн',
                    width: '7%'
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
                    title: 'Авсан салбар',
                    width: '30%',
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