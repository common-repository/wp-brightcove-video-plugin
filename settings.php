<?php
if ('settings.php' == basename($_SERVER['SCRIPT_FILENAME'])){
	die ('Please do not access this file directly. Thanks!');
}
?>

<html>  
<head>   
    <link rel="stylesheet" href="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/css/' ;?>style.css" type="text/css" /> 
    <link rel="stylesheet" href="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/css/' ;?>s.css" type="text/css" />  
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>   
    <script type="text/javascript" src="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/js/' ;?>jquery.validate.js"></script>
    <script type="text/javascript" src="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/js/' ;?>jquery.tablesorter.js"></script>
    <script type="text/javascript" src="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/js/' ;?>quickpager.js"></script>
    <script type="text/javascript" src="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/js/' ;?>shadowbox.js"></script>
    
    
    <Style>
    
    	/* Errors */

		#info, #success, #warning, #error, #validation {
		border: 1px solid;
		margin: 10px 0px;
		padding:5px 5px 5px 50px;
		background-repeat: no-repeat;
		background-position: 10px center;
		margin-top: 25px;
		}
		#info {
		color: #00529B;
		background-color: #BDE5F8;
		background-image: url('<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/images/info.png'; ?>');
		}
		#success {
		color: #4F8A10;
		background-color: #DFF2BF;
		background-image:url('<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/images/good.png';?>');
		}
		#warning {
		color: #9F6000;
		background-color: #FEEFB3;
		background-image: url('<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/images/warning.png';?>');
		}
		#error {
		color: #D8000C;
		background-color: #FFBABA;
		background-image: url('<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/images/error.png';?>');
		}
    
    </style>

</head> 

<body> 

<div class="wrap">

<h2>Brightcove Settings</h2>

<?php

$tokenRead;
$tokenWrite;
$publisherId;
$playerId;
$width;
$height;

$errorTokenRead;
$errorTokenWrite;
$errorPublisherId;
$errorPlayerId;
$errorWidth;
$errorHeight;
$errorPublisherIdN;
$errorPlayerIdN;
$errorWidthN;
$errorHeightN;

$errorExit = false;

