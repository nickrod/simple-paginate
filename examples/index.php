<?php

//

use simplepaginate\SimplePaginate;

//

require __DIR__ . '/../vendor/autoload.php';

//

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//

$page = new SimplePaginate([
  'total_records' => 200,
  'per_page' => 10,
  'current_page' => 1,
  'canonical_url' => 'https://www.mysite.com/',
  'page_links_offset' => 2,
  'url_params' => $_GET,
  'ul_class' => '',
  'li_class' => '',
  'a_class' => ''
]);

?>

<html>
  <head>
    <?=$page->getMetaTags();?>
  </head>
  <body>
    <?=$page->getLinks();?>
  </body>
</html>

<?php

$page->setCurrentPage(2);

?>

<html>
  <head>
    <?=$page->getMetaTags();?>
  </head>
  <body>
    <?=$page->getLinks();?>
  </body>
</html>

<?php

$page->setCurrentPage(3);

?>

<html>
  <head>
    <?=$page->getMetaTags();?>
  </head>
  <body>
    <?=$page->getLinks();?>
  </body>
</html>
