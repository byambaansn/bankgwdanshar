<ul class="nav nav-pills">
    <?php
    foreach ($menus as $menuTab => $menu) {
        $menu = $menu['main'];
        $options = array_key_exists(2, $menu) ? $menu[2] : null;
        echo '<li class="' . isCurrentTab($tab, $menuTab) . '">' . link_to($menu[0], $menu[1], $options) . '</li>';
    }
    ?>
    <li><?php echo link_to('Гарах', '@logout', array('confirm' => 'Та системээс гарах гэж байна.\nҮргэлжлүүлэх үү?')) ?></li>
</ul>

