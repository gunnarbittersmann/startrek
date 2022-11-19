<?php
  const IS_LOGO_VISIBLE = FALSE;
  const IS_DIRECTOR_VISIBLE = !FALSE;
  const IS_WORKTRANSLATION_DATEPUBLISHED_VISIBLE = !FALSE;
  
  const STARFLEET_LOGO = 'starfleet.svg';
  const FAVICON = STARFLEET_LOGO;
  const APPLE_TOUCH_ICON = 'apple-touch-icon.png';
  const STYLESHEET = 'style.css?date=2022-02-13T20:57Z';

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
      <main id="main">
        <table>
          <caption property="name"><?php html($data['name']); ?></caption>
          <?php foreach ($data['containsSeason'] as $season): ?>
            <tbody
              <?php if ($season['@type']): ?>
                property="containsSeason" typeof="<?php html($season['@type']); ?>"
              <?php endif; ?>
            >
              <?php foreach ($season['episode'] as $episode): ?>
                <?php
                  $translationCount = ($episode['workTranslation'] && $episode['workTranslation']['name']) ? NULL : sizeof($episode['workTranslation']);
                  $translation = $episode['workTranslation'][0] ?? $episode['workTranslation'];
                ?>
                <tr property="episode" typeof="<?php html($episode['@type']); ?>">
                  <?php if ($episode['episodeNumber']): ?>
                    <?php $episode['@identifier'] = preg_replace('/\W+/', '', $episode['episodeNumber']); ?>
                    <th
                      property="episodeNumber"
                      <?php if ($translationCount): ?>rowspan="<?php html($translationCount); ?>"<?php endif;?>
                    >
                      <?php html($episode['episodeNumber']); ?>
                    </th>
                  <?php else: ?>
                    <?php $episode['@identifier'] = uniqid(); ?>
                    <th <?php if ($translationCount): ?>rowspan="<?php html($translationCount); ?>"<?php endif;?>></th>
                  <?php endif; ?>
                  <td
                    property="name"
                    id="<?php html($episode['@identifier']); ?>"
                    <?php if ($translationCount): ?>rowspan="<?php html($translationCount); ?>"<?php endif;?>
                  >
                    <?php html($episode['name']); ?>
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
                        /
                        <span property="alternateName">
                          <?php html($translation['alternateName']); ?>
                        </span>
                      <?php else: ?>                    
                        <span property="name"><?php html($translation['name']); ?></span>
                      <?php endif; ?>                    
                    </td>
                  <?php else: ?>
                    <td></td>
                  <?php endif; ?>
                  <td <?php if ($translationCount): ?>rowspan="<?php html($translationCount); ?>"<?php endif;?>>
                    <time property="datePublished"><?php html($episode['datePublished']); ?></time>
                  </td>
                  <?php if (IS_WORKTRANSLATION_DATEPUBLISHED_VISIBLE): ?>
                    <?php if ($translation): ?>
                      <td
                        resource="_:<?php html($episode['episodeNumber']); ?><?php html($translation['inLanguage']); ?>"
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
                          <?php if ($translationCount): ?>rowspan="<?php html($translationCount); ?>"<?php endif;?>
                        >
                          <span property="name"><?php html($episode['director']['name']); ?></span>
                        </td>
                      <?php else: ?>
                        <td <?php if ($translationCount): ?>rowspan="<?php html($translationCount); ?>"<?php endif;?>>
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
                          </li>
                        </td>
                      <?php endif; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                  <?php if ($episode['description']): ?>
                    <td <?php if ($translationCount): ?>rowspan="<?php html($translationCount); ?>"<?php endif;?>>
                      <details lang="de">
                        <summary aria-describedby="<?php html($episode['@identifier']); ?><?php html($translation['inLanguage']); ?>">
                          Handlung
                        </summary>
                        <p property="description">
                          <?php html($episode['description']); ?>
                        </p>
                        <?php if ($episode['sameAs']): ?>
                          <p>
                            mehr in der 
                            <a property="sameAs" href="<?php html($episode['sameAs']); ?>">
                              Wikipedia
                            </a>
                          </p>
                        <?php endif; ?>
                      </details>
                    </td>
                  <?php elseif ($episode['abstract']): ?>
                    <td <?php if ($translationCount): ?>rowspan="<?php html($translationCount); ?>"<?php endif;?>>
                      <details>
                        <summary aria-describedby="<?php html($episode['@identifier']); ?>">
                          Plot
                        </summary>
                        <p property="abstract">
                          <?php html($episode['abstract']); ?>
                        </p>
                        <?php if ($episode['sameAs']): ?>
                          <p>
                            see also 
                            <a property="sameAs" href="<?php html($episode['sameAs']); ?>">
                              Wikipedia
                            </a>
                          </p>
                        <?php endif; ?>
                      </details>
                    </td>
                  <?php else: ?>
                    <td <?php if ($translationCount): ?>rowspan="<?php html($translationCount); ?>"<?php endif;?>></td>
                  <?php endif; ?>
                </tr>
                <?php if ($translationCount): ?>
                  <tr>
                    <td
                      property="workTranslation"
                      typeof="<?php html($episode['workTranslation'][1]['@type']); ?>"
                      lang="<?php html($episode['workTranslation'][1]['inLanguage']); ?>"
                      resource="_:<?php html($episode['@identifier']); ?><?php html($translation['inLanguage']); ?>1"
                      id="<?php html($episode['@identifier']); ?><?php html($translation['inLanguage']); ?>1"
                    >
                      <?php if ($episode['workTranslation'][1]['alternateName']): ?>
                        <s property="name"><?php html($episode['workTranslation'][1]['name']); ?></s>
                        /
                        <span property="alternateName">
                          <?php html($episode['workTranslation'][1]['alternateName']); ?>
                        </span>
                      <?php else: ?>                    
                        <span property="name"><?php html($episode['workTranslation'][1]['name']); ?></span>
                      <?php endif; ?>                    
                    </td>
                    <?php if (IS_WORKTRANSLATION_DATEPUBLISHED_VISIBLE): ?>
                      <td
                        resource="_:<?php html($episode['episodeNumber']); ?><?php html($translation['inLanguage']); ?>1"
                      >
                        <time property="datePublished">
                          <?php html($episode['workTranslation'][1]['datePublished']); ?>
                        </time>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endif; ?>
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
      <div aria-hidden="true">
        <?php readfile(STARFLEET_LOGO); ?>
      </div>
      <table>
        <caption property="name"><?php html($franchise['name']); ?></caption>
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
    </body>
  </html>
<?php endif; ?>
