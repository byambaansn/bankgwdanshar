<form class="transaction_type" action="<?php echo url_for('@to_bankpayment') ?>" method="POST">
    <input type="hidden" name="id" value="<?php echo $transaction_id ?>" />
    <font color="red"><b>Тухайн хуулга нь төлбөр болгосон гүйлгээ болно.</b></font>
    </br>
    <font color="red"><b>Түүний төрлийг солино гэдэгт ээ итгэлтэй байна уу?</b></font>
    <table width="100%">
        <tbody>      
            <tr>
                <td width="40%">
                    <label for="type" class="label">Төлөлт болгох төрөл:</label>
                </td>
                <td width="60%">
                    <select name="type" style="width:100%">
                        <?php foreach ($types as $i => $v): ?>
                        <option value="<?php echo $i ?>" <?php echo ($i == BankpaymentTable::TYPE_CALL_PAYMENT) ? 'selected="selected"' : '' ?>><?php echo $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>        
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" align="right">
                    <input type="submit" value="Хадгалах" />
                </td>
            </tr>
        </tfoot>
    </table>
</form>