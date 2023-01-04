<?php

declare(strict_types=1);

namespace App\Controllers;

class CsvController
{
    private array $selected_variable_array = [];
    private array $column_Id_Array = [];
    private array $reformatted_data = [];
    private array $updated_first_row = [];

    public function __construct(private FileController $file)
    {
    }

    public static function make(FileController $file): static
    {
        return new static($file);
    }

    public function process(array $columnIdArray): array
    {
        $this->columnIdtoInt($columnIdArray);

        sort($this->column_Id_Array);

        foreach ($this->column_Id_Array as $column_Id) {
            $this->setData($column_Id);
        }
        array_unshift($this->reformatted_data, $this->updated_first_row);

        foreach ($this->selected_variable_array as $key => $selected_variables) {
            $this->createDummyVariableArray($this->column_Id_Array[$key], $selected_variables);
        }

        return $this->reformatted_data;
    }

    private function columnIdtoInt(array $column_Id_Array)
    {
        try {
            if (! array_filter($column_Id_Array)) {
                throw new \DomainException("Column-id cannot be empty.");
            }
            foreach ($column_Id_Array as $key => $column_Id) {
                $columnIdArray[$key] = strtoupper($column_Id);

                if (!preg_match('~[A-Z]+~', $column_Id_Array[$key])) {
                    throw new \DomainException('String' . $column_Id_Array[$key]  . 'is not a valid column-id.');
                }
            }
        } catch (\DomainException $e) {
            $e->getMessage();
        }
        foreach ($column_Id_Array as $column_Id) {
            $sum = 0;

            for ($x = 0; $x < strlen($column_Id); $x++) {
                $sum = $sum * 26;
                $sum += (ord($column_Id[$x]) - 65 + 1);
            }

            $this->column_Id_Array[] = $sum - 1;
        }
    }

    private function setData(int $column_Id)
    {
        $array_data = empty($this->reformatted_data) ? $this->file->getFormattedData() : $this->reformatted_data;
        $first_row = empty($this->updated_first_row) ? $this ->file->getFirstRow() : $this->updated_first_row;
        $selected_variables = [];

        foreach ($array_data as $key => $data_row) {
            if (array_key_exists($column_Id, $data_row)) {
                $variable_array = explode(',', trim(strtolower($data_row[$column_Id])));
                $array_data[$key][$column_Id] = $variable_array;
            } else {
                throw new \Exception('column(s) does(do) not exists in the file');
            }
            foreach ($variable_array as $variable) {
                if (!in_array($variable, $selected_variables, true)) {
                    array_push($selected_variables, $variable);
                    array_push($first_row, $variable);
                }
            }
        }
        $this->selected_variable_array[] = $selected_variables;
        $this->reformatted_data = $array_data;
        $this->updated_first_row = $first_row;
    }

    private function createDummyVariableArray(int $column_Id, array $selected_variables)
    {
        foreach ($this->reformatted_data as $key => $data_row) {
            if ($key !== 0) {
                foreach ($selected_variables as $variable) {
                    if (in_array($variable, $data_row[$column_Id], true)) {
                        array_push($this->reformatted_data[$key], 1);
                    } else {
                        array_push($this->reformatted_data[$key], 0);
                    }
                }
                $this->reformatted_data[$key][$column_Id] = implode(
                    ',',
                    $this->reformatted_data[$key][$column_Id]
                );
            }
        }
    }
}
