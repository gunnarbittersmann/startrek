<?php
	const STARFLEET_LOGO = '../starfleet.svg';

	$json = file_get_contents('khan.jsonld');
	$data = json_decode($json, TRUE);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title>Star Trek: Khan</title>
		<link rel="icon" href="../starfleet.svg"/>
		<link rel="mask-icon" href="../starfleet.svg"/>
		<link rel="apple-touch-icon" href="../apple-touch-icon.png"/>
		<link rel="stylesheet" href="../style.css"/>
	</head>
	<body>
		<header>
			<nav>
				<ol>
					<li>
						<a
							title="Star Trek"
							aria-label="Star Trek"
							href="/startrek"
						>
							<?php readfile(STARFLEET_LOGO); ?>
						</a>
					</li>
					<li>
						<a>audio drama</a>
					</li>
					<li>
						<a href="#main" aria-current="page">Khan</a>
					</li>
				</ol>
			</nav>
		</header>
		<main typeof="CreativeWorkSeries">
			<h1><?= htmlspecialchars($data['name']) ?></h1>
			<table>
				<tbody>
					<?php foreach ($data['episode'] as $episode): ?>
						<tr property="episode" typeof="<?= htmlSpecialChars($episode['@type']) ?>">
							<th property="episodeNumber"><?= htmlspecialchars($episode['episodeNumber']) ?></th>
							<td property="name"><?= htmlspecialchars($episode['name']) ?></td>
							<td><time property="datePublished"><?= htmlspecialchars($episode['datePublished']) ?></time></td>
						</tr>
					<?php endforeach; // ($data['episode'] as $episode) ?>
				</tbody>
			</table>
		</main>
		<footer>
			<a href="../privacy">
				<span lang="en">Privacy</span>/<span lang="de">Datenschutz</span>
			</a>
			– Data source:
				<?php if ($data['subjectOf']): ?>
					<?php foreach ($data['subjectOf'] as $index => $source): ?>
						<?php if ($index): ?>
							&amp;
						<? endif; ?>
						<cite property="subjectOf" typeof="Webpage">
							<a
								property="url"
								href="<?= htmlSpecialChars($source['url']) ?>"
							>
								<?= htmlSpecialChars($source['publisher']['name']) ?>
								(<?= htmlSpecialChars($source['inLanguage']) ?>)
							</a>
						</cite>
					<?php endforeach; ?>
				<?php else: ?>
					<cite>Wikipedia</cite>
				<?php endif; ?>
		</footer>
	</body>
</html>
