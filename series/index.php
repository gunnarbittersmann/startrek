<?php
  const PREFERRED_LANG = 'de';
  const IS_LOGO_VISIBLE = FALSE;
  const IS_DIRECTOR_VISIBLE = FALSE;
  const IS_WORKTRANSLATION_DATEPUBLISHED_VISIBLE = FALSE;

  const STARFLEET_LOGO = '../starfleet.svg';
  const FAVICON = STARFLEET_LOGO;
  const APPLE_TOUCH_ICON = '../apple-touch-icon.png';
  const STYLESHEET = '../style.css?date=2023-01-02T14:48Z';
  const SCRIPT = '../script.js';

  $files = scandir('.');

  $json = file_get_contents('series.jsonld');
  $franchise = json_decode($json, TRUE);

  $json = @file_get_contents($_GET['series'] . '.jsonld');
  $data = json_decode($json, TRUE);

  if ($data) {
    $lastSeason = end($data['containsSeason']);
    $lastEpisode = @end($lastSeason['episode']);
    $recentAfterDateString = date_format(date_create('- 1 month'), 'c');
    $hasRecentSeason = (
      !$lastEpisode['datePublished'] OR $lastEpisode['datePublished'] > $recentAfterDateString
    );
  }

  function head($title) {
    $title = htmlSpecialChars($title);
    $starfleet_logo = htmlSpecialChars(STARFLEET_LOGO);
    $favicon = htmlSpecialChars(FAVICON);
    $apple_touch_icon = htmlSpecialChars(APPLE_TOUCH_ICON);
    $stylesheet = htmlSpecialChars(STYLESHEET);
    echo <<<EOT
      <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>$title</title>
        <link rel="icon" href="$favicon"/>
        <link rel="mask-icon" href="$favicon"/>
        <link rel="apple-touch-icon" href="$apple_touch_icon"/>
        <link rel="stylesheet" href="$stylesheet"/>
      </head>
EOT;
  }
