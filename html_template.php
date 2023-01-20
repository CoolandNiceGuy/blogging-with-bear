<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta content="width=device-width, initial-scale=1" name="viewport" />
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Exo:wght@600&family=Prompt&family=Roboto+Mono&display=swap" rel="stylesheet">
		<title><?= $html_title ?></title>
		<link rel="stylesheet"  type="text/css" href="./main.css">
		<script src="https://kit.fontawesome.com/f68e658398.js" crossorigin="anonymous"></script>
	</head>
	<body>

		<header>
			<h1 class="title">Glass Village</h1>
			<a href="<?= $url_root ?>"><div class="btn"><i class="fa-solid fa-house"></i></div></a>
			<a href="https://glassvillage.io/"><div class="btn"><i class="fa-solid fa-globe"></i></div></a>
			<a href="https://github.com/CoolandNiceGuy"><div class="btn"><i class="fa-brands fa-github"></i></div></a>
			<a href="https://www.linkedin.com/in/landon-borges/"><div class="btn"><i class="fa-brands fa-linkedin"></i></div></a>
			<a href="https://twitter.com/Landon_Borges"><div class="btn"><i class="fa-brands fa-twitter"></i></div></a>
		</header>

		<div class="mobileHeader">
			<h1 class="title">Glass Village Blog</h1>
		</div>

		<main>
			<article>
	
				<?= $content ?>
	
			</article>
			<footer>
			<ul>
				<li><?= $copyright ?></li>
			</ul>
		</footer>
		</main>
		<nav>
			<!-- <span class="time"><?= $date ?></span> -->
		</nav>
	</body>
</html>