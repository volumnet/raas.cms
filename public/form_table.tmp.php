<?php 
include $VIEW->tmp($Form->template);
if ($Item->id) { 
    include $VIEW->tmp($Table->template);
}