<?php
/**
 * Тестовый постпроцессор
 */
namespace RAAS\CMS;

class PostprocessorMock extends FilesProcessorInterface
{
    public function process(array $files = [])
    {
        $GLOBALS["postprocessorData"] = $files;
    }
}
