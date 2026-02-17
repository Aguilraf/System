<?php

namespace App\Services;

use SimpleXMLElement;
use Illuminate\Support\Facades\Log;

class XmlParserService
{
    /**
     * Parse XML content and return flattened data.
     *
     * @param string $xmlContent
     * @return array
     */
    public function parse(string $xmlContent): array
    {
        if (trim($xmlContent) === '') {
            return [];
        }

        try {
            // Remove xmlns attributes
            $xmlString = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $xmlContent);
            // Remove namespace prefixes from tags (start and end tags)
            $xmlString = preg_replace('/(<\/?)(\w+):(\w+)/', '$1$3', $xmlString);
            // Remove namespace prefixes from attributes
            $xmlString = preg_replace('/\s(\w+):(\w+)=/', ' $2=', $xmlString);
            
            $xml = new SimpleXMLElement($xmlString);
            
            // Standard JSON convert is a quick way to array, but loses attributes sometimes
            // Logic: Flatten manually to keep attributes and children
            $data = $this->flatten($xml);
            
            return $data;
        } catch (\Exception $e) {
            Log::error("Error parsing XML: " . $e->getMessage());
            return [];
        }
    }

    private function flatten($element, $prefix = '')
    {
        $result = [];

        // Attributes
        foreach ($element->attributes() as $key => $value) {
            $fullKey = $prefix ? $prefix . '.' . $key : $key;
            $result[$fullKey] = (string)$value;
        }

        // Children
        $counts = [];
        foreach ($element->children() as $name => $child) {
            $counts[$name] = ($counts[$name] ?? 0) + 1;
        }

        $indices = [];
        foreach ($element->children() as $name => $child) {
            // If multiple children of same name, use index
            $isMultiple = $counts[$name] > 1;
            $index = $indices[$name] ?? 0;
            $indices[$name] = $index + 1;

            $key = $isMultiple ? "{$name}.{$index}" : $name;
            $fullKey = $prefix ? $prefix . '.' . $key : $key;

            if ($child->count() > 0 || $child->attributes()->count() > 0) {
                $result = array_merge($result, $this->flatten($child, $fullKey));
            } else {
                $result[$fullKey] = (string)$child;
            }
        }

        return $result;
    }
}
