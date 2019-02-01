<?php
/**
 * Файл класса интерфейса кэширования
 */
namespace RAAS\CMS;

/**
 * Класс интерфейса кэширования
 */
class CacheInterface extends AbstractInterface
{
    /**
     * Данные, полученные от интерфейса блока
     * @var mixed
     */
    protected $data;

    /**
     * Конструктор класса
     * @param Block|null $block Блок, для которого применяется интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param array $post Поля $_POST параметров
     * @param array $cookie Поля $_COOKIE параметров
     * @param array $session Поля $_SESSION параметров
     * @param array $server Поля $_SERVER параметров
     * @param array $files Поля $_FILES параметров
     * @param mixed $data Данные, полученные от интерфейса блока
     */
    public function __construct(
        Block $block = null,
        Page $page = null,
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $session = [],
        array $server = [],
        array $files = [],
        $data = null
    ) {
        parent::__construct(
            $block,
            $page,
            $get,
            $post,
            $cookie,
            $session,
            $server,
            $files
        );
        $this->data = $data;
    }


    public function process()
    {
        $cacheCode = null;
        switch ($this->block->cache_type) {
            case Block::CACHE_HTML:
                $cacheCode = $this->getHtmlCacheCode(ob_get_contents());
                break;
            case Block::CACHE_DATA:
                $cacheCode = $this->getDataCacheCode($this->data);
                break;
        }
        if ($cacheCode) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'raas');
            $filename = $this->block->getCacheFile(
                isset($this->server['REQUEST_URI']) ?
                $this->server['REQUEST_URI'] :
                ''
            );
            file_put_contents($tmpFile, $cacheCode);
            rename($tmpFile, $filename);
        }
        return $this->data;
    }


    /**
     * Получает код для кэширования HTML-данных
     * @param string $data Данные (текст) для кэширования
     */
    public function getHtmlCacheCode($data)
    {
        // 2015-11-23, AVS: заменил, т.к. в кэше меню <?php
        // так же заменяется и глючит
        $cacheCode = preg_replace(
            '/\\<\\?xml (.*?)\\?\\>/umi',
            '<?php echo \'<\' . \'?xml $1?\' . ">\\n"?' . '>',
            $data
        );
        return $cacheCode;
    }


    /**
     * Получает код для кэширования произвольных данных
     * @param mixed $data Данные для кэширования
     */
    public function getDataCacheCode($data)
    {
        $cacheId = 'RAASCACHE' . date('YmdHis') . md5(rand());
        $cacheCode = '<' . "?php\nreturn unserialize(<<" . "<'"
                   . $cacheId . "'\n" . serialize($data) . "\n"
                   . $cacheId . "\n);\n";
        return $cacheCode;
    }
}
