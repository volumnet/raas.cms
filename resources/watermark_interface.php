<?php
$processImage = function($filename, $watermarkFilename, $ratio = 0.5, $quality = 90) 
{
    list($sourceImgWidth, $sourceImgHeight, $sourceImgType) = @getimagesize($filename);
    list($waterMarkImgWidth, $waterMarkImgHeight, $waterMarkImgType) = @getimagesize($watermarkFilename);
    $sourceInputFunction = \SOME\Graphics::image_type_to_input_function($sourceImgType);
    $sourceOutputFunction = \SOME\Graphics::image_type_to_output_function($sourceImgType);
    $waterMarkInputFunction = \SOME\Graphics::image_type_to_input_function($waterMarkImgType);
    $sourceImg = $sourceInputFunction($filename);
    $waterMarkImg = $waterMarkInputFunction($watermarkFilename);

    $rate = $waterMarkImgWidth / $waterMarkImgHeight; // Коэфициент соотношения сторон
    $newWidth  = $waterMarkImgWidth; // Ширина участка на исходном изображении, куда будет наложен вотермарк
    $newHeight = $waterMarkImgHeight; // Высота участка на исходном изображении, куда будет наложен вотермарк
    if (($sourceImgWidth * $ratio) < $waterMarkImgWidth) {
        $newWidth = $sourceImgWidth * $ratio; // Ширина вотермарки
        $newHeight = $newWidth / $rate; // Высота вотермарки
    }
    $xSource = ($sourceImgWidth - $newWidth) / 2; // Отступ по оси Х
    $ySource = ($sourceImgHeight - $newHeight) / 2; // Отступ по оси Y

    imagecopyresampled($sourceImg, $waterMarkImg, $xSource, $ySource, 0, 0, $newWidth, $newHeight, $waterMarkImgWidth, $waterMarkImgHeight);
    if ($sourceOutputFunction == 'imagejpeg') {
        $sourceOutputFunction($sourceImg, $filename, $quality);
    } else {
        $sourceOutputFunction($sourceImg, $filename);
    }
    return true;
};

$watermarkImage = 'images/watermark.png';
if (($t->datatype == 'image') && is_file($watermarkImage)) {
    $files = array();
    if ($postProcess) {
        if ($addedAttachments && is_array($addedAttachments)) {
            foreach ($addedAttachments as $row) {
                if ($row->image) {
                    $files[] = $row->file;
                }
            }
        }
    } else {
        $files = (array)$_FILES[$Field->name]['tmp_name'];
        $files = array_filter($files, 'is_file');
        $files = array_values($files);
    }
    foreach ($files as $file) {
        $processImage($file, $watermarkImage);
    }
}