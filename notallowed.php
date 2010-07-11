<div class="bcontent">
    <div style="text-align: center;"><h2>Sorry, You are not allowed here.</h2> <br />
    <h4>In about 5 seconds, you will be sent back home.</h4></div>
</div>
<div class="bscript">
<script type="text/javascript">
function onloadedy() {
    $(document).ready(function() {
        $('.mcontent').slideDown(600).delay(4000).slideUp(400,function(){
                $.ajax({
                    type: "POST",
                    url: "apanel.php",
                    data: "",
                    success: function(data){
                        $('.workingarea').html(data);
                        $('.mcontent').slideUp(100, function(){
                            $('.mcontent').html($('.workingarea').find('.bcontent').html());
                            $('.workingarea').find('.bcontent').html('');
                            $('.mcontent').slideDown(600);
                        });
                    }
                });
            });
    });
    
}
function checkloadedy(){
        if($('.workingarea').find('.bcontent').html() == ''){
            onloadedy();
        }else{
            setTimeout('checkloadedy()', 50);
	    
        }
     }
     checkloadedy();
    </script></div>