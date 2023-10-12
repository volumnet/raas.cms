<?php
/**
 * Интерфейс водяных знаков
 *
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
use RAAS\Attachment;

class WatermarkInterface extends AbstractInterface
{
    /**
     * Поле для обработки
     * @var Field
     */
    public $field = null;

    /**
     * Пост-обработка
     * @var bool
     */
    public $postProcess = false;

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
     * Конструктор класса
     * @param Field $field Обрабатываемое поле
     * @param string $watermark Путь файла с водяными знаками
     * @param bool $postProcess Пост-обработка
     * @param array $get Поля $_GET параметров
     * @param array $post Поля $_POST параметров
     * @param array $cookie Поля $_COOKIE параметров
     * @param array $session Поля $_SESSION параметров
     * @param array $server Поля $_SERVER параметров
     * @param array $files Поля $_FILES параметров
     */
    public function __construct(
        Field $field,
        string $watermark,
        bool $postProcess = false,
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $session = [],
        array $server = [],
        array $files = []
    ) {
        $this->field = $field;
        $this->watermark = $watermark;
        $this->postProcess = $postProcess;
        parent::__construct(null, null, $get, $post, $cookie, $session, $server, $files);
    }


    /**
     * Обработка интерфейса
     * @param Attachment[] $attachmentsToProcess Вложения для обработки (только для пост-обработки)
     */
    public function process(array $attachmentsToProcess = [])
    {
        $watermarkImage = $this->watermark;
        if (!is_file($watermarkImage) && is_file(Application::i()->baseDir . $this->watermark)) {
            $watermarkImage = Application::i()->baseDir . $this->watermark;
        }

        if (($this->field->datatype == 'image') && is_file($watermarkImage)) {
            if ($this->postProcess) {
                $files = array_map(function ($att) {
                    return $att->file;
                }, array_values(array_filter($attachmentsToProcess, function ($att) {
                    return $att->image;
                })));
            } else {
                $files = array_values(array_filter((array)$this->files[$this->field->urn]['tmp_name'], 'is_file'));
            }
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
