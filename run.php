<?php

require_once 'vendor/autoload.php';

use Intervention\Image\ImageManagerStatic as Image;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;


$image = $argv[1];

if (!$image) {
    echo "please input a image file \n";
}

$input = Image::make($image);

$imageName  = explode('/', $image);

$imageName = array_pop($imageName);

$outputName = "output/{$imageName}.xlsx";

$height = $input->getHeight();
$width  = $input->getWidth();

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();
$sheet->getDefaultColumnDimension()->setWidth(3);

$map   = [];
$start = time();
show('----start----');
for ($i = 0; $i < $width; $i++) {
    for ($j = 0; $j < $height; $j++) {
        $color = $input->pickColor($i, $j, 'hex');
        $color = substr($color, 1);
        $cell  = $sheet->getCellByColumnAndRow($i, $j);
        $cell->getStyle()->getFill()->applyFromArray(
            [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => $color
                ],
            ]
        );

    }
    $per = number_format(100 * ($i + 1) / $width, 2);
    show($per . " %");
}

$writer = new Xlsx($spreadsheet);
$writer->save($outputName);

show('cost ' . (time() - $start) . ' s');
show("surprise >>>> {$outputName} <<<<");
show('----end----');


function show($msg)
{
    $date = date('Y-m-d H:i:s');
    echo "[{$date}] $msg\n";
}
