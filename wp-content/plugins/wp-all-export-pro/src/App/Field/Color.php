<?php

namespace Wpae\App\Field;


class Color extends Field
{
    const SECTION = 'detailedInformation';

    public function getValue($snippetData)
    {
        $detailedInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if($detailedInformationData['color'] == self::SELECT_FROM_WOOCOMMERCE_PRODUCT_ATTRIBUTES) {

            if(isset($detailedInformationData['colorAttribute'])) {
                $colorAttribute = $detailedInformationData['colorAttribute'];
                return $this->replaceSnippetsInValue($colorAttribute, $snippetData);
            } else {
                return '';
            }

        } else if($detailedInformationData['color'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($detailedInformationData['colorCV'], $snippetData);
        } else {
            throw new \Exception('Unknown vale '.$detailedInformationData['color'].' for field color');
        }
    }

    public function getFieldName()
    {
        return 'color';
    }
}