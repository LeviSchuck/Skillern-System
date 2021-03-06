<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd" >
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Skillern System</title>
        <!-- jQuery Stuff -->
        <script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.liveq.js"></script>
        <script type="text/javascript" src="js/jquery.general.js"></script>
	<script type="text/javascript" src="js/jquery.progressbar.min.js"></script>
	<script type="text/javascript" src="js/jquery.ahover.js"></script>
	<script type="text/javascript" src="js/jquery.animateToClass.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery.easing-1.3.pack.js"></script>
	
	<script type="text/javascript" src="js/jquery.fancybox-1.3.1.pack.js"></script>
	<script type="text/javascript" src="js/jquery.mousewheel-3.0.2.pack.js"></script>
	<script type="text/javascript" src="js/jquery.contextMenu.js"></script>
	<script type="text/javascript">
	
	function closeWarning(){
		return 'If you do this, you will lose everything you have done on this page. If you want to Go Back, do so with the buttons in the page. Else if you are exiting, go ahead.';
	}
		window.onbeforeunload = closeWarning;
		
	</script>
        <!-- end jQuery Stuff -->
        <!-- Base information like CSS -->
        <link rel="stylesheet" type="text/css" href="css/main.css" media="screen,print" />
	<link rel="stylesheet" type="text/css" href="css/jquery.fancybox-1.3.1.css" media="screen,print" />
        <link rel="stylesheet" type="text/css" href="css/IE.css" media="screen,print" />
	<link rel="stylesheet" type="text/css" href="css/jquery.contextMenu.css" media="screen" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="css/internetexplorer.css" />
	<![endif]-->
        <!-- end Base information -->
</head>
<body>
    <div class="mainwrapper">
	<div class="titlewrap">
        <div class="mtitle"><!-- --></div>
	</div>
        <div class="mcontent"><!-- --></div>
        <div class="mbottom"><!-- --></div>
    </div>
    <div class="workingarea"><!-- --></div>
    <script type="text/javascript">
    
        $(document).ready(function() {
		$('.noselect').livequery( function(){
	    $(this).disableTextSelect();
	    });
		
		$('.mainwrapper').fadeIn(600, function(){
			$('.mcontent').html("Loading.. Please Wait.");
	    $('.mtitle').html("Loading.. Please Wait.");
		$.ajax({
		    url: 'aindex.php',
		    success: function(data) {
			
		       $('.mcontent').slideUp(400, function(){
			    $('.workingarea').html(data);
			    $('.mcontent').html($('.workingarea').find('.bcontent').html());
			    $('.workingarea').find('.bcontent').html('');
		       });
		       
		    }
		});
            });
		
		
		
        });
    </script>

</body>
</html>