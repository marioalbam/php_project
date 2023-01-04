<?php

declare(strict_types=1);

namespace App\Controllers;

class FileController
{
    private string $file_name;
    private string $file_path;
    private $raw_data;
    private array $formatted_data;
    private array $first_row;

    public function __construct(string $file_name)
    {

        $this->file_name = $file_name;

        $this->file_path = $this->createFilePath();
    }

    public static function make($file_name): static
    {
        return new static($file_name);
    }

    public function createFilePath(): string
    {
        $root = dirname(__DIR__) . DIRECTORY_SEPARATOR;

        return $root . '../storage' . DIRECTORY_SEPARATOR . $this->file_name;
    }

    public function extract(): self
    {
        try {
            if (!file_exists($this->file_path)) {
                throw new \Exception('File not found');
            }

            if (is_dir($this->file_path)) {
                throw new \Exception('File not found');
            }

            $this->raw_data = fopen($this->file_path, 'r');

            if (! $this->raw_data) {
                throw new \Exception('File open failed');
            }

            $this->first_row = explode(';', (fgetcsv($this->raw_data))[0]);

            while (($data_row = fgetcsv($this->raw_data, separator:';')) !== false) {
                $this->formatted_data[] = array_map(function ($data_element) {
                    return str_replace([' '], '', $data_element);
                }, $data_row);
            }

            fclose($this->raw_data);

            return $this;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function write(array $updated_file)
    {
        $file = fopen($this->file_path, 'w');

        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        foreach ($updated_file as $line) {
            fputcsv($file, $line, separator: ';');
        }

        fclose($file);
    }

    public function getFormattedData(): array
    {
        return $this->formatted_data;
    }

    public function getFirstRow(): array
    {
        return $this->first_row;
    }
}
