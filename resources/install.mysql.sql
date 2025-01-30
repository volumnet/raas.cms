CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_access (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  material_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material ID#',
  block_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Block ID#',
  allow TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT '1 - allow, 0 - deny',
  to_type TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'To (type)',
  uid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'User ID#',
  gid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Group ID#',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  PRIMARY KEY (id),
  KEY page_id (page_id),
  KEY material_id (material_id),
  KEY block_id (block_id),
  KEY uid (uid),
  KEY gid (gid),
  INDEX priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Site access';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_access_blocks_cache (
  uid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'User ID#',
  block_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Block ID#',
  allow TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT '1 - allow, 0 - deny',
  PRIMARY KEY (uid,block_id),
  KEY uid (uid),
  KEY block_id (block_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Blocks access cache';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_access_materials_cache (
  uid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'User ID#',
  material_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material ID#',
  allow TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT '1 - allow, 0 - deny',
  PRIMARY KEY (uid,material_id),
  KEY uid (uid),
  KEY material_id (material_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Materials access cache';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_access_pages_cache (
  uid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'User ID#',
  page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  allow TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT '1 - allow, 0 - deny',
  PRIMARY KEY (uid,page_id),
  KEY uid (uid),
  KEY page_id (page_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Pages access cache';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  location VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Location',
  vis TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Visibility',
  post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  modify_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  author_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Author ID#',
  editor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Editor ID#',
  block_type VARCHAR(255) NOT NULL DEFAULT 'RAAS\\CMS\\Block_HTML' COMMENT 'Block type',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  inherit TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Inherit',
  nat TINYINT(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Translate address',
  params TEXT NULL DEFAULT NULL COMMENT 'Additional params',
  interface_classname VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Interface classname',
  interface_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Interface snippet ID#',
  widget_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Widget ID#',
  cache_type TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Cache type',
  cache_single_page TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Cache by single pages',
  cache_interface_classname VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Cache interface classname',
  cache_interface_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cache interface snippet ID#',
  vis_material TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Visibility by material',
  PRIMARY KEY (id),
  KEY author_id (author_id),
  KEY editor_id (editor_id),
  INDEX interface_classname (interface_classname),
  KEY interface_id (interface_id),
  KEY widget_id (widget_id),
  INDEX cache_interface_classname (cache_interface_classname),
  KEY cache_interface_id (cache_interface_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_form (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  form INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Form ID#',
  PRIMARY KEY (id),
  KEY form (form)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Form blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_html (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  description MEDIUMTEXT COMMENT 'Text',
  wysiwyg TINYINT(1) unsigned NOT NULL DEFAULT 1 COMMENT 'WYSIWYG editor on',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='HTML blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_material (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  material_type INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
  pages_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Pages var name',
  rows_per_page TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Rows per page',
  sort_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Sorting var name',
  order_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Order var name',
  sort_field_default VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Field for sorting by default',
  sort_order_default VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Default order',
  legacy TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Redirect legacy addresses',
  PRIMARY KEY (id),
  KEY material_type (material_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Material blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_material_filter (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  var VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Variable',
  relation ENUM('=','LIKE','CONTAINED','FULLTEXT','<=','>=') NOT NULL DEFAULT '=' COMMENT 'Relation',
  field VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Field',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  KEY id (id),
  KEY priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Material blocks filtering';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_material_sort (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  var VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Variable',
  field VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Field',
  relation ENUM('asc!','desc!','asc','desc') NOT NULL DEFAULT 'asc!' COMMENT 'Relation',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  KEY id (id),
  KEY priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Material blocks sorting';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_menu (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  menu INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Menu ID#',
  full_menu TINYINT(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Full menu',
  PRIMARY KEY (id),
  KEY menu (menu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Menu blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_pages_assoc (
  block_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Block ID#',
  page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  PRIMARY KEY (block_id,page_id),
  KEY block_id (block_id),
  KEY page_id (page_id),
  INDEX priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Blocks to pages associations';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  search_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Search var name',
  min_length TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Minimal query length',
  pages_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Pages var name',
  rows_per_page TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Rows per page',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Search blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search_languages_assoc (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  `language` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Language',
  PRIMARY KEY (id,`language`),
  KEY id (id),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Search blocks languages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search_material_types_assoc (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  material_type INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
  PRIMARY KEY (id,material_type),
  KEY id (id),
  KEY material_type (material_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Search blocks material types';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search_pages_assoc (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  PRIMARY KEY (id,page_id),
  KEY id (id),
  KEY page_id (page_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Search blocks pages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_data (
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  fid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Field ID#',
  fii INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Field index',
  `value` MEDIUMTEXT COMMENT 'Value',
  inherited TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Inherited',
  PRIMARY KEY (pid,fid,fii),
  KEY pid (pid),
  KEY fid (fid),
  KEY fii (fii),
  INDEX value (value(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Fields data';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_dictionaries (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  vis TINYINT(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Visibility',
  pvis TINYINT(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Parent visibility',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  orderby ENUM('id','urn','name','priority') NOT NULL DEFAULT 'priority' COMMENT 'Order by',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY urn (urn),
  KEY orderby (orderby),
  INDEX priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Dictionaries';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_feedback (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  uid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Site user ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Form ID#',
  page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  material_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material ID#',
  post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  vis TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Visited',
  ip VARCHAR(255) NOT NULL DEFAULT '0.0.0.0' COMMENT 'IP address',
  user_agent VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'User Agent',
  PRIMARY KEY (id),
  KEY uid (uid),
  KEY pid (pid),
  KEY page_id (page_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Feedback';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_fields (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  classname VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Parent class name',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
  gid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Group ID#',
  vis TINYINT(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Visibility',
  datatype VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Data type',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  required TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Required',
  maxlength int(255) NOT NULL,
  multiple TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Multiple data',
  source_type ENUM('','ini','csv','xml','sql','php','dictionary') NOT NULL DEFAULT '' COMMENT 'Source type',
  `source` TEXT COMMENT 'Source',
  defval TEXT COMMENT 'Default value',
  min_val FLOAT NOT NULL DEFAULT '0' COMMENT 'Minimal value',
  max_val FLOAT NOT NULL DEFAULT '0' COMMENT 'Maximal value',
  step FLOAT NOT NULL DEFAULT '0' COMMENT 'Step',
  preprocessor_classname VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Preprocessor classname',
  preprocessor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Preprocessor interface ID#',
  postprocessor_classname VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Postprocessor classname',
  postprocessor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Postprocessor interface ID#',
  placeholder VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Placeholder',
  pattern VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Pattern',
  show_in_table TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Show as table column',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY gid (gid),
  KEY datatype (datatype),
  KEY classname (classname),
  KEY classname_2 (classname,pid),
  INDEX preprocessor_classname (preprocessor_classname),
  KEY preprocessor_id (preprocessor_id),
  INDEX postprocessor_classname (postprocessor_classname),
  KEY postprocessor_id (postprocessor_id),
  INDEX priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Fields';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_fieldgroups (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  classname VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Parent class name',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
  gid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent group ID#',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY gid (gid),
  KEY classname (classname),
  KEY classname_2 (classname,pid),
  INDEX priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Field groups';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_fields_form_vis (
    fid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Field ID#',
    pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Section ID#',

    PRIMARY KEY (fid, pid),
    INDEX (fid),
    INDEX (pid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Fields form';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_forms (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  material_type INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type',
  create_feedback INT UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Create feedback',
  email VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Contact e-mail',
  signature TINYINT(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Require POST signature',
  antispam VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Use anti-spam',
  antispam_field_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Anti-spam field name',
  interface_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Interface ID#',
  PRIMARY KEY (id),
  KEY (interface_id),
  INDEX (urn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Forms';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_groups (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent group ID#',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  description TEXT COMMENT 'Description',
  PRIMARY KEY (id),
  KEY pid (pid),
  INDEX (urn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Groups of users';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_materials (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
  page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Default page ID#',
  vis TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Visibility',
  post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  modify_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  author_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Author ID#',
  editor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Editor ID#',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  description MEDIUMTEXT COMMENT 'Description',
  meta_title VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Meta title',
  meta_description TEXT COMMENT 'Meta description',
  meta_keywords TEXT COMMENT 'Meta keywords',
  h1 VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'H1 title',
  menu_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Menu name',
  breadcrumbs_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Breadcrumbs name',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  visit_counter INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Visit counter',
  modify_counter INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Modify counter',
  changefreq ENUM('','always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT '' COMMENT 'Change frequency',
  last_modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last modified',
  sitemaps_priority DECIMAL(8,2) unsigned NOT NULL DEFAULT '0.50' COMMENT 'Sitemaps priority',
  show_from DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish from date/time',
  show_to DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish to date/time',
  cache_url_parent_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cached URL Parent ID#',
  cache_url VARCHAR(255) NOT NULL DEFAULT '/' COMMENT 'Cached URL',

  PRIMARY KEY (id),
  KEY pid (pid),
  KEY author_id (author_id),
  KEY editor_id (editor_id),
  KEY urn (urn),
  KEY show_from (show_from),
  KEY show_to (show_to),
  INDEX priority (priority),
  INDEX cache_url_parent_id (cache_url_parent_id),
  INDEX cache_url (cache_url)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Translator exceptions';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_materials_pages_assoc (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  PRIMARY KEY (id,pid),
  KEY id (id),
  KEY pid (pid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Materials to pages associations';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_materials_affected_pages_cache (
    material_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
    page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',

    PRIMARY KEY (material_id, page_id),
    KEY (material_id),
    KEY (page_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Materials affected pages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_materials_votes (
    material_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material ID#',
    ip VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'IP-address',
    post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
    vote TINYINT(1) SIGNED NOT NULL DEFAULT 0 COMMENT 'Vote',

    PRIMARY KEY (material_id, ip),
    KEY (material_id),
    KEY (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Materials votes log';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_material_types (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent type ID#',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  global_type TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Global materials',
  PRIMARY KEY (id),
  KEY urn (urn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Material types';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_material_types_affected_pages_for_materials_cache (
    material_type_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
    page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
    nat TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'NAT',

    PRIMARY KEY (material_type_id, page_id),
    KEY (material_type_id),
    KEY (page_id),
    KEY (nat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Material types affected pages for materials';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_material_types_affected_pages_for_self_cache (
    material_type_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
    page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
    nat TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'NAT',

    PRIMARY KEY (material_type_id, page_id),
    KEY (material_type_id),
    KEY (page_id),
    KEY (nat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Material types affected pages for self (for admin)';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_menus (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  domain_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Domain ID#',
  vis TINYINT(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Visibility',
  pvis TINYINT(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Parent visibility',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  url VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URL',
  page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  inherit TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Nesting level',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY page_id (page_id),
  INDEX (urn),
  INDEX priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Menus';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_pages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  vis TINYINT(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Visibility',
  pvis TINYINT(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Parent visibility',
  post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  modify_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  author_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Author ID#',
  editor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Editor ID#',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  response_code INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Service page response code',
  mime VARCHAR(255) NOT NULL DEFAULT 'text/html' COMMENT 'MIME-type',
  meta_title VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Meta title',
  inherit_meta_title TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Inherit meta-title',
  meta_description TEXT COMMENT 'Meta description',
  inherit_meta_description TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Inherit meta-description',
  meta_keywords TEXT COMMENT 'Meta keywords',
  inherit_meta_keywords TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Inherit meta-keywords',
  h1 VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'H1 title',
  menu_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Menu name',
  breadcrumbs_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Breadcrumbs name',
  template VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Template',
  inherit_template TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Inherit meta-title',
  lang VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Language',
  inherit_lang tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Inherit language',
  nat TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Translate address',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  `cache` TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Cache page',
  inherit_cache TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Inherit cache page',
  visit_counter INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Visit counter',
  modify_counter INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Modify counter',
  changefreq ENUM('','always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT '' COMMENT 'Change frequency',
  inherit_changefreq TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Inherit change frequency',
  last_modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last modified',
  sitemaps_priority DECIMAL(8,2) unsigned NOT NULL DEFAULT '0.50' COMMENT 'Sitemaps priority',
  inherit_sitemaps_priority TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Inherit sitemaps priority',
  cache_url VARCHAR(255) NOT NULL DEFAULT '/' COMMENT 'Cached URL',

  PRIMARY KEY (id),
  KEY pid (pid),
  KEY author_id (author_id),
  KEY editor_id (editor_id),
  KEY urn (urn),
  KEY template (template),
  INDEX priority (priority),
  INDEX cache_url (cache_url)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Site pages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_redirects (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  rx tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'RegExp',
  post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date', 
  url_from VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URL from',
  url_to VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URL to',
  priority int unsigned NOT NULL DEFAULT 0 COMMENT 'Priority',
  PRIMARY KEY (id),
  KEY post_date (post_date),
  KEY url_from (url_from)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Redirects';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_snippets (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  author_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Author ID#',
  editor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Editor ID#',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  locked VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Locked symlink',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY author_id (author_id),
  KEY editor_id (editor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Snippets';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_snippet_folders (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  locked TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Locked',
  PRIMARY KEY (id),
  KEY pid (pid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Snippet folders';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_templates (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  author_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Author ID#',
  editor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Editor ID#',
  width INT UNSIGNED NOT NULL DEFAULT '640' COMMENT 'Width',
  height INT UNSIGNED NOT NULL DEFAULT '1024' COMMENT 'Height',
  locations_info TEXT COMMENT 'Locations info',
  PRIMARY KEY (id),
  KEY author_id (author_id),
  KEY editor_id (editor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Templates';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  login VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Login',
  password_md5 VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Password MD5',
  email VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'E-mail',
  post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Registration date',
  vis TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Active',
  lang VARCHAR(255) NOT NULL DEFAULT 'ru' COMMENT 'Language',
  `new` TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'New',
  activated TINYINT(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Activated',
  PRIMARY KEY (id),
  KEY pid (login),
  KEY email (email),
  KEY post_date (post_date),
  KEY vis (vis),
  KEY `new` (`new`),
  KEY activated (activated)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Users';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_users_groups_assoc (
  uid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID#',
  gid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Group ID#',
  PRIMARY KEY (uid,gid),
  KEY uid (uid),
  KEY gid (gid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Users-groups associations';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_users_social (
  uid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'User ID#',
  url VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Social network page URL',
  PRIMARY KEY (uid,url),
  KEY uid (uid),
  KEY url (url)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Users social networks associations';