<?php

namespace Wpae\App\Service;


use Wpae\App\Service\SnippetParser;

class CombineFields
{
    const DOUBLEQUOTES = "**DOUBLEQUOTES**";

    /** @var  SnippetParser */
    private $snippetParset;

    public function __construct()
    {
        $this->snippetParset = new SnippetParser();
    }

    /**
     * @param $functions
     * @param $combineMultipleFieldsValue
     * @param $articleData
     * @return string
     * @internal param $snippetParser
     */
    public static function prepareMultipleFieldsValue($functions, $combineMultipleFieldsValue, $articleData)
    {


        $combineFields = new CombineFields();

        foreach ($functions as $key => $function) {
            if (!empty($function)) {

                $originalFunction = $function;

                $function = str_replace('**OPENARR**', '[', $function);
                $function = str_replace('**CLOSEARR**', ']', $function);

                $combineMultipleFieldsValue = str_replace('[' . $originalFunction. ']', eval('return '.$function.';'), $combineMultipleFieldsValue);

            }
        }

        foreach ($articleData as $key => $vl) {
            $combineMultipleFieldsValue = str_replace('{' . $key . '}', str_replace(self::DOUBLEQUOTES, "\"", $vl), $combineMultipleFieldsValue);
        }

        $snippets = $combineFields->snippetParset->parseSnippets($combineMultipleFieldsValue);

        // Replace empty snippets with empty string
        foreach ($snippets as $snippet) {
            $combineMultipleFieldsValue = str_replace('{'.$snippet.'}', '', $combineMultipleFieldsValue);
        }
        return $combineMultipleFieldsValue;
    }
}