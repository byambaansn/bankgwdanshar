<form id="form" action="" autocomplete="off">
    <fieldset>
        <legend>ХАЙЛТ</legend>
        <?php if (isset($ussd)): ?>
            <span>ТӨРӨЛ:</span>
            <select name="ussd">
                <option value="0">[бүгд]</option>
                <?php foreach ($ussdTypes as $i => $v): ?>
                    <option value="<?php echo $i ?>" <?php echo ($i == $ussd) ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif ?>
        <span>Банк:</span>
        <select id="bank" name="bank">
            <option value="0">[бүгд]</option>
            <?php foreach ($banks as $i => $v): ?>
                <option value="<?php echo $i ?>" <?php echo ($i == $bank) ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($accounts)): ?>
            <span>Дансны дугаар:</span>
            <select id="bank_account" name="bank_account">
                <option value="0">[бүгд]</option>
                <?php foreach ($accounts as $i => $v): ?>
                    <optgroup label="<?php echo BankAccountTable::getTypeName($i) ?>">
                        <?php foreach ($v as $id => $account): ?>
                            <option value="<?php echo $id ?>" <?php echo $id == $accountNumber ? 'selected="selected"' : '' ?>><?php echo $account ?></option>
                        <?php endforeach; ?>
                    </optgroup>    
                <?php endforeach; ?>

            </select>  
        <?php endif ?>
        <span>Огноо:</span>
        <select name="date_type">
            <option value="1" <?php echo ($dateType == 1) ? 'selected="selected"' : '' ?>>Хуулга татсан огноо</option>
            <option value="2" <?php echo ($dateType == 2) ? 'selected="selected"' : '' ?>>Банк огноо</option>
            <option value="3" <?php echo ($dateType == 3) ? 'selected="selected"' : '' ?>>Төлөлт оруулсан огноо</option>
        </select>
        <input type="text" id="dateFrom" name="dateFrom" value="<?php echo $dateFrom ?>"  />-c
        <input type="text" id="dateTo" name="dateTo" value="<?php echo $dateTo ?>"  /> 
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span>Төрөл:</span>
        <select class="multiSelect" id="sta" name="status[]" multiple="true">
            <?php foreach ($statuses as $i => $v): ?>
                <option value="<?php echo $i ?>" <?php echo (in_array($i, $status)) ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
            <?php endforeach; ?>
        </select>
        <span>Ажилтан:</span>
        <select id="staff" name="staff">
            <option value="0">[бүгд]</option>
            <?php foreach ($staffs as $i => $v): ?>
                <option value="<?php echo $i ?>" <?php echo ($i == $staff) ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
            <?php endforeach; ?>
        </select>
        <br/>
        <span>Түлхүүр үг:</span>
        <input type="text" id="keyword" name="keyword" value="<?php echo $keyword ?>"  />
        <?php if (isset($payType) && $payType): ?>
            <span>Төлбөрийн төрөл:</span>
            <select name="payment_type">
                <option value="4" <?php echo ($type == 4) ? 'selected="selected"' : '' ?>>Яриа</option>
                <option value="1" <?php echo ($type == 1) ? 'selected="selected"' : '' ?>>Мобинет</option>
                <option value="5" <?php echo ($type == 5) ? 'selected="selected"' : '' ?>>Нэгж цэнэглэлт</option>
            </select>
        <?php endif ?>
        <label>&nbsp;</label>
        <input type="submit" value="Хайх" />
        <?php if ($hasExcelCredential): ?>
        <input type="submit" value="Excel" name="excel" />
        <?php endif; ?>
    </fieldset>
</form>
<div class="info"> - Шинэ хуулга</div>
<script type="text/javascript">
    $('#bank').change(function () {
        $.ajax({
            type: "GET",
            url: '<?php echo url_for("@transaction_bank_account_list") ?>',
            data: "bank_id=" + getBankAndVendorMap($(this).val()) + "&account=<?php echo $accountNumber ?>",
            dataType: "html",
            success: function (data) {
                $("#bank_account").html(data);
            }
        });
    });
    
    function getBankAndVendorMap(vendorId)
    {
        console.log("vendor :" + vendorId);
        
        var bankId = 0;
        switch (vendorId) {
            case '1':
                bankId = 2;
                break;
            case '8':
                bankId = 9;
                break;
            case '9':
                bankId = 1;
                break;
            case '10':
                bankId = 3;
                break;
            case '11':
                bankId = 4;
                break;
            case '12':
                bankId = 8;
                break;
            case '13':
                bankId = 12;
                break;
            default:
                bankId = 0;
                break;
        }
        console.log("Bank id :" + bankId);
        return bankId;
    }
</script>
