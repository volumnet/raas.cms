<?php
/**
 * Виджет страницы дизайна
 */
namespace RAAS\CMS;

?>
<div class="design">
    <?php foreach ($designs as $blockData) { ?>
        <h2><?php echo htmlspecialchars($blockData['urn'])?></h2>
        <div class="design__list design-list">
          <?php foreach ($blockData['variants'] as $variantCRC32 => $variantData) {
              foreach ($variantData['design'] as $designCRC32 => $designData) { ?>
                  <div class="design-list__item design-item">
                    <a
                      href="/design/blocks/<?php echo htmlspecialchars($blockData['urn'] . '.' . $designCRC32 . '.jpg')?>"
                      class="design-item__image"
                      target="_blank"
                    >
                      <img src="/design/blocks/<?php echo htmlspecialchars($blockData['urn'] . '.' . $designCRC32 . '.jpg')?>" alt="">
                    </a>
                    <div class="design-item__text">
                      <?php echo nl2br(htmlspecialchars(implode("\n", $designData['projects'])))?>
                    </div>
                    <div class="design-item__controls">
                      <?php if ($designData['url'] ?? null) { ?>
                          <a class="btn" href="<?php echo htmlspecialchars((string)$designData['url'])?>" target="_blank">
                            <raas-icon icon="link"></raas-icon>
                          </a>
                      <?php } ?>
                      <a
                        class="btn btn-primary"
                        href="?p=cms&sub=dev&action=design&apply=<?php echo htmlspecialchars($blockData['urn'] . '.' . $variantCRC32 . '.' . $designCRC32)?>"
                        onclick="return confirm('Вы действительно хотите применить этот дизайн?');"
                      >
                        <raas-icon icon="check"></raas-icon>
                      </a>
                    </div>
                  </div>
              <?php }
          } ?>
        </div>
    <?php } ?>
</div>
