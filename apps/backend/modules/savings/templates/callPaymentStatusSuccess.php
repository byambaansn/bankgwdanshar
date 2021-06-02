<div id="recharge-div">
    <form class="savings" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankSavings->id ?>" />

        <fieldset>
            <legend>Төлөлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankSavings->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankSavings->order_p ?>
            <br />
            <b>Төлөлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankSavingsTable::getStatusName($bankSavings->status, BankSavingsAccountTable::ACCOUNT_CALLPAYMENT) ?></legend>

            <?php if ($bankSavings->canReCharge()): ?>
                <label for="savingsChargeMobile">Төлөлт оруулах дугаар:</label>
                <input type="text" id="savingsChargeMobile" name="savingsChargeMobile" value="<?php echo $bankSavings->charge_mobile ?>" maxlength="8" />
                <button id="recharge">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
            <?php else: ?>
                <label>Төлөлт оруулсан дугаар:</label>
                <input type="text" value="<?php echo $bankSavings->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>

<script>
    $('button#recharge').click(function() {
        if (confirm('Та ' + $('#savingsChargeMobile').val() + ' дугаарт ДАХИН төлөлт оруулах гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.savings').attr('action', '<?php echo url_for('@bank_savings_re_callpayment_charge') ?>');
            $('form.savings').submit();
        }
    });

    $('button#successoutcome').click(function() {
        if (confirm('Та ' + $('#savingsChargeMobile').val() + ' дугаартай цэнэглэлтийг АМЖИЛТТАЙ төлөвт оруулах гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.savings').attr('action', '<?php echo url_for('@bank_savings_outcome_success') ?>');
            $('form.savings').submit();
        }
    });
</script>
