<div id="recharge-div">
    <form class="tdb" id="form" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bankTdb->id ?>" />

        <fieldset>
            <legend>Цэнэглэлтийн лог</legend>
            <b>Гүйлгээ:</b> <?php echo $bankTdb->order_id ?>
            <br />
            <b>Тайлбар:</b> <?php echo $bankTdb->order_p ?>
            <br />
            <b>Цэнэглэлтийн хариу:</b> <?php echo $chargeResponse['response'] ?>
            <br />

        </fieldset>

        <fieldset>
            <legend><?php echo BankTdbTable::getStatusName($bankTdb->status, BankTdbTable::TYPE_DEALER) ?></legend>

            <?php if ($bankTdb->canReCharge()): ?>
                <label for="tdbChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="tdbChargeMobile" name="tdbChargeMobile" value="<?php echo $bankTdb->charge_mobile ?>" />
                <button id="recharge" onclick="doSend();">Дахин цэнэглэх</button>
                <br />
                <div id="recharge-confirm">

                </div>
                <?php
            elseif ($bankTdb->canReOutcome()):
                ?>
                <label for="tdbChargeMobile">Цэнэглэх дугаар:</label>
                <input type="text" id="tdbChargeMobile" name="tdbChargeMobile" value="<?php echo $bankTdb->charge_mobile ?>" maxlength="8" disabled="disabled" />
                <br/>
                <?php if ($bankTdb->getSalesOrderId()): ?>
                    Энэ цэнэглэлтийн ЗАРЛАГА үүссэн байна.
                    <button id="successoutcome">Амжилттай төлөвт оруулах </button>  
                <?php else: ?>
                    <button id="reoutcome">Дахин зарлага хийх</button>
                <?php endif; ?>
            <?php else: ?>
                <label>Цэнэглэсэн дугаар:</label>
                <input type="text" value="<?php echo $bankTdb->charge_mobile ?>" disabled="disabled" />
            <?php endif; ?>
        </fieldset>
    </form>
</div>

<script>
    function doSend(){
        if (confirm('Та ' + $('#tdbChargeMobile').val() + ' дугаарт ДАХИН ЦЭНЭГЛЭЛТ хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
                if ($('button#recharge').data('submitted') == '1') {
                    return false;
                } else {
                    $('button#recharge')
                      .attr('data-submitted', '1')
                      .addClass('submitting')
                      .attr('disabled','disabled');
                    $('form.tdb').attr('action', '<?php echo url_for('@bank_tdb_recharge') ?>');
                    $('form.tdb').submit();
                }
        }
    }

    $('button#reoutcome').click(function () {
        if (confirm('Та ' + $('#tdbChargeMobile').val() + ' дугаарт ЗАРЛАГА хийх гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.tdb').attr('action', '<?php echo url_for('@bank_tdb_reoutcome') ?>');
            $('form.tdb').submit();
        }
    });

    $('button#successoutcome').click(function () {
        if (confirm('Та ' + $('#tdbChargeMobile').val() + ' дугаартай цэнэглэлтийг АМЖИЛТТАЙ төлөвт оруулах гэж байна.\nҮргэлжлүүлэх үү?')) {
            $('form.tdb').attr('action', '<?php echo url_for('@bank_tdb_outcome_success') ?>');
            $('form.tdb').submit();
        }
    });
</script>
