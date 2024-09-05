<?php

declare(strict_types=1);

namespace DWenzel\T3events\Service;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class TranslationService
{
    /**
     * Translate a given key
     *
     * @param string $key
     * @param string $extension
     * @param array|null $arguments
     * @codeCoverageIgnore
     * @return string
     */
    public function translate(string $key, string $extension = 't3events', array $arguments = null): string
    {
        $translatedString = LocalizationUtility::translate($key, $extension, $arguments);
        if (is_null($translatedString)) {
            return $key;
        } else {
            return $translatedString;
        }
    }
}
