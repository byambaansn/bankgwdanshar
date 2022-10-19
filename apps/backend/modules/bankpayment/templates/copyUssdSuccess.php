<div id="recharge-div">
    <form id="form" method="POST" autocomplete="off" action="<?php echo url_for('@bankpayment_ussd_copy'); ?>">
        <input type="hidden" value="<?php echo $bankpayment['id']?>" name="id">
        <?php include_partial('bankpayment/copyUssd', array('bankpayment' => $bankpayment, 'transaction' => $transaction, 'isRefund' => 0)) ?>
        <input id="splitBut" name="splitBut" class="btn btn-success" type="submit" value="хуваах">
    </form>
</div>
<script>
    $('#splitBut').click(function () {
        $('#splitBut').hide();
    });
</script> 