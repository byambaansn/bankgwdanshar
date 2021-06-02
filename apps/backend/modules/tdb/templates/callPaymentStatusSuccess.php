<div id="recharge-div">
    <form class="tdb" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankTdb->id ?>" />

        <fieldset>
            <legend>Төлөлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankTdb->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankTdb->order_p ?>
            <br />
            <b>Төлөлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankTdbTable::getStatusName($bankTdb->status, BankTdbTable::TYPE_CALLPAYMENT) ?></legend>

            <?php if ($bankTdb->canReCharge()): ?>
                <label for="tdbChargeMobile">Төлөлт оруулах дугаар:</label>
                <input type="text" id="tdbChargeMobile" name="tdbChargeMobile" value="<?php echo $bankTdb->charge_mobile ?>" maxlength="8" />
                <button id="recharge">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
            <?php else: ?>
                <label>Төлөлт оруулсан дугаар:</label>
                <input type="text" value="<?php echo $bankTdb->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>
<script>
    $('button#recharge').click(function () {
        if (confirm('Та ' + $('#tdbChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.tdb').attr('action', '<?php echo url_for('@bank_tdb_recharge_callpayment') ?>');
            $('form.tdb').submit();
        }
    });
</script>
