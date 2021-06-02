<form id="form" action="<?php echo url_for('@bank_dealer_list') ?>" autocomplete="off">
    <fieldset>
        <legend>ХАЙЛТ</legend>
        Банк:
        <select id="bank" name="bank" class="selectSmall">
            <option value="0">[бүгд]</option>
            <?php foreach ($banks as $i => $v): ?>
                <option value="<?php echo $i ?>" <?php echo ($i == $bank) ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
            <?php endforeach; ?>
        </select>
        Төлөв:
        <select class="multiSelect selectMedium" id="sta" name="status[]" multiple="true">
            <?php foreach ($statuses as $i => $v): ?>
                <option value="<?php echo $i ?>" <?php echo (in_array($i, $status)) ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
            <?php endforeach; ?>
        </select>
        Огноо:
        <input type="text" id="dateFrom" name="dateFrom" value="<?php echo $dateFrom ?>" readonly />-c
        <input type="text" id="dateTo" name="dateTo" value="<?php echo $dateTo ?>" readonly />      
        <input type="text" name="chargedMobile" value="<?php echo $chargedMobile ?>"  placeholder="C дугаар" class="selectSmall"/>
        <input type="text" name="orderedMobile" value="<?php echo $orderedMobile ?>" placeholder="D дугаар" class="selectSmall"/>
        <input type="text" name="orderId" value="<?php echo $orderId ?>" placeholder="Гүйлгээ №" class="selectSmall"/>
        <br />
        <input type="submit" value="Хайх" />
        <?php if ($hasExcelCredential): ?>
            <input type="submit" value="Excel" name="excel" />
        <?php endif; ?>
    </fieldset>
</form>

<div class="info"><b class="red">C</b> - Цэнэглэх дугаар <b class="red">D</b> - Хуулга дээрх дугаар</div>
<form id="form" class="mergeForm" action="" autocomplete="off">
    <table>
        <thead>
            <tr>
                <th rowspan="2" width="90">БАНК</th>
                <th rowspan="2" width="90">№ ГҮЙЛГЭЭ</th>
                <th rowspan="2" width="90">ДАНСНЫ ДУГААР</th>
                <th colspan="2">ДУГААР</th>
                <th rowspan="2" width="60">ТӨРӨЛ</th>
                <th colspan="3">ЦЭНЭГЛЭЛТ ₮</th>
                <th rowspan="2" width="60">ТӨЛӨВ<?php if (isset($allowMerge)): ?><input type="checkbox" class="chAll" name="chAll" amount="0" >   <?php endif; ?></th>
                <th rowspan="2" width="120">БАНК ОГНОО</th>
                <th rowspan="2" width="120">ЭХЭЛСЭН</th>
                <th rowspan="2" width="120">ДУУССАН</th>
            </tr>
            <tr>
                <th width="60" title="Цэнэглэх дугаар">C</th>
                <th width="60" title="Хуулга дээрх дугаар">D</th>

                <th>НЭГЖ</th>
                <th>ТӨЛСӨН</th>
                <th>ЗӨРҮҮ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $dealer): ?>
                <?php $status = DealerCore::getStatusName($dealer['status'], true); ?>
                <tr>
                    <td><?php echo $dealer['bank_name'] ?></td>
                    <td><?php echo $dealer['order_id'] ?></td>
                    <td><?php echo $dealer['bank_account'] ?></td>
                    <td><?php echo $dealer['charge_mobile'] ?></td>
                    <td><?php echo $dealer['order_mobile'] ?></td>
                    <td align="center"><?php echo $dealer['order_type'] ?></td>
                    <td><?php echo $dealer['charge_amount'] ?></td>
                    <td><?php echo $dealer['order_amount'] ?></td>
                    <td><?php echo ($dealer['charge_amount'] - $dealer['order_amount']) ?></td>
                    <td align="center">
                        <?php if (isset($allowMerge)): ?>
                            <input class="tRow" type="checkbox" name="transaction[]" value="<?php echo $dealer['id']; ?>" amount="<?php echo $dealer['order_amount'] ?>">
                        <?php endif; ?>
                        <?php echo link_to($status, DealerCore::getStatusLink($dealer['vendor_id']) . '?id=' . $dealer['id'], array('rel' => 'facebox')) ?>
                        <span title="Оролдлогын тоо">
                            (<?php echo $dealer['try_count'] ?>)
                        </span>
                    </td>
                    <td align="center"><?php echo $dealer['order_date'] ?></td>
                    <td align="center"><?php echo $dealer['created_at'] ?></td>
                    <td align="center"><?php echo $dealer['updated_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7"><?php echo count($rows) ?> үр дүн байна.</th>
                <th colspan="">
                    <span id="mergeAmount">0</span>
                </th>
                <th colspan="2">
                    <?php if (isset($allowMerge)): ?>
                        <input type="button" onclick="openPopup();" value="Нэгтгэх">
                    <?php endif ?>
                </th>
                <th colspan="3"><a rel="facebox" id="openPopup" href="/"></a></th>
            </tr>
        </tfoot>
    </table>
</form>

<script type="text/javascript">
    $('#dateFrom').datepicker();
    $('#dateTo').datepicker();

    $('.chAll').click(function (event) {  //on click
        if (this.checked) { // check select status
            $('input:checkbox.tRow').each(function () { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"   
                $(this).parent().parent().addClass('selected');
            });
        } else {
            $('input:checkbox.tRow').each(function () { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"                      
                $(this).parent().parent().removeClass('selected');
            });
        }
        mergeAmount();
    });
    $('.tRow').click(function (event) {  //on click
        mergeAmount();
    });

    function mergeAmount() {
        var tempAmount = 0;
        $('input:checkbox.tRow').each(function () { //loop through each checkbox
            if (this.checked) { // check select status
                tempAmount += parseFloat($(this).attr("amount"));
            }
        });
        $('#mergeAmount').html(tempAmount);
    }
    function openPopup() {
        console.log('test');
        $('#openPopup').attr('href', '<?php echo url_for('@bank_dealer_merge_amount') ?>?vendorId=<?php echo $bank ?>&' + $('.mergeForm').serialize());
        $('#openPopup').click();

    }
</script>