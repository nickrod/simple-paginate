<?php

//

use simplepaginate\SimplePaginate;

//

require __DIR__ . '/../vendor/autoload.php';

//

$page = new SimplePaginate(['current_page' => 2]);
$page->setCurrentPage(4);
echo $page->getLinks();
