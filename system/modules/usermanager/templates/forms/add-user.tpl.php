<div id="add-user-form-wrapper" class="form-wrapper content-wrapper">
   <div class="section-title">Add User</div>
   <form id="add-user-form" method="post">
      <div class="form-field clearfix">
         <div class="item-label">Username: </div>
         <div class="item">
            <input type="text" name="username" />
         </div>
      </div>
      <div class="form-field clearfix">
         <div class="item-label">Password: </div>
         <div class="item">
            <input type="password" name="password" />
         </div>
      </div>
      <div class="form-field clearfix">
         <div class="item-label">First Name: </div>
         <div class="item">
            <input type="text" name="fname" />
         </div>
      </div>
      <div class="form-field clearfix">
         <div class="item-label">Last Name: </div>
         <div class="item">
            <input type="text" name="lname" />
         </div>
      </div>
      <div class="form-field clearfix">
         <div class="item-label">Roles: </div>
         <div class="item">
            <?php foreach ((array) $roles as $rid => $role): ?>
                   <div class="role clearfix">
                      <input type="checkbox" name="roles[]" value="<?php print $rid; ?>" />
                      <span class="role"><?php print $role; ?></span>
                   </div>
                <?php endforeach; ?>
         </div>
      </div>
      <div class="form-field form-actions clearfix">
         <button type="submit" name="submit" value="add-user">Add User</button>
      </div>
   </form>
</div>