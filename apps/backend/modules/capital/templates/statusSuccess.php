<div id="recharge-div">
    <form class="capital" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankCapital->id ?>" />

        <fieldset>
            <legend>Цэнэглэлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankCapital->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankCapital->order_p ?>
            <br />
            <b>Цэнэглэлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankCapitalTable::getStatusName($bankCapital->status, BankCapitalTable::TYPE_DEALER) ?></legend>

            <?php if ($bankCapital->canReCharge()): ?>
                <label for="capitalChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="capitalChargeMobile" name="capitalChargeMobile" value="<?php echo $bankCapital->charge_mobile ?>" maxlength="8" />
                <button id="recharge" onclick="doSend();">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
                <?php
            elseif ($bankCapital->canReOutcome()):
                ?>
                <label for="capitalChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="capitalChargeMobile" name="capitalChargeMobile" value="<?php echo $bankCapital->charge_mobile ?>" maxlength="8" disabled="disabled" />
                <br/>
                <?php if ($bankCapital->getSalesOrderId()): ?>
                    Энэ цэнэглэлтийн ЗАРЛАГА үүссэн байна.
                    <button id="successoutcome">Амжилттай төлөвт оруулах </button>  
                <?php else: ?>
                    <button id="reoutcome">Дахин зарлага хийх</button>
                <?php endif; ?>
            <?php else: ?>
                <label>Цэнэглэсэн дугаар:</label>
                <input type="text" value="<?php echo $bankCapital->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>

<script>
    function doSend(){
        if (confirm('Та ' + $('#capitalChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
                if ($('button#recharge').data('submitted') == '1') {
                    return false;
                } else {
                    $('button#recharge')
                      .attr('data-submitted', '1')
                      .addClass('submitting')
                      .attr('disabled','disabled');
                    $('form.capital').attr('action', '<?php echo url_for('@bank_capital_recharge') ?>');
                    $('form.capital').submit();
                }
        }
    }

    $('button#reoutcome').click(function() {
        if (confirm('Та ' + $('#capitalChargeMobile').val() + ' дугаарт ЗАРЛАГА хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.capital').attr('action', '<?php echo url_for('@bank_capital_reoutcome') ?>');
            $('form.capital').submit();
        }
    });

    $('button#successoutcome').click(function() {
        if (confirm('Та ' + $('#capitalChargeMobile').val() + ' дугаартай цэнэглэлтийг АМЖИЛТТАЙ төлөвт оруулах гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.capital').attr('action', '<?php echo url_for('@bank_capital_outcome_success') ?>');
            $('form.capital').submit();
        }
    });
</script>
