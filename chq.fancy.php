<?php
require("include/base.php");
//we still should be able to depend on our session...

//OK, determine type. Default is "text"
$editType = "text";
if(!isset($_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][(int)$_REQUEST['col']])){
    die("You need to press the Refresh button. Your session has unfortunately ended.");
}
if(isset($_POST['editType'])){
    //start the multi switch thing, text goes last as that is also default...
    switch(trim(strtolower($_POST['editType']))){
        case "bool"://boolean values, true and false stuff
            {
                $editType = "bool";
                $answcol = 1;
                if(isset($_POST['answcol'])){
                    $answcol = (int)trim($_POST['answcol']);
                }
            }
            break;
        case "multi":
            {
                //get column to edit
                $editType = "multi";
                //now get the column
                $column = 2;
                if(isset($_POST['editcol'])){
                    $column = (int)trim($_POST['editcol']);
                }//use stuff like chr and ord
            }
            break;
        case "numeric":
            {
                $editType = "numeric";
            }
            break;
        case "date":
            {
                $editType = "date";
            }
            break;
        case "text":
        default:
            {
                $editType = "text";
            }
        break;
    }
}



echo '<div class="fancyframe">';

switch($editType){
    case "bool"://boolean values, true and false stuff
        {
            //echo out a group of radio buttons
            echo chr(ord("A")+$x).'. ';
            if(strlen($_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][$answcol]) > 100){
                echo htmlentities(substr($_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][$answcol],0,100)).'...';
            }else{
                echo htmlentities($_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][$answcol]);
            }
            echo ' is <br />';
            echo '<input type="radio" name="boolean" value="1" ';
            if((int)$_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][(int)$_REQUEST['col']]){
                echo 'checked';
            }
            echo ' /> True or <input type="radio" name="boolean" value="0" ';
            if(!(int)$_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][(int)$_REQUEST['col']]){
                echo 'checked';
            }
            echo ' /> False<br />';
        }
        break;
    case "multi":
        {
            //use $column to determine a b or c, but it looks like our session is aleady using numbers instead of letters so chr ord won't be necessary
            $colxcount = 0;
            $colarray = array();
            if(isset($_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][$column])){
                $colarray = explode("\n",$_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][$column]);
                $colxcount = count($colarray);
            }
            echo "Use the drop-down menu below to select the correct answer. <br />";
            //now need to go and provide the options........
            echo '<select name="multi" class="multilist">';
            for($x = 0; $x < $colxcount; $x++){
                echo '<option value="'.$x.'"';
                if($x == (int)$_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][(int)$_REQUEST['col']]){
                    echo ' selected="selected"';
                }
                echo '>';
                echo chr(ord("A")+$x).'. ';
                if(strlen($colarray[$x]) > 100){
                    echo htmlentities(substr($colarray[$x],0,100)).'...';
                }else{
                    echo htmlentities(substr($colarray[$x],0,100));
                }
                echo '</option>'."\n";
            }
            echo '</select>';
            echo '<div class="hidden info">The changes have been made, but they may not show here until the page has reloaded.</div>';
        }
        break;
    case "numeric":
        {
            //you will have to convert this to a real number
            //but for now, this will be ignored like date.
            echo '<input type="text" name="textual" value="'.$_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][(int)$_REQUEST['col']].'" />';
        }
        break;
    case "date":
        {
            //I might implement this sometime later, it might be a bit troublesome.
        }
        break;
    case "text":
        {
            echo '<textarea class="textual"  cols="100" rows="5">';
            echo $_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][(int)$_REQUEST['col']];
            echo '</textarea>';
        }
    break;
}






?><div class="savefancy noselect">Save</div>
</div><!-- end of fancyframe -->
<script type="text/javascript">
    $(document).ready(function() {
       $('.savefancy').click(function(){
            $('.savefancy').slideUp(300);
            $('.fancyframe').find('.textual').attr('readonly', 'readonly');
            //start ajax send
            $.ajax({
                url: "savechq.php",
                global: false,
                type: "POST",
                data: ({data : <?php
                switch($editType){
                    case "bool"://boolean values, true and false stuff
                        {
                           echo "$('.fancyframe').find(\"input[name='boolean']:checked\").val()";
                        }
                        break;
                    case "multi":
                        {
                            echo "$('.fancyframe').find('.multilist').val()";
                        }
                        break;
                    case "numeric":
                        {
                            echo "$('.fancyframe').find('.textual').val()";
                        }
                        break;
                    case "date":
                        {
                            //I might implement this sometime later, it might be a bit troublesome.
                        }
                        break;
                    case "text":
                        {
                            echo "$('.fancyframe').find('.textual').val()";
                        }
                    break;
                }
                //
                
                ?>,
                       sub : <?php echo (int)$_REQUEST['sub'];?>,
                       col : <?php echo (int)$_REQUEST['col'];?>,
                       chqid : <?php echo $_SESSION['editor']['chq']['ID']; ?>,
                       qtype : <?php echo (int)$_SESSION['editor']['qtype']['preset']; ?>}),
                success: function(msg){
                   if(msg == "good"){
                    $('.dragable2, .dragable').each(function(){
                        if(parseInt($(this).find(".data").find('.location').text()) == <?php echo (int)$_REQUEST['sub'];?>){
                            $(this).find(".col<?php echo chr(ord('a')+ (int)$_REQUEST['col']); ?>").find('.fancy').text(<?php
                            switch($editType){
                                case "text":
                                case "numeric":
                                    {
                                        echo "$('.fancyframe').find('.textual').val()";
                                    }
                                    break;
                                case "multi":
                                case "date":
                                case "bool":
                                    {
                                        echo "'You will need to reload the page to see the full effect of the change.'";
                                    }
                                    break;
                            }
                            
                            ?>);
                            $.fancybox.close();
                        }
                    });
                   }else{
                    alert("There seemed to have been an error: " + msg);
                    $('.savefancy').slideDown(300);
                    $('.fancyframe').find('.textual').attr('readonly', '');
                   }
                },
                error : function(msg){
                    alert("There seemed to have been an error: " + msg.statusText);
                    $('.savefancy').slideDown(300);
                    $('.fancyframe').find('.textual').attr('readonly', '');
                }
            });
        });
    });
</script>