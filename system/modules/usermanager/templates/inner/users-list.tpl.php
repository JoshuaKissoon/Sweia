<script>
   $(document).ready(function(){
      /* Using ajax to set a task completed */
      $('a.delete-user').click(function(e) {
         e.preventDefault();
         var uid = $(this).attr("uid");
         var atag = $(this);
         $( "#delete-confirm" ).dialog({
            resizable: false,
            height:180,
            modal: true,
            buttons: {
               "Delete User": function() {
                  $( this ).dialog( "close" );
                  $.ajax({
                     url: "",
                     type: "GET",
                     data: 'uid=' + encodeURIComponent(uid) + '&op=delete-user&type=ajax',
                     dataType: "html",
                     success: function(data)
                     {
                        $(atag).parents("tr#" + uid).slideUp(1000);
                     }
                  });
               },
               Cancel: function() {
                  $( this ).dialog( "close" );
               }
            }
         });
      });
   });
</script>
<?php $count = 0; ?>
<div id="users-list-wrapper" class="content-wrapper">
   <div class="section-title">Users</div>
   <table id="users-list">
      <thead>
         <tr>
            <th>#</th>
            <th>Username</th>
            <th>Full Name</th>
            <th>Created</th>
            <th>Roles</th>
            <th>Operations</th>
         </tr>
      </thead>
      <tbody>
         <?php foreach ((array) $users as $uid): ?>
                <?php $user = new JSmartUser($uid); ?>
                <?php @$rclass = (@$rclass == "odd") ? "even" : "odd"; ?>
                <tr id="<?php print $user->uid; ?>" class="user <?php print $rclass; ?>">
                   <td class="number"><?php print ++$count; ?></td>
                   <td class="username"><?php print $user->username; ?></td>
                   <td class="name"><?php print $user->fullName(); ?></td>
                   <td class="date-created"><?php print $user->date_joined; ?></td>
                   <td class="roles">
                      <?php foreach ($user->getRoles() as $role): ?>
                         <div class="role">
                            <?php print $role; ?>
                         </div>
                      <?php endforeach; ?>
                   </td>
                   <td class="operations">
                      <a href="" class="edit-user" uid="<?php print $user->uid; ?>">edit</a>
                      <a href="" class="delete-user" uid="<?php print $user->uid; ?>">delete</a>
                   </td>
                </tr>
             <?php endforeach; ?>
      <tbody>
   </table>
</div>
<div id="modals" class="hidden">
   <div id="delete-confirm"><p>This user account will be permanently deleted and cannot be recovered. Are you sure?</p></div>
</div>