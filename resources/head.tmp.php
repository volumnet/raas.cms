<title><?php echo htmlspecialchars($Page->meta_title ? $Page->meta_title : $Page->name)?></title>
<?php if ($Page->meta_keywords) { ?>
<meta name="keywords" content="<?php echo htmlspecialchars($Page->meta_keywords)?>" />
<?php } ?>
<?php if ($Page->meta_description) { ?>
<meta name="description" content="<?php echo htmlspecialchars($Page->meta_description)?>" />
<?php } ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width">
<link rel='stylesheet' href='/css/application.css'>
<link rel='stylesheet' href='/css/style.css'>
<!-- Core-->
<script src="/js/application.js"></script>
<script src="/js/bootstrap.carousel.swipe.js"></script>
<?php if (class_exists('RAAS\CMS\Shop\Module')) { ?>
    <script src="/js/setrawcookie.js"></script>
    <script src="/js/setcookie.js"></script>
    <script src="/js/cookiecart.js"></script>
    <script src="/js/ajaxcart.js"></script>
    <script src="/js/ajaxcatalog.js"></script>
    <script src="/js/modal.js"></script>
    <script src="/js/catalog.js"></script>
<?php } ?>
<script src="/js/script.js"></script>
<?php if (is_file('favicon.ico')) { ?>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
<?php } ?>
<?php if (\SOME\HTTP::queryString()) { ?>
    <link rel="canonical" href="http://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))?>">
<?php } ?>
<?php if ($Page->noindex || $Page->Material->noindex) { ?>
    <meta name="robots" content="noindex,nofollow" />
<?php } ?>
