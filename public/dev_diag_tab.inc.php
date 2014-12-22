<?php
$_RAASForm_FormTab = function(\RAAS\FormTab $FormTab) use (&$_RAASForm_Form_Tabbed, &$_RAASForm_Form_Plain, &$_RAASForm_Attrs) {
    $Item = $FormTab->Form->meta['Item'];
    $cVar = $FormTab->name . 'Counter';
    $tVar = $FormTab->name . 'Time';
    ?>
    <p style="font-weight: bold">
      <?php echo CMS\DIAGNOSTICS_COUNTER?>: <?php echo (int)$Item->$cVar?><br />
      <?php echo CMS\DIAGNOSTICS_TOTAL_TIME?>: <?php echo number_format($Item->$tVar, 3, '.', ' ')?>
    </p>
    <?php
    if (array_filter((array)$FormTab->children, function($x) { return $x instanceof \RAAS\FormTab; })) { 
        $_RAASForm_Form_Tabbed($FormTab->children);
    } else {
        $_RAASForm_Form_Plain($FormTab->children);
    }

};