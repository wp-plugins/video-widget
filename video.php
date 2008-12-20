<?php
/*
Plugin Name: Video widget
Description: Adds some YouTube/Dailymotion/Google... sidebar videos. This plugin is based on <a href="http://wordpress.org/extend/plugins/php-code-widget/" title="Executable PHP widget">Executable PHP widget</a> for multiples widgets, <a href="http://nothingoutoftheordinary.com/2007/05/31/wordpress-youtube-widget/" title="YouTube widget">YouTube widget</a> for the idea and <a href="http://www.gate303.net/2007/12/17/video-embedder/" title="Video Embedder">Video Embedder</a> for the video html library.
Author: nikohk
Version: 1.2.3
Author URI: http://www.nikodev.com
Plugin URI: http://www.nikohk.com/plugin-wordpress-video-widget/
*/
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2, 
    as published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

function wp_widget_video($args, $widget_args = 1) {
	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('widget_video');
	if ( !isset($options[$number]) )
		return;

	$id = $options[$number]['id'];
	$idlist = $options[$number]['idlist'];
	
	$type = $options[$number]['source'];
	
	$textbefore = ($options[$number]['textbefore'] != "") ? '<p class="video_widget_before_video">' . $options[$number]['textbefore'] . '</p>' : ""; 
	$textafter = ($options[$number]['textafter'] != "") ? '<p class="video_widget_after_video">' . $options[$number]['textafter'] . '</p>' : ""; 
	
	if (!empty($idlist))
	{
		$videos = explode(";", rtrim($idlist,";"));
		$selectedVideo =  $videos[rand(0,count($videos)-1)];
		$selectedVideo = str_replace("http://", "http//", $selectedVideo);
		$videoInfos = explode(":", $selectedVideo);
		$type = strtolower(trim($videoInfos[0]));
		$id = trim(str_replace("http//", "http://", $videoInfos[1]));

		$textbefore = ($videoInfos[2] != "") ? '<p class="video_widget_before_video">' . trim($videoInfos[2]) . '</p>' : ""; 
		$textafter = ($videoInfos[3] != "") ? '<p class="video_widget_after_video">' . trim($videoInfos[3]) . '</p>' : ""; 
	}
	
	$width = $options[$number]['width'];
	$height = $options[$number]['height'];
	
	
	$code = '';
	switch ($type)
	{
		case 'youtube':
			$content = wp_widget_video_buildEmbed('http://www.youtube.com/v/'.$id.'&rel=0', $width, $height);
		break;	
		case 'dailymotion':
			$content = wp_widget_video_buildEmbed('http://www.dailymotion.com/swf/'.$id, $width, $height);
		break;
		case 'google':
			$content = wp_widget_video_buildEmbed('http://video.google.com/googleplayer.swf?docId='.$id, $width, $height);
		break;
		case 'flv':
			$randomContainerId = rand(0,1000);
			$videoWidgetFolder = get_bloginfo('wpurl').'/wp-content/plugins/video-widget/';
			$content='<div id="container'.$randomContainerId.'"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>';
			$content.='<script type="text/javascript" src="'.$videoWidgetFolder.'swfobject.js"></script>';
			$content.='<script type="text/javascript">
				var s1 = new SWFObject("'.$videoWidgetFolder.'player.swf","ply","'.$width.'","'.$height.'","9","#FFFFFF");
				s1.addParam("allowfullscreen","true");
				s1.addParam("allowscriptaccess","always");
				s1.addParam("flashvars","file='.$id.'");
				s1.write("container'.$randomContainerId.'");
			</script>';
			
		break;
		case 'vimeo':
			$content = wp_widget_video_buildEmbed('http://www.vimeo.com/moogaloop.swf?clip_id='.$id.'&amp;server=www.vimeo.com&amp;fullscreen=1&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=', $width, $height);
		break;
		case 'flickr':
			$content = wp_widget_video_buildEmbed('http://www.flickr.com/apps/video/stewart.swf?photo_id='.$id, $width, $height);
		break;
		case 'metacafe':
			$content = wp_widget_video_buildEmbed('http://www.metacafe.com/fplayer/'.$id.'.swf', $width, $height);
		break;
		case 'liveleak':
			$content = wp_widget_video_buildEmbed('http://www.liveleak.com/player.swf?autostart=false&amp;token='.$id, $width, $height);
		break;
		case 'revver':
			$content = '<script src="http://flash.revver.com/player/1.0/player.js?mediaId:'.$id.';width:'.$width.';height:'.$height.'" type="text/javascript"></script>';
		break;
		case 'ifilm':
			$content = wp_widget_video_buildEmbed('http://www.ifilm.com/efp?flvbaseclip='.$id, $width, $height);
		break;
		case 'myspace':
			$content = wp_widget_video_buildEmbed('http://lads.myspace.com/videos/vplayer.swf?m='.$id.'&amp;v=2&amp;type=video', $width, $height);
		break;
		case 'bliptv':
			$content = wp_widget_video_buildEmbed('http://blip.tv/scripts/flash/showplayer.swf?autostart=false&#038;file=http%3A%2F%2Fcreationsnet%2Eblip%2Etv%2Ffile%2F'.$id.'%2F%3Fskin%3Drss%26sort%3Ddate&#038;fullscreenpage=http%3A%2F%2Fblip%2Etv%2Ffullscreen%2Ehtml&#038;fsreturnpage=http%3A%2F%2Fblip%2Etv%2Fexitfullscreen%2Ehtml&#038;showfsbutton=true&#038;brandlink=http%3A%2F%2Fcreationsnet%2Eblip%2Etv%2F&#038;brandname=cre%2Eations%2Enet&#038;showguidebutton=false&#038;showplayerpath=http%3A%2F%2Fblip%2Etv%2Fscripts%2Fflash%2Fshowplayer%2Eswf', $width, $height);
		break;
		case 'collegehumor':
			$content = wp_widget_video_buildEmbed('http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id='.$id.'&amp;fullscreen=1', $width, $height);
		break;
		case 'videojug':
			$content = wp_widget_video_buildEmbed('http://www.videojug.com/film/player?id='.$id, $width, $height);
		break;
		case 'godtube':
			$content = wp_widget_video_buildEmbed('http://godtube.com/flvplayer.swf?viewkey='.$id, $width, $height);
		break;
		case 'veoh':
			$content = wp_widget_video_buildEmbed('http://www.veoh.com/videodetails2.swf?player=videodetailsembedded&amp;type=v&amp;permalinkId='.$id.'&amp;id=anonymous', $width, $height);
		break;
		case 'break':
			$content = wp_widget_video_buildEmbed('http://embed.break.com/'.$id, $width, $height);
		break;
		case 'movieweb':
			$content = wp_widget_video_buildEmbed('http://www.movieweb.com/v/'.$id, $width, $height);
		break;
		case 'jaycut':
			$content = wp_widget_video_buildEmbed('http://jaycut.se/flash/preview.swf?file=http://jaycut.se/mixes/send_preview/'.$id.'&amp;type=flv&amp;autostart=false', $width, $height);
		break;
		case 'myvideo':
			$content = wp_widget_video_buildEmbed('http://www.myvideo.de/movie/'.$id, $width, $height);
		break;
		case 'clipfish':
			$content = wp_widget_video_buildEmbed('http://www.clipfish.de/videoplayer.swf?as=0&videoid='.$id.'=&r=1&c=0067B3&coop=myspace', $width, $height);
		break;
		case 'viddler':
			$content = wp_widget_video_buildEmbed('http://www.viddler.com/player/'.$id, $width, $height);
		break;
		case 'gametrailers':
			$content = wp_widget_video_buildEmbed('http://www.gametrailers.com/remote_wrap.php?mid='.$id, $width, $height);
		break;
		case 'snotr':
			$content = wp_widget_video_buildEmbed('http://videos.snotr.com/player.swf?video='.$id.'&amp;embedded=true&amp;autoplay=false', $width, $height);
		break;
                case 'taratata':
                        $content = wp_widget_video_buildEmbed('http://video.mytaratata.com/p/fr/'.$id.'.html', $width, $height);
                break;		
                case 'espn':
                        $content = wp_widget_video_buildEmbed('http://sports.espn.go.com/broadband/player.swf?mediaId='.$id, $width, $height);
                break;
                case 'mtvmusic':
                        $content = wp_widget_video_buildEmbed('http://media.mtvnservices.com/mgid:uma:video:mtv.com:'.$id, $width, $height); 
                break;
		case 'quicktime':			
			$content='<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="'.$width.'" height="'.$height.'">';
			$content.='<param name="src" value="'.$id.'" />';
			$content.='<param name="controller" value="true" />';
			$content.='<param name="autoplay" value="false" />';
			$content.='<param name="scale" value="aspect" />';
			$content.='<param name="pluginspage" value="/quicktime/download/" />';
			$content.='<object type="video/quicktime" data="'.$id.'" width="'.$width.'" height="'.$height.'">'."\n";
			$content.='<param name="autoplay" value="false" />';
		 	$content.='<param name="controller" value="true" />';
			$content.='<param name="scale" value="aspect" />';
			$content.='</object>';
			$content.='</object>';			
		break;
		case 'windowsmedia':
			$content='<object classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6" id="player" width="'.$width.'" height="'.$height.'">'."\n";
			$content.='<param name="url" value="'.$id.'" />'."\n";
			$content.='<param name="src" value="'.$id.'" />'."\n";
			$content.='<param name="showcontrols" value="true" />'."\n";
			$content.='<param name="autostart" value="false" />'."\n";
			$content.='<param name="stretchtofit" value="true" />'."\n";
			$content.='<!--[if !IE]>-->'."\n";
			$content.='<object type="video/x-ms-wmv" data="'.$id.'" width="'.$width.'" height="'.$height.'">'."\n";
			$content.='<param name="src" value="'.$id.'" />'."\n";
			$content.='<param name="autostart" value="false" />'."\n";
			$content.='<param name="controller" value="false" />'."\n";
			$content.='<param name="stretchtofit" value="true" />'."\n";
			$content.='</object>'."\n";
			$content.='<!--<![endif]-->'."\n";
			$content.='</object>'."\n";
		break;			
		case 'schooltube' :
                        $content = wp_widget_video_buildEmbed('http://www.schooltube.com/v/'.$id, $width, $height); 
		break;
	}
	
	$title = ($options[$number]['title'] != "") ? $before_title.$options[$number]['title'].$after_title : "";  
	
	echo $before_widget;
	echo $title;
	echo $textbefore;
	echo $content;
	echo $textafter;
	echo $after_widget;	
}