if(isset($_POST['save'])){	
	global $wpdb;
		
	//validations		
	$tokenRead = trim(mysql_real_escape_string(filter_var($_POST['tokenRead'], FILTER_SANITIZE_STRING)));
	$tokenWrite = trim(mysql_real_escape_string(filter_var($_POST['tokenWrite'], FILTER_SANITIZE_STRING)));	
	$publisherId = trim(mysql_real_escape_string(filter_var($_POST['publisherId'], FILTER_SANITIZE_STRING)));
	$playerId = trim(mysql_real_escape_string(filter_var($_POST['playerId'], FILTER_SANITIZE_STRING)));
	$width = trim(mysql_real_escape_string(filter_var($_POST['width'], FILTER_SANITIZE_STRING)));
	$height = trim(mysql_real_escape_string(filter_var($_POST['height'], FILTER_SANITIZE_STRING)));
		
	if(empty($tokenRead) ){								
		$errorTokenRead = 'You have missed Token Read.';	 	
		$errorExit = true;
	}
	if(empty($tokenWrite) ){	
		$errorTokenWrite = 'You have missed Token Write.';	
		$errorExit = true;
	}
	 if(empty($publisherId)) {		
		$errorpublisherId = 'You have missed Plublisher Id.';		
		$errorExit = true;
	 }
	 if(empty($playerId)){		
		$errorPlayerId = 'You have missed Player Id.';	
		$errorExit = true;
	 } 
	 if(empty($width) ){					
		$errorWidth = 'You have missed the width.';	
		$errorExit = true;
	 }
	 if(empty($height) ){				
		$errorHeight = 'You have missed the height.';	
		$errorExit = true;
	 }
	 
	 if(!is_numeric($publisherId)){					
		$errorPublisherIdN = 'You must enter numeric values for publisher Id.';	
		$errorExit = true;
	 }
	 if(!is_numeric($playerId)){					
		$errorPlayerIdN = 'You must enter numeric values for player Id.';		
		$errorExit = true;
	 }
	 if(!is_numeric($width)){					
		$errorWidthN = 'You must enter numeric values for the width.';	
		$errorExit = true;
	 }
	 if(!is_numeric($height)){								
		$errorHeightN = 'You must enter numeric values for height';  		  	
		$errorExit = true;
	 }
	 
	 	
	 //error messages
	 if($errorExit == true){
	 	echo '<div id="error" style="display: block"> ' ;
	 	
	 	if($errorTokenRead)
	 		echo  $errorTokenRead .'<br />';
	 	
	 	
	 	if($errorTokenWrite)
			echo  $errorTokenWrite.'<br />';
		
		if($errorPublisherId)
			echo  $errorPublisherId.'<br />';
		
		if($errorPlayerId)
			echo  $errorPlayerId.'<br />';
		
		if($errorWidth)
			echo  $errorWidth.'<br />';
		
		if($errorHeight)
			echo  $errorHeight.'<br />';
		
		if($errorPublisherIdN)
			echo  $errorPublisherIdN.'<br />';
		
		if($errorPlayerIdN)
			echo  $errorPlayerIdN.'<br />';
		
		if($errorWidthN)
			echo  $errorWidthN.'<br />';
		
		if($errorHeightN)
			echo  $errorHeightN.'<br />' ;	
		
		 	
	 	echo '</div>';
	 	htmlForm();	 	
	 	
	 }else{ 
	 //updating DB	
		$sql = sprintf(
		"UPDATE 
				wp_bc_video_plugin
		 SET	   
				tokenRead = '%s', 
			    tokenWrite = '%s',
			    publisherId = '%s',
			    playerId = '%s',
			    width = '%s',
			    height = '%s'
			    
		WHERE userId = 1
			
			", $tokenRead, $tokenWrite, $publisherId, $playerId, $width, $height
		);
		$result = mysql_query($sql) or die(mysql_error());
		
		if($result){		
			echo '<div id="success" > Information Updated </div>';
			htmlForm();
			
		
		}else{
			echo '<div id="error" >The information was not updated due to an error. Please Contact Your Webmaster.</div>' ;
			htmlForm();
		}
		
	}
	
	

}else{	
	htmlForm();	
}

//generate the html form
function htmlForm(){
	$sql = sprintf("SELECT * FROM wp_bc_video_plugin WHERE userId=1");
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$tokenRead = $row->tokenRead;
		$tokenWrite = $row->tokenWrite;	
		$publisherId = $row->publisherId; 
		$playerId = $row->playerId; 
		$width = $row->width; 
		$height = $row->height;
	}

	echo '
	
	
	<form action="" method="post" id="tokenInfo" name="tokenInfo" >


	<h4>Brightcove Tokens</h4>
	<p>
		<label>Token Read URL</label><br/>
		 <input type="text" id="tokenRead" name="tokenRead" size="75" maxlength="75" value="'.$tokenRead .'" /> 
	</p>
	
	<p>
		<label>Token Write URL</label><br/>
		 <input type="text" id="tokenWrite" name="tokenWrite" size="75" maxlength="75" value="'. $tokenWrite .'" /> 
	</p>
	
	<h4>Video Settings</h4>
	<p>
		<label>Publisher ID</label><br/>
		 <input type="text" id="publisherId" name="publisherId" size="16" maxlength="25" value="'. $publisherId .'" > 
	</p>
	
	<p>
		<label>Player ID</label><br/>
		 <input type="text" id="playerId" name="playerId" size="16" maxlength="25" value="'. $playerId .'" /> 
	</p>
	
	<p>
		<label>Video Width</label><br/>
		 <input type="text" id="width" name="width" size="4" maxlength="4" value="'. $width .'" /> 
	</p>
	
	<p>
		<label>Video height</label><br/>
		 <input type="text" id="height" name="height" size="4" maxlength="4" value="'. $height .'" />
	</p>
		
	<p>
		<input type="submit" class="button" value="Save" name="save" />
	</p>


	</form>
	
	

	';


}

?>

</div> <!-- End Wrap -->

      
</body>  
</html>
  




 

