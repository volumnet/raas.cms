<?php
/**
 * Тестовый препроцессор
 */
namespace RAAS\CMS;

class PreprocessorMock extends FilesProcessorInterface
{
    public function process(array $files = [])
    {
        $GLOBALS["preprocessorData"] = $files;
    }
}
