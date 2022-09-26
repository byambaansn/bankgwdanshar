<?php echo include_component('bankpayment', 'searchForm', array('ussd' => 1, 'status' => $status, 'type' => $type, 'keyword' => $keyword)); ?>
<form id="form" action="<?php echo url_for('@bankpayment_ussd_update') ?>" method="POST" autocomplete="off">
    <table>
        <thead>
            <tr>
                <th  width="20" rowspan="2">№</th>
                <th  width="90" colspan="5">Банкны хуулганы мэдээлэл</th>
                <th  width="40" rowspan="2">Төлөв</th>
                <th  width="40" rowspan="2">Цэнэглэлийн хариу</th>
                <th  width="90" colspan="3">Хэрэглэгчийн мэдээлэл</th>
                <th  width="120" rowspan="2">Ажилтан</th>
                <th  width="40" rowspan="2">Төлөлтийн огноо</th>
                <th  width="80" rowspan="2">Огноо</th>
                <th  width="120" colspan="3">Үйлдэл</th>
            </tr>
            <tr>
                <th  width="50">Банк</th>
                <th  width="90">Дансны дугаар</th>
                <th colspan="1" width="70">Банк огноо</th>
                <th colspan="1" width="150">Гүйлгээний утга</th>
                <th width="60">Төлөлтийн дүн</th>
                <th width="70">Утасны дугаар</th>
                <th width="70">Гэрээний дугаар</th>
                <th width="120">Гэрээний нэр</th>
                <th width="40">Засах</th>
                <th width="40">Хувил</th>
                <th width="40">Буцаах</th>
                <th width="40"> <input type="checkbox" class="chAll" name="chAll"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($rows as $row):
                ?>
            <?php $statusName = BankpaymentTable::getStatusName($row['status'], TRUE); ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $row['bank_name'] ?></td>
                <td><?php echo $row['bank_account'] ?></td>
                <td><?php echo $row['order_date'] ?></td>
                <td align="left"><?php echo $row['order_p'] ?></td>
                <td><?php echo $row['order_amount'] ?></td>
                <td align="left">
                    <?php echo $statusName ?>
                    <span title="Оролдлогын тоо">
                        (<?php echo $row['try_count'] ?>)
                    </span>
                </td>
                <td align="center"><?php echo $row['status_comment'] ?></td>
                <td align="center"><?php echo $row['number'] ?></td>
                <td align="center"><?php echo $row['contract_number'] ?></td>
                <td align="left"><?php echo $row['contract_name'] ?></td>
                <td align="center"><?php echo $row['username'] ?></td>
                <td align="center"><?php echo $row['updated_at'] ?></td>
                <td align="left"><?php echo $row['created_at'] ?></td>
                <td align="center">
                    <a rel="facebox" href="<?php echo url_for('@bankpayment_ussd_update') . '?id=' . $row['id'] ?>"><img
                            style="cursor: pointer" src="/images/icons/edit.png" alt="Засах"></a>
                </td>
                <td align="center">
                    <a rel="facebox" href="<?php echo url_for('@bankpayment_update_copy') . '?id=' . $row['id'] ?>"><img
                            style="cursor: pointer" src="/images/icons/copy.png" alt="Хувилах"></a>
                </td>

                <td align="center">
                    <a rel="facebox" href="<?php echo url_for('@bankpayment_return') . '?id=' . $row['id'] ?>"><img
                            style="cursor: pointer" src="/images/icons/return.png" alt="Буцаах"></a>
                </td>

                <td align="center" style="vertical-align: central">
                    <?php if ($row['status'] != BankpaymentTable::STAT_SUCCESS && $row['status'] != BankpaymentTable::STAT_NEW && $row['id']): ?>
                    <input class="tRow" type="checkbox" name="transaction[]" value="<?php echo $row['id']; ?>">
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">
                    <input name="cancel" type="submit" value="Боломжгүй болгох">
                </th>
                <th colspan="8"></th>
                <th colspan="4">
                    <input type="submit" value="Цэнэглэх">
                </th>
                <th>
                    <input type="checkbox" class="chAll" name="chAll" >
                </th>
            </tr>
        </tfoot>
    </table>

</form>
<script type="text/javascript">
$('#dateFrom').datepicker();
$('#dateTo').datepicker();

    $(document).ready(function () {
        $('.chAll').click(function (event) {  //on click
        if (this.checked) { // check select status
                $('input:checkbox.tRow').each(function () { //loop through each checkbox
                    this.checked = true;  //select all checkboxes with class "checkbox1"   
                $(this).parent().parent().addClass('selected');
            });
        } else {
                $('input:checkbox.tRow').each(function () { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "checkbox1"                      
                $(this).parent().parent().removeClass('selected');
            });
        }
    });
        $(".tRow").click(function () {
        if ($(this).is(':checked')) {
            $(this).parent().parent().addClass('selected');
        } else {
            $(this).parent().parent().removeClass('selected');
        }
    });

});

</script>