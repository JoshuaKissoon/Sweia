<!-- Template for Messages to the user -->
<div id="screen-messages" class="column large-3">
    <?php if (is_array($messages)): ?>
            <?php foreach ($messages as $type => $message_group): ?>
                <?php foreach ($message_group as $message): ?>
                    <div data-alert class="alert-box <?php print $type; ?> radius">
                        <?php print $message; ?>
                        <a href="#" class="close">&times;</a>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
</div>