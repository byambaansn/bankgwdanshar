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
            <!--            <label for="bank_date">Банк огноо:</label>
                        <input type="checkbox" name="bank_date" <?php //echo ($bankDate) ? 'checked' : ''    ?>/>
                        <br />-->
            <label for="sta">Төлөв:</label>
            <select id="status" name="status">
                <option value="0">[бүгд]</option>
                <?php foreach ($statuses as $i => $v): ?>
                    <option value="<?php echo $i ?>" <?php echo $i == $status ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                <?php endforeach; ?>
            </select>
            <br />
            <label for="sta">Банк:</label>
            <select id="bank" name="bank">
                <option value="0">[бүгд]</option>
                <?php foreach ($banks as $i => $v): ?>
                    <option value="<?php echo $i ?>" <?php echo $i == $bank ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                <?php endforeach; ?>
            </select>    <br />
            <label for="sta">Төлбөрийн төрөл:</label>
            <select id="type" name="type">
                <option value="0">[бүгд]</option>
                <?php foreach ($types as $i => $parent): ?>
                    <optgroup label="<?php echo $i ?>">
                        <?php foreach ($parent as $id => $v): ?>
                            <option value="<?php echo $id ?>" <?php echo $id == $type ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
            <br />

        </div>
        <div style="width: 450px;float: left">
            <label for="account">Дансны дугаар:</label>
            <select id="account" name="account">
                <option value="0">[бүгд]</option>
                <?php foreach ($accountNumbers as $i => $v): ?>
                    <option value="<?php echo $i ?>" <?php echo $i == $account ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                <?php endforeach; ?>
            </select>  
            <br />
            <label for="order_id">Гүйлгээ №:</label>
            <input type="text" name="order_id" value="<?php echo $orderId ?>" />
            <br />
            <label for="order_amount">Гүйлгээний дүн:</label>
            <input type="text" name="order_amount" value="<?php echo $orderAmount ?>" />
            <br>
            <label for="order_value">Гүйлгээний Утга:</label>
            <input type="text" name="order_value" value="<?php echo $orderValue ?>" />
        </div>
        <label class="clearfix">&nbsp;</label>
        <input type="submit" value="Хайх" />
        <?php if ($hasExcelCredential): ?>
            <input type="submit" value="Excel" name="excel" />
        <?php endif; ?>
        <?php if ($sf_user->hasCredential('transaction_admin')): ?>
            <input name="sap" type="submit" value="SAP Data"/>
        <?php endif; ?>
    </fieldset>
</form>

<div id="PersonTableContainer"></div>


<script type="text/javascript">
    $('#date_from').datepicker();
    $('#date_to').datepicker();
    var cachedCityOptions = null;
    $(document).ready(function () {
        $('#PersonTableContainer').jtable({
            title: 'Гүйлгээ',
            paging: true, //Enable paging
            footer: true,
            pageSize: 10, //Set page size (default: 10)
            sorting: true, //Enable sorting
            defaultSorting: 'Name ASC', //Set default sorting
            actions: {
                listAction: '<?php echo url_for('@transaction_payment_list?bank=' . $bank . '&date_from=' . $dateFrom . '&date_to=' . $dateTo . '&account=' . $account . '&order_id=' . $orderId . '&order_amount=' . $orderAmount . '&order_value=' . $orderValue . '&status=' . $status . '&type=' . $type) ?>',
                updateAction: '<?php echo url_for('@transaction_payment_update') ?>',
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
                payment_id: {
                    title: 'Авсан салбар',
                    width: '30%',
                    options: function () {
                        if (cachedCityOptions) { //Check for cache
                            return cachedCityOptions;
                        }
                        var options = [];
                        $.ajax({//Not found in cache, get from server
                            url: '<?php echo url_for('@transaction_type_list') ?>',
                            type: 'POST',
                            dataType: 'json',
                            async: false,
                            success: function (data) {
                                if (data.Result != 'OK') {
                                    alert(data.Message);
                                    return;
                                }
                                options = data.Options;
                            }
                        });

                        return cachedCityOptions = options; //Cache results and return options
                    }
                },
                username: {
                    title: 'Ажилтан',
                    width: '30%',
                    edit: false
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
