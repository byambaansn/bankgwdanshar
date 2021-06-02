<form class="khaan" id="form" method="POST">
    <fieldset>
        <legend>&nbsp;Төлөлт шүүх тохиргоо оруулах / засах&nbsp;</legend>
        <label for="priority">Шүүлт хийх эрэмбэ:</label>
        <input type="number" id="priority" name="priority" value="<?php echo isset($priority) ? $priority : '' ?>"/>
        <br/>
        <label for="filter">Шүүх үг:</label>
        <input type="text" id="filter" name="filter" value="<?php echo isset($filter) ? $filter : '' ?>"/>
        <br/>
        <label for="accType">Дансны ангилал:</label>
        <select id="accType" name="accType">
            <?php 
                $option = '<option value="%s" %s>%s</option>';
                if(!isset($accType)) $accType = TransactionTable::TYPE_ALL;
                foreach ($accTypes as $i => $v) {
                    $selected = ($i == $accType) ? 'selected="selected"' : '';
                    printf($option,$i,$selected,$v);
                }
            ?>
        </select>
        <br/>
        <label for="paymentType">Төлбөрийн төрөл:</label>
        <select id="paymentType" name="paymentType">
            <?php 
                if(!isset($paymentType)) $paymentType = PaymentTypeTable::AUTO;
                foreach ($paymentTypes as $i => $type): ?>
                <optgroup label="<?php echo $i ?>">
                    <?php
                        foreach ($type as $j => $v) {
                            $selected = ($j == $paymentType) ? 'selected="selected"' : '';
                            printf($option,$j,$selected,$v);
                        }
                    ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
        <br/>
        <label for="filterType">Шүүлт хийх төрөл:</label>
        <select id="filterType" name="filterType">
            <?php 
                if(!isset($filterType)) $filterType = ConfigAssignmentTable::FILTER_WORD;
                foreach ($filterTypes as $i => $v) {
                    $selected = ($i == $filterType) ? 'selected="selected"' : '';
                    printf($option,$i,$selected,$v);
                }
            ?>
        </select>
        <br/>
        <label for="filterDay">Шүүлт ажиллах өдөр:</label>
        <select id="filterDay" name="filterDay">
            <?php 
                if(!isset($filterDay)) $filterDay = ConfigAssignmentTable::FILTER_EVERY_DAY;
                foreach ($filterDays as $i => $v) {
                    $selected = ($i == $filterDay) ? 'selected="selected"' : '';
                    printf($option,$i,$selected,$v);
                }
            ?>
        </select>
        <br/>
        <label for="status">Шүүлтийн төлөв:</label>
        <select id="status" name="status">
            <?php 
                if(!isset($status)) $status = ConfigAssignmentTable::STATUS_ACTIVE;
                foreach ($statuses as $i => $v) {
                    $selected = ($i == $status) ? 'selected="selected"' : '';
                    printf($option,$i,$selected,$v);
                }
            ?>
        </select>
        <br/>
        <label></label>   
        <input type="hidden" name="id" value="<?php echo $id ?>" />
        <input type="submit" value="хадгалах" />
    </fieldset>
</form>
<table>
    <thead>
        <tr>
            <th  width="20">№</th>
            <th  width="70">Шүүх үг</th>
            <th  width="20">Ангилал</th>
            <th  width="20">Төлбөрийн төрөл</th>
            <th  width="20">Шүүлтийн төрөл</th>
            <th  width="20">Өдөр</th>
            <th  width="20">Төлөв</th>
            <th  width="20">Үйлдэл</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td align="right"><?php echo $row['priority']; ?>&nbsp;</td>
                <td><?php echo $row['filter']; ?></td>
                <td><?php echo $accTypes[$row['acc_type']]; ?></td>
                <td><?php echo $row['PaymentType']['name']; ?></td>
                <td><?php echo $filterTypes[$row['filter_type']]; ?></td>
                <td align="center" <?php if($row['filter_day']) echo 'bgcolor="yellow"' ?>><?php if($row['filter_day']) echo $row['filter_day']; ?></td>
                <td><?php echo $statuses[$row['status']]; ?></td>
                <td align="center">
                    <?php echo link_to(image_tag('icons/edit.png', array('title' => 'засах')), '@transaction_config_assignment?id=' . $row['id']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
