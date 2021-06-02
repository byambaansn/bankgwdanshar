<div id="recharge-div">
    <form class="golomt" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankGolomt->id ?>" />

        <fieldset>
            <legend>Цэнэглэлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankGolomt->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankGolomt->order_p ?>
            <br />
            <b>Цэнэглэлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankGolomtTable::getStatusName($bankGolomt->status, BankGolomtTable::TYPE_DEALER) ?></legend>

            <?php if ($bankGolomt->canReCharge()): ?>
                <label for="golomtChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="golomtChargeMobile" name="golomtChargeMobile" value="<?php echo $bankGolomt->charge_mobile ?>"  />
                <button id="recharge" onclick="doSend();">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
                <?php
            elseif ($bankGolomt->canReOutcome()):
                ?>
                <label for="golomtChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="golomtChargeMobile" name="golomtChargeMobile" value="<?php echo $bankGolomt->charge_mobile ?>" maxlength="8" disabled="disabled" />
                <br/>
                <?php if ($bankGolomt->getSalesOrderId()): ?>
                    Энэ цэнэглэлтийн ЗАРЛАГА үүссэн байна.
                    <button id="successoutcome">Амжилттай төлөвт оруулах </button>  
                <?php else: ?>
                    <button id="reoutcome">Дахин зарлага хийх</button>
                <?php endif; ?>
            <?php else: ?>
                <label>Цэнэглэсэн дугаар:</label>
                <input type="text" value="<?php echo $bankGolomt->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>

<script>
    function doSend(){
        if (confirm('Та ' + $('#golomtChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
                if ($('button#recharge').data('submitted') == '1') {
                    return false;
                } else {
                    $('button#recharge')
                      .attr('data-submitted', '1')
                      .addClass('submitting')
                      .attr('disabled','disabled');
                    $('form.golomt').attr('action', '<?php echo url_for('@bank_golomt_recharge') ?>');
                    $('form.golomt').submit();
                    
                }
        }
    }

    $('button#reoutcome').click(function () {
        if (confirm('Та ' + $('#golomtChargeMobile').val() + ' дугаарт ЗАРЛАГА хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.golomt').attr('action', '<?php echo url_for('@bank_golomt_reoutcome') ?>');
            $('form.golomt').submit();
        }
    });

    $('button#successoutcome').click(function () {
        if (confirm('Та ' + $('#golomtChargeMobile').val() + ' дугаартай цэнэглэлтийг АМЖИЛТТАЙ төлөвт оруулах гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.golomt').attr('action', '<?php echo url_for('@bank_golomt_outcome_success') ?>');
            $('form.golomt').submit();
        }
    });
</script>
