<div id="recharge-div">
    <form class="xac" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankXac->id ?>" />

        <fieldset>
            <legend>Төлөлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankXac->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankXac->order_p ?>
            <br />
            <b>Төлөлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankXacTable::getStatusName($bankXac->status, BankXacTable::TYPE_CALLPAYMENT) ?></legend>

            <?php if ($bankXac->canReCharge()): ?>
                <label for="xacChargeMobile">Төлөлт оруулах дугаар:</label>
                <input type="text" id="xacChargeMobile" name="xacChargeMobile" value="<?php echo $bankXac->charge_mobile ?>" maxlength="8" />
                <button id="recharge">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
            <?php else: ?>
                <label>Төлөлт оруулсан дугаар:</label>
                <input type="text" value="<?php echo $bankXac->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>
<script>
    $('button#recharge').click(function () {
        if (confirm('Та ' + $('#xacChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.xac').attr('action', '<?php echo url_for('@bank_xac_recharge_callpayment') ?>');
            $('form.xac').submit();
        }
    });
</script>
