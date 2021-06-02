<?php include_partial('global/header'); ?>
<body>
    <div id="container">
        <div id="header">
            <h2>
                BANK COLLECTOR :. <?php echo $sf_user->getName() ?>
                <a href="<?php echo url_for('@user_info') ?>" rel="facebox"><img src="/images/icons/info.png" title="мэдээлэл" align="absmiddle" /></a>
            </h2>
            <div id="topmenu">
                <?php include_component_slot('mainmenu'); ?>
            </div>
        </div>
        <div id="top-panel">
            <div id="panel">
                <?php include_component_slot('submenu'); ?>
            </div>
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
