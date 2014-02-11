<title><?php echo htmlspecialchars($Page->meta_title ? $Page->meta_title : $Page->name)?></title>
<?php if ($Page->meta_keywords) { ?>
<meta name="keywords" content="<?php echo htmlspecialchars($Page->meta_keywords)?>" />
<?php } ?>
<?php if ($Page->meta_description) { ?>
<meta name="description" content="<?php echo htmlspecialchars($Page->meta_description)?>" />
<?php } ?>
<link rel='stylesheet' href='/css/application.css'>
<link type="text/css" href="/system/public/jquery-ui/css/redmond/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
<link type="text/css" href="/system/public/timepicker/jquery-ui-timepicker-addon.css" rel="stylesheet" />
<link type="text/css" href="/system/public/colorpicker/css/colorpicker.css" rel="stylesheet" />
<!-- Core-->
<script src="/javascripts/libraries/jquery.min.js"></script>
<script src="/javascripts/libraries/bootstrap.min.js"></script>
<script type="text/javascript" src="/system/public/jquery-ui/js/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="/system/public/modernizr.js"></script>
<script type="text/javascript" src="/system/public/jquery-ui/development-bundle/ui/i18n/jquery.ui.datepicker-<?php echo $VIEW->language?>.js "></script>
<script type="text/javascript" src="/system/public/timepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="/system/public/timepicker/jquery-ui-timepicker-addon-i18n.js"></script>
<script type="text/javascript">$.timepicker.setDefaults($.timepicker.regional['ru']);</script>
<script type="text/javascript" src="/system/public/colorpicker/js/colorpicker.js"></script>
<script src="/system/public/jquery.raas.js"></script>
<script src="/javascripts/script.js"></script>
<!-- Polyfills-->
<!--[if lt IE 9]><script src="/javascripts/libraries/ielt9.js"></script><![endif]-->
<!-- Site specific-->
<link rel="stylesheet" href="/pretty_photo/css/prettyPhoto.css" type="text/css" media="screen" />
<script src="/pretty_photo/js/jquery.prettyPhoto.js" type="text/javascript"></script>
<script type="text/javascript">
  $(document).ready(function(){
    var arr = [];
    $('a').each(function() {
      if (/\.(jpg|jpeg|pjpeg|png|gif)$/i.test($(this).attr('href'))) {
        if (!$(this).attr('title')) {
          $(this).attr('title', '');
        }
        arr.push(this);
      }
    });
    if (arr.length) {
      $(arr).prettyPhoto();
    }
  }); 
</script>