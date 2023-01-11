<?php

// Part of Blogging with Bear by Steven Frank <stevenf@panic.com>

// Edit these variables for your particular setup:

$username = "landonborges";	// your Mac username
// $publish_cmd = "rsync -avz --delete ./public/ \"linode:/var/www/stevenf/wiki\"";

// This rsync command works for me, because "linode" is an alias for the real hostname
// in my ~/.ssh/config, and I've already set up password-less, key-based authentication.
// It rsyncs the _contents_ of "public" in the current directory to 
// "/var/www/stevenf/wiki/" on my webserver.

$time_zone = 'America/Chicago';
$url_root = 'https://blog.glassvillage.io';
$html_title = 'Blog';
$copyright = "&copy;". date("Y") . " <a href=\"https://glassvillage.io/\"><b>Landon Borges</b></a>";
$rss_max = 20;	// maximum number of notes to include in RSS feed
$rss_title = 'glassvillage';
$rss_description = "landon's micro blog";
$rss_lang = "en";
$rss_url = "https://glassvillage.io/index.xml";

// End of config block

require 'Parsedown.php'; // you can get this from: https://github.com/erusev/parsedown/releases/
