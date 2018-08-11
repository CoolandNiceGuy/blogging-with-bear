<?php 
	
// Part of Blogging with Bear by Steven Frank <stevenf@panic.com>
//
// This page generates a reverse-chronological list of notes if loaded
// without parameters.
//
// If the 'page' parameter is given, the page with that name will be
// displayed.  'page' should not include the .txt extension.

require 'config.php';

date_default_timezone_set($time_zone);

if ( isset($_REQUEST['page']) )
{
	// If the "page" parameter is present in the URL, display that page.
	
	$page = $_REQUEST['page'];
	$page = rawurldecode($page);
	
	// Naive sanitization: just remove anything that's not alphanumeric or a dash
	
	$page = preg_replace("/[^0-9A-Za-z\- ]/", "", $page);
}
else
{
	// No "page" parameter was given, display the index.
	
	$page = '';
}

$content = "";
$filenames = array();
	
?>
<?php 
	if ( $page == '' ) 
	{
		// Build the index page by enumerating files in ./public
		
		if ( $dir = opendir("./public") )
		{
			$content .= "<ul>";
			
			while ( $filename = readdir($dir) )
			{
				if ( $filename{0} == '.' || !is_file("./public/$filename") )
				{
					// Skip dot files
					continue;
				}
				
				$file = file_get_contents("./public/$filename");
				$timestamp = get_timestamp($file);
				
				// Add to list of known files, indexed by timestamp
				
				$filenames[$timestamp] = $filename;
			}
		
			closedir($dir);
			
			// Reverse sort by timestamp
			
			krsort($filenames);

			// For each note file we found:

			foreach ( $filenames as $timestamp => $filename )
			{	
				// Remove file extension and URL encode the note title
				
				$title = preg_replace("/\.txt$/", '', $filename);
				$url = rawurlencode($title);
				
				// Format the date
				
				if ( $timestamp == 0 )
				{
					$date = "Undated";
				}
				else
				{
					$date = date("Y-m-d @ g:i A", $timestamp);
				}
				
				// Add a link to the index for this note
				
				$content .= "<li class=\"index\">$date<br><a href=\"?page={$url}\"><b>{$title}</b></a></li>\n";
			}

			$content .= "</ul>";			
			$date = "Most recent";
		}
	}
	else
	{
		// Viewing a specific note, identified by $page
		
		$file = file_get_contents("./public/$page.txt");
		
		// Extract the datestamp and format it
		
		$timestamp = get_timestamp($file);
		$date = date("Y-m-d @ g:i A", $timestamp);
		
		// Apply Markdown and other formatting
		
		$content = format($file, $title);
		
		// Mega-hack to find backlinks.
		// This should probably be cached or just generally done in a much
		// smarter way.
		
		$output = shell_exec("grep -l \"\\[\\[$page\\]\\]\" ./public/*");
		$lines = preg_split("/\n/", $output);
		$references = 0;

		foreach ( $lines as $line )
		{
			$line = trim($line);
			
			if ( $line != '' )
			{
				// Found a backlink to this note in another note
				
				++$references;
				
				if ( $references == 1 )
				{
					// Generate a "See also" section
					
					$content .= "<p><h4 class=\"seealso\">Pages that link here:</h4><ul>";
				}
				
				// Add a link for the found page
				
				preg_match("/\.\/public\/(.*?)\.txt/", $line, $matches);
				$content .= "<li><a class=\"seealso\" href=\"?page=" . rawurlencode($matches[1]) . "\">{$matches[1]}</a></li>\n";
			}
		}
		
		if ( $references > 0 )
		{
			$content .= "</ul>";
		}
		
		// End of backlnks hack
	}
	
	// Now we have all the data we need, push it through the HTML template
	
	require "template.php";
