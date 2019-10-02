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
$sanitize_output = function ($text) {
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
<html lang="<?php echo htmlspecialchars($Page->lang)?>">
  <head>
    <title><?php echo ($Page->meta_title ? $Page->meta_title : $Page->name)?></title>
    <?php if ($Page->meta_keywords) { ?>
        <meta name="keywords" content="<?php echo htmlspecialchars($Page->meta_keywords)?>" />
    <?php } ?>
    <?php if ($Page->meta_description) { ?>
        <meta name="description" content="<?php echo htmlspecialchars($Page->meta_description)?>" />
    <?php } ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel='stylesheet' href='/css/application.css'>
    <link rel='stylesheet' href='/css/animate.css'>
    <link rel='stylesheet' href='/css/style.css?v=<?php echo date('Y-m-d', filemtime('css/style.css'))?>'>
    <link rel='stylesheet' href='/custom.css'>
    <script src="/js/application.js"></script>
    <script src="/js/wow.min.js"></script>
    <script>new WOW().init();</script>
    <script src="/js/jquery.jcarousel.min.js"></script>
    <script src="/js/sliders.js"></script>
    <script src="/js/setrawcookie.js"></script>
    <script src="/js/setcookie.js"></script>
    <?php if (class_exists('RAAS\CMS\Shop\Module')) { ?>
        <script src="/js/cookiecart.js"></script>
        <script src="/js/ajaxcart.js"></script>
        <script src="/js/ajaxcatalog.js"></script>
        <script src="/js/modal.js"></script>
        <script src="/js/catalog.js"></script>
    <?php } ?>
    <script src="/js/script.js?v=<?php echo date('Y-m-d', filemtime('js/script.js'))?>"></script>
    <?php if (is_file('favicon.ico')) { ?>
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
    <?php } ?>
    <?php if (HTTP::queryString()) { ?>
        <link rel="canonical" href="http<?php echo ($_SERVER['HTTPS'] == 'on' ? 's' : '')?>://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))?>">
    <?php } ?>
    <?php if ($Page->noindex || $Page->Material->noindex) { ?>
        <meta name="robots" content="noindex,nofollow" />
    <?php } ?>
    <?php echo $Page->location('head_counters')?>
  </head>
  <body class="body <?php echo !$Page->pid ? ' body_main' : ''?>" data-page-id="<?php echo (int)$Page->id?>"<?php echo $Page->Material->id ? ' data-page-material-id="' . (int)$Page->Material->id . '"' : ''?>>
    <div id="top" class="body__background-holder"<?php echo $bg->id ? ' style="background-image: url(\'/' . htmlspecialchars($bg->fileURL) . '\')"' : ''?>>
      <header class="body__header">
        <div class="container">
          <div class="body__header-inner">
            <div class="row">
              <div class="col-sm-6 body__logo">
                <div class="body__logo-inner">
                    <?php echo $Page->location('logo')?>
                </div>
              </div>
              <div class="col-sm-6 body__contacts-top">
                <div class="body__contacts-top-inner">
                    <?php echo $Page->location('contacts_top')?>
                </div>
              </div>
            </div>
          </div>
          <div class="body__menu-top-outer">
            <div class="row">
              <div class="col-sm-9 body__menu-top">
                <div class="body__menu-top-inner">
                    <?php echo $Page->location('menu_top')?>
                </div>
              </div>
              <div class="col-sm-3 body__search-form">
                <div class="body__search-form-inner">
                    <?php echo $Page->location('search_form')?>
                </div>
              </div>
            </div>
          </div>
          <div class="body__banners">
            <div class="body__banners-inner">
              <?php echo $Page->location('banners')?>
            </div>
          </div>
        </div>
      </header>
      <main class="body__main-container">
        <?php
        $leftText = $Page->location('left');
        $rightText = $Page->location('right');
        $contentText = $Page->location('content');
        if ($contentText) {
            $colspanSM = 4;
            $colspanMD = 3;
        } else {
            $colspanSM = $colspanMD = 6;
        }
        $spanSM = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanSM);
        $spanMD = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanMD);
        ?>
        <div class="body__content-outer">
          <div class="container">
            <div class="row">
                <?php if ($leftText) { ?>
                    <aside class="body__left col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                      <div class="body__left-inner">
                        <?php echo $leftText?>
                      </div>
                    </aside>
                <?php }
                if ($contentText) { ?>
                    <div class="body__content col-sm-<?php echo $spanSM?> col-md-<?php echo $spanMD?>">
                      <div class="body__content-inner">
                        <?php if (!$Page->pid) {
                            echo $contentText;
                        } else {
                            if ((count($Page->parents) + (bool)$Page->Material->id + (bool)$Page->Item->id) > 1) { ?>
                                <ol class="breadcrumb">
                                  <?php foreach ($Page->parents as $row) { ?>
                                      <li>
                                        <a href="<?php echo htmlspecialchars($row->url)?>">
                                          <?php echo htmlspecialchars($row->getBreadcrumbsName())?>
                                        </a>
                                      </li>
                                  <?php } ?>
                                  <?php if ($Page->Material->id || $Page->Item->id) { ?>
                                      <li>
                                        <a href="<?php echo htmlspecialchars($Page->url)?>">
                                          <?php echo htmlspecialchars($Page->getBreadcrumbsName())?>
                                        </a>
                                      </li>
                                  <?php } ?>
                                </ol>
                            <?php } ?>
                            <h1 class="h1">
                              <?php echo htmlspecialchars($Page->getH1())?>
                            </h1>
                            <?php echo $contentText . $Page->location('share');
                        } ?>
                      </div>
                    </div>
                <?php }
                if ($rightText) { ?>
                    <aside class="body__right col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                      <div class="body__right-inner">
                        <?php echo $rightText?>
                      </div>
                    </aside>
                <?php } ?>
            </div>
          </div>
        </div>

        <?php
        $leftText = $Page->location('left2');
        $rightText = $Page->location('right2');
        $contentText = $Page->location('content2');
        if ($contentText) {
            $colspanSM = 4;
            $colspanMD = 3;
        } else {
            $colspanSM = $colspanMD = 6;
        }
        $spanSM = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanSM);
        $spanMD = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanMD);
        if ($leftText || $contentText || $rightText) { ?>
            <div class="body__content2-outer">
              <div class="container">
                <div class="row">
                  <?php if ($leftText) { ?>
                      <aside class="body__left2 col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                        <div class="body__left2-inner">
                          <?php echo $leftText?>
                        </div>
                      </aside>
                  <?php }
                  if ($contentText) { ?>
                      <div class="body__content2 col-sm-<?php echo $spanSM?> col-md-<?php echo $spanMD?>">
                        <div class="body__content2-inner">
                          <?php echo $contentText?>
                        </div>
                      </div>
                  <?php }
                  if ($rightText) { ?>
                      <aside class="body__right2 col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                        <div class="body__right2-inner">
                          <?php echo $rightText?>
                        </div>
                      </aside>
                  <?php } ?>
                </div>
              </div>
            </div>
        <?php }

        $leftText = $Page->location('left3');
        $rightText = $Page->location('right3');
        $contentText = $Page->location('content3');
        if ($contentText) {
            $colspanSM = 4;
            $colspanMD = 3;
        } else {
            $colspanSM = $colspanMD = 6;
        }
        $spanSM = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanSM);
        $spanMD = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanMD);
        if ($leftText || $contentText || $rightText) { ?>
            <div class="body__content3-outer">
              <div class="container">
                <div class="row">
                  <?php if ($leftText) { ?>
                      <aside class="body__left3 col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                        <div class="body__left3-inner">
                          <?php echo $leftText?>
                        </div>
                      </aside>
                  <?php }
                  if ($contentText) { ?>
                      <div class="body__content3 col-sm-<?php echo $spanSM?> col-md-<?php echo $spanMD?>">
                        <div class="body__content3-inner">
                          <?php echo $contentText?>
                        </div>
                      </div>
                  <?php }
                  if ($rightText) { ?>
                      <aside class="body__right3 col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                        <div class="body__right3-inner">
                          <?php echo $rightText?>
                        </div>
                      </aside>
                  <?php } ?>
                </div>
              </div>
            </div>
        <?php }

        $leftText = $Page->location('left4');
        $rightText = $Page->location('right4');
        $contentText = $Page->location('content4');
        if ($contentText) {
            $colspanSM = 4;
            $colspanMD = 3;
        } else {
            $colspanSM = $colspanMD = 6;
        }
        $spanSM = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanSM);
        $spanMD = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanMD);
        if ($leftText || $contentText || $rightText) { ?>
            <div class="body__content4-outer">
              <div class="container">
                <div class="row">
                  <?php if ($leftText) { ?>
                      <aside class="body__left4 col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                        <div class="body__left4-inner">
                          <?php echo $leftText?>
                        </div>
                      </aside>
                  <?php }
                  if ($contentText) { ?>
                      <div class="body__content4 col-sm-<?php echo $spanSM?> col-md-<?php echo $spanMD?>">
                        <div class="body__content4-inner">
                          <?php echo $contentText?>
                        </div>
                      </div>
                  <?php }
                  if ($rightText) { ?>
                      <aside class="body__right4 col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                        <div class="body__right4-inner">
                          <?php echo $rightText?>
                        </div>
                      </aside>
                  <?php } ?>
                </div>
              </div>
            </div>
        <?php }

        $leftText = $Page->location('left5');
        $rightText = $Page->location('right5');
        $contentText = $Page->location('content5');
        if ($contentText) {
            $colspanSM = 4;
            $colspanMD = 3;
        } else {
            $colspanSM = $colspanMD = 6;
        }
        $spanSM = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanSM);
        $spanMD = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanMD);
        if ($leftText || $contentText || $rightText) { ?>
            <div class="body__content5-outer">
              <div class="container">
                <div class="row">
                  <?php if ($leftText) { ?>
                      <aside class="body__left5 col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                        <div class="body__left5-inner">
                          <?php echo $leftText?>
                        </div>
                      </aside>
                  <?php }
                  if ($contentText) { ?>
                      <div class="body__content5 col-sm-<?php echo $spanSM?> col-md-<?php echo $spanMD?>">
                        <div class="body__content5-inner">
                          <?php echo $contentText?>
                        </div>
                      </div>
                  <?php }
                  if ($rightText) { ?>
                      <aside class="body__right5 col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>">
                        <div class="body__right5-inner">
                          <?php echo $rightText?>
                        </div>
                      </aside>
                  <?php } ?>
                </div>
              </div>
            </div>
        <?php } ?>
      </main>
      <footer class="body__footer">
        <div class="container">
          <div class="body__footer-inner">
            <div class="row">
              <div class="col-sm-6 body__copyrights"><?php echo $Page->location('copyrights')?></div>
              <div class="col-sm-6 body__menu-bottom"><?php echo $Page->location('menu_bottom')?></div>
            </div>
          </div>
          <div class="body__developer">Разработка и сопровождение сайта <a href="http://volumnet.ru" target="_blank">Volume&nbsp;Networks</a></div>
        </div>
      </footer>
    </div>
    <?php
    echo $Page->location('footer_counters');
    $content = ob_get_contents();
    ob_end_clean();
    $content = $separateScripts($content);
    echo $sanitize_output($content[0] . $content[1] . $content[2]);
    ?>
  </body>
</html>
