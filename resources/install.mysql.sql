CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_access (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  page_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  material_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Material ID#',
  block_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  allow tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - allow, 0 - deny',
  to_type tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'To (type)',
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID#',
  gid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Group ID#',
  priority int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (id),
  KEY page_id (page_id),
  KEY material_id (material_id),
  KEY block_id (block_id),
  KEY uid (uid),
  KEY gid (gid),
  INDEX priority (priority)
) COMMENT='Site access';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_access_blocks_cache (
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID#',
  block_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  allow tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - allow, 0 - deny',
  PRIMARY KEY (uid,block_id),
  KEY uid (uid),
  KEY block_id (block_id)
) COMMENT='Blocks access cache';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_access_materials_cache (
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID#',
  material_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Material ID#',
  allow tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - allow, 0 - deny',
  PRIMARY KEY (uid,material_id),
  KEY uid (uid),
  KEY material_id (material_id)
) COMMENT='Materials access cache';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_access_pages_cache (
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID#',
  page_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  allow tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - allow, 0 - deny',
  PRIMARY KEY (uid,page_id),
  KEY uid (uid),
  KEY page_id (page_id)
) COMMENT='Pages access cache';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  location varchar(255) NOT NULL DEFAULT '' COMMENT 'Location',
  vis tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Visibility',
  post_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  modify_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  author_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Author ID#',
  editor_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Editor ID#',
  block_type varchar(255) NOT NULL DEFAULT 'RAAS\\CMS\\Block_HTML' COMMENT 'Block type',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  inherit tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Inherit',
  nat tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Translate address',
  params TEXT NULL DEFAULT NULL COMMENT 'Additional params',
  interface_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Interface ID#',
  widget_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Widget ID#',
  cache_type tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Cache type',
  cache_single_page tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Cache by single pages',
  cache_interface_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Cache interface_id',
  vis_material tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Visibility by material',
  PRIMARY KEY (id),
  KEY author_id (author_id),
  KEY editor_id (editor_id),
  KEY cache_interface_id (cache_interface_id)
) COMMENT='Site pages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_form (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID#',
  form int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Form ID#',
  PRIMARY KEY (id),
  KEY form (form)
) COMMENT='Form blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_html (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID#',
  description mediumtext COMMENT 'Text',
  wysiwyg tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'WYSIWYG editor on',
  PRIMARY KEY (id)
) COMMENT='HTML blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_material (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID#',
  material_type int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  pages_var_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Pages var name',
  rows_per_page tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Rows per page',
  sort_var_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Sorting var name',
  order_var_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Order var name',
  sort_field_default varchar(255) NOT NULL DEFAULT '' COMMENT 'Field for sorting by default',
  sort_order_default varchar(255) NOT NULL DEFAULT '' COMMENT 'Default order',
  legacy tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Redirect legacy addresses',
  PRIMARY KEY (id),
  KEY material_type (material_type)
) COMMENT='Material blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_material_filter (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID#',
  var varchar(255) NOT NULL DEFAULT '' COMMENT 'Variable',
  relation enum('=','LIKE','CONTAINED','FULLTEXT','<=','>=') NOT NULL DEFAULT '=' COMMENT 'Relation',
  field varchar(255) NOT NULL DEFAULT '' COMMENT 'Field',
  priority int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Priority',
  KEY id (id),
  KEY priority (priority)
) COMMENT='Material blocks filtering';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_material_sort (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID#',
  var varchar(255) NOT NULL DEFAULT '' COMMENT 'Variable',
  field varchar(255) NOT NULL DEFAULT '' COMMENT 'Field',
  relation enum('asc!','desc!','asc','desc') NOT NULL DEFAULT 'asc!' COMMENT 'Relation',
  priority int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Priority',
  KEY id (id),
  KEY priority (priority)
) COMMENT='Material blocks sorting';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_menu (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID#',
  menu int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Menu ID#',
  full_menu tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Full menu',
  PRIMARY KEY (id),
  KEY menu (menu)
) COMMENT='Menu blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_pages_assoc (
  block_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  page_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  priority int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (block_id,page_id),
  KEY block_id (block_id),
  KEY page_id (page_id),
  INDEX priority (priority)
) COMMENT='Blocks to pages associations';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_php (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID#',
  widget int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Widget ID#',
  PRIMARY KEY (id),
  KEY widget (widget)
) COMMENT='PHP blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID#',
  search_var_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Search var name',
  min_length tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Minimal query length',
  pages_var_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Pages var name',
  rows_per_page tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Rows per page',
  PRIMARY KEY (id)
) COMMENT='Search blocks';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search_languages_assoc (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID#',
  `language` varchar(255) NOT NULL DEFAULT '' COMMENT 'Language',
  PRIMARY KEY (id,`language`),
  KEY id (id),
  KEY `language` (`language`)
) COMMENT='Search blocks languages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search_material_types_assoc (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID#',
  material_type int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  PRIMARY KEY (id,material_type),
  KEY id (id),
  KEY material_type (material_type)
) COMMENT='Search blocks material types';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_blocks_search_pages_assoc (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID#',
  page_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  PRIMARY KEY (id,page_id),
  KEY id (id),
  KEY page_id (page_id)
) COMMENT='Search blocks pages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_data (
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
) COMMENT='Fields data';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_dictionaries (
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
) COMMENT='Dictionaries';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_feedback (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Site user ID#',
  pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Form ID#',
  page_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  material_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Material ID#',
  post_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  vis tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Visited',
  ip varchar(255) NOT NULL DEFAULT '0.0.0.0' COMMENT 'IP address',
  user_agent varchar(255) NOT NULL DEFAULT '0.0.0.0' COMMENT 'User Agent',
  PRIMARY KEY (id),
  KEY uid (uid),
  KEY pid (pid),
  KEY page_id (page_id)
) COMMENT='Feedback';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_fields (
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
) COMMENT='Fields';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_fieldgroups (
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
) COMMENT='Field groups';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_fields_form_vis (
    fid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Field ID#',
    pid INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Section ID#',

    PRIMARY KEY (fid, pid),
    INDEX (fid),
    INDEX (pid)
) COMMENT 'Fields form';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_forms (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  material_type int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Material type',
  create_feedback int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Create feedback',
  email varchar(255) NOT NULL DEFAULT '' COMMENT 'Contact e-mail',
  signature tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Require POST signature',
  antispam varchar(255) NOT NULL DEFAULT '' COMMENT 'Use anti-spam',
  antispam_field_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Anti-spam field name',
  interface_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Interface ID#',
  PRIMARY KEY (id),
  INDEX (urn)
) COMMENT='Forms';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_groups (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent group ID#',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  description text COMMENT 'Description',
  PRIMARY KEY (id),
  KEY pid (pid),
  INDEX (urn)
) COMMENT='Groups of users';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_materials (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  page_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Default page ID#',
  vis tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Visibility',
  post_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  modify_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  author_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Author ID#',
  editor_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Editor ID#',
  urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  description mediumtext COMMENT 'Description',
  meta_title varchar(255) NOT NULL DEFAULT '' COMMENT 'Meta title',
  meta_description text COMMENT 'Meta description',
  meta_keywords text COMMENT 'Meta keywords',
  h1 varchar(255) NOT NULL DEFAULT '' COMMENT 'H1 title',
  menu_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Menu name',
  breadcrumbs_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Breadcrumbs name',
  priority int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Priority',
  visit_counter int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Visit counter',
  modify_counter int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Modify counter',
  changefreq enum('','always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT '' COMMENT 'Change frequency',
  last_modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last modified',
  sitemaps_priority decimal(8,2) unsigned NOT NULL DEFAULT '0.50' COMMENT 'Sitemaps priority',
  show_from datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish from date/time',
  show_to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish to date/time',
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
) COMMENT='Translator exceptions';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_materials_pages_assoc (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Material ID#',
  pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  PRIMARY KEY (id,pid),
  KEY id (id),
  KEY pid (pid)
) COMMENT='Materials to pages associations';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_materials_affected_pages_cache (
    material_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
    page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',

    PRIMARY KEY (material_id, page_id),
    KEY (material_id),
    KEY (page_id)
) COMMENT 'Materials affected pages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_materials_votes (
    material_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material ID#',
    ip VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'IP-address',
    post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
    vote TINYINT(1) SIGNED NOT NULL DEFAULT 0 COMMENT 'Vote',

    PRIMARY KEY (material_id, ip),
    KEY (material_id),
    KEY (ip)
) COMMENT 'Materials votes log';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_material_types (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent type ID#',
  urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  global_type tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Global materials',
  PRIMARY KEY (id),
  KEY urn (urn)
) COMMENT='Material types';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_material_types_affected_pages_for_materials_cache (
    material_type_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
    page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
    nat TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'NAT',

    PRIMARY KEY (material_type_id, page_id),
    KEY (material_type_id),
    KEY (page_id),
    KEY (nat)
) COMMENT 'Material types affected pages for materials';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_material_types_affected_pages_for_self_cache (
    material_type_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Material type ID#',
    page_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Page ID#',
    nat TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'NAT',

    PRIMARY KEY (material_type_id, page_id),
    KEY (material_type_id),
    KEY (page_id),
    KEY (nat)
) COMMENT 'Material types affected pages for self (for admin)';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_menus (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  domain_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Domain ID#',
  vis tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Visibility',
  pvis tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Parent visibility',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  url varchar(255) NOT NULL DEFAULT '' COMMENT 'URL',
  page_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  inherit tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Nesting level',
  priority int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY page_id (page_id),
  INDEX (urn),
  INDEX priority (priority)
) COMMENT='Menus';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_pages (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  vis tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Visibility',
  pvis tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Parent visibility',
  post_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  modify_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  author_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Author ID#',
  editor_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Editor ID#',
  urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  response_code int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Service page response code',
  mime VARCHAR(255) NOT NULL DEFAULT 'text/html' COMMENT 'MIME-type',
  meta_title varchar(255) NOT NULL DEFAULT '' COMMENT 'Meta title',
  inherit_meta_title tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Inherit meta-title',
  meta_description text COMMENT 'Meta description',
  inherit_meta_description tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Inherit meta-description',
  meta_keywords text COMMENT 'Meta keywords',
  inherit_meta_keywords tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Inherit meta-keywords',
  h1 varchar(255) NOT NULL DEFAULT '' COMMENT 'H1 title',
  menu_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Menu name',
  breadcrumbs_name varchar(255) NOT NULL DEFAULT '' COMMENT 'Breadcrumbs name',
  template varchar(255) NOT NULL DEFAULT '' COMMENT 'Template',
  inherit_template tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Inherit meta-title',
  lang varchar(255) NOT NULL DEFAULT '' COMMENT 'Language',
  inherit_lang tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Inherit language',
  nat tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Translate address',
  priority int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Priority',
  `cache` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Cache page',
  inherit_cache tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Inherit cache page',
  visit_counter int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Visit counter',
  modify_counter int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Modify counter',
  changefreq enum('','always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT '' COMMENT 'Change frequency',
  inherit_changefreq tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Inherit change frequency',
  last_modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last modified',
  sitemaps_priority decimal(8,2) unsigned NOT NULL DEFAULT '0.50' COMMENT 'Sitemaps priority',
  inherit_sitemaps_priority tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Inherit sitemaps priority',
  cache_url VARCHAR(255) NOT NULL DEFAULT '/' COMMENT 'Cached URL',

  PRIMARY KEY (id),
  KEY pid (pid),
  KEY author_id (author_id),
  KEY editor_id (editor_id),
  KEY urn (urn),
  KEY template (template),
  INDEX priority (priority),
  INDEX cache_url (cache_url)
) COMMENT='Site pages';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_redirects (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  rx tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'RegExp',
  post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date', 
  url_from varchar(255) NOT NULL DEFAULT '' COMMENT 'URL from',
  url_to varchar(255) NOT NULL DEFAULT '' COMMENT 'URL to',
  priority int unsigned NOT NULL DEFAULT 0 COMMENT 'Priority',
  PRIMARY KEY (id),
  KEY post_date (post_date),
  KEY url_from (url_from)
) COMMENT='Redirects';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_snippets (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  post_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  modify_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  author_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Author ID#',
  editor_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Editor ID#',
  urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  description mediumtext COMMENT 'Code',
  locked tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Locked',
  PRIMARY KEY (id),
  KEY pid (pid),
  KEY author_id (author_id),
  KEY editor_id (editor_id)
) COMMENT='Snippets';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_snippet_folders (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  pid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  locked tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Locked',
  PRIMARY KEY (id),
  KEY pid (pid)
) COMMENT='Snippet folders';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_templates (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  post_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  modify_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  author_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Author ID#',
  editor_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Editor ID#',
  urn varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  description mediumtext COMMENT 'Code',
  width int(10) unsigned NOT NULL DEFAULT '640' COMMENT 'Width',
  height int(10) unsigned NOT NULL DEFAULT '1024' COMMENT 'Height',
  visual tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Template is visual',
  background int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Background attachment ID#',
  locations_info text COMMENT 'Locations info',
  PRIMARY KEY (id),
  KEY author_id (author_id),
  KEY editor_id (editor_id),
  KEY background (background),
  INDEX (urn)
) COMMENT='Templates';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_users (
  id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  login varchar(255) NOT NULL DEFAULT '' COMMENT 'Login',
  password_md5 varchar(255) NOT NULL DEFAULT '' COMMENT 'Password MD5',
  email varchar(255) NOT NULL DEFAULT '' COMMENT 'E-mail',
  post_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Registration date',
  vis tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Active',
  lang varchar(255) NOT NULL DEFAULT 'ru' COMMENT 'Language',
  `new` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'New',
  activated tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Activated',
  PRIMARY KEY (id),
  KEY pid (login),
  KEY email (email),
  KEY post_date (post_date),
  KEY vis (vis),
  KEY `new` (`new`),
  KEY activated (activated)
) COMMENT='Users';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_users_groups_assoc (
  uid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID#',
  gid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Group ID#',
  PRIMARY KEY (uid,gid),
  KEY uid (uid),
  KEY gid (gid)
) COMMENT='Users-groups associations';

CREATE TABLE IF NOT EXISTS {$DBPREFIX$}{$PACKAGENAME$}_users_social (
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID#',
  url varchar(255) NOT NULL DEFAULT '' COMMENT 'Social network page URL',
  PRIMARY KEY (uid,url),
  KEY uid (uid),
  KEY url (url)
) COMMENT='Users social networks associations';