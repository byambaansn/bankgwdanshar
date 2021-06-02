<form id="form" action="<?php echo url_for('@transaction_list') ?>" autocomplete="off">
    <fieldset>
        <legend>ХАЙЛТ</legend>
        <div style="width: 500px;float: left">
            <label for="date_from">Огноо:</label>
            <input type="text" id="date_from" name="date_from" value="<?php echo $dateFrom ?>" readonly />-c
            <input type="text" id="date_to" name="date_to" value="<?php echo $dateTo ?>" readonly /> хүртэл
            <br />
            <label for="bank_from">Банк огноо:</label>
            <input type="checkbox" name="bank_date" <?php echo ($bankDate) ? 'checked' : '' ?>/>
            <br />
            <label for="bank">Банк:</label>
            <select id="bank" name="bank">
                <option value="0">[бүгд]</option>
                <?php foreach ($banks as $i => $v): ?>
                    <option value="<?php echo $i ?>" <?php echo $i == $bank ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                <?php endforeach; ?>
            </select>    <br />
            <label for="account_number">Дансны дугаар:</label>
            <select id="account_number" name="account_number">
                <option value="0">[бүгд]</option>
                <?php foreach ($accountNumbers as $i => $v): ?>
                    <optgroup label="<?php echo BankAccountTable::getTypeName($i) ?>">
                        <?php foreach ($v as $id => $account): ?>
                            <option value="<?php echo $id ?>" <?php echo $id == $accountNumber ? 'selected="selected"' : '' ?>><?php echo $account ?></option>
                        <?php endforeach; ?>
                    </optgroup>    
                <?php endforeach; ?>

            </select>  
            <br />
        </div>
        <div style="width: 450px;float: left">
            <label for="type">Төрөл:</label>
            <select id="type" name="type">
                <option value="1" <?php echo (1 == $orderType) ? 'selected="selected"' : '' ?>>Орлого</option>
                <option value="2" <?php echo (2 == $orderType) ? 'selected="selected"' : '' ?>>Зарлага</option>
            </select>
            <br />
            <label for="sta">Төлөв:</label>
            <select id="status" name="status">
                <option value="0">[бүгд]</option>
                <?php foreach ($statuses as $i => $v): ?>
                    <option value="<?php echo $i ?>" <?php echo $i == $status ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                <?php endforeach; ?>
            </select>
            <br>
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
    </fieldset>
</form>

<form class="form" id="form_type" action="<?php echo url_for('@transaction_update') ?>" autocomplete="off" method="POST">
    <fieldset>
        <legend>Төлөлт болгох</legend>
        <select id="action_type" name="action_type">
            <option value="">-----</option>
            <option value="1">[Шууд]</option>
            <option value="2">[Хуваах]</option>
            <!--<option value="3">[Нийлүүлэх]</option>-->
        </select>
        <select id="payment" name="payment">
            <option value="0">[сонгох]</option>
            <?php foreach ($types as $i => $type): ?>
                <optgroup label="<?php echo $i ?>">
                    <?php foreach ($type as $id => $v): ?>
                        <option value="<?php echo $id ?>" <?php echo $i == $type ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
        <select id="action_split" name="action_split" class="split" style="display: none">
            <option value="">[Хэд салгах]</option>
            <option value="2" >2</option>
            <option value="3" >3</option>
            <option value="4" >4</option>
            <option value="5" >5</option>
        </select>
        <input type="text" name="comment" placeholder="Тайлбар" onblur="" />
        <input type="hidden" onkeyup="calcAmount()" name="" id="amount" style="width: 80px">
        <input type="submit" value="төлөлт болгох">
        <div id="split">

        </div>
    </fieldset>
    <div class="info">       
        <b class="red">N</b> - Шинэ хуулга <b class="green">D</b> - Төлөлт болгосон  
    </div>
    <table>
        <thead>
            <tr>
                <th rowspan="2" width="20"> <input type="checkbox" id="chAll" name="chAll" onclick="checUncheckAll(this);"></th>
                <th rowspan="2" width="70">БАНК</th>
                <th rowspan="2" width="90">ГҮЙЛГЭЭ №</th>
                <th rowspan="2" width="90">ДАНСНЫ ДУГААР</th>
                <th rowspan="2" width="40">ТӨЛӨВ</th>
                <th colspan="5"  width="120">ГҮЙЛГЭЭНИЙ</th>
                <th rowspan="2" width="120">ҮҮССЭН</th>
                <th rowspan="2" width="120">ТӨЛБӨР БОЛГОХ</th>
            </tr>
            <tr>
                <th>ТӨРӨЛ</th>
                <th>УТГА </th>
                <th>ДҮН ₮</th>
                <th >ОГНОО</th>
                <th>САЛБАР</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($rows as $row):
                ?>
                <tr>
                    <td><input type="checkbox" name="transaction[]" value="<?php echo $row->id ?>"></td>
                    <td><?php echo $row->getBank()->getName(); ?></td>
                    <td><?php echo $row->order_id ?></td>
                    <td><?php echo $row->bank_account ?></td>
                    <td align="center"><?php echo ($row->status == 1) ? '<b class="red">N</b>' : (($row->status == 2) ? '<b class="green">D</b>' : '' ) ?></td>
                    <td><?php echo $row->order_type ?></td>
                    <td><?php echo ($row->order_p) ?></td>
                    <td align="center" id="amount<?php echo $row->id ?>"><?php echo $row->order_amount ?></td>
                    <td><?php echo $row->order_date ?></td>
                    <td><?php echo $row->order_branch ?></td>
                    <td><?php echo $row->created_at ?></td>
                    <td align="center">
                    <a rel="facebox" onclick="return confirm('Тухайн гүйлгээг төлбөр болгох уу?')"  href="<?php echo url_for('@to_bankpayment') . '?id=' . $row['id'] ?>"><img style="cursor: pointer" src="/images/icons/add.png" alt="Төлбөр болгох" ></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7"><?php echo $count ?> үр дүн байна. </th>
                <th><?php // echo number_format($totalAmount, 0, '.', ',') ?> </th>
                <th colspan="4"></th>
            </tr>
        </tfoot>
    </table>

