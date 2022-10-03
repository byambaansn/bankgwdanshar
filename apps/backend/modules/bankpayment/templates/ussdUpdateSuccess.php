<div id="recharge-div">
    <form id="form" method="POST" autocomplete="off" action="<?php echo url_for('@bankpayment_update'); ?>">
        <input type="hidden" name="id" value="<?php echo $bankpayment['id'] ?>" />
        <fieldset>
            <legend> Сонгож Цэнэглэх</legend>
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
                        <td> <?php echo $transaction['order_p'] ?></td>
                    </tr>
                    <tr>
                        <td> <b>Төлөлтийн дүн:</b></td>
                        <td> <?php echo $transaction['order_amount'] ?></td>
                    </tr>
                    <tr>
                        <td><b>Төлөв:</b></td>
                        <td> <?php echo $bankpayment['status'] ?></td>
                    </tr>
                    <tr>
                        <td> <b>Гэрээний дугаар:</b></td>
                        <td> <?php echo $bankpayment['contract_number'] ?></td>
                    </tr>
                    <tr>
                        <td> <b>Утасны дугаар:</b></td>
                        <td> <?php echo $bankpayment['number'] ?></td>
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
            <table>
                <tr>
                    <td>
                        <select id="selAction">
                            <option value="Bill">Төлбөр</option>
                            <option value="Unit">Нэгж цэнэглэх</option>
                            <option value="Data">Дата цэнэглэх</option>
                            <option value="SmallUnit">Задгай нэгж </option>
                        </select>
                    </td>
                </tr>
                <tbody id="paymentRow">
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
                                    <option value="<?php echo $id ?>"
                                        <?php echo $i == $type ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <?php endforeach; ?>
                            </select>
                            <input class="right btn btn-payment" type="button" value="Төлөлт болгох" name="btnPayment"
                                id="btnPayment">
                        </td>
                    </tr>
                </tbody>


                <tbody id="unitRow" style="display:none;">
                    <tr>
                        <td  class="number1">
                            <input id="number1" type="text" name="number1" value="" size="8" />
                        </td>
                        <td>
                            <input list="unitType" id="uType" name="unitType">
                            <datalist id="unitType">
                                <option value="promo">7000₮: 4000 нэгж+20 хоног 7gb дата </option>
                                <option value="1000_opt1">1000₮</option>
                                <option value="2500_opt1">2500₮</option>
                                <option value="5000_opt1">5000₮</option>
                                <option value="10000_opt1">10000₮</option>
                                <option value="20000_opt1">20000₮</option>
                                <option value="4000_opt2">4000₮: 1000 нэгж + 7 хоног яриа </option>
                                <option value="14000_opt2">14000₮: 4000 нэгж + 30 хоног яриа </option>
                                <option value="9000_opt2">9000₮: 2000 нэгж + бүх сүлжээнд 7 хоног яриа</option>
                                <option value="28000_opt2">28000₮: 4000 нэгж + бүх сүлжээнд 30 хоног яриа</option>
                            </datalist>
                        </td>
                    </tr>
                    <tr>
                        <td>Цэнэглэлт хийх</td>
                        <td>
                            <input class="right btn btn-chargeunit" type="button" value="Цэнэглэх" name="btnChargeUnit"
                                id="btnChargeUnit">
                        </td>
                    </tr>
                </tbody>
                <tbody id="dataRow" style="display:none;">
                    <tr>
                        <td class="number">
                            <input class="number"  id="number2"type="text" name="number" value="" size="8" />
                        </td>
                        <td>
                            <input list="dataType" id="dType" name="dataType">
                            <datalist id="dataType">
                                <option value="OnDemand_2DAY">2 хоног 2gb 2000₮</option>
                                <option value="OnDemand_4DAY">4 хоног 4gb 4000₮</option>
                                <option value="OnDemand_7DAY">7 хоног 7gb 6500₮</option>
                                <option value="OnDemand_15DAY">15 хоног 15gb 12000₮</option>
                                <option value="OnDemand_30DAY">30 хоног 5gb 12500₮</option>
                                <option value="OnDemand_S_30DAY">30 хоног 10gb 17500₮</option>
                                <option value="OnDemand_M_30DAY">30 хоног 20gb 28000₮</option>
                                <option value="OnDemand_L_30DAY">30 хоног 30gb 38000₮</option>
                                <option value="Unlimited_99GB">30 хоног 99gb 99000₮</option>
                                <option value="30GBNIGHT7">Шөнийн дата 7 хоног 30gb 1500₮</option>
                                <option value="Morning_7DAY">Өглөөний дата 7 хоног 50gb 1500₮</option>
                                <option value="Morning_15DAY">Өглөөний дата 15 хоног 150gb 3500₮</option>
                                <option value="Night_15DAY">Шөнийн дата 15 хоног 100gb 4000₮</option>
                                <option value="University_7DAY">Academy data багц /7 хоног/ 2000₮</option>
                                <option value="ENTER_3DAY">Entertainment багц /3 хоног/ 3000₮</option>
                                <option value="GAMING_30DAY_1GB">Gaming багц/30 хоног / 5000₮</option>
                                <option value="SOCIAL_30DAY">Social багц/30 хоног / 5000₮</option>
                                <option value="MUSIC_30DAY">Music багц/30 хоног / 5000₮</option>
                                <option value="TikTok_7DAY">Tiktok багц /7 хоног / 10000₮</option>
                                <option value="SOCIAL_30DAY_NEW">Social Unlimited багц /30 хоног/ 10000₮</option>
                                <option value="University_30DAY">Academy багц /30 хоног/ 10000₮</option>
                                <option value="NETFLIX_30DAY">Video багц /30 хоног/ 15000₮</option>
                                <option value="VOO_Unlimited_30DAY">VOO дата багц 30 хоног 5900₮</option>
                            </datalist>
                        </td>
                    </tr>
                    <tr>
                        <td>Цэнэглэлт хийх</td>
                        <td>
                            <input class="right btn btn-chargedata" type="button" value="Цэнэглэх" name="btnchargedata"
                                id="btnChargeData">
                        </td>
                    </tr>
                </tbody>
                <tbody id="smallUnitRow" style="display:none;">
                    <tr>

                        <td class="number">
                            <input id="number3" class="number" type="text" name="number" value="" size="8" />
                        </td>
                        <td id="amt0" class="amt">
                            <input id="amount" type="text" name="amount" value="0" size="10" />
                        </td>
                    </tr>
                    <tr>
                        <td>Цэнэглэлт хийх</td>
                        <td>
                            <input class="right btn btn-chargesmall" type="button" value="Цэнэглэх"
                                name="btnchargesmall" id="btnChargeSmall">
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#selAction').change(function() {
        if (this.value == 'Bill') {
            $('#paymentRow').show();
            $('#unitRow').hide();
            $('#dataRow').hide();
            $('#smallUnitRow').hide();
        } else if (this.value == 'Unit') {
            $('#paymentRow').hide();
            $('#unitRow').show();
            $('#dataRow').hide();
            $('#smallUnitRow').hide();
            $('#paymentType').val(0);
        } else if (this.value == 'Data') {
            $('#paymentRow').hide();
            $('#unitRow').hide();
            $('#dataRow').show();
            $('#smallUnitRow').hide();
            $('#paymentType').val(0);
        } else if (this.value == 'SmallUnit') {
            $('#paymentRow').hide();
            $('#unitRow').hide();
            $('#dataRow').hide();
            $('#smallUnitRow').show();
            $('#paymentType').val(0);
        }
    });
});

