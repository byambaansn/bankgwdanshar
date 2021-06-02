<div id="recharge-div">
    <form class="khaan" id="form" method="POST" autocomplete="off" action="<?php echo url_for('@bank_dealer_merge_amount') ?>">
        <input name="vendorId" type="hidden" value="<?php echo $vendorId ?>">
        <fieldset>
            <legend>Гүйлгээг нэгтгэж цэнэглэх</legend>
            <b>Цэнэглэх дүн&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</b><input name="amount" type="text" value="<?php echo $mergeAmount ?>">
            <br />
            <b>Дилерийн төрөл :&nbsp;&nbsp;</b>
            <select name="type">
                <option value="0">Mobile</option>
                <option value="1">Agent</option>
            </select>
            <br />
            <br />
            <b>Дилерийн дугаар :&nbsp;&nbsp;</b><input name="mobile" type="text" value="">
            <br />

            <input type="submit" value="Цэнэглэх"/>
        </fieldset>
    </form>
</div>
