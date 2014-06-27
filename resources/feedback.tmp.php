<?php namespace RAAS\CMS?>
<a name="feedback"></a>
<div class="feedback">
  <form class="form-horizontal" data-role="feedback-form" action="#feedback" method="post" enctype="multipart/form-data">
    <?php eval('?' . '>' . Widget::importByURN('feedback_inner')->description)?>
  </form>
</div>
<script type="text/javascript" src="/js/jquery.form.js"></script>
<script type="text/javascript">
jQuery(function($) {
    $('form[data-role="feedback-form"]').submit(function() {
        $(this).ajaxSubmit({target: this, url: '/ajax/<?php echo $Page->urn == 'contacts' ? 'feedback' : $Page->urn?>/'});
        return false;
    });
})
</script>