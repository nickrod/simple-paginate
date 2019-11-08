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

  private $page_links_offset;

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
    $this->total_records = $total_records;
    $this->per_page = $per_page;
    $this->canonical_url = $canonical_url;
    $this->page_links_offset = $page_links_offset;
    $this->url_params = $url_params;
    $this->total_pages = ceil($this->total_records / $this->per_page);

    // set current page, do some basic validation

    if ($current_page < 1)
    {
      $this->current_page = 1;
    }
    elseif ($current_page > $this->total_pages && $this->total_pages > 0)
    {
      $this->current_page = $this->total_pages;
    }
    else
    {
      $this->current_page = $current_page;
    }

    // set prev and next pages

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

  // get canonical links

  public function getLinks()
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

  // get the paging html

  public function getTags($ul_class = "", $li_class = "", $a_class = "")
  {
    $tags = '';

    // only show paging links if there is more than one page

    if ($this->total_pages > 1)
    {
      $ul_class = (!empty($ul_class)) ? ("class='" . $ul_class . "'") : "";
      $li_class = (!empty($li_class)) ? ("class='" . $li_class . "'") : "";
      $a_class = (!empty($a_class)) ? ("class='" . $a_class . "'") : "";

      //

      $tags .= "<ul $ul_class>";

      // show prev/first links

      if ($this->current_page > 1)
      {
        $tags .= "<li $li_class><a $a_class href='" . $this->canonical_url . "?page=" . $this->previous_page . $this->url_params . "'>Previous</a></li>";
        $tags .= "<li $li_class><a $a_class href='" . $this->canonical_url . "?page=1" . $this->url_params . "'>First</a></li>";
      }

      // page links

      for ($i = $this->start; $i <= $this->end; $i++)
      {
        if ($i == $this->current_page)
        {
          $li_class = (!empty($li_class)) ? ("class='" . $li_class . " active'") : "class='active'";
          $tags .= "<li $li_class><b>$i</b></li>";
        }
        else
        {
          $tags .= "<li $li_class><a $a_class href='" . $this->canonical_url . "?page=" . $i . $this->url_params . "'>" . $i . "</a></li>";
        }
      }

      // show last/next links

      if ($this->current_page < $this->total_pages)
      {
        $tags .= "<li $li_class><a $a_class href='" . $this->canonical_url . "?page=" . $this->total_pages . $this->url_params . "'>Last</a></li>";
        $tags .= "<li $li_class><a $a_class href='" . $this->canonical_url . "?page=" . $this->next_page . $this->url_params . "'>Next</a></li>";
      }

      //

      $tags .= "</ul>";
    }

    //

    return $tags;
  }
}
