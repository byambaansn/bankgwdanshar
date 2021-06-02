<form id="form" action="<?php echo url_for('@bank_candy_list') ?>" autocomplete="off">
    <fieldset>
        <legend>ХАЙЛТ</legend>
        Банк:
        <select id="bank" name="bank" class="selectSmall">
            <option value="0">[бүгд]</option>
            <?php foreach ($banks as $i => $v): ?>
                <option value="<?php echo $i ?>" <?php echo ($i == $bank) ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
            <?php endforeach; ?>
        </select>
        Төрөл:
        <select id="type" name="type" class="selectSmall">
            <option value="0">[бүгд]</option>
            <?php foreach ($types as $i => $v): ?>
                <option value="<?php echo $i ?>" <?php echo ($i == $type) ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
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
        <input type="text" name="keyword" value="<?php echo $orderId ?>" placeholder="Түлхүүр үг" class="selectSmall"/>
        <br />
        <input type="submit" value="Хайх" />
        <?php if ($hasExcelCredential): ?>
            <input type="submit" value="Excel" name="excel" />
        <?php endif; ?>
    </fieldset>
</form>
<table>
    <thead>
        <tr>
            <th rowspan="2" width="90">БАНК</th>
            <th rowspan="2" width="90">№ ГҮЙЛГЭЭ</th>
            <th rowspan="2" width="90">ДАНС</th>
            <th rowspan="2" width="90">ТӨРӨЛ</th>
            <th rowspan="2" width="120">ДУГААР</th>
            <th rowspan="2" width="120">ЦЭНЭГЛЭЛТ</th>
            <th rowspan="2" width="240">ТӨЛӨВ</th>
            <th rowspan="2" width="120">ЭХЭЛСЭН</th>
            <th rowspan="2" width="120">ДУУССАН</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $candy): ?>
            <?php $status = BankpaymentTable::getStatusName($candy['status'], true); ?>
            <tr <?php if ($candy['parent_id'] > 0): ?> class="water-green" <?php endif; ?>>
                <td><?php echo $candy['bank_name'] ?></td>
                <td><?php echo $candy['bank_order_id'] ?></td>
                <td><?php echo $candy['bank_account'] ?></td>
                <td><?php echo CandyLoanCore::typeToName($candy['type']) ?></td>
                <td><?php echo $candy['number'] ?></td>
                <td><?php echo $candy['paid_amount'] ?></td>
                <td align="center">
                    <?php echo link_to($status, '@bank_candy_status' . '?id=' . $candy['id'] . '&bank_order_id=' . $candy['bank_order_id'] . '&vendor_id=' . $candy['vendor_id'], array('rel' => 'facebox')) ?>
                    <span title="Оролдлогын тоо">
                        (<?php echo $candy['try_count'] ?>)
                    </span>
                </td>
                <td align="center"><?php echo $candy['created_at'] ?></td>
                <td align="center"><?php echo $candy['updated_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="12"><?php echo count($rows) ?> үр дүн байна.</th>
        </tr>
    </tfoot>
</table>
<script type="text/javascript">
    $('#dateFrom').datepicker();
    $('#dateTo').datepicker();
</script>
