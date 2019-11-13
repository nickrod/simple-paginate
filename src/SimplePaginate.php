<?php

//

namespace nickrod\SimplePaginate;

//

class SimplePaginate
{
  // total records

  private $total_records = 100;

  // number of records per page

  private $per_page = 20;

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

  // total number of pages needed

  private $total_pages;

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
    if (isset($options['total_records']) && is_int($options['total_records']))
    {
      $this->total_records = $options['total_records'];
    }

    //

    if (isset($options['per_page']) && is_int($options['per_page']))
    {
      $this->per_page = $options['per_page'];
    }

    //

    if (isset($options['canonical_url']) && is_string($options['canonical_url']))
    {
      $this->canonical_url = filter_var($options['canonical_url'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    //

    if (isset($options['page_links_offset']) && is_int($options['page_links_offset']))
    {
      $this->page_links_offset = $options['page_links_offset'];
    }

    //

    if (isset($options['url_params']) && is_string($options['url_params']))
    {
      $this->url_params = filter_var('&' . $options['url_params'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    //

    if (isset($options['ul_class']) && is_string($options['ul_class']))
    {
      $this->ul_class = filter_var($options['ul_class'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    //

    if (isset($options['li_class']) && is_string($options['li_class']))
    {
      $this->li_class = filter_var($options['li_class'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    //

    if (isset($options['a_class']) && is_string($options['a_class']))
    {
      $this->a_class = filter_var($options['a_class'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    // calculate total pages

    $this->total_pages = ceil($this->total_records / $this->per_page);

    // set current page, do some basic validation

    if (isset($options['current_page']) && is_int($options['current_page']))
    {
      if ($options['current_page'] < 1)
      {
        $this->current_page = 1;
      }
      elseif ($options['current_page'] > $this->total_pages && $this->total_pages > 0)
      {
        $this->current_page = $this->total_pages;
      }
      else
      {
        $this->current_page = $options['current_page'];
      }
    }

    // set previous and next pages

    $this->previous_page = $this->current_page - 1;
    $this->next_page = $this->current_page + 1;

    // set db and page offset 

    $this->db_offset = ($this->current_page - 1) * $this->per_page;
    $this->offset = ($this->current_page - 1) * $this->per_page + 1;

    // start page links

    if (($this->current_page - $this->page_links_offset) > 0)
    {
      $this->start = $this->current_page - $this->page_links_offset;
    }
    else
    {
      $this->start = 1;
    }

    // end page links

    if (($this->current_page + $this->page_links_offset) < $this->total_pages)
    {
      $this->end = $this->current_page + $this->page_links_offset;
    }
    else
    {
      $this->end = $this->total_pages;
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
}
