<?php
namespace RAAS\CMS;

?>
<form action="/search/" class="search_form">
  <div class="search_form__inner">
    <input name="search_string" class="form-control" type="text" value="<?php echo htmlspecialchars($_GET['search_string'])?>" placeholder="<?php echo SITE_SEARCH?>..." />
  </div>
  <button class="search_form__button"></button>
</form> 