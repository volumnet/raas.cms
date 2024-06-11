<?php
/**
 * Поле интерфейса
 */
declare(strict_types=1);

namespace RAAS\CMS;

use Exception;
use Error;
use ReflectionClass;
use SOME\Text;
use RAAS\Application;

/**
 * Поле интерфейса
 */
class InterfaceField extends SnippetField
{
    public function __construct(array $params = [])
    {
        $ignoredSnippetFoldersURNs = (array)($params['meta']['ignoredSnippetFoldersURNs'] ?? ['__raas_views']);
        $rootInterfaceClass = ($params['meta']['rootInterfaceClass'] ?? null) ?: null;

        // Получим список подходящих классов
        $children = [];
        if ($rootInterfaceClass) {
            $children[] = [
                'value' => '',
                'caption' => Package::i()->view->_('INTERFACE_CLASSES'),
                'disabled' => 'disabled',
                'children' => $this->getInterfaceClasses($rootInterfaceClass),
            ];
        }
        $children = array_merge($children, $this->getChildrenArr($ignoredSnippetFoldersURNs));
        $defaultParams = [
            'children' => $children,
        ];
        $arr = array_merge($defaultParams, $params);
        $arr['meta']['ignoredSnippetFoldersURNs'] = $ignoredSnippetFoldersURNs; // Здесь, чтобы не было перекрытия
        parent::__construct($arr);
    }


    /**
     * Получает список пунктов по классам интерфейсов
     * @param string $currentClass Текущий класс
     * @param array <pre><code>array<string[] Имя класса => string Описание класса></code></pre>
     *     Соответствия имен и описаний классов (для рекурсии)
     * @param bool $root Корневой класс (для рекурсии)
     * @return Option[]
     */
    public function getInterfaceClasses(string $currentClass, array $classnames = [], bool $root = true): array
    {
        $result = [];
        if (!$classnames && $root) {
            $classMapFile = Application::i()->baseDir . '/vendor/composer/autoload_classmap.php';
            if (is_file($classMapFile)) {
                $classnames = include $classMapFile;
                $classnames = array_filter(array_keys($classnames), function ($x) use ($currentClass) {
                    try {
                        return @class_exists($x) && (($x == $currentClass) || @is_subclass_of($x, $currentClass));
                        // @ для подавления ошибок совместимости для phpQuery
                    // @codeCoverageIgnoreStart
                    // Не могу воспроизвести ошибочные классы в рамках теста
                    } catch (Exception $e) {
                        // Для устранения некорректных классов
                    } catch (Error $x) {
                        // Для устранения некорректных классов
                    }
                    return false;
                    // @codeCoverageIgnoreEnd
                });
            }
            sort($classnames);
        }

        if ($root) {
            $currentClasses = [$currentClass];
        } else {
            $currentClasses = array_values(array_filter($classnames, function ($x) use ($currentClass) {
                try {
                    return get_parent_class($x) == $currentClass;
                // @codeCoverageIgnoreStart
                // Не могу воспроизвести ошибочные классы в рамках теста
                } catch (Exception $e) {
                        // Для устранения некорректных классов
                } catch (Error $x) {
                    // Для устранения некорректных классов
                }
                return false;
                // @codeCoverageIgnoreEnd
            }));
        }
        foreach ($currentClasses as $classname) {
            $ch = $this->getInterfaceClasses($classname, $classnames, false);
            $reflectionClass = new ReflectionClass($classname);
            if ($reflectionClass->isAbstract()) {
                $result = array_merge($result, $ch);
            } else {
                $summary = Text::getClassCaption($classname);
                $caption = $classname;
                if ($summary && ($summary != $classname)) {
                    $caption .= ': ' . $summary;
                }
                $resultEntry = [
                    'value' => $classname,
                    'caption' => $caption,
                ];
                if ($ch) {
                    $resultEntry['children'] = $ch;
                }
                $result[] = $resultEntry;
            }
        }

        return $result;
    }


    public function exportDefault()
    {
        $meta = (array)$this->meta;
        $rootInterfaceClass = ($meta['rootInterfaceClass'] ?? null) ?: null;
        $interfaceClassnameFieldName = ($meta['interfaceClassnameFieldName'] ?? null) ?: '';
        $item = $this->Form->Item;
        $value = $this->datatypeStrategy->getPostData($this);
        $valueToSet = $this->datatypeStrategy->export($value);
        if (class_exists($valueToSet) &&
            (
                !$rootInterfaceClass ||
                ($valueToSet == $rootInterfaceClass) ||
                is_subclass_of($valueToSet, $rootInterfaceClass)
            ) &&
            $interfaceClassnameFieldName
        ) {
            $item->$interfaceClassnameFieldName = $valueToSet;
            $item->{$this->name} = 0;
        } else {
            if ($interfaceClassnameFieldName) {
                $item->$interfaceClassnameFieldName = '';
            }
            $item->{$this->name} = (int)$valueToSet;
        }
    }


    public function importDefault()
    {
        $meta = (array)$this->meta;
        $interfaceClassnameFieldName = ($meta['interfaceClassnameFieldName'] ?? null) ?: '';
        $value = null;
        if ($interfaceClassnameFieldName) {
            $value = $this->Form->Item->$interfaceClassnameFieldName;
        }
        if (!$value) {
            $value = $this->Form->Item->{$this->name};
        }
        $result = $this->datatypeStrategy->import($value);
        return $result;
    }
}
