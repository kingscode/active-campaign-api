<?php

namespace Kingscode\ActiveCampaignApi\Helper;

class CustomFieldParser
{
    public static function parse($fieldId, $value): array
    {
        if(empty($value)){
            return [];
        }
        return [
            'field' => $fieldId,
            'value' => $value,
        ];
    }

    public static function bulkParse($fieldId, $value): array
    {
        if(empty($value)){
            return [];
        }
        return [
            'id' => $fieldId,
            'value' => $value,
        ];
    }
}
