<div id="recharge-div">
    <form id="form" method="POST" autocomplete="off" action="<?php echo url_for('@bankpayment_ulusnet_update'); ?>">
        <input type="hidden" name="id" value="<?php echo $bankpayment['id'] ?>" />
        <fieldset>
            <legend>Утасны дугаар засах</legend>
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td><b>БАНК:</b></td>
                        <td> <?php echo VendorTable::getNameById($bankpayment['vendor_id']) ?></td>
                    </tr>
                    <tr>
                        <td><b>Дансны дугаар:</b></td>
                        <td> <?php echo $transaction['bank_account'] ?></td>
                    </tr>
                    <tr>
                        <td><b>Банк огноо:</b></td>
                        <td><?php echo $transaction['order_date'] ?></td>
                    </tr>
                    <tr>
                        <td><b>Гүйлгээний утга:</b></td>
                        <td>   <?php echo $transaction['order_p'] ?></td>
                    </tr>
                    <tr>
                        <td>   <b>Төлөлтийн дүн:</b></td>
                        <td>  <?php echo $transaction['order_amount'] ?></td>
                    </tr>
                    <tr>
                        <td><b>Төлөв:</b></td>
                        <td>  <?php echo $bankpayment['status'] ?></td>
                    </tr>
                    <tr>
                        <td> <b>Гэрээний дугаар:</b></td>
                        <td>   <?php echo $bankpayment['contract_number'] ?></td>
                    </tr>
                    <tr>
                        <td> <b>Гэрээний үлдэгдэл:</b></td>
                        <td> <?php echo $bankpayment['contract_amount'] ?></td>
                    </tr>
                    <tr>
                        <td><b>Гэрээний нэр:</b></td>
                        <td> <?php echo $bankpayment['contract_name'] ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Утасны дугаар:</td>
                        <td><input type="text" id="number" name="number" value="" maxlength="8" /></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center">
                            <input class="btn btn-success" type="submit" value="засах">
                        </td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </form>
</div>
<script>
    $(document).ready(function () {
        $('#subtract').on('input', function () {
            var subtrahend = $('#subtrahend').html();
            $('#answer').html(subtrahend - $('#subtract').val());
        });
    });

</script>
