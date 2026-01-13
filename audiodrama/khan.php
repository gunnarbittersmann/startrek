<?php
	const PREFERRED_LANG = 'de';
	const STARFLEET_LOGO = '../starfleet.svg';
	const STYLESHEET = '../style.css?date=2025-10-28T01:55Z';
	const SCRIPT = '../script.js';

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
		<link rel="stylesheet" href="<?= htmlSpecialChars(STYLESHEET) ?>"/>
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
							<?php if ($episode['description'] || $episode['abstract'] || $episode['subjectOf']): ?>
								<?php
									$hasPlot = ($episode['description'] || $episode['abstract']);
									if ($hasPlot) {
										$plotType = ($episode['description']) ? 'description' : 'abstract';
										if ($episode[$plotType]['@value']) {
											$plotValue = $episode[$plotType]['@value'];
											$plotLang = $episode[$plotType]['@language'];
										}
										elseif (is_array($episode[$plotType])) {
											$plotArray = array_filter($episode[$plotType], function ($entry) {
												return $entry['@language'] == PREFERRED_LANG;
											});
											if (!count($plotArray)) {
												$plotArray = $episode[$plotType];
											}
											$firstKey = array_key_first($plotArray);
											$plotValue = $plotArray[$firstKey]['@value'];
											$plotLang = $plotArray[$firstKey]['@language'];
										}
									}
								?>
								<td>
									<details lang="<?= htmlSpecialChars($plotLang) ?>">
										<summary
											aria-describedby="<?= htmlSpecialChars($episode['@identifier']) ?><?= ($plotLang == 'de' && $translation) ? 'de' : '' ?>"
										>
											<?php if ($hasPlot): ?>
												<?php if ($plotLang == 'de'): ?>
													Handlung
												<?php else: ?>
													Plot
												<?php endif; ?>
											<?php else: ?>
												Links
											<?php endif; ?>
										</summary>
										<?php if ($hasPlot): ?>
											<p property="<?= htmlSpecialChars($plotType) ?>">
												<?= htmlSpecialChars($plotValue) ?>
											</p>
										<?php endif; // ($hasPlot) ?>
										<?php if ($episode['subjectOf']): ?>
											<p>
												<?php if ($plotLang == 'de'): ?>
													siehe auch:
												<?php else: ?>
													see also:
												<?php endif; ?>
												<?php if ($episode['subjectOf']['url']): ?>
													<span property="subjectOf" typeof="Webpage">
														<a
															property="url"
															href="<?= htmlSpecialChars($episode['subjectOf']['url']) ?>"
														>
															<?= htmlSpecialChars($episode['subjectOf']['publisher']['name']) ?>
															(<?= htmlSpecialChars($episode['subjectOf']['inLanguage']) ?>)
														</a>
													</span>
												<?php else: // ($episode['subjectOf']['url']) ?>
													<?php foreach ($episode['subjectOf'] as $index => $source): ?>
														<?php if ($index): ?>
															&amp;
														<?php endif; ?>
														<span property="subjectOf" typeof="Webpage">
															<a
																property="url"
																href="<?= htmlSpecialChars($source['url']) ?>"
															>
																<?= htmlSpecialChars($source['publisher']['name']) ?>
																(<?= htmlSpecialChars($source['inLanguage']) ?>)
															</a>
														</span>
													<?php endforeach; // ($episode['subjectOf'] as $index => $source) ?>
												<?php endif; // ($episode['subjectOf']['url']) ?>
											</p>
										<?php endif; // ($episode['subjectOf']) ?>
									</details>
								</td>
							<?php else: // ($episode['description'] || $episode['abstract'] || $episode['subjectOf']) ?>
								<td></td>
							<?php endif; // ($episode['description'] || $episode['abstract'] || $episode['subjectOf']) ?>
							<?php if ($episode['audio']): ?>
								<td>
									<details lang="en" property="audio" typeof="AudioObject">
										<summary>Audio</summary>
										<meta
											property="embedUrl"
											content="<?= htmlSpecialChars($episode['audio']['embedUrl']) ?>"
										/>
										<iframe allowfullscreen="" aria-label="audio"></iframe>
									</details>
								</td>
							<?php else: // ($episode['audio']) ?>
								<td></td>
							<?php endif; // ($episode['audio']) ?>
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
		<script>
			<?php readfile(SCRIPT); ?>
		</script>
	</body>
</html>
