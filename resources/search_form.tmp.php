<?php
/**
 * Виджет блока "{{WIDGET_NAME}}"
 * @param Block_PHP $Block Текущий блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

?>
<form action="/search/" class="{{WIDGET_CSS_CLASSNAME}}">
  <div class="{{WIDGET_CSS_CLASSNAME}}__inner">
    <input name="search_string" class="{{WIDGET_CSS_CLASSNAME}}__input" type="text" value="<?php echo htmlspecialchars($_GET['search_string'])?>" placeholder="<?php echo SITE_SEARCH?>..." />
  </div>
  <button class="{{WIDGET_CSS_CLASSNAME}}__button"></button>
</form>
