<div id="recharge-div">
    <form class="capitron" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankCapitron->id ?>" />

        <fieldset>
            <legend>Цэнэглэлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankCapitron->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankCapitron->order_p ?>
            <br />
            <b>Цэнэглэлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankCapitronTable::getStatusName($bankCapitron->status, BankCapitronTable::TYPE_CALLPAYMENT) ?></legend>

            <?php if ($bankCapitron->canReCharge()): ?>
                <label for="capitronChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="capitronChargeMobile" name="capitronChargeMobile" value="<?php echo $bankCapitron->charge_mobile ?>"/>
                <button id="recharge" onclick="doSend();">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
                <?php
            elseif ($bankCapitron->canReOutcome()):
                ?>
                <label for="capitronChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="capitronChargeMobile" name="capitronChargeMobile" value="<?php echo $bankCapitron->charge_mobile ?>" maxlength="8" disabled="disabled" />
                <br/>
                <?php if ($bankCapitron->getSalesOrderId()): ?>
                    Энэ цэнэглэлтийн ЗАРЛАГА үүссэн байна.
                    <button id="successoutcome">Амжилттай төлөвт оруулах </button>  
                <?php else: ?>
                    <button id="reoutcome">Дахин зарлага хийх</button>
                <?php endif; ?>
            <?php else: ?>
                <label>Цэнэглэсэн дугаар:</label>
                <input type="text" value="<?php echo $bankCapitron->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>

<script>
    function doSend(){
        if (confirm('Та ' + $('#capitronChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
                if ($('button#recharge').data('submitted') == '1') {
                    return false;
                } else {
                    $('button#recharge')
                      .attr('data-submitted', '1')
                      .addClass('submitting')
                      .attr('disabled','disabled');
                    $('form.capitron').attr('action', '<?php echo url_for('@bank_capitron_recharge') ?>');
                    $('form.capitron').submit();
                }
        }
    }

    $('button#reoutcome').click(function () {
        if (confirm('Та ' + $('#capitronChargeMobile').val() + ' дугаарт ЗАРЛАГА хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.capitron').attr('action', '<?php echo url_for('@bank_capitron_reoutcome') ?>');
            $('form.capitron').submit();
        }
    });

    $('button#successoutcome').click(function () {
        if (confirm('Та ' + $('#capitronChargeMobile').val() + ' дугаартай цэнэглэлтийг АМЖИЛТТАЙ төлөвт оруулах гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.capitron').attr('action', '<?php echo url_for('@bank_capitron_outcome_success') ?>');
            $('form.capitron').submit();
        }
    });
</script>
