<!-- Template for Messages to the user -->
<script>
    $(document).ready(function(){
        $("a.exit").on("click", function(e){
            e.preventDefault();
            /* When Someone clicks the exit button, close the message */
            $(this).parents("li.screen-message").fadeOut(1500);
        });
        var temp = window.setInterval(function(){
            /* Set functions to be called every second */
            hideMessages();
        }, 5000);
        var moused = 0;
        $("#screen-messages-wrapper").mouseover(function(){
            moused = 1;
        });
        $("#screen-messages-wrapper").mouseout(function(){
            moused = 0;
        });
        function hideMessages()
        {
            if(moused == 0)
            {
                $("#screen-messages-wrapper").fadeOut(3000);
                window.clearInterval(temp);
            }
        }
    });
</script>
<div id="screen-messages-wrapper">
    <ul id="screen-messages">
        <?php foreach (@$messages as $type => $message_group): ?>
                <?php foreach ($message_group as $message): ?>
                    <li class="screen-message <?php print $type; ?>">
                        <div class="exit-icon clearfix"><a href="#" class="exit"></a></div>
                        <?php print $message; ?>
                    </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
    </ul>
</div>