<?php

// Edit these variables for your particular setup:

$username = "landonborges";	// your Mac username

$time_zone = 'America/Chicago';
$url_root = 'https://blog.glassvillage.io'; // whatever url you're hosting your blog @
$html_title = 'Blog';
$copyright = "&copy;". date("Y") . " <a href=\"https://glassvillage.io/\"><b>Landon Borges</b></a>";
$rss_max = 20;	// maximum number of notes to include in RSS feed
$rss_title = 'glassvillage';
$rss_description = "landon's micro blog";
$rss_lang = "en";
$rss_url = "https://glassvillage.io/index.xml";

// End of config block

require 'Parsedown.php'; // you can get this from: https://github.com/erusev/parsedown/releases/
