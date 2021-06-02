<option value="0">[бүгд]</option>
<?php foreach ($accountNumbers as $i => $v): ?>
    <optgroup label="<?php echo BankAccountTable::getTypeName($i) ?>">
        <?php foreach ($v as $id => $account): ?>
            <option value="<?php echo $id ?>" <?php echo $id == $accountNumber ? 'selected="selected"' : '' ?>><?php echo $account ?></option>
        <?php endforeach; ?>
    </optgroup>    
<?php endforeach; ?>
