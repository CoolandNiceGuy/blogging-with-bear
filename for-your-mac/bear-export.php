#!/usr/bin/php
<?php

// bear-export.php
// Part of Blogging with Bear by Steven Frank <stevenf@panic.com>
//
// This script is meant to be run on a Mac with a fully synced-up Bear installation
// that has some notes tagged as #public.
//
// Run it from wherever it lives. The PHP that ships with macOS 10.13 has the
// necessary dependencies.
//
// $ chmod +x bear-export.php
// $ ./bear-export.php
//
// BEFORE YOU RUN THIS: 
//
// Note that it does an "rm -r public" in your current directory! If you happen to have
// an important directory called "public" there, don't run the script or it will be 
// deleted!
//
// ALSO: 
// 
// You'll want to change these vars:

$username = 'stevenf';
$publish_cmd = "rsync -avz --delete ./public \"linode:/var/www/stevenf/wiki\"";

// This rsync command works for me, because "linode" is an alias for the real hostname
// in my ~/.ssh/config, and I've already set up password-less, key-based authentication.
// It rsyncs the directory "public" in the current directory to create/update
// "/var/www/stevenf/wiki/public" on my webserver.

// Step 1: Copy Bear's sqlite database out of its container

system("cp \"/Users/{$username}/Library/Containers/net.shinyfrog.bear/Data/Documents/Application Data/database.sqlite\" bear.sqlite");

// Remove any existing "public" directory and re-create it

system("rm -r public");
system("mkdir public");

// Open the DB

$db = new SQLite3("bear.sqlite");

// Look up the ID number of the "public" tag.  This will be different on different
// Macs, even if you're syncing Bear!

$result = $db->query("SELECT * FROM ZSFNOTETAG WHERE ZTITLE = 'public'");
$row = $result->fetchArray();
$public_tag_id = $row['Z_PK'];

// Get the IDs of notes that have the "public" tag

$result = $db->query("SELECT * FROM Z_5TAGS WHERE Z_10TAGS = $public_tag_id");

while ( $row = $result->fetchArray() )
{
	// For each note...
	
	$note_id = $row['Z_5NOTES'];

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

// Run the publish command

system($publish_cmd);

// Clean up after ourselves

system("rm -r public bear.sqlite");