$(document).ready(function() {
    $('#notification').html("asdasdasd");
    $('#subtract').on('input', function() {
        var subtrahend = $('#subtrahend').html();
        $('#answer').html(subtrahend - $('#subtract').val());
    });
});
$('#btnPayment').click(function() {
    if ($('#payment').val() == 0)
        alert('Төлөлт болгох сонголт сонгоно уу!!!');
    else
    if (confirm('Та төлөлт болгохдоо итгэлтэй байна уу?')) {
        $.ajax({
            type: "POST",
            url: "<?php echo url_for('@bankpayment_update_make_payment') ?>",
            data: "id=" + <?php echo $bankpayment['id']; ?> + "&payment=" + $('#payment').val(),
            dataType: "json",
            success: function(data) {
                if (data.code == 1) {
                    $('#btnPayment').hide();
                    // $('#btnPayment').setAttribute("disabled","disabled");
                    window.location.reload(true);
                } else if (data.code == 2) {
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            },
            error: function(data) {
                alert('Систем дээр алдаа гарлаа! ');
                console.log(data.message);
            }
        });
    }
});


$('#btnChargeUnit').click(function() {
    if ($('#uType').val() == "")
        alert('Та цэнэглэх картаа сонгоно уу!!!');
    else
    if (confirm('Та цэнэглэхдээ итгэлтэй байна уу?')) {
    $.ajax({
        url: "<?php echo url_for('@bankpayment_ussd_chargeunit')?>",
        data: "id=" + <?php echo $bankpayment['id']; ?> + "&number="+$('#number1').val()+"&card="+$('#uType').val(),
        type: "POST",
        success: function(data) {
            alert("Амжилттай цэнэглэгдлээ");
        },
        error: function(data) {
            alert("Цэнэглэлт амжилтгүй");
        }
    });
}
});


$('#btnChargeData').click(function() {
    if ($('#uType').val() == "")
        alert('Та цэнэглэх картаа сонгоно уу!!!');
    else
    if (confirm('Та цэнэглэхдээ итгэлтэй байна уу?')) {
    $.ajax({
        url: "<?php echo url_for('@bankpayment_ussd_chargedata')?>",
        data: "number="+$('#number2').val()+"&card="+$('#dType').val(),
        type: "POST",
        success: function(data) {
            alert("Амжилттай цэнэгллээ");
        },
        error: function(data) {
            alert("Цэнэглэлт амжилтгүй");
        }
    });
  }
});

$('#btnChargeSmall').click(function() {
    if ($('#amount').val() == 0)
        alert('Та цэнэглэх дүнгээ оруулна уу!!!');
    else
    if (confirm('Та цэнэглэхдээ итгэлтэй байна уу?')) {

    $.ajax({
        url: "<?php echo url_for('@bankpayment_ussd_chargesmall')?>",
        data: "number="+$('#number3').val()+"&amount="+$('#amount').val()+"&order_amount="+ <?php echo $transaction['order_amount'] ?>,
        type: "POST",
        success: function(data) {
            alert("Амжилттай цэнэгллээ");
        },
        error: function(data) {
            alert("Цэнэглэлт амжилтгүй");
        }
    });
}
});
</script>