<?php

//

declare(strict_types=1);

//

namespace simpleapi;

//

class SimpleApi
{
  // input

  private $input = [];

  // file var

  private $file_var = '';

  // file name

  private $file_name = '';

  // directory

  private $directory = '/tmp';

  // path

  private $path = '';

  // method

  private $method = '';

  // body

  private $body = [];

  // request

  private $param = [];

  // uri

  private $uri = [];

  // header

  private $header = [];

  // file

  private $file = [];

  // constructor

  public function __construct(array $options = [])
  {
    if (isset($options['input']) === true)
    {
      $this->setInput($options['input']);
    }

    //

    if (isset($options['file_var']) === true)
    {
      $this->setFileVar($options['file_var']);
    }

    //

    if (isset($options['file_name']) === true)
    {
      $this->setFileName($options['file_name']);
    }

    //

    if (isset($options['directory']) === true)
    {
      $this->setDirectory($options['directory']);
    }

    //

    $this->setPath();
    $this->setMethod();
    $this->setBody();
    $this->setParam();
    $this->setUri();
    $this->setHeader();
    $this->setFile();
  }

  // getters

  public function getInput(): array
  {
    return $this->input;
  }

  //

  public function getFileVar(): string
  {
    return $this->file_var;
  }

  //

  public function getFileName(): string
  {
    return $this->file_name;
  }

  //

  public function getDirectory(): string
  {
    return $this->directory;
  }

  //

  public function getPath(): string
  {
    return $this->path;
  }

  //

  public function getMethod(): string
  {
    return $this->method;
  }

  //

  public function getBody(): array
  {
    return $this->body;
  }

  //

  public function getParam(): array
  {
    return $this->param;
  }

  //

  public function getUri(): array
  {
    return $this->uri;
  }

  //

  public function getHeader(): array
  {
    return $this->header;
  }

  //

  public function getFile(): array
  {
    return $this->file;
  }

  //

  public function uploadFile(string &$postdata = ''): void
  {
    if ($this->path !== '')
    {
      $response = [];

      //

      if ($this->method === 'PUT')
      {
        $putdata = fopen('php://input', 'r');
        $fp = fopen($this->path, 'w');

        //

        if ($putdata !== false && $fp !== false)
        {
          while ($data = fread($putdata, 1024))
          {
            fwrite($fp, $data);
          }

          //

          fclose($fp);
          fclose($putdata);
        }
        else
        {
          throw new \RuntimeException('File cannot be opened: ' . $this->path);
        }
      }
      elseif ($this->method === 'POST')
      {
        if ($this->file !== [])
        {
          if ($this->file['error'] === UPLOAD_ERR_OK)
          {
            if (move_uploaded_file($this->file['tmp_name'], $this->path) === false)
            {
              throw new \RuntimeException('File couldn\'t be moved: ' . $this->path);
            }
          }
          else
          {
            throw new \RuntimeException('File couldn\'t be uploaded: ' . $this->file['error']);
          }
        }
        else
        {
          $fp = fopen($this->path, 'w');

          //

          if ($fp !== false)
          {
            fwrite($fp, $postdata);
            fclose($fp);
          }
          else
          {
            throw new \RuntimeException('File cannot be opened: ' . $this->path);
          }
        }
      }
      else
      {
        throw new \RuntimeException('Method isn\'t supported: ' . $this->method);
      }
    }
    else
    {
      throw new \RuntimeException('Path hasn\'t been set');
    }
  }

  //

  public function deleteFile(): void
  {
    if (is_writable($this->path) === true)
    {
      unlink($this->path);
    }
  }

  //

  public function exportCsv(array &$data = []): void
  {
    if ($this->file_name !== '')
    {
      if ($this->method === 'GET')
      {
        header('HTTP/1.1 200');
        header('Cache-Control: max-age=0, no-cache, must-revalidate');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $this->file_name);

        //

        $df = fopen('php://output', 'w');

        //

        if ($df !== false)
        {
          fputcsv($df, array_keys(($data !== []) ? $data[0] : $data));

          //

          foreach ($data as $row)
          {
            fputcsv($df, $row);
          }

          //

          fclose($df);
        }
        else
        {
          throw new \RuntimeException('File cannot be opened: php://output');
        }
      }
      else
      {
        throw new \RuntimeException('Method isn\'t supported: ' . $this->method);
      }
    }
    else
    {
      throw new \RuntimeException('File name hasn\'t been set');
    }
  }

  // setters

  private function setInput(array $input): void
  {
    $this->input = $input;
  }

  //

  private function setFileVar(string $file_var): void
  {
    $this->file_var = $file_var;
  }

  //

  public function setFileName(string $file_name): void
  {
    if (preg_match('/^([-\.\w]+)$/u', $file_name) > 0)
    {
      $this->file_name = $file_name;
      $this->setPath();
    }
    else
    {
      throw new \InvalidArgumentException('File name is invalid: ' . $file_name);
    }
  }

  //

  public function setDirectory(string $directory): void
  {
    if (is_dir($directory) === true)
    {
      $this->directory = $directory;
      $this->setPath();
    }
    else
    {
      throw new \InvalidArgumentException('Directory is invalid: ' . $directory);
    }
  }

  //

  private function setPath(): void
  {
    if ($this->file_name !== '' && $this->directory !== '')
    {
      $this->path = rtrim($this->directory, '/') . '/' . $this->file_name;
    }
  }

  //

  private function setMethod(): void
  {
    if (isset($_SERVER['REQUEST_METHOD']) === true && $_SERVER['REQUEST_METHOD'] !== '')
    {
      $this->method = $_SERVER['REQUEST_METHOD'];
    }
    else
    {
      throw new \RuntimeException('Method couldn\'t be set');
    }
  }

  //

  private function setBody(): void
  {
    if ($this->method === 'GET' || $this->method === 'POST')
    {
      $body = filter_input_array(INPUT_POST, $this->input, false);

      //

      if ($body !== null && $body !== false)
      {
        $this->body = $body;
      }
    }
  }

  //

  private function setParam(): void
  {
    if ($this->method === 'GET' || $this->method === 'POST')
    {
      $param = filter_input_array(INPUT_GET, $this->input, false);

      //

      if ($param !== null && $param !== false)
      {
        $this->param = $param;
      }
    }
  }

  //

  private function setUri(): void
  {
    if ($this->method !== 'PUT' && isset($_SERVER['PATH_INFO']) === true && $_SERVER['PATH_INFO'] !== '')
    {
      $this->uri = explode('/', trim($_SERVER['PATH_INFO'], '\x20\x2f'));
    }
  }

  //

  private function setHeader(): void
  {
    $header = apache_request_headers();

    //

    if ($header !== null && $header !== false)
    {
      $this->header = $header;
    }
  }

  //

  private function setFile(): void
  {
    if ($this->method === 'POST' && $this->file_var !== '' && isset($_FILES[$this->file_var]) === true)
    {
      $this->file = $_FILES[$this->file_var];
    }
  }

  //

  public static function getRandomString(int $length = 10): string
  {
    return bin2hex(random_bytes($length));
  }

  //

  public static function printResponse(?array &$data = [], int $status = 200, string $message = 'OK'): void
  {
    header('Content-Type: application/json');
    header('HTTP/1.1 ' . $status);

    //

    $response['status'] = $status;
    $response['status_message'] = $message;
    $response['data'] = $data;

    //

    echo json_encode($response);
  }
}
