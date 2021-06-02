<?php
//print_r(DealerCharge::charge(95753502, 200));
?>

<form id="form" action="<?php echo url_for('@bank_candy_dealer') ?>" autocomplete="off">
    <fieldset>
        <legend>ХАЙЛТ</legend>

        <label for="dateFrom">Огноо:</label>
        <input type="text" id="dateFrom" name="dateFrom" value="<?php echo $dateFrom ?>" readonly />-c
        <input type="text" id="dateTo" name="dateTo" value="<?php echo $dateTo ?>" readonly /> хүртэл
        <br />

        <label for="chargedMobile">C дугаар:</label>
        <input type="text" name="chargedMobile" value="<?php echo $chargedMobile ?>" />
        <span class="desc">цэнэглэлт хийсэн дугаар</span>
        <br />

        <label for="orderedMobile">D дугаар:</label>
        <input type="text" name="orderedMobile" value="<?php echo $orderedMobile ?>" />
        <span class="desc">хуулган дээрх гэрээтийн бичсэн дугаар</span>
        <br />

        <label for="orderId">Гүйлгээ №:</label>
        <input type="text" name="orderId" value="<?php echo $orderId ?>" />
        <br />

        <label for="sta">Төлөв:</label>
        <select id="sta" name="status">
            <option value="0">[бүгд]</option>
            <?php foreach ($status as $i => $v): ?>
                <option value="<?php echo $i ?>"><?php echo $v ?></option>
            <?php endforeach; ?>
        </select>
        <br />

        <label>&nbsp;</label>
        <input type="submit" value="Хайх" />
        <button type="reset">Шинэчлэх</button>
        <?php if ($hasExcelCredential): ?>
        <a href="<?php echo url_for('@bank_candy_dealer_list_excel?' . $urlParams) ?>"><input type="button" value="Excel"/></a>
        <?php endif; ?>
    </fieldset>
</form>

<div class="info"><b class="red">C</b> - Цэнэглэх дугаар <b class="red">D</b> - Хуулга дээрх дугаар</div>

<table>
    <thead>
        <tr>
            <th rowspan="2" width="90">№ ГҮЙЛГЭЭ</th>
            <th rowspan="2" width="90">ДАНСНЫ ДУГААР</th>
            <th colspan="2">ДУГААР</th>
            <th rowspan="2" width="60">ТӨРӨЛ</th>
            <th colspan="3">ЦЭНЭГЛЭЛТ ₮</th>
            <th rowspan="2" width="60">ТӨЛӨВ</th>
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
        <?php foreach ($pager->getResults() as $candy): ?>
            <?php $status = BankCandyTable::getStatusName($candy->status, 1, true); ?>
            <tr>
                <td><?php echo $candy->order_id ?></td>
                <td><?php echo $candy->bank_account ?></td>
                <td><?php echo $candy->charge_mobile ?></td>
                <td><?php echo $candy->order_mobile ?></td>
                <td align="center"><?php echo $candy->order_type ?></td>
                <td><?php echo $candy->charge_amount ?></td>
                <td><?php echo $candy->order_amount ?></td>
                <td><?php echo ($candy->charge_amount - $candy->order_amount) ?></td>
                <td align="center">
                    <?php echo link_to($status, '@bank_candy_dealer_status?id=' . $candy->id, array('rel' => 'facebox')) ?>
                    <span title="Оролдлогын тоо">
                        (<?php echo $candy->try_count ?>)
                    </span>
                </td>
                <td align="center"><?php echo $candy->created_at ?></td>
                <td align="center"><?php echo $candy->updated_at ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="12"><?php echo $pager->getNbResults() ?> үр дүн байна.</th>
        </tr>
    </tfoot>
</table>

<?php echo pager_navigation($pager, AppTools::getQueryString('@bank_candy_dealer')) ?>

<script type="text/javascript">
    $('#dateFrom').datepicker();
    $('#dateTo').datepicker();

    $('#sta').val('<?php echo $sta ?>');
</script>
