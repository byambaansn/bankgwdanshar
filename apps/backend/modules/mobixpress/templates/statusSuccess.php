<div id="recharge-div">
    <form class="savings" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankMobixpress->id ?>" />

        <fieldset>
            <legend>Цэнэглэлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankMobixpress->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankMobixpress->order_p ?>
            <br />
            <b>Цэнэглэлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankMobixpressTable::getStatusName($bankMobixpress->status) ?></legend>

            <?php if ($bankMobixpress->canReCharge()): ?>
                <label for="chargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="chargeMobile" name="chargeMobile" value="<?php echo $bankMobixpress->charge_mobile ?>" maxlength="8" />
                <button id="recharge">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
            <?php else: ?>
                <?php if ($bankMobixpress->status == BankMobixpressTable::STAT_NEW): ?>
                    <label>Цэнэглэсэн дугаар:</label>
                    <input type="text" value="<?php echo $bankMobixpress->charge_mobile ?>"  id="changeNumber" name="changeNumber"/>
                    <button id="changeNumber">Дугаар солих</button>
                <?php else: ?>
                    <label>Цэнэглэсэн дугаар:</label>
                    <input type="text" value="<?php echo $bankMobixpress->charge_mobile ?>" disabled="disabled" />
                <?php endif; ?>
            <?php endif; ?>
        </fieldset>
    </form>
</div>

<script>
    $('button#recharge').click(function () {
        if (confirm('Та ' + $('#chargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.savings').attr('action', '<?php echo url_for('@bank_mobixpress_recharge') ?>');
            $('form.savings').submit();
        }
    });
    $('button#changeNumber').click(function () {
        if (confirm('Та ' + $('#changeNumber').val() + ' дугаарт ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.savings').attr('action', '<?php echo url_for('@bank_mobixpress_change_number') ?>');
            $('form.savings').submit();
        }
    });

</script>
