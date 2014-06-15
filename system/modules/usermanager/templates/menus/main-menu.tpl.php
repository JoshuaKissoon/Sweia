<div id="user-mgmt-menu-wrapper" class="clearfix">
   <div class="section-title">Menu</div>
   <ul id="user-mgmt-menu">
      <?php foreach ($menu_items as $url => $item): ?>
             <li class="mi <?php print $item['class']; ?>">
                <a href="<?php print $url; ?>">
                   <?php print $item['title']; ?>
                </a>
             </li>
          <?php endforeach; ?>
   </ul>
</div>