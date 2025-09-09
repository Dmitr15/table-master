<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Storage;

class ProcessFileController extends Controller
{
    public function viewFile(string $filePath):array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $filePath);
        }
     
        $spreadsheet = IOFactory::load($filePath);
        
        $sheet = $spreadsheet->getActiveSheet();
        
        $data = $sheet->toArray();

        return $data;
    }
    
}
