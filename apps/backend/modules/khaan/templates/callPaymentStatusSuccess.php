<div id="recharge-div">
    <form class="khaan" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankKhaan->id ?>" />

        <fieldset>
            <legend>Төлөлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankKhaan->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankKhaan->order_p ?>
            <br />
            <b>Төлөлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankKhaanTable::getStatusName($bankKhaan->status, BankKhaanTable::TYPE_CALLPAYMENT) ?></legend>

            <?php if ($bankKhaan->canReCharge()): ?>
                <label for="khaanChargeMobile">Төлөлт оруулах дугаар:</label>
                <input type="text" id="khaanChargeMobile" name="khaanChargeMobile" value="<?php echo $bankKhaan->charge_mobile ?>" maxlength="8" />
                <button id="recharge">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
            <?php else: ?>
                <label>Төлөлт оруулсан дугаар:</label>
                <input type="text" value="<?php echo $bankKhaan->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>
<script>
    $('button#recharge').click(function() {
        if (confirm('Та ' + $('#khaanChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.khaan').attr('action', '<?php echo url_for('@bank_khaan_recharge_callpayment') ?>');
            $('form.khaan').submit();
        }
    });
</script>
