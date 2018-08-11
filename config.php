<?php

// Part of Blogging with Bear by Steven Frank <stevenf@panic.com>

// Edit these variables for your particular setup:

$time_zone = 'America/Los_Angeles';
$url_root = 'https://stevenf.com/wiki/';
$html_title = '~stevenf wikiblog';
$copyright = "&copy; 2002-". date("Y") . " <a href=\"https://stevenf.com/\"><b>Steven Frank</b></a>";
$rss_max = 20;	// maximum number of notes to include in RSS feed
$rss_title = 'stevenf';
$rss_description = "Steven Frank's micro blog";
$rss_lang = "en";
$rss_url = "https://stevenf.com/index.xml";

// End of config block

require 'Parsedown.php'; // you can get this from: https://github.com/erusev/parsedown/releases/

function format($file, $title)
{
	global $url_root;
	
	// This function apples HTML formatting to the Markdown-formatted text in $file,
	// with the given title
	
	$Parsedown = new Parsedown();

	// Convert [[links like this]] into
	// <a href="index.php?page=links%20like%20this">links like this</a>

	$file = preg_replace_callback("/\[\[(.*?)\]\]/", function ($matches) {
			global $url_root;
			
			// Extract name of note from within double brackets
			
            $name = preg_replace("/\[\[(.*?)\]\]/", "\\1", $matches[0]);
            
            // Encode spaces
            
            $link = preg_replace("/ /", "%20", $matches[0]);
            
            // Remove double brackets
            
			$link = preg_replace("/\[\[(.*?)\]\]/", "\\1", $link);
			
			// Return as <a> tag
			
            return "<a href=\"$url_root{$link}.html\">{$name}</a>";
        }, $file);

	// I don't know what to do with Bear's #tags and #tags# yet, so just remove them

	$file = preg_replace("/#(\w+)/", "", $file); 
	$file = preg_replace("/ (\w+)#/", "", $file); 

	// Convert image references to <img> tags
	
	$file = preg_replace("/\[image\:.*\/(.*?)\]/", "<img src=\"public/" . rawurlencode($title) . "/\\1\">", $file);
	
	// Remove the datestamp, as it's extracted out into the sidebar elsewhere
	
	$file = preg_replace("/^###### Date: .*$/m", "", $file);
	
	// Apply the rest of the Markdown rules
	
	$file = $Parsedown->text($file);

	return $file;
}

function get_timestamp($file)
{
	// Look for the specially formatted datestamp in the string $file, and 
	// return it as a UNIX time:
	//
	// ###### Date: Aug 10, 2018 at 3:47 PM
	//
	// (Note: easily generate that with Cmd-Shift-7 in Bear)
	
	if ( preg_match("/^###### Date: (.*?)$/ms", $file, $matches) )
	{
		// Remove the substring " at " so PHP can parse the date,
		// and convert it into a UNIX timestamp
		
		$matches[1] = preg_replace("/ at /", " ", $matches[1]);
		$timestamp = strtotime($matches[1]);
	}
	else
	{
		// No datestamp found
		
		$timestamp = 0;
	}

	return $timestamp;
}