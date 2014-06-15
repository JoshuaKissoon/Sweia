<script>
   $(document).ready(function(){
      /* Using ajax to set a task completed */
      $('a.delete-role').click(function(e) {
         e.preventDefault();
         var rid = $(this).attr("rid");
         var atag = $(this);
         $( "#delete-confirm" ).dialog({
            resizable: false,
            height:180,
            modal: true,
            buttons: {
               "Delete Role": function() {
                  $( this ).dialog( "close" );
                  $.ajax({
                     url: "", type: "GET",
                     data: 'rid=' + encodeURIComponent(rid) + '&op=delete-role&type=ajax',
                     dataType: "html",
                     success: function(data)
                     {
                        $(atag).parents("tr#" + rid).slideUp(1000);
                     }
                  });
               },
               Cancel: function() { $( this ).dialog( "close" ); }
            }
         });
      });
   });
</script>
<?php $count = 0; ?>
<div id="users-list-wrapper" class="content-wrapper">
   <div class="section-title">Roles</div>
   <table id="users-list">
      <thead>
         <tr>
            <th>#</th>
            <th>Role</th>
            <th>Description</th>
            <th>Operations</th>
         </tr>
      </thead>
      <tbody>
         <?php foreach ((array) $roles as $rid): ?>
                <?php $role = new Role($rid); ?>
                <?php @$rclass = (@$rclass == "odd") ? "even" : "odd"; ?>
                <tr id="<?php print $role->rid; ?>" class="role <?php print $rclass; ?>">
                   <td class="number"><?php print ++$count; ?></td>
                   <td class="username"><?php print $role->role; ?></td>
                   <td class="name"><?php print $role->description; ?></td>
                   <td class="operations">
                      <a href="" class="edit-role" rid="<?php print $role->rid; ?>">edit</a>
                      <a href="" class="delete-role" rid="<?php print $role->rid; ?>">delete</a>
                   </td>
                </tr>
             <?php endforeach; ?>
      <tbody>
   </table>
</div>
<div id="modals" class="hidden">
   <div id="delete-confirm"><p>This role will be permanently deleted and cannot be recovered. Are you sure?</p></div>
</div>