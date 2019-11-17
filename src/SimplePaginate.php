<?php

//

namespace simplepaginate;

//

class SimplePaginate
{
  // total records

  private $total_records = 100;

  // number of records per page

  private $per_page = 10;

  // current page

  private $current_page = 1;

  // canonical url

  private $canonical_url;

  // page links offset

  private $page_links_offset = 3;

  // url params

  private $url_params = '';

  // previous page

  private $previous_page;

  // next page

  private $next_page;

  // total number of pages

  private $total_pages = 10;

  // db offset

  private $db_offset;

  // offset

  private $offset;

  // start page link

  private $start;

  // end page link

  private $end;

  // ul class

  private $ul_class = 'pagination';

  // li class

  private $li_class = 'page-item';

  // a class

  private $a_class = 'page-link';

  // constructor

  public function __construct($options = [])
  {
    if (!empty($options['total_records']))
    {
      $this->setTotalRecords($options['total_records']);
    }

    //

    if (!empty($options['per_page']))
    {
      $this->setPerPage($options['per_page']);
    }

    //

    if (!empty($options['canonical_url']))
    {
      $this->setCanonicalUrl($options['canonical_url']);
    }

    //

    if (!empty($options['page_links_offset']))
    {
      $this->setPageLinksOffset($options['page_links_offset']);
    }

    // set current page

    if (!empty($options['current_page']))
    {
      $this->setCurrentPage($options['current_page']);
    }
    else
    {
      $this->calculate();
    }

    //

    if (!empty($options['url_params']))
    {
      $this->setUrlParams($options['url_params']);
    }

    //

    if (isset($options['ul_class']))
    {
      $this->setUlClass($options['ul_class']);
    }

    //

    if (isset($options['li_class']))
    {
      $this->setLiClass($options['li_class']);
    }

    // set the a class 

    if (isset($options['a_class']))
    {
      $this->setAClass($options['a_class']);
    }
  }

  // get offset page links

  public function getOffset()
  {
    return $this->offset;
  }

  // get offset for db limit

  public function getDbOffset()
  {
    return $this->db_offset;
  }

  // get paging meta tags

  public function getMetaTags()
  {
    $tags = '';

    //

    if ($this->current_page == 1)
    {
      $tags = "<link rel='next' href='" . $this->canonical_url . "?page=" . $this->next_page . "' />\n";
    }
    elseif ($this->current_page == $this->total_pages)
    {
      $tags = "<link rel='prev' href='" . $this->canonical_url . "?page=" . $this->previous_page . "' />\n";
    }
    else
    {
      $tags = "<link rel='prev' href='" . $this->canonical_url . "?page=" . $this->previous_page . "' />\n";
      $tags .= "<link rel='next' href='" . $this->canonical_url . "?page=" . $this->next_page . "' />\n";
    }

    //

    return $tags;
  }

  // get the paging links

  public function getLinks()
  {
    $links = '';

    // only show paging links if there is more than one page

    if ($this->total_pages > 1)
    {
      $links .= "<ul class='{$this->ul_class}'>";

      // show prev/first links

      if ($this->current_page > 1)
      {
        $links .= "<li class='{$this->li_class}'><a class='{$this->a_class}' href='" . $this->canonical_url . "?page=" . $this->previous_page . $this->url_params . "'>Previous</a></li>";
        $links .= "<li class='{$this->li_class}'><a class='{$this->a_class}' href='" . $this->canonical_url . "?page=1" . $this->url_params . "'>First</a></li>";
      }

      // page links

      for ($i = $this->start; $i <= $this->end; $i++)
      {
        if ($i == $this->current_page)
        {
          $links .= "<li class='{$this->li_class} active'><b>$i</b></li>";
        }
        else
        {
          $links .= "<li class='{$this->li_class}'><a class='{$this->a_class}' href='" . $this->canonical_url . "?page=" . $i . $this->url_params . "'>" . $i . "</a></li>";
        }
      }

      // show last/next links

      if ($this->current_page < $this->total_pages)
      {
        $links .= "<li class='{$this->li_class}'><a class='{$this->a_class}' href='" . $this->canonical_url . "?page=" . $this->total_pages . $this->url_params . "'>Last</a></li>";
        $links .= "<li class='{$this->li_class}'><a class='{$this->a_class}' href='" . $this->canonical_url . "?page=" . $this->next_page . $this->url_params . "'>Next</a></li>";
      }

      //

      $links .= "</ul>";
    }

    //

    return $links;
  }

