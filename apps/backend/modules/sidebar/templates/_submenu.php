<ul>
    <?php
    foreach ($menus as $menuTab => $menu) {
        list($name, $uri, $class) = $menu;
        echo '<li class="' . isCurrentTab($sub_tab, $menuTab) . '">' . link_to($name, $uri, array('class' => $class)) . '</li>';
    }
    ?>
</ul>