function wp_widget_video_buildEmbed($code, $width, $height)
{
	$object = '<object type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" data="'.$code.'">';
	$object .= '<param name="movie" value="'.$code.'" />';
	$object .= '<param name="wmode" value="transparent" />';
	$object .= '<param name="quality" value="high" />';
	$object .= '</object>';
	return $object;
}

function wp_widget_video_control($widget_args) 
{
	global $wp_registered_widgets;
	static $updated = false;

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
		
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('widget_video');
	if ( !is_array($options) )
		$options = array();

	if ( !$updated && !empty($_POST['sidebar']) ) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) {
			if ( 'widget_video' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				unset($options[$widget_number]);
			}
		}

		foreach ( (array) $_POST['widget-video'] as $widget_number => $widget_content ) {
			$title = strip_tags(stripslashes($widget_content['title']));
			$source = stripslashes( $widget_content['source'] );
			$id = stripslashes( $widget_content['id'] );
			$idlist = stripslashes( $widget_content['idlist'] );
			$width = stripslashes( $widget_content['width'] );
			$height = stripslashes( $widget_content['height'] );
			$textbefore = stripslashes( $widget_content['textbefore'] );
			$textafter = stripslashes( $widget_content['textafter'] );
			
			$options[$widget_number] = compact( 'title', 'source', 'id', 'idlist', 'width', 'height', 'textbefore', 'textafter');
		}

		update_option('widget_video', $options);
		$updated = true;
	}

	if ( -1 == $number ) {
		$title = 'video title';
		$source = 'youtube';
		$id = '';
		$idlist = '';
		$width = '200';
		$height = '165';
		$number = '%i%';
		$textbefore = '';
		$textafter = '';
	} 
	else {
		$title = attribute_escape($options[$number]['title']);
		$source = $options[$number]['source'];
		$id = $options[$number]['id'];
		$idlist = $options[$number]['idlist'];
		$width = $options[$number]['width'];
		$height = $options[$number]['height'];
		$textbefore = str_replace('"', "'", $options[$number]['textbefore']);
		$textafter = str_replace('"', "'", $options[$number]['textafter']);
	}
