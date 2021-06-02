    <input type="hidden" value="<?php echo $transaction['order_amount']?>" id="bal" name="bal">
    <div>
        <fieldset>
            <legend>Гүйлгээг хувааж төлөлт болгох</legend>
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
            </table>
            <table class="network_big_table">
            <thead>
                <tr>
                    <th>
                        Салбар эсэх
                    </th>
                    <th>
                        Гэрээний дугаар эсвэл салбар
                    </th>
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
                    <td id="checkBox0" class="checkBox">
                        <input id = "checkBranch0" type="checkbox" name="checkBranch0" value="0" onchange="viewBranch(0)"/>
                    </td>
                    <td id="contract0" class="contract">
                        <input id = "contractNum0" type="text" name="contractNum0" value="" size="20" />
                        <select id="payment0" name="payment0" hidden="" onchange="check(0)">
                            <option value="0">[сонгох]</option>
                            <?php foreach ($types as $i => $type): ?>
                                <optgroup label="<?php echo $i ?>">
                                    <?php foreach ($type as $id => $v): ?>
                                        <option value="<?php echo $id ?>" <?php echo $i == $type ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </td>
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
                '<td class="checkBox" id="checkBox' + index + '"> ' +
                '<input id="checkBranch' + index + '" name="checkBranch' + index + '" type="checkbox" value="0" onchange="viewBranch(' + index + ')"> ' +
                '</input></td>' +
                '<td class="contract" id="contract' + index + '"> ' +
                '<input id="contractNum' + index + '" name="contractNum' + index + '" type="text" size="20" > ' +
                '</input>' +
                '<select id="payment' + index + '" name="payment' + index + '" hidden="" onchange="check(' + index + ')"> ' +
                $('#payment0').html() +
                '</select></td>' +
                '<td class="number" id="number' + index + '"> ' +
                '<input id="contNumber' + index + '" name="contNumber' + index + '" type="text" size="20" > ' +
                '</input></td>' +
                '<td class="amt" id="amt' + index + '"> ' +
                '<input id="amount' + index + '" name="amount' + index + '" value="0" type="text" size="10" onchange="changeBalance()"> ' +
                '</input></td>' +
                '</tr>';
    }
    
    function viewBranch(index) {
        if($("#checkBranch" + index).val() == 0) {
            $('#contractNum' + index).hide();
            $('#contractNum' + index).val("");
            $('#contNumber' + index).hide();
            $('#contNumber' + index).val("");
            $('#payment' + index).show();
            $("#checkBranch" + index).val(1);
        }
        else {
            $('#contractNum' + index).show();
            $('#contNumber' + index).show();
            $('#payment' + index).hide();
            $('#payment' + index).val(0);
            $("#checkBranch" + index).val(0);
        }
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
    
    function check(branch) {
        for (var i = 0, max = index; i <= max; i++) {
            if (branch == i) continue;
            if ($('#payment' + i).val() == $('#payment' + branch).val() && $("#checkBranch" + i).val() != 0) {
                alert("Тухайн салбар сонгогдсон тул өөр салбар сонгоно уу!!!");
                $('#payment' + branch).val(0);
                break;
            }
        }
    }
</script>