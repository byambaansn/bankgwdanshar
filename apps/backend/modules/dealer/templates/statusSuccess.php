<div id="recharge-div">
    <form class="khaan" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankKhaan->id ?>" />

        <fieldset>
            <legend>Цэнэглэлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankKhaan->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankKhaan->order_p ?>
            <br />
            <b>Цэнэглэлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankKhaanTable::getStatusName($bankKhaan->status, BankKhaanTable::TYPE_DEALER) ?></legend>

            <?php if ($bankKhaan->canReCharge()): ?>
                <label for="khaanChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="khaanChargeMobile" name="khaanChargeMobile" value="<?php echo $bankKhaan->charge_mobile ?>" maxlength="8" />
                <button id="recharge" onclick="doSend();">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
                <?php
            elseif ($bankKhaan->canReOutcome()):
                ?>
                <label for="khaanChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="khaanChargeMobile" name="khaanChargeMobile" value="<?php echo $bankKhaan->charge_mobile ?>" maxlength="8" disabled="disabled" />
                <br/>
                <?php if ($bankKhaan->getSalesOrderId()): ?>
                    Энэ цэнэглэлтийн ЗАРЛАГА үүссэн байна.
                    <button id="successoutcome">Амжилттай төлөвт оруулах </button>  
                <?php else: ?>
                    <button id="reoutcome">Дахин зарлага хийх</button>
                <?php endif; ?>
            <?php else: ?>
                <label>Цэнэглэсэн дугаар:</label>
                <input type="text" value="<?php echo $bankKhaan->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>

<script>
    function doSend(){
        if (confirm('Та ' + $('#khaanChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
                if ($('button#recharge').data('submitted') == '1') {
                    return false;
                } else {
                    $('button#recharge')
                      .attr('data-submitted', '1')
                      .addClass('submitting')
                      .attr('disabled','disabled');
                    $('form.khaan').attr('action', '<?php echo url_for('@bank_khaan_recharge') ?>');
                    $('form.khaan').submit();
                }
        }
    }

    $('button#reoutcome').click(function() {
        if (confirm('Та ' + $('#khaanChargeMobile').val() + ' дугаарт ЗАРЛАГА хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.khaan').attr('action', '<?php echo url_for('@bank_khaan_reoutcome') ?>');
            $('form.khaan').submit();
        }
    });

    $('button#successoutcome').click(function() {
        if (confirm('Та ' + $('#khaanChargeMobile').val() + ' дугаартай цэнэглэлтийг АМЖИЛТТАЙ төлөвт оруулах гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.khaan').attr('action', '<?php echo url_for('@bank_khaan_outcome_success') ?>');
            $('form.khaan').submit();
        }
    });
</script>
