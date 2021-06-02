<div id="recharge-div">
    <form id="form" method="POST" autocomplete="off" action="<?php echo url_for('@bankpayment_return'); ?>">
        <input type="hidden" value="<?php echo $bankpayment['id']?>" name="id">
        <fieldset>
            <legend>Гүйлгээг хувааж төлөлт болгох</legend>
            <table class="table table-striped">
                <tbody>
                <?php if($isSuccess):?>
                    <tr>
                        <td>
                            <b>НӨАТ буцаах :</b>
                        </td>
                        <td>
                            <select id="returnBill" name="returnBill">
                                <option value="0">[сонгох]</option>
                                <?php foreach ($returnBillList as $bill): ?>
                                    <option value="<?php echo $bill['ebarimt']['billId']?>" ><?php echo 'Төлөлтийн огноо: '.date('Y-m-d H:i:s',strtotime($bill['ebarimt']['vatDate'])).' Төлөлтийн дүн: '.$bill['ebarimt']['amount']?></option>
                                <?php endforeach; ?>
                                <?php //foreach ($returnBillList as $bill): ?>
                                    <!--<option value="<?php //echo $bill['c_billId']?>" ><?php //echo 'Төлөлтийн огноо: '.date('Y-m-d H:i:s',strtotime($bill['c_posresdate'])).' Төлөлтийн дүн: '.$bill['c_amount']?></option>-->
                                <?php //endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <b>Буцаах төрөл :</b>
                    </td>
                    <td>
                        <select id="returnType" name="returnType">
                            <option value="choose">[сонгох]</option>
                            <option value="notBill">Биллээс бусад төрлийн үйлилгээ авсан</option>
                            <option value="notValidContract">Гэрээ болон утасны дугаар буруу бичсэн</option>
                            <option value="copyContract">Илүү төлөлт болон төлөлтийг хуваах</option>
                        </select>
                    </td>
                </tr>
                <tr id="paymentRow" style="display: none">
                    <td>
                        <b>Төлөлт :</b>
                    </td>
                    <td>
                        <select id="paymentType" name="paymentType">
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
                </tr>
                <tr id="contractRow" style="display: none">
                    <td>
                        <b>Гэрээний дугаар :</b>
                    </td>
                    <td>
                        <input type="text" id="contract" name="contract"/>
                    </td>
                </tr>
                <tr id="desc">
                    <td style="Red">
                        <b>Тайлбар :</b>
                    </td>
                    <td>
                        <textarea rows="4" cols="50" name="description"></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
            <b class="red">Та амжилттай төлөвтэй гүйлгээг засаж байгаа бол АХ орж буруу төлөлтийг заавал устгана уу</b>
            <panel id="copyPanel" style="display: none">
                <?php include_partial('bankpayment/copyPayment', array('bankpayment' => $bankpayment, 'transaction' => $transaction, 'types' => $types, 'isRefund' => 1)) ?>
            </panel>
            <br/><img id="image" title="Буцаах" alt="Буцаах" src="/images/icons/return.png"><button id="refundBtn" type="submit" onclick="return confirm('Та төлөлт буцаана гэдэгт ээ итгэлтэй байна уу?') ? confirmed() : false;">Хадгалах</button>
        </fieldset>
    </form>
</div>
<script>
    $(document).ready(function () {
        $('#returnType').change(function () {
            if (this.value == 'notBill') {
                $('#paymentRow').show();
                $('#contractRow').hide();
                $('#copyPanel').hide();
                $('#paymentType').val(0);
            }
            else if (this.value == 'notValidContract') {
                $('#paymentRow').hide();
                $('#contractRow').show();
                $('#copyPanel').hide();
                $('#contract').val('');
            }
            else if (this.value == 'copyContract') {
                $('#paymentRow').hide();
                $('#contractRow').hide();
                $('#copyPanel').show();
            }
        });
    });
    
    function confirmed() {
        $('#refundBtn').hide();
        $('#image').hide();
        return true;
    }
</script>
