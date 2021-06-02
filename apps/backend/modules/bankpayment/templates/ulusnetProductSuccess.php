<form class="khaan" id="form" method="POST" autocomplete="off">
    <fieldset>
        <legend> Улуснэт цэнэглэх карт оруулах/засах</legend>
        <label for="bank">Код:</label>
        <input type="text" id="code" name="code" value="<?php echo isset($product) ? $product['code'] : '' ?>"/>
        <br/>
        <label for="type">Нэр:</label>
        <input type="text" id="name" name="name" value="<?php echo isset($product) ? $product['name'] : '' ?>"/>
        <br/>
        <label for="account">Үнэ:</label>
        <input type="text" id="price" name="price" value="<?php echo isset($product) ? $product['price'] : '' ?>"/>
        <br/>
        <label></label>   
        <input type="hidden" value="<?php echo $id ?>" name="id"  />
        <input type="submit" value="хадгалах"  />
    </fieldset>
</form>
<table>
    <thead>
        <tr>
            <th >№</th>
            <th >Код</th>
            <th >Нэр</th>
            <th >Үнэ</th>
            <th >Огноо</th>
            <th >Үйлдэл</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($rows as $row):
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $row['code'] ?></td>
                <td><?php echo $row['name'] ?></td>
                <td><?php echo $row['price'] ?></td>
                <td align="left"><?php echo $row['created_at'] ?></td>
                <td align="center">
                    <a href="<?php echo url_for('@bankpayment_ulusnet_card') . '?id=' . $row['id'] ?>"><img style="cursor: pointer" src="/images/icons/edit.png" alt="Засах" ></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>
