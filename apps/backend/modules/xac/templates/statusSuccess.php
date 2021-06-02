<div id="recharge-div">
    <form class="xac" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankXac->id ?>" />

        <fieldset>
            <legend>Цэнэглэлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankXac->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankXac->order_p ?>
            <br />
            <b>Цэнэглэлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankXacTable::getStatusName($bankXac->status, BankXacTable::TYPE_DEALER) ?></legend>

            <?php if ($bankXac->canReCharge()): ?>
                <label for="xacChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="xacChargeMobile" name="xacChargeMobile" value="<?php echo $bankXac->charge_mobile ?>" />
                <button id="recharge" onclick="doSend();">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
                <?php
            elseif ($bankXac->canReOutcome()):
                ?>
                <label for="xacChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="xacChargeMobile" name="xacChargeMobile" value="<?php echo $bankXac->charge_mobile ?>" maxlength="8" disabled="disabled" />
                <br/>
                <?php if ($bankXac->getSalesOrderId()): ?>
                    Энэ цэнэглэлтийн ЗАРЛАГА үүссэн байна.
                    <button id="successoutcome">Амжилттай төлөвт оруулах </button>  
                <?php else: ?>
                    <button id="reoutcome">Дахин зарлага хийх</button>
                <?php endif; ?>
            <?php else: ?>
                <label>Цэнэглэсэн дугаар:</label>
                <input type="text" value="<?php echo $bankXac->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>

<script>
    function doSend(){
        if (confirm('Та ' + $('#xacChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
                if ($('button#recharge').data('submitted') == '1') {
                    return false;
                } else {
                    $('button#recharge')
                      .attr('data-submitted', '1')
                      .addClass('submitting')
                      .attr('disabled','disabled');
                    $('form.xac').attr('action', '<?php echo url_for('@bank_xac_recharge') ?>');
                    $('form.xac').submit();
                }
        }
    }

    $('button#reoutcome').click(function () {
        if (confirm('Та ' + $('#xacChargeMobile').val() + ' дугаарт ЗАРЛАГА хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.xac').attr('action', '<?php echo url_for('@bank_xac_reoutcome') ?>');
            $('form.xac').submit();
        }
    });

    $('button#successoutcome').click(function () {
        if (confirm('Та ' + $('#xacChargeMobile').val() + ' дугаартай цэнэглэлтийг АМЖИЛТТАЙ төлөвт оруулах гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.xac').attr('action', '<?php echo url_for('@bank_xac_outcome_success') ?>');
            $('form.xac').submit();
        }
    });
</script>
