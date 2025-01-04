<?php
	const PREFERRED_LANG = 'de';
	const IS_DIRECTOR_VISIBLE = FALSE;
	const IS_AUTHOR_VISIBLE = FALSE;
	const IS_WORKTRANSLATION_DATEPUBLISHED_VISIBLE = FALSE;

	const STARFLEET_LOGO = '../starfleet.svg';
	const FAVICON = STARFLEET_LOGO;
	const APPLE_TOUCH_ICON = '../apple-touch-icon.png';
	const STYLESHEET = '../style.css?date=2022-10-12T13:13Z';
	const SCRIPT = '../script.js';

	$json = file_get_contents('movies.jsonld');
	$data = json_decode($json, TRUE);
?>
<!DOCTYPE html>
<html
	id="movies"
	lang="<?= htmlSpecialChars($data['inLanguage']) ?>"
	typeof="<?= htmlSpecialChars($data['@type']) ?>"
	vocab="<?= htmlSpecialChars($data['@context']['@vocab'] ?? $data['@context']) ?>"
>
	<head>
		<meta charset="UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title><?= htmlSpecialChars($data['name']) ?> movies</title>
		<link rel="icon" href="<?= htmlSpecialChars(FAVICON) ?>"/>
		<link rel="mask-icon" href="<?= htmlSpecialChars(FAVICON) ?>"/>
		<link rel="apple-touch-icon" href="<?= htmlSpecialChars(APPLE_TOUCH_ICON) ?>"/>
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
						<a href="#main" aria-current="page">movies</a>
					</li>
				</ol>
			</nav>
		</header>
		<main id="main">
			<h1>Movies</h1>
			<table>
				<?php foreach ($data['hasPart'] as $era): ?>
					<tbody
						<?php if ($era['@type']): ?>
							property="hasPart" typeof="<?= htmlSpecialChars($era['@type']) ?>"
						<?php endif; ?>
					>
						<?php foreach ($era['hasPart'] as $movie): ?>
							<?php // $translationCount = ($movie['workTranslation'] && $movie['workTranslation']['name']) ? NULL : sizeof($movie['workTranslation']); ?>
							<?php $translation = $movie['workTranslation'][1] ?? $movie['workTranslation'][0] ?? $movie['workTranslation']; ?>
							<tr property="hasPart" typeof="<?= htmlSpecialChars($movie['@type']) ?>">
								<?php $movie['@identifier'] = uniqid(); ?>
								<td property="name" id="<?= htmlSpecialChars($movie['@identifier']) ?>"
									<?php if (is_array($movie['name'])): ?>
										lang="<?= htmlSpecialChars($movie['name']['@language'] ?? 'und') ?>"
									<?php endif; ?>
								>
									<?= htmlSpecialChars($movie['name']['@value'] ?? $movie['name']) ?>
								</td>
								<?php if ($translation): ?>
									<td
										property="workTranslation"
										typeof="<?= htmlSpecialChars($translation['@type']) ?>"
										lang="<?= htmlSpecialChars($translation['inLanguage']) ?>"
										resource="_:<?= htmlSpecialChars($movie['@identifier']) ?><?= htmlSpecialChars($translation['inLanguage']) ?>"
										id="<?= htmlSpecialChars($movie['@identifier']) ?><?= htmlSpecialChars($translation['inLanguage']) ?>"
									>
										<span property="name"
											<?php if (is_array($translation['name'])): ?>
												lang="<?= htmlSpecialChars($translation['name']['@language'] ?? 'und') ?>"
											<?php endif; ?>
										>
											<?= htmlSpecialChars($translation['name']['@value'] ?? $translation['name']) ?>
										</span>
									</td>
								<?php else: ?>
									<td></td>
								<?php endif; ?>
								<td>
									<time property="datePublished"><?= htmlSpecialChars($movie['datePublished']) ?></time>
								</td>
								<?php if (IS_WORKTRANSLATION_DATEPUBLISHED_VISIBLE): ?>
									<?php if ($translation): ?>
										<td
											resource="_:<?= htmlSpecialChars($movie['@identifier']) ?><?= htmlSpecialChars($translation['inLanguage']) ?>"
										>
											<time property="datePublished">
												<?= htmlSpecialChars($translation['datePublished']) ?>
											</time>
										</td>
									<?php else: ?>
										<td></td>
									<?php endif; ?>
								<?php endif; ?>
								<?php if (IS_DIRECTOR_VISIBLE): ?>
									<?php if ($movie['director']): ?>
										<?php if ($movie['director']['name']): ?>
											<td
												property="director"
												typeof="<?= htmlSpecialChars($movie['director']['@type']) ?>"
												resource="https://bittersmann.de/startrek/persons/<?= htmlSpecialChars($movie['director']['@id']) ?>"
											>
												<span property="name"><?= htmlSpecialChars($movie['director']['name']) ?></span>
											</td>
										<?php else: ?>
											<td>
												<ul>
													<?php foreach ($movie['director'] as $director): ?>
														<li
															property="director"
															typeof="<?= htmlSpecialChars($director['@type']) ?>"
															resource="https://bittersmann.de/startrek/persons/<?= htmlSpecialChars($director['@id']) ?>"
														>
															<span property="name"><?= htmlSpecialChars($director['name']) ?></span>
														</li>
													<?php endforeach; ?>
												</ul>
											</td>
										<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php if (IS_AUTHOR_VISIBLE): ?>
									<?php if ($movie['author']): ?>
										<?php if ($movie['isBasedOn'] && $movie['isBasedOn']['author']): ?>
											<td>
												<dl>
													<div>
														<dt>screenplay by:</dt>
														<?php if ($movie['author']['name']): ?>
															<dd
																property="author"
																typeof="<?= htmlSpecialChars($movie['author']['@type']) ?>"
																resource="https://bittersmann.de/startrek/persons/<?= htmlSpecialChars($movie['author']['@id']) ?>"
															>
																<span property="name"><?= htmlSpecialChars($movie['author']['name']) ?></span>
															</dd>
														<?php else: ?>
															<dd>
																<ul>
																	<?php foreach ($movie['author'] as $author): ?>
																		<li
																			property="author"
																			typeof="<?= htmlSpecialChars($author['@type']) ?>"
																			resource="https://bittersmann.de/startrek/persons/<?= htmlSpecialChars($author['@id']) ?>"
																		>
																			<span property="name"><?= htmlSpecialChars($author['name']) ?></span>
																		</li>
																	<?php endforeach; ?>
																</ul>
															</dd>
														<?php endif; ?>
													</div>
													<div property="isBasedOn" typeof="<?= htmlSpecialChars($movie['isBasedOn']['@type']) ?>">
														<dt>story by:</dt>
														<?php if ($movie['isBasedOn']['author']['name']): ?>
															<dd
																property="author"
																typeof="<?= htmlSpecialChars($movie['isBasedOn']['author']['@type']) ?>"
																resource="https://bittersmann.de/startrek/persons/<?= htmlSpecialChars($movie['isBasedOn']['author']['@id']) ?>"
															>
																<span property="name"><?= htmlSpecialChars($movie['isBasedOn']['author']['name']) ?></span>
															</dd>
														<?php else: ?>
															<dd>
																<ul>
																	<?php foreach ($movie['isBasedOn']['author'] as $author): ?>
																		<li
																			property="author"
																			typeof="<?= htmlSpecialChars($author['@type']) ?>"
																			resource="https://bittersmann.de/startrek/persons/<?= htmlSpecialChars($author['@id']) ?>"
																		>
																			<span property="name"><?= htmlSpecialChars($author['name']) ?></span>
																		</li>
																	<?php endforeach; ?>
																</ul>
															</dd>
														<?php endif; ?>
													</div>
												</dl>
											</td>
										<?php else: ?>
											<?php if ($movie['author']['name']): ?>
												<td
													property="author"
													typeof="<?= htmlSpecialChars($movie['author']['@type']) ?>"
													resource="https://bittersmann.de/startrek/persons/<?= htmlSpecialChars($movie['author']['@id']) ?>"
												>
													<span property="name"><?= htmlSpecialChars($movie['author']['name']) ?></span>
												</td>
											<?php else: ?>
												<td>
													<ul>
														<?php foreach ($movie['author'] as $author): ?>
															<li
																property="author"
																typeof="<?= htmlSpecialChars($author['@type']) ?>"
																resource="https://bittersmann.de/startrek/persons/<?= htmlSpecialChars($author['@id']) ?>"
															>
																<span property="name"><?= htmlSpecialChars($author['name']) ?></span>
															</li>
														<?php endforeach; ?>
													</ul>
												</td>
											<?php endif; ?>
										<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($movie['description'] || $movie['abstract']): ?>
									<?php
										$plotType = ($movie['description']) ? 'description' : 'abstract';
										$plotLang = ($movie[$plotType][PREFERRED_LANG]) ? PREFERRED_LANG : array_keys($movie[$plotType])[0];
									?>
									<td>
										<details lang="<?= htmlSpecialChars($plotLang) ?>">
											<summary aria-describedby="<?= htmlSpecialChars($movie['@identifier']) ?><?= ($plotLang == 'de' && $translation) ? 'de' : '' ?>">
												<?php if ($plotLang == 'de'): ?>
													Handlung
												<?php else: ?>
													Plot
												<?php endif; ?>
											</summary>
											<p property="<?= htmlSpecialChars($plotType) ?>">
												<?= htmlSpecialChars($movie[$plotType][$plotLang]) ?>
											</p>
											<?php if ($movie['subjectOf']): ?>
												<p>
													<?php if ($plotLang == 'de'): ?>
														siehe auch:
													<?php else: ?>
														see also:
													<?php endif; ?>
													<?php if ($movie['subjectOf']['url']): ?>
														<span property="subjectOf" typeof="Webpage">
															<a
																property="url"
																href="<?= htmlSpecialChars($movie['subjectOf']['url']) ?>"
															>
																<?= htmlSpecialChars($movie['subjectOf']['publisher']['name']) ?>
																(<?= htmlSpecialChars($movie['subjectOf']['inLanguage']) ?>)
															</a>
														</span>
													<?php else: ?>
														<?php foreach ($movie['subjectOf'] as $index => $source): ?>
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
														<?php endforeach; ?>
													<?php endif; ?>
												</p>
											<?php endif; ?>
										</details>
									</td>
								<?php else: ?>
									<td></td>
								<?php endif; ?>
								<?php if ($movie['review']): ?>
									<?php if ($movie['review']['video']): ?>
										<td property="review" typeof="Review">
											<details lang="en" property="video" typeof="VideoObject">
												<summary
													aria-describedby="<?= htmlSpecialChars($movie['@identifier']) ?>"
													<?php if ($movie['review']['name']): ?>
														title="<?= htmlSpecialChars($movie['review']['name']) ?>"
													<?php endif; ?>
												>
													<?php if ($movie['review']['creator'] && $movie['review']['creator']['name']): ?>
														<span property="creator" typeof="<?= htmlSpecialChars($movie['review']['creator']['@type']) ?>">
															<span class="visually-hidden" property="name"><?= htmlSpecialChars($movie['review']['creator']['name']) ?></span>
															<abbr aria-hidden="true"><?= htmlSpecialChars($movie['review']['creator']['name'][0]) ?></abbr>
														</span>
													<?php endif; ?>
												</summary>
												<meta
													property="embedUrl"
													content="<?= htmlSpecialChars($movie['review']['video']['embedUrl']) ?>"
												/>
												<iframe
													allowfullscreen=""
													aria-label="<?= htmlSpecialChars($movie['review']['name']) ?>"
													aria-describedby="<?= htmlSpecialChars($movie['@identifier']) ?>">
												</iframe>
											</details>
										</td>
									<?php else: ?>
										<td>
											<ul>
												<?php foreach ($movie['review'] as $review): ?>
													<li property="review" typeof="Review">
														<details lang="en" property="video" typeof="VideoObject" name="review-<?= htmlSpecialChars($movie['@identifier']) ?>">
															<summary
																aria-describedby="<?= htmlSpecialChars($movie['@identifier']) ?>"
																<?php if ($review['name']): ?>
																	title="<?= htmlSpecialChars($review['name']) ?>"
																<?php endif; ?>
															>
																<?php if ($review['creator'] && $review['creator']['name']): ?>
																	<span property="creator" typeof="<?= htmlSpecialChars($review['creator']['@type']) ?>">
																		<span class="visually-hidden" property="name"><?= htmlSpecialChars($review['creator']['name']) ?></span>
																		<abbr aria-hidden="true"><?= htmlSpecialChars($review['creator']['name'][0]) ?></abbr>
																	</span>
																<?php endif; ?>
															</summary>
															<meta
																property="embedUrl"
																content="<?= htmlSpecialChars($review['video']['embedUrl']) ?>"
															/>
															<iframe
																allowfullscreen=""
																aria-label="<?= htmlSpecialChars($review['name']) ?>"
																aria-describedby="<?= htmlSpecialChars($movie['@identifier']) ?>">
															</iframe>
														</details>
													</li>
												<?php endforeach; ?>
											</ul>
										</td>
									<?php endif; ?>
								<?php else: ?>
									<td></td>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				<?php endforeach; ?>
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
