<!DOCTYPE html>

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="title" content="BANKPAYMENT" />
        <title>BANKPAYMENT</title>
        <link rel="shortcut icon" href="/images/favicon.ico" />
        <link rel="stylesheet" type="text/css" media="screen" href="/css/jqgrid/ui.jqgrid-bootstrap.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="/css/jqgrid/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="/js/facebox/facebox.css" />
        <script type="text/javascript" src="/js/jqgrid/jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="/js/jqgrid/jquery.jqGrid.min.js"></script>
        <script type="text/javascript" src="/js/jqgrid/i18n/grid.locale-en.js"></script>
        <script type="text/javascript" src="/js/jqgrid/bootstrap.min.js"></script>
        <script type="text/javascript" src="/js/facebox/facebox.js"></script>
    </head>
    <body>
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

        <?php echo $sf_content ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('a[rel*=facebox]').facebox();
            });
        </script>
    </body>
</html>