  // setters

  public function setTotalRecords($total_records)
  {
    if (!is_int($total_records))
    {
      throw new InvalidArgumentException("'total_records' must be an integer");
    }
    elseif ($total_records < 1)
    {
      throw new RangeException("'total_records' must be greater than zero");
    }
    else
    {
      $this->total_records = $total_records;
      $this->setTotalPages();
    }
  }

  //

  public function setPerPage($per_page)
  {
    if (!is_int($per_page))
    {
      throw new InvalidArgumentException("'per_page' must be an integer");
    }
    elseif ($per_page < 1)
    {
      throw new RangeException("'per_page' must be greater than zero");
    }
    else
    {
      $this->per_page = $per_page;
      $this->setTotalPages();
    }
  }

  //

  public function setCanonicalUrl($canonical_url)
  {
    if (!is_string($canonical_url))
    {
      throw new InvalidArgumentException("'canonical_url' must be a string");
    }
    elseif (!filter_var($canonical_url, FILTER_VALIDATE_URL))
    {
      throw new InvalidArgumentException("'canonical_url' must be a valid url");
    }
    else
    {
      $this->canonical_url = filter_var(strtok($canonical_url, '?'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
  }

  //

  public function setPageLinksOffset($page_links_offset)
  {
    if (!is_int($page_links_offset))
    {
      throw new InvalidArgumentException("'page_links_offset' must be an integer");
    }
    elseif ($page_links_offset < 1 || $page_links_offset > 50)
    {
      throw new RangeException("'page_links_offset' must be greater than zero and less than 50");
    }
    else
    {
      $this->page_links_offset = $page_links_offset;
    }
  }

  //

  public function setUrlParams($url_params)
  {
    if (!is_array($url_params))
    {
      throw new InvalidArgumentException("'url_params' must be an array");
    }
    else
    {
      unset($url_params['page']);
      $this->url_params = filter_var('&' . http_build_query($url_params), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
  }

  //

  public function setUlClass($ul_class)
  {
    if (!is_string($ul_class))
    {
      throw new InvalidArgumentException("'ul_class' must be a string");
    }
    else
    {
      $this->ul_class = filter_var($ul_class, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
  }

  //

  public function setLiClass($li_class)
  {
    if (!is_string($li_class))
    {
      throw new InvalidArgumentException("'li_class' must be a string");
    }
    else
    {
      $this->li_class = filter_var($li_class, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
  }

  //

  public function setAClass($a_class)
  {
    if (!is_string($a_class))
    {
      throw new InvalidArgumentException("'a_class' must be a string");
    }
    else
    {
      $this->a_class = filter_var($a_class, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
  }

  //

  public function setCurrentPage($current_page)
  {
    if (!is_int($current_page))
    {
      throw new InvalidArgumentException("'current_page' must be an integer");
    }
    elseif ($current_page < 1)
    {
      throw new RangeException("'current_page' must be greater than zero");
    }
    elseif ($current_page > $this->total_pages)
    {
      throw new RangeException("'current_page' cannot be greater than 'total_pages'");
    }
    else
    {
      $this->current_page = $current_page;
    }

    //

    $this->calculate();
  }

  //

  private function calculate()
  {
    // set previous and next pages

    $this->setPreviousPage();
    $this->setNextPage();

    // set db and page offset 

    $this->setDbOffset();
    $this->setOffset();

    // set start and end page links

    $this->setStart();
    $this->setEnd();
  }

  //

  private function setTotalPages()
  {
    $this->total_pages = ceil($this->total_records / $this->per_page);
  }

  //

  private function setPreviousPage()
  {
    $this->previous_page = $this->current_page - 1;
  }

  //

  private function setNextPage()
  {
    $this->next_page = $this->current_page + 1;
  }

  //

  private function setDbOffset()
  {
    $this->db_offset = ($this->current_page - 1) * $this->per_page;
  }

  //

  private function setOffset()
  {
    $this->offset = ($this->current_page - 1) * $this->per_page + 1;
  }

  //

  private function setStart()
  {
    if (($this->current_page - $this->page_links_offset) > 0)
    {
      $this->start = $this->current_page - $this->page_links_offset;
    }
    else
    {
      $this->start = 1;
    }
  }

  //

  private function setEnd()
  {
    if (($this->current_page + $this->page_links_offset) < $this->total_pages)
    {
      $this->end = $this->current_page + $this->page_links_offset;
    }
    else
    {
      $this->end = $this->total_pages;
    }
  }
}
