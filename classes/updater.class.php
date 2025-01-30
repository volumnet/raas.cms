<?php
/**
 * Менеджер обновлений
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\File;
use SOME\SOME;
use SOME\Text;
use RAAS\Application;
use RAAS\IContext;
use RAAS\Updater as RAASUpdater;

/**
 * Класс менеджера обновлений
 * @codeCoverageIgnore Поскольку мы не можем хранить все версии
 */
class Updater extends RAASUpdater
{
    public function preInstall()
    {
        // 2025 год - 8
        // 2024 год - 7/8
        // 2023 год - 7
        // 2022 год - 5/7
        // 2021 год - 5 -- убираем его и ранее
        $v = (string)($this->Context->registryGet('baseVersion') ?? '');
        if (version_compare($v, '4.3.29') < 0) {
            $this->update20220203();
            $this->update20220217();
        }
        if (version_compare($v, '4.3.58') < 0) {
            $this->update20230503();
        }
        if (version_compare($v, '4.3.66') < 0) {
            $this->update20231008();
        }
        if (version_compare($v, '4.3.83') < 0) {
            $this->update20240402();
        }
        if (version_compare($v, '4.3.87') < 0) {
            $this->update20240610();
        }
        if (version_compare($v, '4.3.92') < 0) {
            $this->update20240702();
        }
        // ПО ВОЗМОЖНОСТИ НЕ ПИШЕМ СЮДА, А ПИШЕМ В postInstall
    }


    public function postInstall()
    {
        $v = (string)($this->Context->registryGet('baseVersion') ?? '');
        $w = new Webmaster();
        $w->checkStdInterfaces();
        $sqlQuery = "SELECT COUNT(*) FROM " . SOME::_dbprefix() . "cms_pages";
        if (!$this->SQL->getvalue($sqlQuery)) {
            $w->createSite();
        }
    }


    /**
     * Добавляет видимость полей по формам, заполняет ее
     */
    public function update20220203()
    {
        if (in_array(SOME::_dbprefix() . "cms_fields", $this->tables) &&
            !in_array(SOME::_dbprefix() . "cms_fields_form_vis", $this->tables)
        ) {
            // Создадим форму
            $sqlQuery = "CREATE TABLE IF NOT EXISTS " . SOME::_dbprefix() . "cms_fields_form_vis (
                            fid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Field ID#',
                            pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Section ID#',
                            vis TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Visibility',

                            PRIMARY KEY (fid, pid),
                            INDEX (fid),
                            INDEX (pid)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT 'Fields form'";
            $this->SQL->query($sqlQuery);

            // Выберем все поля, относящиеся к типам материалов
            $sqlQuery = "SELECT id, pid
                           FROM " . SOME::_dbprefix() . "cms_fields
                          WHERE classname = 'RAAS\\\\CMS\\\\Material_Type'
                            AND pid";
            $sqlResult = $this->SQL->query($sqlQuery);

            MaterialTypeRecursiveCache::i()->refresh();
            $sqlArr = [];
            foreach ($sqlResult as $sqlRow) {
                $selfAndChildrenMaterialTypesIds = MaterialTypeRecursiveCache::i()->getSelfAndChildrenIds($sqlRow['pid']);
                foreach ($selfAndChildrenMaterialTypesIds as $materialTypeId) {
                    $sqlArr[] = [
                        'fid' => (int)$sqlRow['id'],
                        'pid' => (int)$materialTypeId,
                    ];
                }
            }
            $this->SQL->add(SOME::_dbprefix() . "cms_fields_form_vis", $sqlArr);
        }
    }


