<?php
  const PREFERRED_LANG = 'de';

  const STARFLEET_LOGO = 'series/starfleet.svg';
  const FAVICON = STARFLEET_LOGO;
  const APPLE_TOUCH_ICON = 'series/apple-touch-icon.png';
  const STYLESHEET = 'series/style.css?date=2022-10-12T13:13Z';

  $files = scandir('series');

  $json = file_get_contents('series/series.jsonld');
  $franchise = json_decode($json, TRUE);

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
<html
  id="index"
  lang="<?= htmlSpecialChars($franchise['inLanguage']) ?>"
  typeof="<?= htmlSpecialChars($franchise['@type']) ?>"
  vocab="<?= htmlSpecialChars($franchise['@context']['@vocab'] ?? $franchise['@context']) ?>"
>
  <?php head($franchise['name']); ?>
  <body>
    <style>
      main > ul > li {
        display: flex;
        margin: 4em 0;
      }

      main > ul > li > a {
        min-width: 6em;
      }
 
      #series-list {
        display: flex;
        flex-wrap: wrap;
        gap: 1em 2em;
        padding-inline: 0;
        max-width: 60em;
      }
    </style>
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
        <li><a href="timelines">Timelines</a></li>
      </ul>
    </main>
  </body>
</html>
