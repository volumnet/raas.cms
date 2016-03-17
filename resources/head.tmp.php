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
<script src="/js/script.js"></script>
<?php if (\SOME\HTTP::queryString()) { ?>
    <link rel="canonical" href="http://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))?>">
<?php } ?>
<?php if ($Page->noindex || $Page->Material->noindex) { ?>
    <meta name="robots" content="noindex,nofollow" /> 
<?php } ?>