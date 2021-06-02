<div id="recharge-div">
    <form id="form" class="candyLoan" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankpayment->id ?>" />
        <input type="hidden" name="bank_order_id" value="<?php echo $bank->id ?>" />
        <fieldset>
            <legend>Цэнэглэлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bank->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bank->order_p ?>
            <br />
            <b>Цэнэглэлтийн хариу:</b> <?php echo isset($chargeResponse['info']) ? $chargeResponse['info'] : $chargeResponse['error']['message'] ?>
            <br />
            <?php // echo isset($chargeResponse['result']['candyData']) ? json_encode($chargeResponse['result']['candyData']) : '-' ?>

        </fieldset>

        <fieldset>
            <legend><?php echo BankpaymentTable::getStatusName($bank->status) ?></legend>

            <?php
            if (in_array($bankpayment->status, array(
                        BankpaymentTable::STAT_PROCESS,
                        BankpaymentTable::STAT_FAILED_CHARGE,
                        BankpaymentTable::STAT_BANKPAYMENT_TRANS_VALUE,
                        BankpaymentTable::STAT_BANKPAYMENT_AMOUNT))
            && $canRefund == 1):
                ?>
                <label for="khaanChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="number" name="number" value="<?php echo $bankpayment->number ?>" maxlength="8" />
                <input type="hidden" name="vendor_id" value="<?php echo $bank->vendor_id ?>" />
                <button id="recharge">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
            <?php else: ?>
                <label>Цэнэглэсэн дугаар:</label>
                <input type="text" value="<?php echo $bankpayment->number ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>

<script>
    $('button#recharge').click(function () {
        if (confirm('Та ' + $('#number').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.candyLoan').attr('action', '<?php echo url_for('@bank_candy_recharge') ?>');
            $('form.candyLoan').submit();
        }
    });
</script>
