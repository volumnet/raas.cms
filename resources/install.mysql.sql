CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  location VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Location',
  vis TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Visibility',
  post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  modify_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  author_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Author ID#',
  editor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Editor ID#',
  block_type VARCHAR(255) NOT NULL DEFAULT 'RAAS\\CMS\\Block_HTML' COMMENT 'Block type',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  inherit TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Inherit',
  nat TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Translate address',
  params VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Additional params',
  PRIMARY KEY (id),
  KEY author_id (author_id),
  KEY editor_id (editor_id)
) COMMENT 'Site pages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_form (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  form INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Form ID#',
  std_interface TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Standard interface',
  interface text COMMENT 'Interface code',
  widget INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Widget ID#',
  description text COMMENT 'Widget code',
  PRIMARY KEY (id),
  KEY form (form),
  KEY widget (widget)
) COMMENT 'Form blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_html (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  description text COMMENT 'Text',
  PRIMARY KEY (id)
) COMMENT 'HTML blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_material (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  material_type INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
  std_interface TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Standard interface',
  interface text COMMENT 'Interface code',
  widget INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Widget ID#',
  description text COMMENT 'Widget code',
  pages_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Pages var name',
  rows_per_page TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Rows per page',
  sort_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Sorting var name',
  order_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Order var name',
  sort_field_default VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Field for sorting by default',
  sort_order_default VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Default order',
  PRIMARY KEY (id),
  KEY material_type (material_type),
  KEY widget (widget)
) COMMENT 'Material blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_material_filter (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  var VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Variable',
  relation enum('=','LIKE','CONTAINED','FULLTEXT','<=','>=') NOT NULL DEFAULT '=' COMMENT 'Relation',
  field VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Field',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  KEY id (id),
  KEY priority (priority)
) COMMENT 'Material blocks filtering';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_material_sort (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  var VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Variable',
  field VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Field',
  relation enum('asc!','desc!','asc','desc') NOT NULL DEFAULT 'asc!' COMMENT 'Relation',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  KEY id (id),
  KEY priority (priority)
) COMMENT 'Material blocks sorting';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_menu (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  menu INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Menu ID#',
  full_menu TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Full menu',
  std_interface TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Standard interface',
  interface text COMMENT 'Interface code',
  widget INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Widget ID#',
  description text COMMENT 'Widget code',
  PRIMARY KEY (id),
  KEY menu (menu),
  KEY widget (widget)
) COMMENT 'Menu blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_pages_assoc (
  block_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Block ID#',
  page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  PRIMARY KEY (block_id,page_id),
  KEY block_id (block_id),
  KEY page_id (page_id)
) COMMENT 'Blocks to pages associations';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_php (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  description text COMMENT 'Code',
  widget INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Widget ID#',
  PRIMARY KEY (id),
  KEY widget (widget)
) COMMENT 'PHP blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  search_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Search var name',
  min_length TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Minimal query length',
  pages_var_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Pages var name',
  rows_per_page TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Rows per page',
  std_interface TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Standard interface',
  interface text COMMENT 'Interface code',
  widget INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Widget ID#',
  description text COMMENT 'Widget code',
  PRIMARY KEY (id),
  KEY widget (widget)
) COMMENT 'Search blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search_languages_assoc (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  `language` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Language',
  PRIMARY KEY (id,`language`),
  KEY id (id),
  KEY `language` (`language`)
) COMMENT 'Search blocks languages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search_material_types_assoc (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  material_type INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
  PRIMARY KEY (id,material_type),
  KEY id (id),
  KEY material_type (material_type)
) COMMENT 'Search blocks material types';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search_pages_assoc (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID#',
  page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  PRIMARY KEY (id,page_id),
  KEY id (id),
  KEY page_id (page_id)
) COMMENT 'Search blocks pages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_data (
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  fid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Field ID#',
  fii INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Field index',
  `value` text COMMENT 'Value',
  inherited TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Inherited',
  PRIMARY KEY (pid,fid,fii),
  KEY pid (pid),
  KEY fid (fid),
  KEY fii (fii),
  FULLTEXT KEY `value` (`value`)
) COMMENT 'Pages fields';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_dictionaries (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  vis TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Visibility',
  pvis TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Parent visibility',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  orderby enum('id','urn','name','priority') NOT NULL DEFAULT 'priority' COMMENT 'Order by',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY urn (urn),
  KEY orderby (orderby)
) COMMENT 'Dictionaries';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_feedback (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  uid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Site user ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Form ID#',
  page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  vis TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Visited',
  ip VARCHAR(255) NOT NULL DEFAULT '0.0.0.0' COMMENT 'IP address',
  user_agent VARCHAR(255) NOT NULL DEFAULT '0.0.0.0' COMMENT 'User Agent',
  PRIMARY KEY (id),
  KEY uid (uid),
  KEY pid (pid),
  KEY page_id (page_id)
) COMMENT 'Feedback';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_fields (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  classname VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Parent class name',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
  datatype VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Data type',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  required TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Required',
  maxlength int(255) NOT NULL,
  multiple TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Multiple data',
  source_type enum('','ini','csv','xml','sql','php','dictionary') NOT NULL DEFAULT '' COMMENT 'Source type',
  `source` text COMMENT 'Source',
  min_val float NOT NULL DEFAULT 0 COMMENT 'Minimal value',
  max_val float NOT NULL DEFAULT 0 COMMENT 'Maximal value',
  placeholder VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Placeholder',
  show_in_table TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Show as table column',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY datatype (datatype),
  KEY classname (classname),
  KEY classname_2 (classname,pid)
) COMMENT 'Material fields';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_forms (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  material_type INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type',
  create_feedback INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Create feedback',
  email VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Contact e-mail',
  signature TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Require POST signature',
  antispam VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Use anti-spam',
  antispam_field_name VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Anti-spam field name',
  std_template INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Use standard template',
  description text COMMENT 'E-mail template',
  PRIMARY KEY (id)
) COMMENT 'Forms';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_materials (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
  vis TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Visibility',
  post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  modify_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  author_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Author ID#',
  editor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Editor ID#',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  description text COMMENT 'Description',
  meta_title VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Meta title',
  meta_description text COMMENT 'Meta description',
  meta_keywords text COMMENT 'Meta keywords',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY author_id (author_id),
  KEY editor_id (editor_id),
  KEY urn (urn)
) COMMENT 'Translator exceptions';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_materials_pages_assoc (
  id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  PRIMARY KEY (id,pid),
  KEY id (id),
  KEY pid (pid)
) COMMENT 'Materials to pages associations';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_material_types (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  global_type TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Global materials',
  PRIMARY KEY (id),
  KEY urn (urn)
) COMMENT 'Material types';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_menus (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  vis TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Visibility',
  pvis TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Parent visibility',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  url VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URL',
  page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  inherit TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Nesting level',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY page_id (page_id)
) COMMENT 'Menus';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_pages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  vis TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Visibility',
  pvis TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Parent visibility',
  post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  modify_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  author_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Author ID#',
  editor_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Editor ID#',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  response_code INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Service page response code',
  meta_title VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Meta title',
  inherit_meta_title TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Inherit meta-title',
  meta_description text COMMENT 'Meta description',
  inherit_meta_description TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Inherit meta-description',
  meta_keywords text COMMENT 'Meta keywords',
  inherit_meta_keywords TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Inherit meta-keywords',
  template VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Template',
  inherit_template TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Inherit meta-title',
  lang VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Language',
  inherit_lang TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Inherit language',
  nat TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Translate address',
  priority INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Priority',
  `cache` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cache page',
  inherit_cache TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Inherit cache page',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY author_id (author_id),
  KEY editor_id (editor_id),
  KEY urn (urn),
  KEY template (template)
) COMMENT 'Site pages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_pages_data (
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
  fid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Field ID#',
  fii INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Field index',
  `value` text COMMENT 'Value',
  PRIMARY KEY (pid,fid,fii),
  KEY pid (pid),
  KEY fid (fid),
  KEY fii (fii)
) COMMENT 'Pages fields';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_templates (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  description text COMMENT 'Code',
  width INT UNSIGNED NOT NULL DEFAULT 640 COMMENT 'Width',
  height INT UNSIGNED NOT NULL DEFAULT 1024 COMMENT 'Height',
  visual TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Template is visual',
  background INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Background attachment ID#',
  locations_info text COMMENT 'Locations info',
  PRIMARY KEY (id),
  KEY background (background)
) COMMENT 'Templates';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_widgets (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  urn VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  description text COMMENT 'Code',
  PRIMARY KEY (id),
  KEY pid (pid)
) COMMENT 'Widgets';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_widget_folders (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Parent ID#',
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name',
  PRIMARY KEY (id),
  KEY pid (pid)
) COMMENT 'Widget folders';