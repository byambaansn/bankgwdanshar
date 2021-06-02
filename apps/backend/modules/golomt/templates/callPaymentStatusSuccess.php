<div id="recharge-div">
    <form class="golomt" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankGolomt->id ?>" />

        <fieldset>
            <legend>Төлөлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankGolomt->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankGolomt->order_p ?>
            <br />
            <b>Төлөлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankGolomtTable::getStatusName($bankGolomt->status, BankGolomtTable::TYPE_CALLPAYMENT) ?></legend>

            <?php if ($bankGolomt->canReCharge()): ?>
                <label for="golomtChargeMobile">Төлөлт оруулах дугаар:</label>
                <input type="text" id="golomtChargeMobile" name="golomtChargeMobile" value="<?php echo $bankGolomt->charge_mobile ?>" maxlength="8" />
                <button id="recharge">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
            <?php else: ?>
                <label>Төлөлт оруулсан дугаар:</label>
                <input type="text" value="<?php echo $bankGolomt->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>
<script>
    $('button#recharge').click(function () {
        if (confirm('Та ' + $('#golomtChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.golomt').attr('action', '<?php echo url_for('@bank_golomt_recharge_callpayment') ?>');
            $('form.golomt').submit();
        }
    });
</script>
