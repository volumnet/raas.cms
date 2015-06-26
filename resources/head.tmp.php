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