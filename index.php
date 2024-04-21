<?php
  const PREFERRED_LANG = 'de';

  const STARFLEET_LOGO = 'starfleet.svg';
  const FAVICON = STARFLEET_LOGO;
  const APPLE_TOUCH_ICON = 'apple-touch-icon.png';
  const STYLESHEET = 'style.css?date=2022-10-12T13:13Z';
  const SCRIPT = 'script.js';

  $files = scandir('series');

  $json = file_get_contents('series/series.jsonld');
  $franchise = json_decode($json, TRUE);

  $json = file_get_contents('presentations/presentations.jsonld');
  $presentations = json_decode($json, TRUE);
?>
<!DOCTYPE html>
<html
  id="index"
  lang="<?= htmlSpecialChars($franchise['inLanguage']) ?>"
  typeof="<?= htmlSpecialChars($franchise['@type']) ?>"
  vocab="<?= htmlSpecialChars($franchise['@context']['@vocab'] ?? $franchise['@context']) ?>"
>
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $franchise['name'] ?></title>
    <link rel="icon" href="<?= htmlSpecialChars(FAVICON) ?>"/>
    <link rel="mask-icon" href="<?= htmlSpecialChars(FAVICON) ?>"/>
    <link rel="apple-touch-icon" href="<?= htmlSpecialChars(APPLE_TOUCH_ICON) ?>"/>
    <link rel="stylesheet" href="<?= htmlSpecialChars(STYLESHEET) ?>"/>
    <style>
      main > ul > li {
        display: flex;
        margin: 4em 0;
      }

      main > ul > li > a {
        min-width: 6em;
      }
 
      main > ul > li > :is(ul, ol) {
        display: flex;
        flex-wrap: wrap;
        gap: 1em 2em;
        padding-inline: 0;
        max-width: 60em;
      }
      
      #presentation-list img {
        width: auto;
        height: 8em;
      }
    </style>
  </head>
  <body>
    <header>
      <div aria-hidden="true">
        <?php readfile(STARFLEET_LOGO); ?>
      </div>
    </header>
    <main>
      <h1 property="name"><?= htmlSpecialChars($franchise['name']) ?></h1>
      <ul>
        <li>
          <a href="series">Series</a>
          <ol id="series-list">
            <?php foreach ($franchise['hasPart'] as $series): ?>
              <li property="hasPart" typeof="<?= htmlSpecialChars($series['@type']) ?>">
                <a
                  <?php if (in_array(mb_strtolower($series['identifier']) . '.jsonld', $files)): ?>
                    href="series/<?= htmlSpecialChars(mb_strtolower($series['identifier'])) ?>"
                  <?php endif; ?>
                >
                  <?php if ($series['image']): ?>
                    <img
                      property="image"
                      src="<?= htmlSpecialChars($series['image']) ?>"
                      alt="<?= htmlSpecialChars($series['name']) ?>"
                    />
                  <?php else: ?>
                    <?= htmlSpecialChars($series['name']) ?>
                  <?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ol>
        </li>
        <li><a href="movies">Movies</a></li>
        <li><a href="books">Books</a></li>
        <li><a href="timelines">Timelines</a></li>
        <li>
          <a>Music</a>
          <ul>
            <li>
              <a href="music/subspace-rhapsody">Subspace Rhapsody</a>
            </li>
          </ul>
        </li>
        <li>
          <a>Artwork</a>
          <ul>
            <li>
              <a href="artwork/discovery-opening-titles">Discovery seasons opening titles</a>
            </li>
            <li>
              <a href="artwork/dsc-universes-opening-titles">Discovery universes opening titles</a>
            </li>
            <li>
              <a href="artwork/snw-real-flat-opening-titles">Strange New Worlds real/flat opening titles</a>
            </li>
            <li>
              <a href="artwork/ld-films">Lower Decks and film posters</a>
            </li>
          </ul>
        </li>
        <li>
          <a>Presentations</a>
          <ol id="presentation-list">
            <?php foreach ($presentations as $presentation): ?>
              <li>
                <a href="<?= htmlSpecialChars($presentation['sameAs']) ?>">
                  <img
                    src="<?= htmlSpecialChars($presentation['image']) ?>"
                    alt="<?= htmlSpecialChars($presentation['name']) ?>"
                  />
                </a>
              </li>
            <?php endforeach; ?>
          </ol>
        </li>
      </ul>
    </main>
    <script>
      <?php readfile(SCRIPT); ?>
    </script>
  </body>
</html>
