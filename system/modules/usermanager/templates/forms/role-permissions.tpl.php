<div id="role-permissions-wrapper" class="content-wrapper">
   <div class="section-title">Role Permissions</div>
   <form id="role-permissions-form" method="post">
      <table id="role-permissions">
         <thead>
            <tr>
               <th>Permissions</th>
               <?php foreach ($roles as $rid => $role): ?>
                      <th><?php print $role->role; ?></th>
                   <?php endforeach; ?>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($permissions as $perm => $permission): ?>
                   <?php @$rclass = (@$rclass == "odd") ? "even" : "odd"; ?>
                   <tr class="<?php print $rclass; ?>">
                      <td><?php print $permission->title; ?></td>
                      <?php foreach ($roles as $rid => $role): ?>
                         <td class="checkbox-wrapper">
                            <?php $checked = ($role->hasPermission($perm)) ? "checked" : ""; ?>
                            <input type="checkbox" name="<?php print "roles[$rid]" . "[]"; ?>" value="<?php print $perm; ?>" <?php print $checked; ?> />
                         </td>
                      <?php endforeach; ?>
                   </tr>
                <?php endforeach; ?>
         </tbody>
      </table>
      <div class="form-field form-actions clearfix">
         <button type="submit" name="submit" value="save-permissions">Save</button>
      </div>
   </form>
</div>