?>
		<p>
			<input type="hidden" class="widget-width" value="250" />

			<label for="video-title-<?php echo $number; ?>">Title:</label>
			<input class="widefat" id="video-title-<?php echo $number; ?>" name="widget-video[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" />
			
			<label for="video-width-<?php echo $number; ?>">Width (px):</label>
			<input class="widefat" id="video-width-<?php echo $number; ?>" name="widget-video[<?php echo $number; ?>][width]" type="text" value="<?php echo $width; ?>" />
			
			<label for="video-height-<?php echo $number; ?>">Height (px):</label>
			<input class="widefat" id="video-height-<?php echo $number; ?>" name="widget-video[<?php echo $number; ?>][height]" type="text" value="<?php echo $height; ?>" />
			
			<br /><br />
			(Multiple videos)<br />	
			<label for="video-idlist-<?php echo $number; ?>">ID list (for random display):</label>
			<textarea class="widefat" id="video-idlist-<?php echo $number; ?>" name="widget-video[<?php echo $number; ?>][idlist]" rows="5"><?php echo $idlist; ?></textarea>
			(Not sure about video (list) ID ? Have a look on <a href="http://www.nikohk.com/plugin-wordpress-video-widget/" title="Video Widget Plugin">Video Widget Plugin</a> documentation)
			<br /><br />

			(OR single video)<br />
			<label for="video-source-<?php echo $number; ?>">Source:</label>
			<select class="widefat" name="widget-video[<?php echo $number; ?>][source]" id="video-source-<?php echo $number; ?>">
			<?php
			//videos types
			$sources = array();
			$sources['youtube']='Youtube';
			$sources['dailymotion']='Dailymotion';
			$sources['google']='Google Video';
			$sources['flv']='Flv';
			$sources['vimeo']='Vimeo';			
			$sources['flickr']='Flickr';			
			$sources['metacafe']='Metacafe';			
			$sources['liveleak']='LiveLeak';
			$sources['revver']='Revver';
			$sources['ifilm']='iFilm';
			$sources['myspace']='MySpace';
			$sources['bliptv']='Blip.tv';
			$sources['collegehumor']='CollegeHumor';
			$sources['videojug']='VideoJug';
			$sources['godtube']='GodTube';
			$sources['veoh']='Veoh';
			$sources['break']='Break';
			$sources['movieweb']='Movieweb';
			$sources['jaycut']='Jaycut';
			$sources['myvideo']='Myvideo';
			$sources['clipfish']='Clipfish';
			$sources['viddler']='Viddler';
			$sources['gametrailers']='Gametrailers';
			$sources['snotr']='Snotr';			
			$sources['taratata']='Taratata';			
			$sources['espn']='Espn';			
			$sources['mtvmusic']='Mtv Music';			
			$sources['quicktime']='Quicktime';
			$sources['windowsmedia']='Windows media player';
			$sources['schooltube']='SchoolTube';
                        ksort($sources);
                        
	 		foreach ($sources as $key => $value)
			{
				$selected = '';
				if ($key == $source)
				{
					$selected = ' selected="selected"';
				}
				echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
			}
			?>
			</select>
			
			<label for="video-id-<?php echo $number; ?>">ID:</label>
			<input class="widefat" id="video-id-<?php echo $number; ?>" name="widget-video[<?php echo $number; ?>][id]" type="text" value="<?php echo $id; ?>" />

			<label for="video-textbefore-<?php echo $number; ?>">Text before video:</label>
			<input class="widefat" id="video-textbefore-<?php echo $number; ?>" name="widget-video[<?php echo $number; ?>][textbefore]" type="text" value="<?php echo $textbefore; ?>" />

			<label for="video-textafter-<?php echo $number; ?>">Text after video:</label>
			<input class="widefat" id="video-textafter-<?php echo $number; ?>" name="widget-video[<?php echo $number; ?>][textafter]" type="text" value="<?php echo $textafter; ?>" />

			<input type="hidden" id="video-submit-<?php echo $number; ?>" name="video-submit-<?php echo $number; ?>" value="1" />
		</p>
