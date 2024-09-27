<?php

declare(strict_types=1);

namespace DWenzel\T3events\Service;

use DWenzel\T3events\Factory\DemandedRepositoryFactory;
use DWenzel\T3events\Factory\NoDemandedRepositoryFoundForKeyException;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FilterOptionsService
{
    public function __construct(
        private readonly TranslationService $translationService,
        private readonly DemandedRepositoryFactory $demandedRepositoryFactory
    )
    {
    }

    public function getFilterOptions(array $settings): array
    {
        $filterOptions = [];

        foreach ($settings as $key => $value) {

            try {
                $repository = $this->demandedRepositoryFactory->getDemandedRepositoryImplementationByKey($key);

                if (!empty($value)) {
                    $result = $repository->findMultipleByUid($value, 'title');
                } else {
                    $result = $repository->findAll();
                }
                $filterOptions[$key . 's'] = $result;

            } catch (NoDemandedRepositoryFoundForKeyException) {
                // Log this - what should be done? Configuration is wrong in this case...
            }

            if ($key === 'periods') {
                $periodOptions = [];
                $periodEntries = [SI::FUTURE_ONLY, SI::PAST_ONLY, SI::ALL, SI::SPECIFIC];

                if (!empty($value)) {
                    $periodEntries = GeneralUtility::trimExplode(',', $value, true);
                }

                foreach ($periodEntries as $entry) {
                    $period = new \stdClass();
                    $period->key = $entry;
                    $period->value = $this->translationService->translate('label.period.' . $entry, 't3events');
                    $periodOptions[] = $period;
                }
                $filterOptions['periods'] = $periodOptions;
            }
        }

        return $filterOptions;
    }
}
