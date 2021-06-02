<div id="recharge-div">
    <form class="capitron" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankCapitron->id ?>" />

        <fieldset>
            <legend>Төлөлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankCapitron->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankCapitron->order_p ?>
            <br />
            <b>Төлөлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankCapitronTable::getStatusName($bankCapitron->status, $bankCapitron::TYPE_CALLPAYMENT) ?></legend>

            <?php if ($bankCapitron->canReCharge()): ?>
                <label for="capitronChargeMobile">Төлөлт оруулах дугаар:</label>
                <input type="text" id="capitronChargeMobile" name="capitronChargeMobile" value="<?php echo $bankCapitron->charge_mobile ?>" maxlength="8" />
                <button id="recharge">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
            <?php else: ?>
                <label>Төлөлт оруулсан дугаар:</label>
                <input type="text" value="<?php echo $bankCapitron->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>
<script>
    $('button#recharge').click(function() {
        if (confirm('Та ' + $('#capitronChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.capitron').attr('action', '<?php echo url_for('@bank_capitron_recharge_callpayment') ?>');
            $('form.capitron').submit();
        }
    });
</script>
