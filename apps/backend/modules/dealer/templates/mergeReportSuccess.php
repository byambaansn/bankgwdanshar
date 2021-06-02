<form id="form" action="<?php echo url_for('@bank_dealer_merge_report') ?>" autocomplete="off">
    <fieldset>
        <legend>ХАЙЛТ</legend>
        Банк:
        <select id="bank" name="bank" class="selectSmall">
            <option value="0">[бүгд]</option>
            <?php foreach ($banks as $i => $v): ?>
                <option value="<?php echo $i ?>" <?php echo ($i == $bank) ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
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
<table>
    <thead>
        <tr>
            <th rowspan="2" width="90">ID</th>
            <th colspan="2" width="90">Нэгтгэсэн</th>
            <th rowspan="2" width="90">БАНК</th>
            <th rowspan="2" width="90">№ ГҮЙЛГЭЭ</th>
            <th rowspan="2" width="90">ДАНСНЫ ДУГААР</th>
            <th colspan="2">ДУГААР</th>
            <th rowspan="2" width="60">ТӨРӨЛ</th>
            <th colspan="3">ЦЭНЭГЛЭЛТ ₮</th>
            <th rowspan="2" width="60">ТӨЛӨВ</th>
            <th rowspan="2" width="120">БАНК ОГНОО</th>
        </tr>
        <tr>

            <th width="90">Огноо</th>
            <th width="90">Ажилтан</th>

            <th width="60" title="Цэнэглэх дугаар">C</th>
            <th width="60" title="Хуулга дээрх дугаар">D</th>

            <th>НЭГЖ</th>
            <th>ТӨЛСӨН</th>
            <th>ЗӨРҮҮ</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $dealer): ?>
            <tr>
                <td><?php echo $dealer['id'] ?></td>
                <td><?php echo $dealer['merge_created_at'] ?></td>
                <td><?php echo $dealer['created_user'] ?></td>
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
                    <?php echo DealerCore::getStatusName($dealer['status'], true) ?>
                    <span title="Оролдлогын тоо">
                        (<?php echo $dealer['try_count'] ?>)
                    </span>
                </td>
                <td align="center"><?php echo $dealer['order_date'] ?></td>
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

            </th>
            <th colspan="3"><a rel="facebox" id="openPopup" href="/"></a></th>
        </tr>
    </tfoot>
</table>

<script type="text/javascript">
    $('#dateFrom').datepicker();
    $('#dateTo').datepicker();
</script>