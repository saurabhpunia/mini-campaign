<?php

namespace App\Traits;

trait CsvTrait
{

    /**
     * function name: getDataFromCsvInFormat
     * function: used to fetch and format the csv data
     * @param $objFile - request file
     * @param $arrFormat - required format
     * @return required format array
     */
    public function getDataFromCsvInFormat($objFile, $arrFormat){
        return array_map(function($row) use ($arrFormat){
            $arrExploded = explode(',', $row);
            return [
                $arrFormat[0] => trim($arrExploded[0]),
                $arrFormat[1] => trim($arrExploded[1])
            ];
        }, file($objFile));
    }
}
