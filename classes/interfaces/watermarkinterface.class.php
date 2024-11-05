<?php
/**
 * Стандартный интерфейс водяных знаков
 *
 * <pre><code>
 * Используемые типы
 * [Данные об изображении] => [
 *     'filename' => string Файл изображения,
 *     'width' => int Ширина в пикселях,
 *     'height' => int Высота в пикселях,
 *     'type' => int IMAGETYPE_-константа типа изображения
 *     'image' => GdImage|resource Изображение
 * ]
 * </code></pre>
 */
namespace RAAS\CMS;

use Exception;
use GdImage;
use SOME\Graphics;
use RAAS\Application;
use RAAS\Attachment;

/**
 * Стандартный интерфейс водяных знаков
 */
class WatermarkInterface extends FilesProcessorInterface
{
    /**
     * Путь к файлу водяных знаков
     * @var string
     */
    public $watermark = '/files/cms/common/image/design/watermark.png';

    /**
     * Соотношение ширины водяного знака к ширине
     * @var float
     */
    public $ratio = 1;

    /**
     * Качество сохраняемого изображения (0-100, только для JPEG)
     * @var int
     */
    public $quality = 90;

    /**
     * Обработка интерфейса
     * @param string[] $files Файлы для обработки
     */
    public function process(array $files = [])
    {
        $watermarkImage = $this->watermark;
        if (!is_file($watermarkImage) && is_file(Application::i()->baseDir . $this->watermark)) {
            $watermarkImage = Application::i()->baseDir . $this->watermark;
        }
        $files = array_values(array_filter($files, 'is_file'));

        if (is_file($watermarkImage)) {
            foreach ($files as $file) {
                try {
                    $this->processImage($file, $watermarkImage, $this->ratio, $this->quality);
                } catch (Exception $e) {
                }
            }
        }
    }


    /**
     * Обрабатывает изображение
     * @param string $filename Путь исходного изображения для обработки
     * @param string $watermark Путь файла водяных знаков
     * @param float $ratio Относительный размер водяного знака на исходном изображении
     * @param int $quality Качество сохраняемого изображения
     * @return bool true
     */
    public function processImage(string $filename, string $watermark, float $ratio = 1, int $quality = 90): bool
    {
        $sourceImgData = $this->getImageData($filename);
        $watermarkImgData = $this->getImageData($watermark);
        $sourceOutputFunction = Graphics::image_type_to_output_function($sourceImgData['type']);

        $img = $this->applyWatermark($sourceImgData, $watermarkImgData, $ratio);

        if ($sourceOutputFunction == 'imagejpeg') {
            $sourceOutputFunction($img, $filename, $quality);
        } else {
            $sourceOutputFunction($img, $filename);
        }
        return true;
    }


    /**
     * Получает информацию о изображении
     * @param string $filename Файл изображения
     * @return array <pre><code>Данные об изображении</code></pre>
     * @throws Exception В случае, если файл не найден, либо не является изображением, либо неизвестного формата,
     *     либо не удалось открыть
     */
    public function getImageData(string $filename): array
    {
        if (!is_file($filename)) {
            throw new Exception('Файл ' . $filename . ' не найден', 1);
        }
        $imagesize = getimagesize($filename);
        if (!($imagesize[0] ?? false) || !($imagesize[1] ?? false) || !($imagesize[2] ?? false)) {
            throw new Exception('Файл ' . $filename . ' не является изображением');
        }
        $inputFunction = Graphics::image_type_to_input_function($imagesize[2]);
        if (!$inputFunction) {
            throw new Exception('Файл ' . $filename . ' неизвестного формата');
        }
        $image = $inputFunction($filename);
        if (!$inputFunction) {
            throw new Exception('Не удалось открыть файл ' . $filename . ' как изображение');
        }

        $result = [
            'filename' => $filename,
            'width' => $imagesize[0],
            'height' => $imagesize[1],
            'type' => $imagesize[2],
            'image' => $image,
        ];
        return $result;
    }


    /**
     * Применяет водяной знак к источнику
     * @param array $sourceImgData <pre><code>Данные об изображении</code></pre> Данные входного файла
     * @param array $watermarkImgData <pre><code>Данные об изображении</code></pre> Данные файла водяного знака
     * @param float $ratio Относительный размер водяного знака на исходном изображении
     * @return mixed Изображение с наложенным водяным знаком
     */
    public function applyWatermark(array $sourceImgData, array $watermarkImgData, float $ratio = 1)
    {
        $img = $sourceImgData['image'];

        $rate = $watermarkImgData['width'] / $watermarkImgData['height']; // Коэфициент соотношения сторон
        $newWidth  = $watermarkImgData['width']; // Ширина участка на исходном изображении, куда будет наложен водяной знак
        $newHeight = $watermarkImgData['height']; // Высота участка на исходном изображении, куда будет наложен водяной знак

        if (($sourceImgData['width'] * $ratio) < $newWidth) {
            $newWidth = $sourceImgData['width'] * $ratio; // Ширина водяного знака
            $newHeight = $newWidth / $rate; // Высота водяного знака
        }
        if (($sourceImgData['height'] * $ratio) < $newHeight) {
            $newHeight = $sourceImgData['height'] * $ratio; // Ширина водяного знака
            $newWidth = $newHeight * $rate; // Высота водяного знака
        }
        $xSource = ($sourceImgData['width'] - $newWidth) / 2; // Отступ по оси Х
        $ySource = ($sourceImgData['height'] - $newHeight) / 2; // Отступ по оси Y

        imagecopyresampled(
            $img,
            $watermarkImgData['image'],
            $xSource,
            $ySource,
            0,
            0,
            $newWidth,
            $newHeight,
            $watermarkImgData['width'],
            $watermarkImgData['height']
        );
        return $img;
    }
}
