<?php
//print_r(DealerCharge::charge(95753502, 200));
?>

<form id="form" action="<?php echo url_for('@bank_khaan_call_payment_list') ?>" autocomplete="off">
    <fieldset>
        <legend>ХАЙЛТ</legend>

        <label for="dateFrom">Огноо:</label>
        <input type="text" id="dateFrom" name="dateFrom" value="<?php echo $dateFrom ?>" readonly />-c
        <input type="text" id="dateTo" name="dateTo" value="<?php echo $dateTo ?>" readonly /> хүртэл
        <br />

        <label for="orderedMobile">Дугаар:</label>
        <input type="text" name="orderedMobile" value="<?php echo $orderedMobile ?>" />
        <span class="desc">хуулган дээрх дугаар</span>
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
        <a href="<?php echo url_for('@bank_khaan_call_payment_excel?' . $urlParams) ?>"><input type="button" value="Excel"/></a>
        <?php endif; ?>
    </fieldset>
</form>

<div class="info"> <b class="red">D</b> - Хуулга дээрх дугаар</div>

<table>
    <thead>
        <tr>
            <th  width="90">№ ГҮЙЛГЭЭ</th>
            <th  width="90">ДАНСНЫ ДУГААР</th>
            <th  width="90">ХАРЬЦСАН ДАНС</th>
            <th colspan="1">ДУГААР</th>
            <th  width="60">ТӨРӨЛ</th>
            <th colspan="1">ТӨЛСӨН ₮</th>
            <th width="60">ТӨЛӨВ</th>
            <th  width="120">БАНК ОГНОО</th>
            <th  width="120">ЭХЭЛСЭН</th>
            <th  width="120">ДУУССАН</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pager->getResults() as $khaan): ?>
            <?php $status = BankKhaanTable::getStatusName($khaan->status, BankKhaanTable::TYPE_CALLPAYMENT, true); ?>
            <tr>
                <td><?php echo $khaan->order_id ?></td>
                <td><?php echo $khaan->bank_account ?></td>
                <td><?php echo $khaan->related_account ?></td>
                <td><?php echo $khaan->order_mobile ?></td>
                <td align="center"><?php echo $khaan->order_type ?></td>
                <td><?php echo $khaan->order_amount ?></td>
                <td align="center">
                    <?php echo link_to($status, '@bank_khaan_call_payment_status?id=' . $khaan->id, array('rel' => 'facebox')) ?>
                    <span title="Оролдлогын тоо">
                        (<?php echo $khaan->try_count ?>)
                    </span>
                </td>
                <td align="center"><?php echo $khaan->order_date ?></td>
                <td align="center"><?php echo $khaan->created_at ?></td>
                <td align="center"><?php echo $khaan->updated_at ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="12"><?php echo $pager->getNbResults() ?> үр дүн байна.</th>
        </tr>
    </tfoot>
</table>

<?php echo pager_navigation($pager, AppTools::getQueryString('@bank_khaan_call_payment_list')) ?>

<script type="text/javascript">
    $('#dateFrom').datepicker();
    $('#dateTo').datepicker();

    $('#sta').val('<?php echo $sta ?>');
</script>
