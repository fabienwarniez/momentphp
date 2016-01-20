#!/usr/bin/env php
<?php

$directory = "../../moment/locale/";

$directoryHandler = opendir($directory);

$result = [];

while (false !== ($filename = readdir($directoryHandler))) {
    if (strpos($filename, '.js') === false) {
        echo sprintf("Not a js file: %s\n", $filename);
        continue;
    }
    echo sprintf("Processing %s...\n", $filename);

    $content = file_get_contents(sprintf("%s%s", $directory, $filename));
    $pattern = "/relativeTime ?: {\s((?:\s*[a-zA-Z]+ ?: '.+',?)+)/";
    $matches = [];
    preg_match($pattern, $content, $matches);

    $narrowedDownContent = $matches[1];
    $keyPattern = "/([a-zA-Z]+) ?: /";
    $matches = [];
    preg_match_all($keyPattern, $narrowedDownContent, $matches);
    $keys = $matches[1];

    $valuePattern = "/ ?: '(.+)'/";
    $matches = [];
    preg_match_all($valuePattern, $narrowedDownContent, $matches);
    $values = $matches[1];

    if (count($keys) != 13 || count($values) != 13) {
        echo sprintf("Unmatched characters in file: %s, keys: %d, values: %d\n", $filename, count($keys), count($values));
        continue;
    }

    $mergedArray = [];
    for ($i = 0; $i < 13; $i++) {
        $mergedArray[$keys[$i]] = $values[$i];
    }

    $locale = substr($filename, 0, strlen($filename) - 3);

    $result[$locale] = $mergedArray;
}

$template = '<?php

namespace FabienWarniez\HowLongAgoBundle\Service;

/**
 * This class is auto-generated, modify the script that generates it if needed.
 *
 * @author  Fabien Warniez <fabien@warniez.com>
 * @package FabienWarniez\HowLongAgoBundle\Service
 */
class Translations
{
    /**
     * @param string $locale The locale.
     * @param string $key    The translation key.
     *
     * @return string|null
     */
    public static function getTranslation($locale, $key)
    {
        $translations = [
%s
        ];

        return isset($translations[$locale][$key]) ? $translations[$locale][$key] : null;
    }
}
';

$formattedTranslationsArray = [];
foreach ($result as $locale => $translations) {
    $formattedTranslationArray = [];
    foreach ($translations as $translationKey => $translation) {
        $formattedTranslationArray[] = sprintf("                '%s' => '%s'", $translationKey, $translation);
    }
    $formattedTranslationsArray[] = sprintf("            '%s' => %s", $locale, "[\n" . implode(",\n", $formattedTranslationArray) . "\n            ]");
}

$fileContent = sprintf($template, implode(",\n", $formattedTranslationsArray));

file_put_contents('../Service/Translations.php', $fileContent);

echo "Successfully wrote file.\n";
