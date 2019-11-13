<?php

//

namespace simplepaginate\tests;

//

use simplepaginate\SimplePaginate;

//

require __DIR__ . '/../vendor/autoload.php';

//

$page = new SimplePaginate();
echo $page->getLinks();

//

class SimplePaginateTest
{
}
