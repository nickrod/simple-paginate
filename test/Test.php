<?php

//

namespace nickrod\simplepaginate\test;

//

$page = new SimplePaginate(['a_class' => 'thats-it', 'url_params' => 'blah=34&sdf=3w', 'page_links_offset' => 2]);

//

echo $page->getMetaTags() . "\n";
