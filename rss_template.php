<?php echo "<"; ?>?xml version="1.0" encoding="utf-8"?>
<rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
    <channel>
        <title><?= $rss_title ?></title>
        <link><?= $url_root ?></link>
        <description><?= $rss_description ?></description>
        <docs>http://blogs.law.harvard.edu/tech/rss</docs>
        <language><?= $rss_lang ?></language>
        <pubDate><?= $rss_date ?></pubDate>
        <lastBuildDate><?= $rss_date ?></lastBuildDate>
        <atom:link href="<?= $rss_url ?>" rel="self" type="application/rss+xml"/>
<?= $content ?>
    </channel>
</rss>
