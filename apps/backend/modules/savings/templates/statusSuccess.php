<div id="recharge-div">
    <form class="savings" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankSavings->id ?>" />

        <fieldset>
            <legend>Цэнэглэлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankSavings->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankSavings->order_p ?>
            <br />
            <b>Цэнэглэлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankSavingsTable::getStatusName($bankSavings->status, BankSavingsAccountTable::ACCOUNT_DEALER) ?></legend>

            <?php if ($bankSavings->canReCharge()): ?>
                <label for="savingsChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="savingsChargeMobile" name="savingsChargeMobile" value="<?php echo $bankSavings->charge_mobile ?>"/>
                <button id="recharge" onclick="doSend();">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
                <?php
            elseif ($bankSavings->canReOutcome()):
                ?>
                <label for="savingsChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="savingsChargeMobile" name="savingsChargeMobile" value="<?php echo $bankSavings->charge_mobile ?>" maxlength="8" disabled="disabled" />
                <br/>
                <?php if ($bankSavings->getSalesOrderId()): ?>
                    Энэ цэнэглэлтийн ЗАРЛАГА үүссэн байна.
                    <button id="successoutcome">Амжилттай төлөвт оруулах </button>
                <?php else: ?>
                    <button id="reoutcome">Дахин зарлага хийх</button>
                <?php endif; ?>
            <?php else: ?>
                <?php if ($bankSavings->status == BankSavingsTable::STAT_NEW): ?>
                    <label>Цэнэглэсэн дугаар:</label>
                    <input type="text" value="<?php echo $bankSavings->charge_mobile ?>"  id="changeNumber" name="changeNumber"/>
                    <button id="changeNumber">Дугаар солих</button>
                <?php else: ?>
                    <label>Цэнэглэсэн дугаар:</label>
                    <input type="text" value="<?php echo $bankSavings->charge_mobile ?>" disabled="disabled" />
                <?php endif; ?>
            <?php endif; ?>
        </fieldset>
    </form>
</div>

<script>
    function doSend(){
        if (confirm('Та ' + $('#savingsChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
                if ($('button#recharge').data('submitted') == '1') {
                    return false;
                } else {
                    $('button#recharge')
                      .attr('data-submitted', '1')
                      .addClass('submitting')
                      .attr('disabled','disabled');
                    $('form.savings').attr('action', '<?php echo url_for('@bank_savings_recharge') ?>');
                    $('form.savings').submit();
                }
        }
    }
    $('button#changeNumber').click(function () {
        if (confirm('Та ' + $('#changeNumber').val() + ' дугаарт ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.savings').attr('action', '<?php echo url_for('@bank_savings_change_number') ?>');
            $('form.savings').submit();
        }
    });

    $('button#reoutcome').click(function () {
        if (confirm('Та ' + $('#savingsChargeMobile').val() + ' дугаарт ЗАРЛАГА хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.savings').attr('action', '<?php echo url_for('@bank_savings_reoutcome') ?>');
            $('form.savings').submit();
        }
    });

    $('button#successoutcome').click(function () {
        if (confirm('Та ' + $('#savingsChargeMobile').val() + ' дугаартай цэнэглэлтийг АМЖИЛТТАЙ төлөвт оруулах гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.savings').attr('action', '<?php echo url_for('@bank_savings_outcome_success') ?>');
            $('form.savings').submit();
        }
    });
</script>
