<?php

//

//namespace nickrod\simplepaginate;

//

class SimplePaginate
{
  // total records

  private $total_records;

  // number of records per page

  private $per_page;

  // current page

  private $current_page;

  // canonical url

  private $canonical_url;

  // page links offset

  private $page_links_offset = 3;

  // url params

  private $url_params;

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

  public $start;

  // end page link

  public $end;

  // constructor

  //public function __construct($total_records, $per_page, $current_page, $canonical_url, $total_page_links = 6, $url_params = '')
  public function __construct($options = [])
  {
    if (!empty($options['total_records']) && is_int($options['total_records']))
    {
      $this->total_records = $options['total_records'];
    }

    //

    if (!empty($options['per_page']) && is_int($options['per_page']))
    {
      $this->per_page = $options['per_page'];
    }

    //

    if (!empty($options['canonical_url']) && is_string($options['canonical_url']) && filter_var($options['canonical_url'], FILTER_VALIDATE_URL))
    {
      $this->canonical_url = addslashes($options['canonical_url']);
    }

    //

    if (!empty($options['page_links_offset']) && is_int($options['page_links_offset']))
    {
      $this->page_links_offset = $options['page_links_offset'];
    }

    //

    if (!empty($options['url_params']) && is_string($options['url_params']))
    {
      $this->url_params = filter_var($options['url_params'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    // calculate total pages

    $this->total_pages = ceil($this->total_records / $this->per_page);

    // set current page, do some basic validation

    if (!empty($options['current_page']) && is_int($options['current_page']))
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

  //public function getLinks($ul_class = "", $li_class = "", $a_class = "")
  public function getLinks($options = [])
  {
    $links = '';

    // only show paging links if there is more than one page

    if ($this->total_pages > 1)
    {
      $ul_class = (!empty($ul_class)) ? ("class='" . $ul_class . "'") : "";
      $li_class = (!empty($li_class)) ? ("class='" . $li_class . "'") : "";
      $a_class = (!empty($a_class)) ? ("class='" . $a_class . "'") : "";

      //

      $links .= "<ul $ul_class>";

      // show prev/first links

      if ($this->current_page > 1)
      {
        $links .= "<li $li_class><a $a_class href='" . $this->canonical_url . "?page=" . $this->previous_page . $this->url_params . "'>Previous</a></li>";
        $links .= "<li $li_class><a $a_class href='" . $this->canonical_url . "?page=1" . $this->url_params . "'>First</a></li>";
      }

      // page links

      for ($i = $this->start; $i <= $this->end; $i++)
      {
        if ($i == $this->current_page)
        {
          $li_class = (!empty($li_class)) ? ("class='" . $li_class . " active'") : "class='active'";
          $links .= "<li $li_class><b>$i</b></li>";
        }
        else
        {
          $links .= "<li $li_class><a $a_class href='" . $this->canonical_url . "?page=" . $i . $this->url_params . "'>" . $i . "</a></li>";
        }
      }

      // show last/next links

      if ($this->current_page < $this->total_pages)
      {
        $links .= "<li $li_class><a $a_class href='" . $this->canonical_url . "?page=" . $this->total_pages . $this->url_params . "'>Last</a></li>";
        $links .= "<li $li_class><a $a_class href='" . $this->canonical_url . "?page=" . $this->next_page . $this->url_params . "'>Next</a></li>";
      }

      //

      $links .= "</ul>";
    }

    //

    return $links;
  }
}
