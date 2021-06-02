<?php include_partial('global/header'); ?>
<body style="background-size: 100% 100px;">
    <div id="container">
        <div id="header">
            <h2> BANK COLLECTOR</h2>
        </div>
        <div id="wrapper">
            <div id="content">
                <?php if ($sf_user->hasFlash('success')): ?>
                    <div class="success message"><?php echo $sf_user->getFlash('success') ?></div>
                <?php endif; ?>
                <?php if ($sf_user->hasFlash('info')): ?>
                    <div class="info message"><?php echo $sf_user->getFlash('info') ?></div>
                <?php endif; ?>
                <?php if ($sf_user->hasFlash('warning')): ?>
                    <div class="warning message"><?php echo $sf_user->getFlash('warning') ?></div>
                <?php endif; ?>
                <?php if ($sf_user->hasFlash('error')): ?>
                    <div class="error message"><?php echo $sf_user->getFlash('error') ?></div>
                <?php endif; ?>

                <?php echo $sf_content ?>
            </div>
        </div>
        <?php include_partial('global/footer') ?>
    </div>
</body>
</html>
