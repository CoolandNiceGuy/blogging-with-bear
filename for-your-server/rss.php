<?php 

// Part of Blogging with Bear by Steven Frank <stevenf@panic.com>

// This script generates the RSS feed. It outputs a configurable
// number of items in reverse chronological order.

require 'config.php';

date_default_timezone_set($time_zone);
header('Content-type: application/rss+xml');

$content = "";
$filenames = array();

if ( $dir = opendir("./public") )
{
	// Iterate over all notes in public directory
	
	while ( $filename = readdir($dir) )
	{
		if ( $filename{0} == '.' || !is_file("./public/$filename") )
		{
			// Skip dot files
			
			continue;
		}
		
		// Read this note
		
		$file = file_get_contents("./public/$filename");
		
		// Extract timestamp
		
		$timestamp = get_timestamp($file);
		
		// Add to list of known notes indexed by timestamp
		
		$filenames[$timestamp] = $filename;
	}

	closedir($dir);
	
	// Reverse-chrono sort the list of notes
	
	krsort($filenames);
	
	$rss_date = '';
	$i = 0;
	
	// For each note found...
	
	foreach ( $filenames as $timestamp => $filename )
	{	
		// Extract and URL encode its title
		
		$title = preg_replace("/\.txt$/", '', $filename);
		$url = rawurlencode($title);
		
		// Generate an RSS-compatible date from the datestamp
		
		$page_date = date("r", $timestamp);
		
		if ( $rss_date == '' )
		{
			// Set the pubDate for the feed to the most recent note
			
			$rss_date = $page_date;
		}
		
		// Read the note and apply formatting
		
		$body = file_get_contents("./public/$filename");
		$body = format($body, $title);
		
		// Add an RSS item for this note

		$content .= "	<item>\n";
		$content .= "		<description><![CDATA[$body]]></description>\n";
		$content .= "		<link>{$url_root}index.php%3Fpage=$url</link>\n";
		$content .= "		<pubDate>$page_date</pubDate>\n";
		$content .= "		<guid isPermaLink=\"true\">{$url_root}index.php%3Fpage=$url</guid>\n";
		$content .= "	</item>\n";
		
		// Stop if we've done the maximum number ofnotes
		
		++$i;

		if ( $i == $rss_max )
		{
			break;
		}				
	}
}

// Now that we have everything, push it through the RSS template

require "rss_template.php";
