<form id="form" action="<?php echo url_for('@refund_list') ?>">
                <fieldset style="width:430px; height: 200px; float: left; margin-right: 200px">
                    <legend>ХАЙЛТ</legend>

                    <label for="dateFrom">Огноо:</label>
                    <input type="text" id="dateFrom" name="dateFrom" value="<?php echo $dateFrom ?>" readonly />-c
                    <input type="text" id="dateTo" name="dateTo" value="<?php echo $dateTo ?>" readonly /> хүртэл

                    <label for="number">Утасны дугаар:</label>
                    <input type="text" name="number" value="<?php echo $number ?>" />

                    <label for="contract">Гэрээний дугаар:</label>
                    <input type="text" name="contract" value="<?php echo $contract ?>" />

                    <label for="orderDesc">Гүйлгээний утга:</label>
                    <input type="text" name="orderDesc" value="<?php echo $orderDesc ?>" />
                </fieldset>
                <fieldset style="width:430px; height: 200px;">
                    <legend>НЭМЭЛТ ХАЙЛТ</legend>

                    <label for="number">Төлбөрийн төрөл:</label>
                    <select name="payment_type">
                        <option value="4" <?php echo ($type == 4) ? 'selected="selected"' : '' ?>>Яриа</option>
                        <option value="1" <?php echo ($type == 1) ? 'selected="selected"' : '' ?>>Мобинет</option>
                    </select>

                    <label for="number">Тайлангийн төрөл:</label>
                    <select name="refund_type">
                        <option value="1" <?php echo ($refundType == 1) ? 'selected="selected"' : '' ?>>Буцаалтын тайлан</option>
                        <option value="2" <?php echo ($refundType == 2) ? 'selected="selected"' : '' ?>>Засвар болон гүйлгээ хуваасан</option>
                    </select>
                </fieldset>
                <center style="padding-right: 200px">
                    <input type="submit" value="Хайх" />
                    <?php if ($hasExcelCredential): ?>
                    <a href="<?php echo url_for('@refund_list_excel?' . $urlParams) ?>"><input type="button" value="Excel"/></a>
                    <?php endif; ?>
                </center>
</form>
<div style="overflow-x: auto;width: 100%">

    <table style="width:1650px!important">
        <thead>
            <tr>
                <th  width="20" rowspan="2">№</th>
                <th width="90" colspan="6">Банкны хуулганы мэдээлэл</th>
                <th width="90" colspan="3">Гэрээтэй холбоотой мэдээлэл</th>
                <th rowspan="2" width="60">Хуучин төлөлт оруулсан ажилтан</th>
                <th rowspan="2" width="80">Хуучин төлөлт оруулсан огноо</th>
                <th width="250" colspan="6"><?php echo ($refundType == 1) ? 'Буцаалтын ' : 'Засварын ' ?>мэдээлэл</th>
                <th rowspan="2" width="60">Төлөлт оруулсан ажилтан</th>
                <th rowspan="2" width="80">Төлөлт оруулсан огноо</th>
                <th rowspan="2" width="60">Салбар</th>
                <th rowspan="2" width="150">Тайлбар</th>
            </tr>
            <tr>
                <th rowspan="2" width="90">Төлсөн суваг</th>
                <th rowspan="2" width="90">Дансны дугаар</th>
                <th rowspan="2" width="80">Банк огноо</th>
                <th rowspan="2" width="90">Төлбөрийн төрөл</th>
                <th rowspan="2" width="150">Гүйлгээний утга</th>
                <th rowspan="2" width="70">Төлөлтийн дүн</th>
                
                <th rowspan="2" width="100">НӨАТ илгээгч компани</th>
                <th rowspan="2" width="60">Утасны дугаар</th>
                <th rowspan="2" width="60">Гэрээний дугаар</th>
                
                <th rowspan="2" width="70">Шинэ төлөлтийн дүн</th>
                <th rowspan="2" width="150"><?php echo ($refundType == 1) ? 'Буцаалтын ' : 'Засварын ' ?>төрөл</th>
                <th rowspan="2" width="90">Өмнөх утга</th>
                <th rowspan="2" width="90">Шинэ утга</th>
                <th rowspan="2" width="60"><?php echo ($refundType == 1) ? 'Буцаалт ' : 'Засвар ' ?>хийсэн ажилтан</th>
                <th rowspan="2" width="80"><?php echo ($refundType == 1) ? 'Буцаалт ' : 'Засвар ' ?>хийсэн огноо</th>
            </tr>
        </thead>
        <tbody>
            
            <?php $i = 1; foreach ($refundResutlt as $refund): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $refund['bank_name'] ?></td>
                    <td><?php echo $refund['bank_account'] ?></td>
                    <td><?php echo $refund['order_date'] ?></td>
                    <td><?php echo $refund['item_type'] ?></td>
                    <td><?php echo $refund['order_desc'] ?></td>
                    <td><?php echo $refund['order_amount'] ?></td>
                    <td><?php echo $refund['vat_company'] ?></td>
                    <td><?php echo $refund['number'] ?></td>
                    <td><?php echo $refund['contract_number'] ?></td>
                    <td><?php echo $refund['username'] ?></td>
                    <td align="center"><?php echo $refund['updated_at'] ?></td>
                    <td><?php echo $refund['paid_amount'] ?></td>
                    <td bgcolor="#a8cdf3"><b><?php echo $refund['refund_type'] ?></b></td>
                    <td bgcolor="#a8cdf3"><font color="red"><b><?php echo $refund['old_value'] ?></b></font></td>
                    <td bgcolor="#a8cdf3"><font color="green"><b><?php echo $refund['new_value'] ?></b></font></td>
                    <td><?php echo $refund['refund_user'] ?></td>
                    <td align="center"><?php echo $refund['refund_date'] ?></td>
                    <td><?php echo $refund['new_username'] ?></td>
                    <td align="center"><?php echo $refund['new_updated_at'] ?></td>
                    <td><?php echo $refund['payment_type'] ?></td>
                    <td><?php echo $refund['refund_desc'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $('#dateFrom').datepicker();
    $('#dateTo').datepicker();
</script>
