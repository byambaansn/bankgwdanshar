<div id="recharge-div">
    <form id="form" class="candyLoan" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $bank->id ?>" />

        <fieldset>
            <legend>CANDY дансны мэдээлэл</legend>
            <table>
                <tr>
                    <th colspan="2"> <?php echo $candyHtml; ?></th>
                </tr>
                <?php if (isset($candyLoan)): ?>
                    <?php foreach ($candyLoan['items'] as $row): ?>
                        <tr>
                            <td> <b>Total loan:</b></td>
                            <td><?php echo $row['total']; ?></td>
                        </tr>
                        <tr>
                            <td> <b>PlanDate:</b></td>
                            <td><?php echo $row['planDate']; ?></td>
                        </tr>
                        <tr>
                            <td> <b>StartDate:</b></td>
                            <td><?php echo $row['loan']['startDate']; ?></td>
                        </tr>
                        <tr>
                            <td> <b>Category:</b></td>
                            <td><?php echo $row['loan']['category']; ?></td>
                        </tr>

                        <tr>
                            <td> <b>Status:</b></td>
                            <td><?php echo $row['loan']['status']; ?></td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2"> No loan</td>
                    </tr>
                <?php endif; ?>
            </table>
        </fieldset>
    </form>
</div>

