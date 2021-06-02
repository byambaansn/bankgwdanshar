<form id="form" method="POST">
    <fieldset>
        <legend>&nbsp;Хаалтын огноо&nbsp;</legend>
        <label for="blockType">Хаалтын төрөл:</label>
        <select id="blockType" name="blockType">
            <?php
                $option = '<option value="%s" %s>%s</option>';
                if(!isset($blockType)) $blockType = BlockDateTable::BLOCK_PAYMENT;
                foreach ($blockTypes as $i => $v) {
                    $selected = ($i == $blockType) ? 'selected="selected"' : '';
                    printf($option,$i,$selected,$v);
                }
            ?>
        </select>
        <br/>
        <label for="block_date" class="label">Зарлагын огноо:</label>
            <input type="text" id="block_date" name="block_date" value="<?php echo $block_date ?>" readonly />
        <br/>
        <label for="is_active">Төлөв:</label>
        <select id="is_active" name="is_active">
            <option value="0" <?php echo $is_active ? '' : 'selected="selected"' ?>>Идэвхгүй</option>
            <option value="1" <?php echo $is_active ? 'selected="selected"' : '' ?>>Идэвхтэй</option>
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
            <th  width="70">Хаалтын төрөл</th>
            <th  width="20">Огноо</th>
            <th  width="20">Үүсгэсэн</th>
            <th  width="20">Үүсгэсэн огноо</th>
            <th  width="20">Төлөв</th>
            <th  width="20">Үйлдэл</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $row['block_type'] == 'BLOCK_PAYMENT' ? 'Төлбрийн хаалт' : '' ?></td>
                <td><?php echo $row['block_date'] ?></td>
                <td><?php echo $row['created_user_id'] ?></td>
                <td><?php echo $row['created_at'] ?></td>
                <td><?php echo $row['is_active'] ? 'Идэвхтэй' : 'Идэвхгүй' ?></td>
                <td align="center">
                    <?php echo link_to(image_tag('icons/edit.png', array('title' => 'засах')), '@bankpayment_block_date?id=' . $row['id']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript">
    $("#block_date").datepicker({dateFormat: 'yy-mm-dd'});
</script>