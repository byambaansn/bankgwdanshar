<div id="recharge-div">
    <form class="capital" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankCapital->id ?>" />

        <fieldset>
            <legend>Төлөлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankCapital->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankCapital->order_p ?>
            <br />
            <b>Төлөлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankCapitalTable::getStatusName($bankCapital->status, BankCapitalTable::TYPE_CALLPAYMENT) ?></legend>

            <?php if ($bankCapital->canReCharge()): ?>
                <label for="capitalChargeMobile">Төлөлт оруулах дугаар:</label>
                <input type="text" id="capitalChargeMobile" name="capitalChargeMobile" value="<?php echo $bankCapital->charge_mobile ?>" maxlength="8" />
                <button id="recharge">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
            <?php else: ?>
                <label>Төлөлт оруулсан дугаар:</label>
                <input type="text" value="<?php echo $bankCapital->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>
<script>
    $('button#recharge').click(function() {
        if (confirm('Та ' + $('#capitalChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.capital').attr('action', '<?php echo url_for('@bank_capital_recharge_callpayment') ?>');
            $('form.capital').submit();
        }
    });
</script>
