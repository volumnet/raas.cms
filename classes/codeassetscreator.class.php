<?php
/**
 * Генератор файлов верстки для сниппетов
 *
 * Предустановленные типы:
 * <pre><code>
 * <Метаданные CSS-класса> => [
 *     'name' => Название CSS-класса,
 *     'modifiers' => array<
 *         string[] Название CSS-класса модификатора, без учета корневого (напр. _new) => string (то же самое)
 *     >
 *     'elements' => array<
 *         string[] Название CSS-класса элемента, без учета корневого (напр. __item) => [
 *             'name' => Название CSS-класса элемента, без учета корневого (напр. __item),
 *             'modifiers' => Название CSS-класса модификатора, без учета элемента (напр. _new) => string (то же самое),
 *         ]
 *     >,
 *     'hasVue' =>? string Название тега, если к классу подключен Vue-компонент,
 *     'vueSlot' =>? string Наименование слота, если подключен Vue-компонент
 * ]
 * </code></pre>
 */
declare(strict_types=1);

namespace RAAS\CMS;

use RAAS\Application;

/**
 * Генератор файлов верстки для сниппетов
 */
class CodeAssetsCreator
{
    /**
     * Корневой каталог блоков (относительно корня сайта)
     */
    const ROOT_ASSETS_DIR = '/dev/src/_blocks';

    /**
     * Сниппет для обработки
     * @var Snippet
     */
    protected $snippet;

    /**
     * Список CSS-классов
     * @var array <pre><code>array<string[] Название CSS-класса => <Метаданные CSS-класса>></code></pre>
     */
    protected $cssClasses = [];

    /**
     * Конструктор класса
     * @param Snippet $snippet Сниппет
     */
    public function __construct(Snippet $snippet)
    {
        $this->snippet = $snippet;
        $this->parseCode();
    }


    public function __get($var)
    {
        switch ($var) {
            case 'rootURN':
                return str_replace('_', '-', $this->snippet->urn);
                break;
            case 'code':
                return (string)$this->snippet->description;
                break;
        }
    }


    /**
     * Разбирает код на классы
     */
    protected function parseCode()
    {
        $tagsRx = '/\\<[\\w\\-]+(.*?[^\\?])?\\>/umis';
        if (preg_match_all($tagsRx, $this->code, $regs)) {
            foreach ($regs[0] as $tag) {
                $this->parseTag($tag);
            }
        }
        // var_dump($this->cssClasses['catalog-article']['elements']['__available']); exit;
    }


    /**
     * Разбирает тег
     * @param string $tag Код тега в виде '<...>'
     */
    protected function parseTag(string $tag)
    {
        preg_match('/\\<([\\w\\-]+).*?\\>/umis', $tag, $regs);
        $tagName = $regs[1];

        if (preg_match('/ class="(.*?)"/umis', $tag, $regs)) {
            $classNames = explode(' ', trim($regs[1]));
            $classNames = array_map('trim', $classNames);
            $classNames = array_map(function ($x) {
                if (preg_match('/^' . preg_quote($this->rootURN) . '([\\w\\-]*)/', $x, $regs)) {
                    return $regs[0];
                }
                return null;
            }, $classNames);
            $classNames = array_filter($classNames);
            foreach ($classNames as $className) {
                $temp = explode('_', $className, 2);
                $baseClass = $temp[0];
                if (!isset($this->cssClasses[$baseClass])) {
                    $this->cssClasses[$baseClass] = [
                        'name' => $baseClass,
                    ];
                }
                if (isset($temp[1]) && $temp[1]) {
                    if (preg_match('/^' . preg_quote($baseClass) . '__([\\w\\-]+)/umis', $className, $regs)) {
                        // Элемент или модификатор элемента
                        $temp = explode('_', $regs[1], 2);
                        $baseElement = '__' . $temp[0];
                        if (!isset($this->cssClasses[$baseClass]['elements'][$baseElement])) {
                            $this->cssClasses[$baseClass]['elements'][$baseElement] = [
                                'name' => $baseElement,
                            ];
                        }
                        if (isset($temp[1]) && $temp[1]) {
                            $modifier = '_' . $temp[1];
                            $this->cssClasses[$baseClass]['elements'][$baseElement]['modifiers'][$modifier] = $modifier;
                        }
                    } elseif (preg_match('/^' . preg_quote($baseClass) . '_([\\w\\-]+)/umis', $className, $regs)) {
                        // Модификатор основного класса
                        $className = '_' . $regs[1];
                        $this->cssClasses[$baseClass]['modifiers'][$className] = $className;
                    }
                }
            }
        }
        if (preg_match('/ data-vue-role="(.*?)"/umis', $tag, $regs)) {
            $vueRole = trim($regs[1]);
            if (mb_substr($vueRole, 0, mb_strlen($this->rootURN)) == $this->rootURN) {
                if (!isset($this->cssClasses[$vueRole])) {
                    $this->cssClasses[$vueRole] = [
                        'name' => $vueRole,
                    ];
                }
                $this->cssClasses[$vueRole]['hasVue'] = $tagName;
                if (preg_match('/ (data-)?v-slot="(.*?)"/umis', $tag, $regs)) {
                    $this->cssClasses[$vueRole]['vueSlot'] = trim($regs[2]);
                }
            }
        }
    }


