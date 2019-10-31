<?php
/**
 * Основной шаблон
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

use SOME\HTTP;
// use voku\helper\HtmlMin;

$bgPage = $Page;
while (!$bgPage->background->id && $bgPage->pid) {
    $bgPage = $bgPage->parent;
}
$bg = $bgPage->background;
unset($bgPage);

/**
 * Минификация HTML
 * @param string $text Входной HTML-код
 * @return string
 */
$sanitizeOutput = function ($text) {
    // $htmlMin = new HtmlMin();
    // $htmlMin->doRemoveSpacesBetweenTags(false);
    // $htmlMin->doRemoveWhitespaceAroundTags(false);
    // $text = $htmlMin->minify($text);
    return $text;
};


/**
 * Разделение текста на общий HTML, скрипты и, возможно, стили
 * @param string $text Входной HTML
 * @return [string общий HTML, string скрипты, string стили]
 */
$separateScripts = function ($text) {
    $rx = '/\\<script.*?\\>.*?\\<\\/script\\>/umis';
    $rxStyle = '/\\<link[^\\>]*?stylesheet[^\\>]*?\\>/umis';
    $scripts = $styles = '';
    $result = $text;
    if (preg_match_all($rx, $text, $regs)) {
        foreach ($regs[0] as $i => $script) {
            if (!preg_match('/(maps.*?yandex.*constructor)/umis', $script)) {
                $scripts .= $script . "\n";
                $result = str_replace($script, '', $result);
            }
        }
    }
    // if (preg_match_all($rxStyle, $text, $regs)) {
    //     foreach ($regs[0] as $i => $style) {
    //         $styles .= $style . "\n";
    //         $result = str_replace($style, '', $result);
    //     }
    // }
    return array($result, $scripts, $styles);
};

