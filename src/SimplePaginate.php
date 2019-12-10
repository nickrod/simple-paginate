<?php

//

declare(strict_types=1);

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

  public function __construct(array $options = [])
  {
    if (isset($options['total_records']))
    {
      $this->setTotalRecords($options['total_records']);
    }

    //

    if (isset($options['per_page']))
    {
      $this->setPerPage($options['per_page']);
    }

    //

    if (isset($options['canonical_url']))
    {
      $this->setCanonicalUrl($options['canonical_url']);
    }

    //

    if (isset($options['page_links_offset']))
    {
      $this->setPageLinksOffset($options['page_links_offset']);
    }

    // set current page

    if (isset($options['current_page']))
    {
      $this->setCurrentPage($options['current_page']);
    }
    else
    {
      $this->calculate();
    }

    //

    if (isset($options['url_params']))
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

    //

    if (isset($options['a_class']))
    {
      $this->setAClass($options['a_class']);
    }
  }

  // get offset page links

  public function getOffset(): int
  {
    return $this->offset;
  }

  // get offset for db limit

  public function getDbOffset(): int
  {
    return $this->db_offset;
  }

  // get paging meta tags

  public function getMetaTags(): string
  {
    $tags = '';

    //

    if ($this->current_page == 1)
    {
      $tags = "\t<link rel='next' href='" . $this->canonical_url . "?page=" . $this->next_page . "' />\n";
    }
    elseif ($this->current_page == $this->total_pages)
    {
      $tags = "\t<link rel='prev' href='" . $this->canonical_url . "?page=" . $this->previous_page . "' />\n";
    }
    else
    {
      $tags = "\t<link rel='prev' href='" . $this->canonical_url . "?page=" . $this->previous_page . "' />\n";
      $tags .= "\t<link rel='next' href='" . $this->canonical_url . "?page=" . $this->next_page . "' />\n";
    }

    //

    return $tags;
  }

  // get the paging links

  public function getLinks(): string
  {
    $links = '';

    // only show paging links if there is more than one page

    if ($this->total_pages > 1)
    {
      $links .= "<ul class='{$this->ul_class}'>\n";

      // show prev/first links

      if ($this->current_page > 1)
      {
        $links .= "\t<li class='{$this->li_class}'><a class='{$this->a_class}' href='" . $this->canonical_url . "?page=" . $this->previous_page . $this->url_params . "'>Previous</a></li>\n";
        $links .= "\t<li class='{$this->li_class}'><a class='{$this->a_class}' href='" . $this->canonical_url . "?page=1" . $this->url_params . "'>First</a></li>\n";
      }

      // page links

      for ($i = $this->start; $i <= $this->end; $i++)
      {
        if ($i == $this->current_page)
        {
          $links .= "\t<li class='{$this->li_class} active'><b>$i</b></li>\n";
        }
        else
        {
          $links .= "\t<li class='{$this->li_class}'><a class='{$this->a_class}' href='" . $this->canonical_url . "?page=" . $i . $this->url_params . "'>" . $i . "</a></li>\n";
        }
      }

      // show last/next links

      if ($this->current_page < $this->total_pages)
      {
        $links .= "\t<li class='{$this->li_class}'><a class='{$this->a_class}' href='" . $this->canonical_url . "?page=" . $this->total_pages . $this->url_params . "'>Last</a></li>\n";
        $links .= "\t<li class='{$this->li_class}'><a class='{$this->a_class}' href='" . $this->canonical_url . "?page=" . $this->next_page . $this->url_params . "'>Next</a></li>\n";
      }

      //

      $links .= "</ul>\n";
    }

    //

    return $links;
  }

  // setters

  public function setTotalRecords(int $total_records): void
  {
    if ($total_records < 1)
    {
      throw new \RangeException("'total_records' must be greater than zero");
    }
    else
    {
      $this->total_records = $total_records;
      $this->setTotalPages();
    }
  }

  //

  public function setPerPage(int $per_page): void
  {
    if ($per_page < 1)
    {
      throw new \RangeException("'per_page' must be greater than zero");
    }
    else
    {
      $this->per_page = $per_page;
      $this->setTotalPages();
    }
  }

  //

  public function setCanonicalUrl(string $canonical_url): void
  {
    if (!filter_var($canonical_url, FILTER_VALIDATE_URL))
    {
      throw new \InvalidArgumentException("'canonical_url' must be a valid url");
    }
    else
    {
      $this->canonical_url = filter_var(strtok($canonical_url, '?'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
  }

  //

  public function setPageLinksOffset(int $page_links_offset): void
  {
    if ($page_links_offset < 1 || $page_links_offset > 50)
    {
      throw new \RangeException("'page_links_offset' must be greater than zero and less than 50");
    }
    else
    {
      $this->page_links_offset = $page_links_offset;
    }
  }

  //

  public function setUrlParams(array $url_params): void
  {
    unset($url_params['page']);
    $this->url_params = filter_var('&' . http_build_query($url_params), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  }

  //

  public function setUlClass(string $ul_class): void
  {
    $this->ul_class = filter_var($ul_class, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  }

  //

  public function setLiClass(string $li_class): void
  {
    $this->li_class = filter_var($li_class, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  }

  //

  public function setAClass(string $a_class): void
  {
    $this->a_class = filter_var($a_class, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  }

  //

  public function setCurrentPage(int $current_page): void
  {
    if ($current_page < 1)
    {
      throw new \RangeException("'current_page' must be greater than zero");
    }
    elseif ($current_page > $this->total_pages)
    {
      throw new \RangeException("'current_page' cannot be greater than 'total_pages'");
    }
    else
    {
      $this->current_page = $current_page;
    }

    //

    $this->calculate();
  }

  //

  private function calculate(): void
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

  private function setTotalPages(): void
  {
    $this->total_pages = ceil($this->total_records / $this->per_page);
  }

  //

  private function setPreviousPage(): void
  {
    $this->previous_page = $this->current_page - 1;
  }

  //

  private function setNextPage(): void
  {
    $this->next_page = $this->current_page + 1;
  }

  //

  private function setDbOffset(): void
  {
    $this->db_offset = ($this->current_page - 1) * $this->per_page;
  }

  //

  private function setOffset(): void
  {
    $this->offset = ($this->current_page - 1) * $this->per_page + 1;
  }

  //

  private function setStart(): void
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

  private function setEnd(): void
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
