<?php

// Part of Blogging with Bear by Steven Frank <stevenf@panic.com>

// Edit these variables for your particular setup:

$username = 'stevenf';	// your Mac username
$publish_cmd = "rsync -avz --delete ./public/ \"linode:/var/www/stevenf/wiki\"";

// This rsync command works for me, because "linode" is an alias for the real hostname
// in my ~/.ssh/config, and I've already set up password-less, key-based authentication.
// It rsyncs the _contents_ of "public" in the current directory to 
// "/var/www/stevenf/wiki/" on my webserver.

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
