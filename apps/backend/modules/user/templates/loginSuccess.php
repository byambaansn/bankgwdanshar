<?php foreach ($form->getGlobalErrors() as $error): ?>
    <div class="warning"><?php echo $error ?></div>
<?php endforeach; ?>

<form id="form" action="<?php url_for('@login') ?>" method="POST">
    <input type="hidden" value="<?php echo $referer ?>" name="referer" />
    <fieldset>
        <legend>Нэвтрэх</legend>
        
        <?php echo $form['username']->renderError() ?>
        <?php echo $form['username']->renderLabel('Хэрэглэгчийн нэр:') ?>
        <?php echo $form['username'] ?>
        <br />
        
        <?php echo $form['password']->renderError() ?>
        <?php echo $form['password']->renderLabel('Нууц үг:') ?>
        <?php echo $form['password'] ?>
        <br />
        
        <label>&nbsp;</label>
        <input type="submit" value="Нэвтрэх" />
    </fieldset>
</form>