<?php
}

function wp_widget_video_register() {
	if ( !$options = get_option('widget_video') )
		$options = array();
	$widget_ops = array('classname' => 'widget_video', 'description' => __('Adds YouTube/Dailymotion/Google... video'));
	$control_ops = array('width' => 460, 'height' => 350, 'id_base' => 'video');
	$name = __('Video widget');

	$id = false;
	foreach ( array_keys($options) as $o ) {

		// Old widgets can have null values for some reason
		if (    !isset($options[$o]['title']) ||
                        !isset($options[$o]['source'])  ||
                        !isset($options[$o]['id'])  ||
                        !isset($options[$o]['idlist'])  ||
                        !isset($options[$o]['width'])  ||
                        !isset($options[$o]['height'])  ||
                        !isset($options[$o]['textbefore'])  ||
                        !isset($options[$o]['textafter']) )
		{
                        continue;		
		}                        

		$id = "video-$o"; // Never never never translate an id
		wp_register_sidebar_widget($id, $name, 'wp_widget_video', $widget_ops, array( 'number' => $o ));
		wp_register_widget_control($id, $name, 'wp_widget_video_control', $control_ops, array( 'number' => $o ));
	}
	
	// If there are none, we register the widget's existance with a generic template
         if ( !$id ) {
		wp_register_sidebar_widget( 'video-1', $name, 'wp_widget_video', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'video-1', $name, 'wp_widget_video_control', $control_ops, array( 'number' => -1 ) );
	}
        
	
}

add_action( 'widgets_init', 'wp_widget_video_register' );

?>