?>
<!DOCTYPE html>
<?php if ($data): ?>
  <html
    id="<?= htmlSpecialChars($_GET['series']) ?>"
    lang="<?= htmlSpecialChars($data['inLanguage']) ?>"
    typeof="<?= htmlSpecialChars($data['@type']) ?>"
    vocab="<?= htmlSpecialChars($data['@context']['@vocab'] ?? $data['@context']) ?>"
  >
    <?php head($data['name'] . ' episode list'); ?>
    <body>
      <header>
        <a href="#main" class="skip-link">skip navigation</a>
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
              <a href="<?= htmlSpecialChars($_SERVER['SCRIPT_NAME']) ?>">series</a>:
            </li>
            <?php foreach ($franchise['hasPart'] as $series): ?>
              <li>
                <a
                  title="<?= htmlSpecialChars($series['name']) ?>"
                  aria-label="<?= htmlSpecialChars($series['name']) ?>"
                  <?php if (mb_strtolower($series['identifier']) == $_GET['series']): ?>
                    href="#main" aria-current="page"
                  <?php elseif (in_array(mb_strtolower($series['identifier']) . '.jsonld', $files)): ?>
                    href="<?= htmlSpecialChars(mb_strtolower($series['identifier'])) ?>"
                  <?php endif; ?>
                >
                  <?= htmlSpecialChars(mb_strtoupper($series['identifier'])) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ol>
        </nav>
      </header>
      <main id="main">
        <h1 property="name"><?= htmlSpecialChars($data['name']) ?></h1>
        <table>
          <?php foreach ($data['containsSeason'] as $season): ?>
            <?php if ($season['episode']): ?>
              <tbody
                <?php if ($season['@type']): ?>
                  property="containsSeason" typeof="<?= htmlSpecialChars($season['@type']) ?>"
                <?php endif; ?>
              >
                <?php foreach ($season['episode'] as $episode): ?>
                  <?php // $translationCount = ($episode['workTranslation'] && $episode['workTranslation']['name']) ? NULL : sizeof($episode['workTranslation']); ?>
                  <?php $translation = $episode['workTranslation'][1] ?? $episode['workTranslation'][0] ?? $episode['workTranslation']; ?>
                  <tr property="episode" typeof="<?= htmlSpecialChars($episode['@type']) ?>">
                    <?php if ($episode['episodeNumber']): ?>
                      <?php $episode['@identifier'] = $data['identifier'] . preg_replace('/,\s*/', '-', $episode['episodeNumber']); ?>
                      <th property="episodeNumber">
                        <?= htmlSpecialChars($episode['episodeNumber']) ?>
                      </th>
                    <?php else: ?>
                      <?php $episode['@identifier'] = uniqid(); ?>
                      <th></th>
                    <?php endif; ?>
                    <td property="name" id="<?= htmlSpecialChars($episode['@identifier']) ?>"
                      <?php if (is_array($episode['name'])): ?>
                        lang="<?= htmlSpecialChars($episode['name']['@language'] ?? 'und') ?>"
                      <?php endif; ?>
                    >
                      <?= htmlSpecialChars($episode['name']['@value'] ?? $episode['name']) ?>
                    </td>
                    <?php if ($translation): ?>
                      <td
                        property="workTranslation"
                        typeof="<?= htmlSpecialChars($translation['@type']) ?>"
                        lang="<?= htmlSpecialChars($translation['inLanguage']) ?>"
                        resource="_:<?= htmlSpecialChars($episode['@identifier']) ?><?= htmlSpecialChars($translation['inLanguage']) ?>"
                        id="<?= htmlSpecialChars($episode['@identifier']) ?><?= htmlSpecialChars($translation['inLanguage']) ?>"
                      >
                        <?php if ($translation['alternateName']): ?>
                          <?php if ($data['identifier'] == 'TOS'): ?>
                            <s property="name"><?= htmlSpecialChars($translation['name']) ?></s>
                          <?php else: ?>
                            <span property="name"><?= htmlSpecialChars($translation['name']) ?></span>
                          <?php endif; ?>
                          <?php if (is_array($translation['alternateName'])): ?>
                            <?php foreach ($translation['alternateName'] as $alternateName): ?>
                              /
                              <span property="alternateName">
                                <?= htmlSpecialChars($alternateName) ?>
                              </span>
                            <?php endforeach; ?>
                          <?php else: ?>
                            /
                            <span property="alternateName">
                              <?= htmlSpecialChars($translation['alternateName']) ?>
                            </span>
                          <?php endif; ?>
                        <?php else: ?>
                          <span property="name"
                            <?php if (is_array($translation['name'])): ?>
                              lang="<?= htmlSpecialChars($translation['name']['@language'] ?? 'und') ?>"
                            <?php endif; ?>
                          >
                            <?= htmlSpecialChars($translation['name']['@value'] ?? $translation['name']) ?>
                          </span>
                        <?php endif; ?>
                      </td>
                    <?php else: ?>
                      <td></td>
                    <?php endif; ?>
                    <td>
                      <time property="datePublished"><?= htmlSpecialChars($episode['datePublished']) ?></time>
                    </td>
                    <?php if (IS_WORKTRANSLATION_DATEPUBLISHED_VISIBLE): ?>
                      <?php if ($translation): ?>
                        <td
                          resource="_:<?= htmlSpecialChars($episode['@identifier']) ?><?= htmlSpecialChars($translation['inLanguage']) ?>"
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
                      <?php if ($episode['director']): ?>
                        <?php if ($episode['director']['name']): ?>
                          <td
                            property="director"
                            typeof="<?= htmlSpecialChars($episode['director']['@type']) ?>"
                            resource="https://bittersmann.de/startrek/persons/<?= htmlSpecialChars($episode['director']['@id']) ?>"
                          >
                            <span property="name"><?= htmlSpecialChars($episode['director']['name']) ?></span>
                          </td>
                        <?php else: ?>
                          <td>
                            <ul>
                              <?php foreach ($episode['director'] as $director): ?>
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
                    <?php if ($episode['description'] OR $episode['abstract']): ?>
                      <?php
                        $plotType = ($episode['description']) ? 'description' : 'abstract';
                        $plotLang = ($episode[$plotType][PREFERRED_LANG]) ? PREFERRED_LANG : array_keys($episode[$plotType])[0];
                      ?>
                      <td>
                        <details lang="<?= htmlSpecialChars($plotLang) ?>">
                          <summary aria-describedby="<?= htmlSpecialChars($episode['@identifier']) ?><?= htmlSpecialChars($translation['inLanguage']) ?>">
                            <?php if ($plotLang == 'de'): ?>
                              Handlung
                            <?php else: ?>
                              Plot
                            <?php endif; ?>
                          </summary>
                          <p property="<?= htmlSpecialChars($plotType) ?>">
                            <?= htmlSpecialChars($episode[$plotType][$plotLang]) ?>
                          </p>
                          <?php if ($episode['sameAs']): ?>
                            <p>
                              <?php if ($plotLang == 'de'): ?>
                                mehr in der
                              <?php else: ?>
                                see also
                              <?php endif; ?>
                              <a property="sameAs" href="<?= htmlSpecialChars($episode['sameAs']) ?>">
                                Wikipedia
                              </a>
                            </p>
                          <?php endif; ?>
                        </details>
                      </td>
                    <?php else: ?>
                      <td></td>
                    <?php endif; ?>
                    <?php if ($episode['review']): ?>
                      <?php if ($episode['review']['video']): ?>
                        <td property="review" typeof="Review">
                          <details lang="en" property="video" typeof="VideoObject">
                            <summary aria-describedby="<?= htmlSpecialChars($episode['@identifier']) ?>">
                              Ups &amp; Downs
                            </summary>
                            <meta
                              property="embedUrl"
                              content="<?= htmlSpecialChars($episode['review']['video']['embedUrl']) ?>"
                            />
                            <iframe
                              allowfullscreen=""
                              aria-label="Ups &amp; Downs"
                              aria-describedby="<?= htmlSpecialChars($episode['@identifier']) ?>">
                            </iframe>
                          </details>
                        </td>
                      <?php else: ?>
                        <td>
                          <ul>
                            <?php foreach ($episode['review'] as $review): ?>
                              <li property="review" typeof="Review">
                                <details lang="en" property="video" typeof="VideoObject">
                                  <summary aria-describedby="<?= htmlSpecialChars($episode['@identifier']) ?>">
                                    Ups &amp; Downs
                                  </summary>
                                  <meta
                                    property="embedUrl"
                                    content="<?= htmlSpecialChars($review['video']['embedUrl']) ?>"
                                  />
                                  <iframe
                                    allowfullscreen=""
                                    aria-label="Ups &amp; Downs"
                                    aria-describedby="<?= htmlSpecialChars($episode['@identifier']) ?>">
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
            <?php endif; ?>
          <?php endforeach; ?>
        </table>
      </main>
      <footer>
        <a href="../privacy">
          <span lang="en">Privacy</span>/<span lang="de">Datenschutz</span>
        </a> – 
        Data source:
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
                Wikipedia (<?= htmlSpecialChars($source['inLanguage']) ?>)</a>
            </cite>
          <?php endforeach; ?>
        <?php elseif ($data['sameAs']): ?>
          <cite property="subjectOf" typeof="Webpage">
            <a
              property="url"
              href="<?= htmlSpecialChars($data['sameAs']) ?>"
            >
              Wikipedia
            </a>
          </cite>
        <?php else: ?>
          <cite>Wikipedia</cite>
        <?php endif; ?>
        <?php if ($hasRecentSeason AND $lastSeason['subjectOf']): ?>
          – season <?= htmlSpecialChars($lastSeason['seasonNumber']) ?>:
          <?php foreach ($lastSeason['subjectOf'] as $index => $source): ?>
            <?php if ($index): ?>
              &amp;
            <? endif; ?>
            <cite property="subjectOf" typeof="Webpage">
              <a
                property="url"
                href="<?= htmlSpecialChars($source['url']) ?>"
              >
                Wikipedia (<?= htmlSpecialChars($source['inLanguage']) ?>)</a>
            </cite>
          <?php endforeach; ?>
        <?php endif; ?>
      </footer>
      <script>
        <?php readfile(SCRIPT); ?>
      </script>
    </body>
  </html>