ob_start();
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($Page->lang)?>" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# <?php echo $Page->headPrefix?>">
  <head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# <?php echo $Page->headPrefix?>">
    <title><?php echo ($Page->meta_title ? $Page->meta_title : $Page->name)?></title>
    <?php if ($Page->meta_keywords) { ?>
        <meta name="keywords" content="<?php echo htmlspecialchars($Page->meta_keywords)?>" />
    <?php } ?>
    <?php if ($Page->meta_description) { ?>
        <meta name="description" content="<?php echo htmlspecialchars($Page->meta_description)?>" />
    <?php } ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php echo $Page->headData; ?>
    <?php echo Package::i()->asset([
        '/css/application.css',
        '/css/animate.css',
        '/css/style.css'
    ])?>
    <link rel="stylesheet" href="/custom.css">
    <?php echo Package::i()->asset([
        '/js/application.js',
        '/js/wow.min.js',
    ])?>
    <script>new WOW().init();</script>
    <?php
    $assets2 = [
        '/js/jquery.jcarousel.min.js',
        '/js/sliders.js',
        '/js/setrawcookie.js',
        '/js/setcookie.js',
        '/js/vue.min.js',
        '/js/vue-w3c-valid.min.js',
    ];
    if (class_exists('RAAS\CMS\Shop\Module')) {
        $assets2 = array_merge($assets2, [
            '/js/cookiecart.js',
            '/js/ajaxcart.js',
            '/js/ajaxcatalog.js',
            '/js/modal.js',
            '/js/raas-catalog-item-mixin.vue.js',
            '/js/catalog.js',
        ]);
    }
    $assets2[] = '/js/script.js';
    echo Package::i()->asset($assets2);
    ?>
    <?php if (is_file('favicon.ico')) { ?>
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
    <?php } ?>
    <?php if (HTTP::queryString()) { ?>
        <link rel="canonical" href="http<?php echo (mb_strtolower($_SERVER['HTTPS']) == 'on' ? 's' : '')?>://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))?>">
    <?php } ?>
    <?php if ($Page->noindex || $Page->Material->noindex) { ?>
        <meta name="robots" content="noindex,nofollow" />
    <?php } ?>
    <?php echo $Page->location('head_counters')?>
  </head>
  <body class="body <?php echo !$Page->pid ? ' body_main' : ''?>" data-page-id="<?php echo (int)$Page->id?>"<?php echo $Page->Material->id ? ' data-page-material-id="' . (int)$Page->Material->id . '"' : ''?>>
    <div id="top" class="body__background-holder"<?php echo $bg->id ? ' style="background-image: url(\'/' . htmlspecialchars($bg->fileURL) . '\')"' : ''?>>
      <header class="body__header">
        <div class="body__row body__row_menu-top">
          <div class="body__container body__container_menu-top">
            <div class="body__menu-top">
              <?php echo $Page->location('menu_top')?>
            </div>
            <div class="body__socials-top">
              <?php echo $Page->location('socials_top')?>
            </div>
          </div>
        </div>
        <div class="body__row body__row_header-inner">
          <div class="body__container body__container_header-inner">
            <div class="body__logo">
              <?php echo $Page->location('logo')?>
            </div>
            <div class="body__contacts-top">
              <?php echo $Page->location('contacts_top')?>
            </div>
            <div class="body__menu-user">
              <?php echo $Page->location('menu_user')?>
            </div>
          </div>
        </div>
        <div class="body__row body__row_menu-main">
          <div class="body__container body__container_menu-main">
            <div class="body__menu-main">
              <?php echo $Page->location('menu_main')?>
            </div>
            <div class="body__search-form">
              <?php echo $Page->location('search_form')?>
            </div>
          </div>
        </div>
      </header>
      <?php if ($bannersText = $Page->location('banners')) { ?>
          <div class="body__row body__row_banners">
            <div class="body__container body__container_banners">
              <div class="body__banners">
                <?php echo $bannersText?>
              </div>
            </div>
          </div>
      <?php } ?>
      <main class="body__main">
        <?php
        // Чтобы размещения "подхватывались" шаблоном, нужно их объявить явно
        $contentLocations = [
            [
                'left' => $Page->location('left'),
                'content' => $Page->location('content'),
                'right' => $Page->location('right'),
            ],
            [
                'left' => $Page->location('left2'),
                'content' => $Page->location('content2'),
                'right' => $Page->location('right2'),
            ],
            [
                'left' => $Page->location('left3'),
                'content' => $Page->location('content3'),
                'right' => $Page->location('right3'),
            ],
            [
                'left' => $Page->location('left4'),
                'content' => $Page->location('content4'),
                'right' => $Page->location('right4'),
            ],
            [
                'left' => $Page->location('left5'),
                'content' => $Page->location('content5'),
                'right' => $Page->location('right5'),
            ],
        ];
        for ($i = 0; $i <= count($contentLocations); $i++) {
            $leftText = $contentLocations[$i]['left'];
            $rightText = $contentLocations[$i]['right'];
            $contentText = $contentLocations[$i]['content'];
            if (!$i || $leftText || $contentText || $rightText) {
                ?>
                <div class="body__row body__row_content body__row_content<?php echo ($i + 1)?>">
                  <div class="body__container body__container_content body__container_content_<?php echo ($i + 1)?>">
                    <?php if ($leftText) { ?>
                        <aside class="body__left body__left_<?php echo ($i + 1)?>">
                          <?php echo $leftText?>
                        </aside>
                    <?php }
                    if (!$i || $contentText) { ?>
                        <div class="body__content body__content_<?php echo ($i + 1)?>">
                          <?php if ($i || !$Page->pid) {
                              echo $contentText;
                          } else {
                              Snippet::importByURN('breadcrumbs')->process(['page' => $Page]);
                              ?>
                              <h1 class="h1 body__title">
                                <?php echo htmlspecialchars($Page->getH1())?>
                              </h1>
                              <?php echo $contentText . $Page->location('share');
                          } ?>
                        </div>
                    <?php }
                    if ($rightText) { ?>
                        <aside class="body__right body__right<?php echo ($i + 1)?>">
                          <?php echo $rightText?>
                        </aside>
                    <?php } ?>
                  </div>
                </div>
            <?php }
        } ?>
      </main>
      <footer class="body__footer">
        <div class="body__row body__row_footer">
          <div class="body__container body__container_footer">
            <div class="body__copyrights">
              <?php echo $Page->location('copyrights')?>
            </div>
            <div class="body__contacts-bottom">
              <?php echo $Page->location('contacts_bottom')?>
            </div>
            <div class="body__menu-bottom">
              <?php echo $Page->location('menu_bottom')?>
            </div>
            <div class="body__socials-bottom">
              <?php echo $Page->location('socials_bottom')?>
            </div>
          </div>
        </div>
        <div class="body__developer">
          Разработка и сопровождение сайта
          <a href="http://volumnet.ru" target="_blank">Volume&nbsp;Networks</a>
        </div>
      </footer>
    </div>
    <script>
    new VueW3CValid({
        el: '.body'
    });
    </script>
    <?php
    echo $Page->location('footer_counters');
    $content = ob_get_contents();
    ob_end_clean();
    $content = $separateScripts($content);
    echo $sanitizeOutput($content[0] . $content[1] . $content[2]);
    // Убрать </body> и </html> при использовании htmlMin - он закрывает их автоматически
    ?>
  </body>
</html>