    /**
     * Генерирует текст Vue-файла по метаданным
     * @param array $data <pre><code><Метаданные CSS-класса></code></pre> Метаданные CSS-класса
     * @return string
     */
    public function generateVue(array $data): string
    {
        $text = '<style lang="scss">' . "\n"
              . '// .' . $data['name'] . ' {' . "\n"
              . '//     $self: &;' . "\n"
              . '//     ' . "\n"
              . '//     ' . "\n"
              . '//     ' . "\n";
        foreach (($data['modifiers'] ?? []) as $modifier) {
            $text .= '//     &' . $modifier . ' {' . "\n"
                  .  '//         ' . "\n"
                  .  '//     }' . "\n";
        }
        foreach (($data['elements'] ?? []) as $element) {
            $text .= '//     &' . $element['name'] . ' {' . "\n"
                  .  '//         ' . "\n";
            foreach (($element['modifiers'] ?? []) as $elementModifier) {
                $text .= '//         &' . $elementModifier . ' {' . "\n"
                      .  '//             ' . "\n"
                      .  '//         }' . "\n";
            }
            $text .= '//     }' . "\n";
        }
        $text .= '// }' . "\n"
              .  '</style>';
        if ($data['hasVue'] ?? null) {
            $text .= "\n\n\n"
                  .  '<template>' . "\n"
                  .  '  <' . $data['hasVue'] . ' class="' . $data['name'] . '">' . "\n";
            if ($data['vueSlot'] ?? null) {
                $text .= '    <slot v-bind="self"></slot>' . "\n";
            } else {
                $text .= '    ' . "\n";
            }
            $text .= '  </' . $data['hasVue'] . '>' . "\n"
                  .  '</template>' . "\n\n\n"
                  .  '<script>' . "\n"
                  .  'export default {' . "\n"
                  .  '    props: {' . "\n"
                  .  '        ' . "\n"
                  .  '    },' . "\n"
                  .  '    data() {' . "\n"
                  .  '        return {' . "\n"
                  .  '            ' . "\n"
                  .  '        };' . "\n"
                  .  '    },' . "\n"
                  .  '    mounted() {' . "\n"
                  .  '        ' . "\n"
                  .  '    },' . "\n"
                  .  '    methods: {' . "\n"
                  .  '        ' . "\n"
                  .  '    },' . "\n"
                  .  '    computed: {' . "\n";
            if ($data['vueSlot'] ?? null) {
                $text .= '        self() {' . "\n"
                      .  '            return {...this};' . "\n"
                      .  '        },' . "\n";
            } else {
                $text .= '        ' . "\n";
            }
            $text .= '    },' . "\n"
                  .  '};' . "\n"
                  .  '</script>';
        }
        return $text;
    }


    /**
     * Генерирует текст index.js (либо article.js / list.js)
     * @param bool|null $isArticle null - для создания index.js,
     *     true - для создания article.js,
     *     false - для создания list.js
     * @return string
     */
    public function generateRootJS(bool $isArticle = null): string
    {
        $cssClasses = $this->cssClasses;
        if ($isArticle !== null) {
            $cssClasses = array_filter($cssClasses, function ($x) use ($isArticle) {
                return $isArticle xor !preg_match('/^' . preg_quote($this->rootURN) . '-article/umis', $x['name']);
            });
        }
        $text = '';
        foreach ($cssClasses as $classData) {
            if ($classData['hasVue'] ?? false) {
                $text .= "import " . $this->toPascalCase($classData['name']) . " from './" . $classData['name'] . ".vue';\n";
            } else {
                $text .= "import './" . $classData['name'] . ".vue';\n";
            }
        }
        $text .= "\n"
              .  'export default {' . "\n";
        foreach ($cssClasses as $classData) {
            if ($classData['hasVue'] ?? false) {
                $text .= "    '" . $classData['name'] . "': " . $this->toPascalCase($classData['name']) . ",\n";
            }
        }
        $text .= '}';
        return $text;
    }


    /**
     * Генерирует файлы верстки
     */
    public function createAssets()
    {
        $files = [];
        foreach ($this->cssClasses as $cssClass => $cssClassData) {
            $files[$cssClass . '.vue'] = $this->generateVue($cssClassData);
        }
        if (!$files) {
            return;
        }
        if (isset($this->cssClasses[$this->rootURN . '-article'])) {
            $files['list.js'] = $this->generateRootJS(false);
            $files['article.js'] = $this->generateRootJS(true);
        } else {
            $files['index.js'] = $this->generateRootJS();
        }
        // var_dump($files); exit;
        $blockDir = Application::i()->baseDir . static::ROOT_ASSETS_DIR . '/' . $this->rootURN;
        if (!file_exists($blockDir)) {
            mkdir($blockDir, 0777, true);
        }
        foreach ($files as $filename => $text) {
            $filepath = $blockDir . '/' . $filename;
            if (!file_exists($filepath)) {
                file_put_contents($filepath, $text);
            }
        }
    }


    /**
     * Преобразует текст в Pascal case
     * @param string $text Текст в формате snake-case
     * @return string Текст в формате PascalCase
     */
    public function toPascalCase(string $text): string
    {
        $result = preg_replace_callback('/\\-(\\w)/umis', function ($regs) {
            return mb_strtoupper($regs[1]);
        }, $text);
        $result = mb_strtoupper(mb_substr($result, 0, 1)) . mb_substr($result, 1);
        return $result;
    }
}
