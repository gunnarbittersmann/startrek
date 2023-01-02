<?php
  const PREFERRED_LANG = 'de';
  const IS_LOGO_VISIBLE = FALSE;
  const IS_DIRECTOR_VISIBLE = FALSE;
  const IS_WORKTRANSLATION_DATEPUBLISHED_VISIBLE = FALSE;

  const STARFLEET_LOGO = 'starfleet.svg';
  const FAVICON = STARFLEET_LOGO;
  const APPLE_TOUCH_ICON = 'apple-touch-icon.png';
  const STYLESHEET = 'style.css?date=2022-10-12T13:13Z';
  const SCRIPT = 'script.js';

  $files = scandir('.');

  $json = file_get_contents('startrek.jsonld');
  $franchise = json_decode($json, TRUE);

  $json = @file_get_contents($_GET['series'] . '.jsonld');
  $data = json_decode($json, TRUE);

  function html($str) {
    echo htmlSpecialChars($str);
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
    id="<?php html($_GET['series']); ?>"
    lang="<?php html($data['inLanguage']); ?>"
    typeof="<?php html($data['@type']); ?>"
    vocab="<?php html($data['@context']['@vocab'] ?? $data['@context']); ?>"
  >
    <?php head($data['name'] . ' episode list'); ?>
    <body>
      <header>
        <a href="#main" class="skip-link">skip navigation</a>
        <nav>
          <ol>
            <li>
              <a
                title="Star Trek series"
                aria-label="Star Trek series"
                href="<?php html($_SERVER['SCRIPT_NAME']); ?>"
              >
                <?php readfile(STARFLEET_LOGO); ?>
              </a>
            </li>
            <?php foreach ($franchise['hasPart'] as $series): ?>
              <li>
                <a
                  title="<?php html($series['name']); ?>"
                  aria-label="<?php html($series['name']); ?>"
                  <?php if (mb_strtolower($series['identifier']) == $_GET['series']): ?>
                    href="#main" aria-current="page"
                  <?php elseif (in_array(mb_strtolower($series['identifier']) . '.jsonld', $files)): ?>
                    href="<?php html(mb_strtolower($series['identifier'])); ?>"
                  <?php endif; ?>
                >
                  <?php html(mb_strtoupper($series['identifier'])); ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ol>
        </nav>
      </header>
      <main id="main">
        <h1 property="name"><?php html($data['name']); ?></h1>
        <table>
          <?php foreach ($data['containsSeason'] as $season): ?>
            <tbody
              <?php if ($season['@type']): ?>
                property="containsSeason" typeof="<?php html($season['@type']); ?>"
              <?php endif; ?>
            >
              <?php foreach ($season['episode'] as $episode): ?>
                <?php // $translationCount = ($episode['workTranslation'] && $episode['workTranslation']['name']) ? NULL : sizeof($episode['workTranslation']); ?>
                <?php $translation = $episode['workTranslation'][1] ?? $episode['workTranslation'][0] ?? $episode['workTranslation']; ?>
                <tr property="episode" typeof="<?php html($episode['@type']); ?>">
                  <?php if ($episode['episodeNumber']): ?>
                    <?php $episode['@identifier'] = $data['identifier'] . preg_replace('/,\s*/', '-', $episode['episodeNumber']); ?>
                    <th property="episodeNumber">
                      <?php html($episode['episodeNumber']); ?>
                    </th>
                  <?php else: ?>
                    <?php $episode['@identifier'] = uniqid(); ?>
                    <th></th>
                  <?php endif; ?>
                  <td property="name" id="<?php html($episode['@identifier']); ?>"
                    <?php if (is_array($episode['name'])): ?>
                      lang="<?php html($episode['name']['@language'] ?? 'und'); ?>"
                    <?php endif; ?>
                  >
                    <?php html($episode['name']['@value'] ?? $episode['name']); ?>
                  </td>
                  <?php if ($translation): ?>
                    <td
                      property="workTranslation"
                      typeof="<?php html($translation['@type']); ?>"
                      lang="<?php html($translation['inLanguage']); ?>"
                      resource="_:<?php html($episode['@identifier']); ?><?php html($translation['inLanguage']); ?>"
                      id="<?php html($episode['@identifier']); ?><?php html($translation['inLanguage']); ?>"
                    >
                      <?php if ($translation['alternateName']): ?>
                        <s property="name"><?php html($translation['name']); ?></s>
                        <?php if (is_array($translation['alternateName'])): ?>
                          <?php foreach ($translation['alternateName'] as $alternateName): ?>
                            /
                            <span property="alternateName">
                              <?php html($alternateName); ?>
                            </span>
                          <?php endforeach; ?>
                        <?php else: ?>
                          /
                          <span property="alternateName">
                            <?php html($translation['alternateName']); ?>
                          </span>
                        <?php endif; ?>
                      <?php else: ?>
                        <span property="name"
                          <?php if (is_array($translation['name'])): ?>
                            lang="<?php html($translation['name']['@language'] ?? 'und'); ?>"
                          <?php endif; ?>
                        >
                          <?php html($translation['name']['@value'] ?? $translation['name']); ?>
                        </span>
                      <?php endif; ?>
                    </td>
                  <?php else: ?>
                    <td></td>
                  <?php endif; ?>
                  <td>
                    <time property="datePublished"><?php html($episode['datePublished']); ?></time>
                  </td>
                  <?php if (IS_WORKTRANSLATION_DATEPUBLISHED_VISIBLE): ?>
                    <?php if ($translation): ?>
                      <td
                        resource="_:<?php html($episode['@identifier']); ?><?php html($translation['inLanguage']); ?>"
                      >
                        <time property="datePublished">
                          <?php html($translation['datePublished']); ?>
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
                          typeof="<?php html($episode['director']['@type']); ?>"
                          resource="https://bittersmann.de/startrek/persons/<?php html($episode['director']['@id']); ?>"
                        >
                          <span property="name"><?php html($episode['director']['name']); ?></span>
                        </td>
                      <?php else: ?>
                        <td>
                          <ul>
                            <?php foreach ($episode['director'] as $director): ?>
                              <li
                                property="director"
                                typeof="<?php html($director['@type']); ?>"
                                resource="https://bittersmann.de/startrek/persons/<?php html($director['@id']); ?>"
                              >
                                <span property="name"><?php html($director['name']); ?></span>
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
                      <details lang="<?php html($plotLang); ?>">
                        <summary aria-describedby="<?php html($episode['@identifier']); ?><?php html($translation['inLanguage']); ?>">
                          <?php if ($plotLang == 'de'): ?>
                            Handlung
                          <?php else: ?>
                            Plot
                          <?php endif; ?>
                        </summary>
                        <p property="<?php html($plotType); ?>">
                          <?php html($episode[$plotType][$plotLang]); ?>
                        </p>
                        <?php if ($episode['sameAs']): ?>
                          <p>
                            <?php if ($plotLang == 'de'): ?>
                              mehr in der
                            <?php else: ?>
                              see also
                            <?php endif; ?>
                            <a property="sameAs" href="<?php html($episode['sameAs']); ?>">
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
                          <summary aria-describedby="<?php html($episode['@identifier']); ?>">
                            Ups &amp; Downs
                          </summary>
                          <meta
                            property="embedUrl"
                            content="<?php html($episode['review']['video']['embedUrl']); ?>"
                          />
                          <iframe
                            allowfullscreen=""
                            aria-label="Ups &amp; Downs"
                            aria-describedby="<?php html($episode['@identifier']); ?>">
                          </iframe>
                        </details>
                      </td>
                    <?php else: ?>
                      <td>
                        <ul>
                          <?php foreach ($episode['review'] as $review): ?>
                            <li property="review" typeof="Review">
                              <details lang="en" property="video" typeof="VideoObject">
                                <summary aria-describedby="<?php html($episode['@identifier']); ?>">
                                  Ups &amp; Downs
                                </summary>
                                <meta
                                  property="embedUrl"
                                  content="<?php html($review['video']['embedUrl']); ?>"
                                />
                                <iframe
                                  allowfullscreen=""
                                  aria-label="Ups &amp; Downs"
                                  aria-describedby="<?php html($episode['@identifier']); ?>">
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
        Data source:
        <a
          <?php if ($data['sameAs']): ?>
            property="sameAs"
            href="<?php html($data['sameAs']); ?>"
          <?php endif; ?>
        >
          Wikipedia
        </a>
      </footer>
      <script>
        <?php readfile(SCRIPT); ?>
      </script>
    </body>
  </html>
<?php else: ?>
  <html
    id="index"
    lang="<?php html($franchise['inLanguage']); ?>"
    typeof="<?php html($franchise['@type']); ?>"
    vocab="<?php html($franchise['@context']['@vocab'] ?? $franchise['@context']); ?>"
  >
    <?php head($franchise['name'] . ' series'); ?>
    <body>
      <header>
        <div aria-hidden="true">
          <?php readfile(STARFLEET_LOGO); ?>
        </div>
      </header>
      <main>
        <h1 property="name"><?php html($franchise['name'] . ' series'); ?></h1>
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
              <tr property="hasPart" typeof="<?php html($series['@type']); ?>">
                <?php if (IS_LOGO_VISIBLE): ?>
                  <?php if ($series['image']): ?>
                    <td>
                      <a
                        aria-hidden="true"
                        tabindex="-1"
                        <?php if (in_array(mb_strtolower($series['identifier']) . '.jsonld', $files)): ?>
                          href="?series=<?php html(mb_strtolower($series['identifier'])); ?>"
                        <?php endif; ?>
                      >
                        <img property="image" src="<?php html($series['image']); ?>" alt=""/>
                      </a>
                    </td>
                  <?php else: ?>
                    <td></td>
                  <?php endif; ?>
                <?php endif; ?>
                <th property="name">
                  <a
                    <?php if (in_array(mb_strtolower($series['identifier']) . '.jsonld', $files)): ?>
                      href="<?php html(mb_strtolower($series['identifier'])); ?>"
                    <?php endif; ?>
                  >
                    <?php html($series['name']); ?>
                  </a>
                </th>
                <?php if ($series['startDate']): ?>
                  <td>
                    <time property="startDate">
                      <?php html($series['startDate']); ?>
                    </time>
                  </td>
                <?php else: ?>
                  <td></td>
                <?php endif; ?>
                <?php if ($series['endDate']): ?>
                  <td>
                    <time property="endDate">
                      <?php html($series['endDate']); ?>
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
    </body>
  </html>
<?php endif; ?>