</form>

<?php echo isset($pager) ? pager_navigation($pager, AppTools::getQueryString('@transaction_list')) : '' ?>

<script type="text/javascript">


    $('#date_from').datepicker();
    $('#date_to').datepicker();
    $("#action_split").change(function () {
        var html = '';
        $('#split').html(html);

        if ($(this).val() > 1) {
            for (var i = 1; i <= $(this).val(); i++) {
                // console.log('test' + i);
                $('input#amount').clone().attr('id', 'amount' + i).attr('type', 'text').attr('name', 'amount[' + i + ']').appendTo('#split');
                $('select#payment').clone().attr('id', 'payment' + i).attr('name', 'payment[' + i + ']').appendTo('#split').show();
                //   html += '<input type="text" onkeyup="calcAmount()" name="amount[]" id="amount' + i + '">+';
            }
            html += '=<input readonly type="text" name="total" id="total">';
        } else {
            html = '';
        }
        $('#split').append(html);
        // $('select#payment').clone().attr('id', 'payment1').appendTo('#split').show();
        html = '';
    });

    $("#action_type").change(function () {
        var id = $(this).val();
        if (id == 2) {
            if (countChecked() != 1) {
                if (countChecked() > 1) {
                    alert('Нэгээс олон гүйлгээ хуваах боломжгүй.');
                } else {
                    alert('Та хуваах гүйлгээгээ сонгоно уу.');
                }
                $(this).val('');
                return false;
            }
            $('.split').show();
            $('#payment').hide();
        } else {
            $('#payment').show();
            $('.split').hide();
        }
        $('#split').html('');
    });
    $('#bank').change(function () {
        $.ajax({
            type: "GET",
            url: '<?php echo url_for("@transaction_bank_account_list") ?>',
            data: "bank_id=" + $(this).val() + "&account=<?php echo $accountNumber ?>",
            dataType: "html",
            success: function (data) {
                $("#account_number").html(data);
            }
        });
    });
    function countChecked() {
        var n = $("input:checked").length;
        return n;
    }
    function calcAmount() {
        var totalAmount = $('#amount' + $("input:checked").val()).html();
        $('#total').val(totalAmount);
        var total = 0;
        $('input[name^="amount"]').each(function (index) {
            if ($('#action_split').val() == (index + 1))
            {
                $(this).val($('#total').val() - total);
            } else {
                if ($(this).val() !== '') {
                    total += parseFloat($(this).val());
                    // console.log('eeeeeeeee' + total);
                }
            }

        });
    }
    function checUncheckAll(checkAll) {
        var checkedFlag = false;
        var field = document.getElementsByName("transaction[]");
        if (checkAll.checked == true) {
            for (i = 0; i < field.length; i++) {
                field[i].checked = true;
            }
        } else {
            for (i = 0; i < field.length; i++) {
                field[i].checked = false;

            }
        }
    }
</script>
