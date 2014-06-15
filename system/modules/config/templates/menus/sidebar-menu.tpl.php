<div id="sidebar-menu-wrapper">
    <ul id="sidebar-menu">
        <?php foreach((array)@$items as $url => $title):?>
        <li>
            <a href="<?php print $url;?>">
                <?php print $title; ?>
            </a>
        </li>
        <?php endforeach;?>
    </ul>
</div>