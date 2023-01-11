<?php
require_once 'vendor/autoload.php';

$git = new CzProject\GitPhp\Git;
$path = '/Users/landonborges/Side Projects/blogging-with-bear/public';
$repo = $git->open('/Users/landonborges/Side Projects/blogging-with-bear'); //this path works! absolute path is needed

function publish_changes() {
  global $git, $path;

  $publish_repo = $git->init($path);
  $publish_repo->addAllChanges();
  $date = date('Y-m-d H:i:s', time());
  $publish_repo->commit($date);
  $publish_repo->addRemote('origin', 'https://github.com/CoolandNiceGuy/bear-blog-posts.git');
  $publish_repo->push(['origin', 'main'], ['-u', '-f']);
}
?>