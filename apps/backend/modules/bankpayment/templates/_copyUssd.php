    <input type="hidden" value="<?php echo $transaction['order_amount']?>" id="bal" name="bal">
    <div>
        <fieldset>
            <legend>Гүйлгээг хуваах</legend>
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
                        <td> <b>Дугаар:</b></td>
                        <td>   <?php echo $bankpayment['number'] ?></td>
                    </tr>
                    <tr>
                        <td><b>Гэрээний нэр:</b></td>
                        <td> <?php echo $bankpayment['contract_name'] ?></td>
                    </tr>
                </tbody>
            </table>
            <table class="network_big_table">
            <thead>
                <tr>
                    <th>
                        Утасны дугаар
                    </th>
                    <th>
                        Дүн
                    </th>
                </tr>
            </thead>
            <tbody id="Rows">
                <tr id="RowCopy0">
                    <td id="number0" class="number">
                        <input id = "contNumber0" type="text" name="contNumber0" value="" size="20" />
                    </td>
                    <td id="amt0" class="amt">
                        <input id = "amount0" type="text" name="amount0" value="0" size="10" onchange="changeBalance()"/>
                    </td>
                </tr>
            </tbody>
        </table>
        <table>
            <tr>
                <button type="button" id="btnAdd"> Мөр нэмэх</button>
                <button type="button" id="btnRemove"> Мөр хасах</button>
                <input id="rowCount" type="hidden" value="0" name="rowCount">
            </tr>
            <tr>
                <td> Үлдэгдэл : <b id="balance" style="color:red"><?php echo $transaction['order_amount'] ?><b></td>
            </tr>
        </table>
        </fieldset>
    </div>
<script>
    $(document).ready(function () {
        $('#subtract').on('input', function () {
            var subtrahend = $('#subtrahend').html();
            $('#answer').html(subtrahend - $('#subtract').val());
        });
    });

    var index = 0;
    var amount = parseFloat($("#balance").text());

    $('#btnAdd').click(function () {
        index++;
        $('#Rows').append(template());
        $('#rowCount').val(index);
    });
    $('#btnRemove').click(function () {
        if (index != 0) {
            $('#RowCopy' + index).remove();
            index--;
            $('#rowCount').val(index);
            changeBalance();
        }
    });

    function template() {
        return '<tr id="RowCopy' + index + '">' +
                '<td class="number" id="number' + index + '"> ' +
                '<input id="contNumber' + index + '" name="contNumber' + index + '" type="text" size="20" > ' +
                '</input></td>' +
                '<td class="amt" id="amt' + index + '"> ' +
                '<input id="amount' + index + '" name="amount' + index + '" value="0" type="text" size="10" onchange="changeBalance()"> ' +
                '</input></td>' +
                '</tr>';
    }
    
    function changeBalance() {
        
        var total = 0;
        for (var i = 0, max = index; i <= max; i++) {
            total += parseFloat($("#amount" + i).val());
        }
        
        var amt = (amount - total).toFixed(2).toString();
        $("#balance").text(amt);
        $("#bal").val(amt);
    }
   
</script>