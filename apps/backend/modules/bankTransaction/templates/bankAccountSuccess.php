<form class="khaan" id="form" method="POST" autocomplete="off">

    <fieldset>
        <legend>Банкны данс оруулах/засах</legend>
        <label for="bank">Банк:</label>
        <select id="bank" name="bank">
            <option value="">[сонгох]</option>
            <?php foreach ($banks as $i => $v): ?>
                <?php
                if (isset($bank) && $i == $bank) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                ?>
                <option value="<?php echo $i ?>" <?php echo $selected ?>><?php echo $v ?></option>
            <?php endforeach; ?>
        </select>
        <br/>
        <label for="company">Компани:</label>
        <select id="company" name="company">
            <option value="">[сонгох]</option>
            <?php foreach ($companies as $i => $v): ?>
                <?php
                if (isset($company) && $i == $company) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                ?>
                <option value="<?php echo $i ?>" <?php echo $selected ?>><?php echo $v ?></option>
            <?php endforeach; ?>
        </select>
        <br/>
        <label for="type">Төрөл:</label>
        <select id="type" name="type">
            <option value="">[сонгох]</option>
            <?php foreach ($types as $i => $t): ?>
                <?php
                if (isset($type) && $i == $type) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                ?>
                <option value="<?php echo $i ?>" <?php echo $selected ?>><?php echo $t ?></option>
            <?php endforeach; ?>
        </select>
        <br/>
        <label for="account">Дансны нэр (alias):</label>
        <input type="text" id="account_alias" name="account_alias" value="<?php echo isset($accountAlias) ? $accountAlias : '' ?>"/>
        <br/>
        <label for="account">Данс:</label>
        <input type="text" id="account" name="account" value="<?php echo isset($account) ? $account : '' ?>"/>
        <br/>
        <label for="sap_account">SAP данс:</label>
        <input type="text" id="sap_account" name="sap_account" value="<?php echo isset($sapAccount) ? $sapAccount : '' ?>"/>
        <br/>
        <label for="sap_gl_account">SAP данс (GL):</label>
        <input type="text" id="sap_gl_account" name="sap_gl_account" value="<?php echo isset($sapGlAccount) ? $sapGlAccount : '' ?>"/>
        <br/>
        <label for="sap_gl_account">Банк код:</label>
        <input type="text" id="bank_code" name="bank_code" value="<?php echo isset($bankCode) ? $bankCode : '' ?>"/>
        <br/>
        <label></label>   
        <input type="hidden" value="<?php echo $id ?>" name="id"  />
        <input type="submit" value="хадгалах"  />
    </fieldset>
</form>
<table>
    <thead>
        <tr>
            <th  width="20">№</th>
            <th  width="70">Банк</th>
            <th  width="70">Компани</th>
            <th  width="70">Төрөл</th>
            <th  width="90">Дансны нэр </th>
            <th  width="90">Данс(Банкнаас хуулга татахад ашиглана)</th>
            <th  width="90">SAP1</th>
            <th  width="90">SAP2</th>
            <th  width="90">Банк код</th>
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
                <td><?php echo $row->getBank()->getName(); ?></td>
                <td><?php echo $row->getCompany()->getName(); ?></td>
                <td><?php echo BankAccountTable::getTypeName($row->getType()); ?></td>
                <td><?php echo $row->getAccountAlias(); ?></td>
                <td><?php echo $row->getAccount(); ?></td>
                <td><?php echo $row->getSapAccount(); ?></td>
                <td><?php echo $row->getSapGlAccount(); ?></td>
                <td><?php echo $row->getBankCode(); ?></td>
                <td align="center">
                    <?php echo link_to(image_tag('icons/edit.png', array('title' => 'засах')), '@transaction_bank_account?id=' . $row->id) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>
