<!DOCTYPE html>
<html lang="en">
	<!-- Part of Blogging with Bear by Steven Frank <stevenf@panic.com> -->
	<head>
		<meta charset="utf-8">
		<title><?= $html_title ?></title>
		<link rel="stylesheet" type="text/css" href="<?= $url_root ?>main.css">
	</head>
	<body>

		<header>
			<h1><a href="<?= $url_root ?>"><?= $html_title ?></a></h1>
		</header>

		<nav>
			<span class="time"><?= $date ?></span>
		</nav>

		<main>
			<article>
	
				<?= $content ?>
	
			</article>
		</main>

		<footer>
			<ul>
				<li><?= $copyright ?></li>
			</ul>
		</footer>

	</body>
</html>