<?php
/*
Plugin Name: Easy GitHub Gist
Plugin URI: http://wordpress.org/extend/plugins/easy-github-gist/
Description: Easy GitHub Gist Plugin allows you to embed GitHub Gists from https://gist.github.com/.
Usage: Just put the GitHub Gist url in the content.
Version: 0.2 
Author: Sivan 
Author URI: http://lightcss.com/
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
function get_content_from_url($url) {
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
	$content = curl_exec($ch);
	curl_close($ch);
	return $content;
}

function gist_raw($id, $file) {
	$request = "https://raw.github.com/gist/".$id."/".$file;
	return get_content_from_url($request);
}

function gist_raw_html($gist_raw) {
	return "<div style='margin-bottom:1em;padding:0;'><noscript><code><pre style='overflow:auto;margin:0;padding:0;border:1px solid #DDD;'>".htmlentities($gist_raw)."</pre></code></noscript></div>";
}

function gist_shortcode($atts) {
	$id = $atts['id'];
	$file = $atts['file'];
	$html = sprintf('<script src="https://gist.github.com/%s.js%s"></script>', $id, $file ? '?file='.$file : '');
	$gist_raw = gist_raw($id, $file);
	if ($gist_raw != null) {
		$html = $html.gist_raw_html($gist_raw);
	}
	return $html;
}
add_shortcode('gist','gist_shortcode');

//autoreplace gist links to shortcodes
function gist_shortcode_filter($content) {
	return preg_replace('/https:\/\/gist.github.com\/([\d]+)[\.js\?]*[\#]*file[=|-|_]+([\w\.]+)(?![^<]*<\/a>)/i', '[gist id="${1}" file="${2}"]', $content );
}
add_filter( 'the_content', 'gist_shortcode_filter', 9);

?>