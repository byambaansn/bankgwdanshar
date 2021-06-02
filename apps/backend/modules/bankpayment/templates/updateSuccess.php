<div id="recharge-div">
    <form id="form" method="POST" autocomplete="off" action="<?php echo url_for('@bankpayment_update'); ?>">
        <input type="hidden" name="id" value="<?php echo $bankpayment['id'] ?>" />
        <fieldset>
            <legend>Гэрээний дугаар засах</legend>
            <div id="notification" style="display: none">sahdiashdiashd</div>
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
                        <td>Гэрээний дугаар:</td>
                        <td>
                            <input type="text" id="contract_number" name="contract_number" value="" maxlength="8" />
                            <input class="right btn btn-success" type="submit" value="засах">
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Төлөлт болгох:</td>
                        <td>
                            <select id="payment" name="payment">
                                <option value="0">[сонгох]</option>
                                <?php foreach ($types as $i => $type): ?>
                                    <optgroup label="<?php echo $i ?>">
                                        <?php foreach ($type as $id => $v): ?>
                                            <option value="<?php echo $id ?>" <?php echo $i == $type ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                            <input class="right btn btn-payment" type="button" value="Төлөлт болгох" name="btnPayment" id="btnPayment">
                        </td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </form>
</div>
<script>
    $(document).ready(function () {
        $('#notification').html("asdasdasd");
        $('#subtract').on('input', function () {
            var subtrahend = $('#subtrahend').html();
            $('#answer').html(subtrahend - $('#subtract').val());
        });
    });
    
    $('#btnPayment').click(function () {
        if ($('#payment').val() == 0)
            alert('Төлөлт болгох сонголт сонгоно уу!!!');
        else
            if (confirm('Та төлөлт болгохдоо итгэлтэй байна уу?')) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo url_for('@bankpayment_update_make_payment') ?>",
                    data: "id=" +<?php echo $bankpayment['id']; ?> + "&payment=" + $('#payment').val(),
                    dataType: "json",
                    success: function (data) {
                        if (data.code == 1) {
                            $('#btnPayment').hide();
//                            $('#btnPayment').setAttribute("disabled","disabled");
                            window.location.reload(true);
                        }
                        else if (data.code == 2) {
                            alert(data.message);
                        }
                        else {
                            alert(data.message);
                        }
                    },
                    error: function (data) {
                        alert('Систем дээр алдаа гарлаа! ');
                        console.log(data.message);
                    }
                });
            }
    });
</script>
