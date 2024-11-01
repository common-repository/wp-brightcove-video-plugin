<?php 
/* 
Plugin Name: WP Brightcove Video Plugin
Plugin URI: http://xtremenews.info/wordpress-plugins/wp-brightcove-video-plugin/ 
Description: This plugin will help you to get all video information from your brightcove account. You won't need to go into my.brightcove.com account to get your videos information. You can find it here. You only need to provide your Token Read URL and Token Write, It also allows you to insert your video in your wordpress posts.
Version: 0.1 
Author: Moyo
Author URI: http://xtremenews.info


Copyright 2011  Moyo  

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA


*/

if ('wp-brightcove-video-plugin.php' == basename($_SERVER['SCRIPT_FILENAME'])){
	die ('Please do not access this file directly. Thanks!');
}



$mu_db_version = '1.0' ;

//creating the table
function createBCVideoPlugin() {
	global $wpdb;
	global $mu_db_version;
   
	$table_name = "wp_bc_video_plugin";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
	  		userId int(20) NOT NULL AUTO_INCREMENT,
	  		tokenWrite varchar(64) ,
	  		tokenRead varchar(64),
	  		publisherId varchar(64),
	  		playerId varchar(64),
	  		width varchar(4), 
	  		height varchar(4),
	  		
	  		  	
	  		PRIMARY KEY (userId)
		)";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		
		$sql1 = sprintf(
				"INSERT INTO wp_bc_video_plugin (
					tokenWrite, tokenRead, publisherId, playerId, width, height
				) VALUES (
					'Token Write Goes Here', 'Token Read Goes Here', '1705665024', '1911416499', '486', '412'
				)"
			);
			
		$result = mysql_query($sql1) or die(mysql_error());

		add_option("createBCVideoPlugin", $mu_db_version);
		
	}	
}

//adding table
register_activation_hook(__FILE__,'createBCVideoPlugin');


// Hook for adding admin menus
add_action('admin_menu', 'wp_bc_video_plugin_menu');


// action function for above hook
function wp_bc_video_plugin_menu() {
    // Add a new top-level menu 
   add_menu_page('WP Brightcove Video Plugin', 'WP Brightcove', 'manage_options', 'wp-brightcove-video-plugin', 
                  'wp_bc_videos', get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/images/menuIcon.png '  );
    
    
    
   // Add a submenu to the custom top-level menu:
    add_submenu_page('wp-brightcove-video-plugin', __('Brightcove Settings','bc-settings'), __('Brightcove Settings','bc-settings'), 'manage_options', 'bc-settings', 'wp_bc_settings');              
                  
}



function wp_bc_videos(){
	if( !current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page' );
    }
	require_once('videos.php');
}

function wp_bc_settings(){
	if( !current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page' );
    }
	require_once('settings.php');
}




function Brightcove_Parse($content)
{
    $content = preg_replace_callback("/\[brightcove ([^]]*)\/\]/i", "Brightcove_Render", $content);
    return $content;
}

function Brightcove_Render($matches)
{


	 global $video, $player, $publisher, $width, $height, $arguments;

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



	//set video info for brightcove player
	//Set the publisher ID - YOU MUST SET THIS TO YOUR OWN PUBLISHER ID
	$publisher = $publisherId;
	
	//Set a default player to use - YOU MUST SET THIS TO YOUR OWN DEFAULT PLAYER
	$player = $playerId;
	
	//Set width and height for the default video player
	$width = $width;
	$height = $height;
	
	//Define default video variable
	$videoid = 0;
	
	//The actual parse content function called by the filter
	//This will use the callback function BCVideo_Render to do the
	//actual text replacement for the widget
   
    $output = '';
    $matches[1] = str_replace(array('&#8221;','&#8243;'), '', $matches[1]);
    preg_match_all('/(\w*)=(.*?) /i', $matches[1], $attributes);
    $arguments = array();

    foreach ( (array) $attributes[1] as $key => $value )
  {
        // Strip out legacy quotes
        $arguments[$value] = str_replace('"', '', $attributes[2][$key]);
    }


    if (( !array_key_exists('video', $arguments) ) && ( !array_key_exists('player', $arguments) ))
  {
        return '<div style="background-color:#f99; padding:10px;">Brightcove Player Widget Error: Required parameter "video" or "player" is missing!</div>';
        exit;
    }
    else
    {
    $video = $arguments['video'];
  }

    if( array_key_exists('width', $arguments) )
  {
        $height = $arguments['width'];
    }

    if( array_key_exists('height', $arguments) )
  {
        $height = $arguments['height'];
    }

    if( array_key_exists('player', $arguments) )
  {
        $player = $arguments['player'];
    }
         
  $output .= '<script language="JavaScript" type="text/javascript" src="http://admin.brightcove.com/js/BrightcoveExperiences.js"></script>
		
		<object id="myExperience$BCpost" class="BrightcoveExperience">
		  <param name="bgcolor" value="#FFFFFF" />
		  <param name="width" value="480" />
		  <param name="height" value="270" />
		  <param name="playerID" value="'.$player.'" />
		  <param name="publisherID" value="'.$publisher.'"/>
		  <param name="isVid" value="true" />
		  <param name="isUI" value="true" /> 
		  <param name="@videoPlayer" value="'.$video.'" />
		</object>';    
             
    return $output;
}

//Add a filter hook - this registers the function for all content
//text (Pages and Posts) to search for the [CONTRIBUTOR_WIDGET] tag.
add_filter('the_content', 'Brightcove_Parse');


?>