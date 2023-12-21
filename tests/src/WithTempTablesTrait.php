<?php
/**
 * Трейт для временных баз данных
 */
namespace RAAS\CMS;

use RAAS\Application;

trait WithTempTablesTrait
{
    public static function setUpBeforeClass(): void
    {
        $sqlQuery = "CREATE TEMPORARY TABLE IF NOT EXISTS tmp_entities (
                        id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
                        pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
                        name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
                        description TEXT NULL DEFAULT NULL COMMENT 'Description',
                        priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',

                        PRIMARY KEY (id),
                        KEY (pid),
                        INDEX (priority)
                    ) COMMENT 'Custom entity'";
        Application::i()->SQL->query($sqlQuery);

        $sqlQuery = "CREATE TEMPORARY TABLE IF NOT EXISTS tmp_fields (
                        id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
                        classname varchar(255) NOT NULL DEFAULT '' COMMENT 'Parent class name',
                        pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
                        gid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Group ID#',
                        vis tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Visibility',
                        datatype varchar(255) NOT NULL DEFAULT '' COMMENT 'Data type',
                        urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
                        `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
                        required tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Required',
                        maxlength int(255) NOT NULL,
                        multiple tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Multiple data',
                        source_type enum('','ini','csv','xml','sql','php','dictionary') NOT NULL DEFAULT '' COMMENT 'Source type',
                        `source` text COMMENT 'Source',
                        defval text COMMENT 'Default value',
                        min_val float NOT NULL DEFAULT '0' COMMENT 'Minimal value',
                        max_val float NOT NULL DEFAULT '0' COMMENT 'Maximal value',
                        step float NOT NULL DEFAULT '0' COMMENT 'Step',
                        preprocessor_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Preprocessor interface ID#',
                        postprocessor_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Postprocessor interface ID#',
                        placeholder varchar(255) NOT NULL DEFAULT '' COMMENT 'Placeholder',
                        pattern VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Pattern',
                        show_in_table tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Show as table column',
                        priority int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Priority',
                        PRIMARY KEY (id),
                        KEY pid (pid),
                        KEY gid (gid),
                        KEY datatype (datatype),
                        KEY classname (classname),
                        KEY classname_2 (classname,pid),
                        KEY preprocessor_id (preprocessor_id),
                        KEY postprocessor_id (postprocessor_id),
                        INDEX priority (priority)
                    ) COMMENT='Fields'";
        Application::i()->SQL->query($sqlQuery);

        $sqlQuery = "CREATE TEMPORARY TABLE IF NOT EXISTS tmp_data (
                        pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
                        fid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Field ID#',
                        fii int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Field index',
                        `value` mediumtext COMMENT 'Value',
                        inherited tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Inherited',
                        PRIMARY KEY (pid,fid,fii),
                        KEY pid (pid),
                        KEY fid (fid),
                        KEY fii (fii),
                        INDEX value (value(32))
                    ) COMMENT='Fields data'";
        Application::i()->SQL->query($sqlQuery);

        $sqlQuery = "CREATE TEMPORARY TABLE IF NOT EXISTS tmp_dictionaries (
                        id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
                        pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
                        vis tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Visibility',
                        pvis tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Parent visibility',
                        urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
                        `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
                        priority int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Priority',
                        orderby enum('id','urn','name','priority') NOT NULL DEFAULT 'priority' COMMENT 'Order by',
                        PRIMARY KEY (id),
                        KEY pid (pid),
                        KEY urn (urn),
                        KEY orderby (orderby),
                        INDEX priority (priority)
                      ) COMMENT='Dictionaries'";
        Application::i()->SQL->query($sqlQuery);
    }


    public function setUp(): void
    {
        $sqlQuery = "TRUNCATE TABLE tmp_entities";
        Application::i()->SQL->query($sqlQuery);
        $sqlQuery = "TRUNCATE TABLE tmp_fields";
        Application::i()->SQL->query($sqlQuery);
        $sqlQuery = "TRUNCATE TABLE tmp_data";
        Application::i()->SQL->query($sqlQuery);
        $sqlQuery = "TRUNCATE TABLE tmp_dictionaries";
        Application::i()->SQL->query($sqlQuery);
    }


    public static function tearDownAfterClass(): void
    {
        $sqlQuery = "DROP TABLE IF EXISTS tmp_entities";
        Application::i()->SQL->query($sqlQuery);
        $sqlQuery = "DROP TABLE IF EXISTS tmp_fields";
        Application::i()->SQL->query($sqlQuery);
        $sqlQuery = "DROP TABLE IF EXISTS tmp_data";
        Application::i()->SQL->query($sqlQuery);
        $sqlQuery = "DROP TABLE IF EXISTS tmp_dictionaries";
        Application::i()->SQL->query($sqlQuery);
    }
}
