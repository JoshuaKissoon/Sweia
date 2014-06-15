<div id="module-list-wrapper">
    <div class="section-title">Modules</div>
    <table id="modules-list">
        <thead>
            <tr>
                <th>Module</th>
                <th>Title</th>
                <th>Description</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($modules as $modname => $mod): ?>
                    <tr>
                        <td><?php print $modname; ?></td>
                        <td><?php print $mod->title; ?></td>
                        <td><?php print $mod->description; ?></td>
                        <td><?php print $mod->type; ?></td>
                        <td><?php print ($mod->status) ? "Enabled" : "Disabled"; ?></td>
                    </tr>
                <?php endforeach; ?>
        </tbody>
    </table>
</div>