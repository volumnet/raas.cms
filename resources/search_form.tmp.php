<?php
namespace RAAS\CMS;

?>
<form action="/search/" class="search-form">
  <div class="search-form__inner">
    <input name="search_string" class="search-form__input form-control" type="text" value="<?php echo htmlspecialchars($_GET['search_string'])?>" placeholder="<?php echo SITE_SEARCH?>..." />
  </div>
  <button class="search-form__button"></button>
</form>