    /**
     * Добавляет группы полей
     */
    public function update20220217()
    {
        if (!in_array(SOME::_dbprefix() . "cms_fieldgroups", $this->tables)) {
            $sqlQuery = "CREATE TABLE IF NOT EXISTS " . SOME::_dbprefix() . "cms_fieldgroups (
                            id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
                            classname varchar(255) NOT NULL DEFAULT '' COMMENT 'Parent class name',
                            pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
                            gid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent group ID#',
                            urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
                            `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
                            priority int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Priority',
                            PRIMARY KEY (id),
                            KEY pid (pid),
                            KEY gid (gid),
                            KEY classname (classname),
                            KEY classname_2 (classname,pid),
                            INDEX priority (priority)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Field groups'";
            $this->SQL->query($sqlQuery);
        }
        if (in_array(SOME::_dbprefix() . "cms_fields", $this->tables) &&
            !in_array('gid', $this->columns(SOME::_dbprefix() . "cms_fields"))
        ) {
            $sqlQuery = "ALTER TABLE " . SOME::_dbprefix() . "cms_fields
                           ADD gid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Group ID#' AFTER pid,
                           ADD KEY (gid)";
            $this->SQL->query($sqlQuery);
        }
    }


    /**
     * Добавляет индекс на значения таблицы cms_data
     */
    public function update20230503()
    {
        if (in_array(SOME::_dbprefix() . "cms_data", $this->tables)) {
            $sqlQuery = "SELECT COUNT(*)
                           FROM information_schema.statistics
                          WHERE TABLE_SCHEMA = ?
                            AND table_name = 'cms_data'
                            AND index_name = 'value'";
            $sqlBind = [Application::i()->dbname];
            $sqlResult = $this->SQL->getvalue([$sqlQuery, $sqlBind]);
            if (!$sqlResult) {
                $sqlQuery = "ALTER TABLE cms_data ADD INDEX value (value(32))";
                $this->SQL->query($sqlQuery);
            }
        }
    }


    /**
     * Убирает название сниппетов и шаблонов (заменяет автоматическими)
     */
    public function update20231008()
    {
        if (in_array(SOME::_dbprefix() . "cms_snippets", $this->tables) &&
            in_array('name', $this->columns(SOME::_dbprefix() . "cms_snippets"))
        ) {
            $sqlQuery = "ALTER TABLE " . SOME::_dbprefix() . "cms_snippets DROP name";
            $this->SQL->query($sqlQuery);
        }
        if (in_array(SOME::_dbprefix() . "cms_templates", $this->tables) &&
            in_array('name', $this->columns(SOME::_dbprefix() . "cms_templates"))
        ) {
            $sqlQuery = "ALTER TABLE " . SOME::_dbprefix() . "cms_templates DROP name";
            $this->SQL->query($sqlQuery);
        }
    }


    /**
     * Удалим таблицу cms_blocks_php
     */
    public function update20240402()
    {
        if (in_array(SOME::_dbprefix() . "cms_blocks_php", $this->tables)) {
            $sqlQuery = "DROP TABLE " . SOME::_dbprefix() . "cms_blocks_php";
            $this->SQL->query($sqlQuery);
        }
    }


    /**
     * Обновления по версии 4.3.87
     */
    public function update20240610()
    {
        // В cms_feedback.user_agent по умолчанию пустая строка
        if (in_array(SOME::_dbprefix() . "cms_feedback", $this->tables)) {
            $sqlQuery = "ALTER TABLE " . SOME::_dbprefix() . "cms_feedback
                        CHANGE user_agent user_agent VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'User Agent'";
            $this->SQL->query($sqlQuery);
        }

        // в cms_blocks поля interface_classname и cache_interface_classname,
        // а также ключи interface_id, widget_id, interface_classname и cache_interface_classname
        if (in_array(SOME::_dbprefix() . "cms_blocks", $this->tables) &&
            !in_array('interface_classname', $this->columns(SOME::_dbprefix() . "cms_blocks"))
        ) {
            $sqlQuery = "ALTER TABLE " . SOME::_dbprefix() . "cms_blocks
                           ADD interface_classname VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Interface classname' AFTER params,
                           ADD cache_interface_classname VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Cache interface classname' AFTER cache_single_page,
                           ADD INDEX (interface_classname),
                           ADD KEY (interface_id),
                           ADD KEY (widget_id),
                           ADD INDEX (cache_interface_classname)";
            $this->SQL->query($sqlQuery);

            // в cms_forms ключ interface_id
            if (in_array(SOME::_dbprefix() . "cms_forms", $this->tables)) {
                $sqlQuery = "ALTER TABLE " . SOME::_dbprefix() . "cms_forms
                               ADD KEY (interface_id)";
                $this->SQL->query($sqlQuery);
            }
        }

        // в cms_fields поля preprocessor_classname и postprocessor_classname
        if (in_array(SOME::_dbprefix() . "cms_fields", $this->tables) &&
            !in_array('preprocessor_classname', $this->columns(SOME::_dbprefix() . "cms_fields"))
        ) {
            $sqlQuery = "ALTER TABLE " . SOME::_dbprefix() . "cms_fields
                           ADD preprocessor_classname VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Preprocessor classname' AFTER step,
                           ADD postprocessor_classname VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Postprocessor classname' AFTER preprocessor_id,
                           ADD INDEX (preprocessor_classname),
                           ADD INDEX (postprocessor_classname)";
            $this->SQL->query($sqlQuery);
        }

        if (in_array(SOME::_dbprefix() . "cms_snippets", $this->tables)) {
            $sqlQuery = "SELECT COUNT(*) FROM " . SOME::_dbprefix() . "cms_snippets WHERE urn = '__raas_material_interface'";
            $sqlResult = (int)$this->SQL->getvalue($sqlQuery);
            if ($sqlResult > 0) {
                foreach ([
                    '__raas_cache_interface' => CacheInterface::class,
                    '__raas_form_interface' => FormInterface::class,
                    '__raas_material_interface' => MaterialInterface::class,
                    '__raas_menu_interface' => MenuInterface::class,
                    '__raas_search_interface' => SearchInterface::class,
                    '__raas_watermark_interface' => WatermarkInterface::class,
                ] as $snippetURN => $interfaceClassname) {
                    $sqlBind = ['snippetURN' => $snippetURN, 'interfaceClassname' => $interfaceClassname];
                    // Заменим основной интерфейс
                    $sqlQuery = "UPDATE " . SOME::_dbprefix() . "cms_blocks AS tB
                                   JOIN " . SOME::_dbprefix() . "cms_snippets AS tS ON tB.interface_id = tS.id
                                    SET tB.interface_id = 0,
                                        tB.interface_classname = :interfaceClassname
                                  WHERE tS.urn = :snippetURN";
                    $this->SQL->query([$sqlQuery, $sqlBind]);
                    // Заменим интерфейс кэширования
                    $sqlQuery = "UPDATE " . SOME::_dbprefix() . "cms_blocks AS tB
                                   JOIN " . SOME::_dbprefix() . "cms_snippets AS tS ON tB.cache_interface_id = tS.id
                                    SET tB.cache_interface_id = 0,
                                        tB.cache_interface_classname = :interfaceClassname
                                  WHERE tS.urn = :snippetURN";
                    $this->SQL->query([$sqlQuery, $sqlBind]);
                    // Заменим интерфейс процессоров
                    $sqlQuery = "UPDATE " . SOME::_dbprefix() . "cms_fields AS tF
                                   JOIN " . SOME::_dbprefix() . "cms_snippets AS tS ON tF.preprocessor_id = tS.id
                                    SET tF.preprocessor_id = 0,
                                        tF.preprocessor_classname = :interfaceClassname
                                  WHERE tS.urn = :snippetURN";
                    $this->SQL->query([$sqlQuery, $sqlBind]);
                    $sqlQuery = "UPDATE " . SOME::_dbprefix() . "cms_fields AS tF
                                   JOIN " . SOME::_dbprefix() . "cms_snippets AS tS ON tF.postprocessor_id = tS.id
                                    SET tF.postprocessor_id = 0,
                                        tF.postprocessor_classname = :interfaceClassname
                                  WHERE tS.urn = :snippetURN";
                    $this->SQL->query([$sqlQuery, $sqlBind]);
                    // Удалим сниппеты
                    $sqlQuery = "DELETE FROM " . SOME::_dbprefix() . "cms_snippets WHERE urn = ?";
                    $this->SQL->query([$sqlQuery, [$snippetURN]]);
                }
            }
        }
    }


    /**
     * Преобразует данные сниппетов и шаблонов
     * ВАЖНО!!! здесь меняем все сниппеты, включая модульные, поскольку нет возможности оценить,
     * какие к кому принадлежат, и, соответственно, адекватно убрать/проставить locked
     */
    public function update20240702()
    {
        $dir = Application::i()->baseDir . '/inc/snippets';
        // СНИППЕТЫ
        if (in_array(SOME::_dbprefix() . "cms_snippets", $this->tables) &&
            in_array('description', $this->columns(SOME::_dbprefix() . "cms_snippets"))
        ) {
            $lockedMapping = [
                '__raas_form_notify' => 'form_notification.php',
                '__raas_shop_order_notify' => 'shop/form_notification.php',
                '__raas_users_recovery_notify' => 'users/recovery_notification.php',
                '__raas_users_register_notify' => 'users/register_notification.php',
            ];

            // 1. Преобразуем все locked в строки
            $sqlQuery = "ALTER TABLE " . SOME::_dbprefix() . "cms_snippets
                        CHANGE locked locked VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Locked'";
            $this->SQL->query($sqlQuery);
            $sqlQuery = "UPDATE " . SOME::_dbprefix() . "cms_snippets SET locked = '' WHERE 1";
            $this->SQL->query($sqlQuery);
            foreach ($lockedMapping as $urn => $symlink) {
                $sqlQuery = "UPDATE " . SOME::_dbprefix() . "cms_snippets SET locked = ? WHERE urn = ?";
                $this->SQL->query([$sqlQuery, [$symlink, $urn]]);
            }

            // 2. Вытащим все сниппеты в файлы
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            if (!is_file($dir . '/.htaccess')) {
                file_put_contents($dir . '/.htaccess', "Order deny,allow\nDeny from all");
                chmod($dir . '/.htaccess', 0755);
            }
            $sqlQuery = "SELECT urn, description FROM " . SOME::_dbprefix() . "cms_snippets WHERE locked = ''";
            $sqlResult = $this->SQL->get($sqlQuery);
            foreach ($sqlResult as $sqlRow) {
                $filename = $dir . '/' . $sqlRow['urn'] . '.tmp.php';
                if (!is_file($filename)) {
                    file_put_contents($filename, $sqlRow['description']);
                }
                chmod($filename, 0777);
            }

            // 3. Удалим description и прочие ненужные поля
            $sqlQuery = "ALTER TABLE " . SOME::_dbprefix() . "cms_snippets
                          DROP post_date,
                          DROP modify_date,
                          DROP description";
            $this->SQL->query($sqlQuery);
            File::unlink(Application::i()->baseDir . '/cache/system/snippets');
        }

        // ШАБЛОНЫ
        if (in_array(SOME::_dbprefix() . "cms_templates", $this->tables) &&
            in_array('description', $this->columns(SOME::_dbprefix() . "cms_templates"))
        ) {
            // 1. Вытащим все шаблоны в файлы
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            if (!is_file($dir . '/.htaccess')) {
                file_put_contents($dir . '/.htaccess', "Order deny,allow\nDeny from all");
                chmod($dir . '/.htaccess', 0755);
            }
            $sqlQuery = "SELECT id, description FROM " . SOME::_dbprefix() . "cms_templates";
            $sqlResult = $this->SQL->get($sqlQuery);
            foreach ($sqlResult as $sqlRow) {
                $filename = $dir . '/template' . $sqlRow['id'] . '.tmp.php';
                if (!is_file($filename)) {
                    file_put_contents($filename, $sqlRow['description']);
                }
                chmod($filename, 0777);
            }

            // 2. Удалим description и прочие ненужные поля
            $sqlQuery = "ALTER TABLE " . SOME::_dbprefix() . "cms_templates
                          DROP post_date,
                          DROP modify_date,
                          DROP urn,
                          DROP description,
                          DROP visual,
                          DROP background";
            $this->SQL->query($sqlQuery);
            File::unlink(Application::i()->baseDir . '/cache/system/templates');
        }
    }
}
