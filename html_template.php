<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?= $html_title ?></title>
		<link rel="stylesheet" href="./main.css">
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