<div id="recharge-div">
    <form id="form" method="POST" autocomplete="off" action="<?php echo url_for('@bankpayment_update_copy'); ?>">
        <input type="hidden" value="<?php echo $bankpayment['id']?>" name="id">
        <?php include_partial('bankpayment/copyPayment', array('bankpayment' => $bankpayment, 'transaction' => $transaction, 'types' => $types, 'isRefund' => 0)) ?>
        <input id="splitButton" name="splitButton" class="btn btn-success" type="submit" value="хуваах">
    </form>
</div>
<script>
    $('#splitButton').click(function () {
        $('#splitButton').hide();
    });
</script>    
