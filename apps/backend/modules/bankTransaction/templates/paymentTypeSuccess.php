<form class="khaan" id="form" method="POST" autocomplete="off">

    <fieldset>
        <legend>Төлбөрийн төрөл оруулах/засах</legend>

        <label for="name">Нэр:</label>
        <input type="text" id="name" name="name" value="<?php echo isset($name) ? $name : '' ?>"/>
        <br/>
        <label for="name">Төлөв:</label>
        <select id="status" name="status">
            <option value="">[сонгох]</option>
            <?php foreach ($statuses as $i => $v): ?>
                <?php
                if (isset($status) && $i == $status) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                ?>
                <option value="<?php echo $i ?>" <?php echo $selected ?>><?php echo $v ?></option>
            <?php endforeach; ?>
        </select>
        <br/>
        <label></label>   
        <input type="submit" value="хадгалах"  />
    </fieldset>
</form>
<table>
    <thead>
        <tr>
            <th  width="20">№</th>
            <th  width="70">Нэр</th>
            <th  width="90">Төлөв</th>
            <th  width="90">Үүссэн</th>
            <th  width="90">Үйлдэл</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($rows as $row):
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $row->getName(); ?></td>
                <td><?php echo PaymentTypeTable::getStatusName($row->getStatus()); ?></td>
                <td><?php echo $row->getCreatedAt(); ?></td>
                <td align="center">
                    <?php echo link_to(image_tag('icons/edit.png', array('title' => 'засах')), '@transaction_type?id=' . $row->id) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>