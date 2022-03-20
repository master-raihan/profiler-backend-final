<?php
namespace App\Helpers;

class CsvParser
{
    protected $path;

    public function load(string $path)
    {
        if(!file_exists($path))
        {
            throw new Exception("File does not exists. Please re-check the file path.");
        }else{
            $this->path = $path;
        }
    }

    public function read(bool $isHeaderAvailable = true)
    {
        $parsedValues = array();
        $row = 0;

        if ($isHeaderAvailable)
        {
            if (($handle = fopen($this->path, "r")) !== false)
            {
                $headers = fgetcsv($handle, 1000, ",");
                while (($data = fgetcsv($handle, 1000, ",")) !== false)
                {
                    $parsedValues[$row] = array_combine($headers, $data);
                    $row++;
                }
                fclose($handle);
            }
        }else{
            if (($handle = fopen($this->path, "r")) !== false)
            {
                while (($data = fgetcsv($handle, 1000, ",")) !== false)
                {
                    $parsedValues[$row] = $data;
                    $row++;
                }
                fclose($handle);
            }
        }
        return $parsedValues;
    }

    public function headings()
    {
        $headers = array();
        if (($handle = fopen($this->path, "r")) !== false)
        {
            $headers = fgetcsv($handle, 1000, ",");
            fclose($handle);
        }

        return $headers;
    }

}