<?php else: ?>
  <html
    id="index"
    lang="<?= htmlSpecialChars($franchise['inLanguage']) ?>"
    typeof="<?= htmlSpecialChars($franchise['@type']) ?>"
    vocab="<?= htmlSpecialChars($franchise['@context']['@vocab'] ?? $franchise['@context']) ?>"
  >
    <?php head($franchise['name'] . ' series'); ?>
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
              <a href="#main" aria-current="page">series</a>
            </li>
          </ol>
        </nav>
      </header>
      <main id="main">
        <h1 property="name"><?= htmlSpecialChars($franchise['name'] . ' series') ?></h1>
        <table>
          <thead>
            <tr>
              <?php if (IS_LOGO_VISIBLE): ?>
                <th></th>
              <?php endif; ?>
              <th>series</th>
              <th>start</th>
              <th>end</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($franchise['hasPart'] as $series): ?>
              <tr property="hasPart" typeof="<?= htmlSpecialChars($series['@type']) ?>">
                <?php if (IS_LOGO_VISIBLE): ?>
                  <?php if ($series['image']): ?>
                    <td>
                      <a
                        aria-hidden="true"
                        tabindex="-1"
                        <?php if (in_array(mb_strtolower($series['identifier']) . '.jsonld', $files)): ?>
                          href="?series=<?= htmlSpecialChars(mb_strtolower($series['identifier'])) ?>"
                        <?php endif; ?>
                      >
                        <img property="image" src="<?= htmlSpecialChars($series['image']) ?>" alt=""/>
                      </a>
                    </td>
                  <?php else: ?>
                    <td></td>
                  <?php endif; ?>
                <?php endif; ?>
                <th property="name">
                  <a
                    <?php if (in_array(mb_strtolower($series['identifier']) . '.jsonld', $files)): ?>
                      href="<?= htmlSpecialChars(mb_strtolower($series['identifier'])) ?>"
                    <?php endif; ?>
                  >
                    <?= htmlSpecialChars($series['name']) ?>
                  </a>
                </th>
                <?php if ($series['startDate']): ?>
                  <td>
                    <time property="startDate">
                      <?= htmlSpecialChars($series['startDate']) ?>
                    </time>
                  </td>
                <?php else: ?>
                  <td></td>
                <?php endif; ?>
                <?php if ($series['endDate']): ?>
                  <td>
                    <time property="endDate">
                      <?= htmlSpecialChars($series['endDate']) ?>
                    </time>
                  </td>
                <?php else: ?>
                  <td></td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </main>
      <script>
        <?php readfile(SCRIPT); ?>
      </script>
    </body>
  </html>
<?php endif; ?>
