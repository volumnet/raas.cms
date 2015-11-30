<?php
$text = $Block->description;
$text = str_replace(' href="' . htmlspecialchars($Page->initialURL) . '"', '', $text);
echo $text;
