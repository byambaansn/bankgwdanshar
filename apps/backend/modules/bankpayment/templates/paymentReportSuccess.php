<?php echo include_component('bankpayment', 'searchForm', array('status' => $status, 'type' => $type, 'keyword' => $keyword, 'payType' => 1, 'accounts' => $accountNumbers)); ?>
<form id="form" action="<?php echo url_for('@bankpayment_payment_report') ?>" method="POST" autocomplete="off">
    <table>
        <thead>
            <tr>
                <th  width="20" rowspan="2">№</th>
                <th  width="90" colspan="5">Банкны хуулганы мэдээлэл</th>
                <th  width="40" rowspan="2">Төлөв</th>
                <th  width="40" rowspan="2">Төлөлтийн хариу</th>
                <th  width="90" colspan="5">Гэрээтэй холбоотой мэдээлэл</th>
                <th  width="120" rowspan="2">Ажилтан</th>
                <th  width="40" rowspan="2">Төлөлтийн огноо</th>
                <th  width="80" rowspan="2">Огноо</th>
                <th  width="80" rowspan="2">Салбар</th>
            </tr>
            <tr>
                <th  width="50">Банк</th>
                <th  width="90">Дансны дугаар</th>
                <th colspan="1" width="70">Банк огноо</th>
                <th colspan="1" width="150">Гүйлгээний утга</th>
                <th width="60">Төлөлтийн дүн</th>
                <th  width="70">Утасны дугаар</th>
                <th  width="70">Гэрээний дугаар</th>
                <th  width="80">Гэрээний үлдэгдэл</th>
                <th  width="120">Гэрээний нэр</th>
                <th  width="40">Гэрээний цикл</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($rows as $row):
                ?>
                <?php $statusName = BankpaymentTable::getStatusName($row['status'], TRUE); ?>
            <tr <?php if ($row['parent_id'] > 0): ?> class="water-green" <?php elseif ($row['pay_type'] != 'NO_REFUND'): ?> class="water-yellow" <?php endif; ?>>
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
                    <td align="center">
                        <?php
                        echo $row['number'];
                        if ($row['number']) {
                            echo '<a  rel="facebox" href="' . url_for('@bank_candy_remain_show?isdn=' . $row['number']) . '"><img style="cursor: pointer" src="/images/icons/candy.png" alt="Засах" ></a>';
                        }
                        ?>
                    </td>
                    <td align="center">
                        <?php echo $row['contract_number'] ?>
                    </td>
                    <td align="center"><?php echo $row['contract_amount'] ?></td>
                    <td align="left"><?php echo $row['contract_name'] ?></td>
                    <td align="center"><?php echo $row['bill_cycle'] ?></td>
                    <td align="center"><?php echo $row['username'] ?></td>
                    <td align="center"><?php echo $row['updated_at'] ?></td>
                    <td align="left"><?php echo $row['created_at'] ?></td>
                    <td align="center"><?php echo $row['payment_type'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</form>
<script type="text/javascript">
    $('#dateFrom').datepicker();
    $('#dateTo').datepicker();
</script>