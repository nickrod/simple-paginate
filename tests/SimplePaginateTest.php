<?php

//

namespace simplepaginate\tests;

//

use simplepaginate\SimplePaginate;

//

require __DIR__ . '/../vendor/autoload.php';

//

$page = new SimplePaginate(['a_class' => '']);
echo $page->getLinks();

//

class SimplePaginateTest
{
}
