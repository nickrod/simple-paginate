<?php

//

use simplepaginate\SimplePaginate;

//

require __DIR__ . '/../vendor/autoload.php';

//

$page = new SimplePaginate(['canonical_url' => 'https://www.google.com?blah=sdf&bad=er']);
$page->setCurrentPage(4);
echo $page->getLinks();
