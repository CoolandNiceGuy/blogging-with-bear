#!/usr/bin/php
<?php
// bear-export.php
//
// This script is meant to be run on a Mac with a fully synced-up Bear installation
// that has some notes tagged as #public.
//
// Run it from wherever it lives. The PHP that ships with macOS 10.13 has the
// necessary dependencies.
//
//
// BEFORE YOU RUN THIS: 
//
// Note that it does an "rm -r public" in your current directory! If you happen to have
// an important directory called "public" there, don't run the script or it will be 
// deleted!
//
// ALSO: 
// 
// You'll want to change the vars in config.php:

require 'config.php';
require __DIR__ . '/GitPublishHelper.php';

date_default_timezone_set($time_zone);

$bear_img_dir = "/Users/{$username}/Library/Group Containers/9K33E3U3T4.net.shinyfrog.bear/Application Data/Local Files/Note Images/";
// Step 1: Copy Bear's sqlite database out of its container

system("cp \"/Users/{$username}/Library/Group Containers/9K33E3U3T4.net.shinyfrog.bear/Application Data/database.sqlite\" bear.sqlite");

// Remove any existing "public" directory and re-create it

system("rm -rf public");
system("mkdir -p public/images");

// Step 2: Create .txt files from all the notes tagged "public" 

// Open the DB

$db = new SQLite3("bear.sqlite");

// Look up the ID number of the "public" tag.  This will be different on different
// Macs, even if you're syncing Bear!

$result = $db->query("SELECT * FROM ZSFNOTETAG WHERE ZTITLE = 'public'");
$row = $result->fetchArray();
$public_tag_id = $row['Z_PK'];

// Get the IDs of notes that have the "public" tag

$result = $db->query("SELECT * FROM Z_7TAGS WHERE Z_14TAGS = $public_tag_id");

while ( $row = $result->fetchArray() )
{
	// For each note...
	
	$note_id = $row['Z_7NOTES'];

	$result2 = $db->query("SELECT * FROM ZSFNOTE WHERE Z_PK = $note_id");

	while ( $row2 = $result2->fetchArray() )
	{
		// ... get the title and text. ZTRASHEDDATE is NULL if note is not in 
		// the trash.
		
		$title = $row2['ZTITLE'];
		$text = $row2['ZTEXT'];
		$trashed = $row2['ZTRASHEDDATE'];

		// We only want notes that are not in the trash
		
		if ( $trashed == '' )
		{
			// Write out the note's text into the public directory
			
			$fp = fopen("public/$title.txt", "w");
			fwrite($fp, $text);
			fclose($fp);
		}
	}
}

// Close the Database

$db->close();

// Step 3: Build the index page (public/index.html) by enumerating 
// the .txt files in ./public

$filenames = array();
$content = "";

if ( $dir = opendir("./public") )
{
	$content .= "<ul>";
	
	while ( $filename = readdir($dir) )
	{
		if ( $filename[0] == '.' || !is_file("./public/$filename") )
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
		
		$content .= "<li class=\"index\">$date<br><a href=\"{$url}.html\"><b>{$title}</b></a></li>\n";
	}

	$content .= "</ul>";			
	// $date = "Most recent";
}

// Write public/index.html

ob_start();
require "html_template.php";
$html = ob_get_clean();
if (ob_get_length()) ob_end_clean();

$fp = fopen("public/index.html", "w");
fwrite($fp, $html);
fclose($fp);

// Step 4: Write individual HTML pages for each exported .txt note

foreach ( $filenames as $page )
{
	$title = preg_replace("/\.txt$/", "", $page);
	
	// Viewing a specific note, identified by $page

	$file = file_get_contents("./public/$page");

	// Extract the datestamp and format it

	$timestamp = get_timestamp($file);
	$date = date("Y-m-d @ g:i A", $timestamp);

	// Apply Markdown and other formatting

	$content = format($file, $title);

	// Mega-hack to find backlinks.
	// This should probably be cached or just generally done in a much
	// smarter way.

	$output = shell_exec("grep -l \"\\[\\[$title\\]\\]\" ./public/*.txt");
	
	$lines = preg_split("/\n/", $output ?? '');
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
			$content .= "<li><a class=\"seealso\" href=\"" . rawurlencode($matches[1]) . ".html\">{$matches[1]}</a></li>\n";
		}
	}

	if ( $references > 0 )
	{
		$content .= "</ul>";
	}

	// End of backlnks hack

	// Now we have all the data we need, push it through the HTML template

	ob_start();
	require "html_template.php";
	$html = ob_get_clean();
	if (ob_get_length()) ob_end_clean();

	$fp = fopen("public/$title.html", "w");
	fwrite($fp, $html);
	fclose($fp);
}

// Step 5: Create RSS feed at public/index.xml

$content = '';
	
$rss_date = '';
$i = 0;

// For each note found...

foreach ( $filenames as $timestamp => $filename )
{	
	// Extract and URL encode its title
	
	print "-> $filename\n";
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
	$content .= "		<title><![CDATA[$title]]></title>\n";
	$content .= "		<description><![CDATA[$body]]></description>\n";
	$content .= "		<link>{$url_root}$url.html</link>\n";
	$content .= "		<pubDate>$page_date</pubDate>\n";
	$content .= "		<guid isPermaLink=\"true\">{$url_root}$url.html</guid>\n";
	$content .= "	</item>\n";
	
	// Stop if we've done the maximum number ofnotes
	
	++$i;

	if ( $i == $rss_max )
	{
		break;
	}				
}

// Now that we have everything, push it through the RSS template

ob_start();
require "rss_template.php";
$html = ob_get_clean();
if (ob_get_length()) ob_end_clean();

$fp = fopen("public/index.xml", "w");
fwrite($fp, $html);
fclose($fp);

// Step 6: Copy CSS

system("cp main.css ./public/");

// Step 7: Run the publish command

system("rm public/*.txt");

publish_changes();  

// Step 8: Clean up after ourselves

system("rm -rf public bear.sqlite");

// Done!

exit;


// Support functions

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
	// ToDo: Extract tags to use as elements in UI

	$file = preg_replace("/#(\w+)/", "", $file); 
	$file = preg_replace("/ (\w+)#/", "", $file); 

	// Convert image references to <img> tags
	
	
	# find all [image:] tags within the note
	preg_match_all("/\[image\:.*?\]/mis", $file, $images);


	# for all found matches
	#  - extract the path 
	#  - copy the image file to public/images
	#  - replace the [image:] with <img> referencing the new file
	# Bear stores each image in a note related subdir, but here they all end up in the same dir
	foreach($images[0] as $image) {
		global $bear_img_dir;
		preg_match("/\[image\:(.*?)\]/mis", $image, $matches);
		$image_file_path = $matches[1];
		system("cp -R \"" . $bear_img_dir . $image_file_path . "\" public/images/");

		preg_match("/.*\/(.*)$/", $image_file_path, $image_subpath_match);

		$file = preg_replace("/\[image\:" . preg_quote($image_file_path, "/") . "\]/", "<img src=\"images/" . $image_subpath_match[1] . "\">", $file);

	}


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

	if ( preg_match("/^###### (.*?)$/ms", $file, $matches) )
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