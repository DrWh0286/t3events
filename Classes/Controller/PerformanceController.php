<?php

namespace DWenzel\T3events\Controller;

/**
 * This file is part of the "Events" project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use DWenzel\T3events\Domain\Model\Dto\PerformanceDemand;
use DWenzel\T3events\Domain\Model\Dto\Search;
use DWenzel\T3events\Domain\Model\Dto\SearchFactory;
use DWenzel\T3events\Domain\Model\Performance;
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Domain\Repository\PerformanceRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use DWenzel\T3events\Utility\SettingsInterface as SI;

/**
 * Class PerformanceController
 *
 * @package DWenzel\T3events\Controller
 */
class PerformanceController extends ActionController implements FilterableControllerInterface
{
    use CategoryRepositoryTrait;
    use DemandTrait;
    use EntityNotFoundHandlerTrait;
    use FilterableControllerTrait;
    use PerformanceDemandFactoryTrait;
    use SessionTrait;
    use SettingsUtilityTrait;
    use TranslateTrait;

    public const PERFORMANCE_LIST_ACTION = 'listAction';
    public const PERFORMANCE_QUICK_MENU_ACTION = 'quickMenuAction';
    public const PERFORMANCE_SHOW_ACTION = 'showAction';
    public const SESSION_NAME_SPACE = 'performanceController';

    /**
     * performanceRepository
     *
     * @var PerformanceRepository
     */
    protected $performanceRepository;

    /**
     * genreRepository
     *
     * @var GenreRepository
     */
    protected $genreRepository;

    /**
     * venueRepository
     *
     * @var VenueRepository
     */
    protected $venueRepository;

    /**
     * eventTypeRepository
     *
     * @var EventTypeRepository
     */
    protected $eventTypeRepository;

    /**
     * TYPO3 Content Object
     *
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObject;

    protected $buttonConfiguration = [];
    private SearchFactory $searchFactory;

    /**
     * Constructor
     */
    public function __construct(PerformanceRepository $performanceRepository, GenreRepository $genreRepository, VenueRepository $venueRepository, EventTypeRepository $eventTypeRepository, SearchFactory $searchFactory)
    {
        $this->namespace = get_class($this);
        $this->performanceRepository = $performanceRepository;
        $this->genreRepository = $genreRepository;
        $this->venueRepository = $venueRepository;
        $this->eventTypeRepository = $eventTypeRepository;
        $this->searchFactory = $searchFactory;
    }

    /**
     * Returns a configuration array for buttons
     * in the form
     * [
     *   [
     *      ButtonDemand::TABLE_KEY => 'tx_t3events_domain_model_event',
     *      ButtonDemand::LABEL_KEY => 'button.listAction',
     *      ButtonDemand::ACTION_KEY => 'list',
     *      ButtonDemand::ICON_KEY => 'ext-t3events-type-default'
     *   ]
     * ]
     * Each entry in the array describes one button
     * @return array
     */
    public function getButtonConfiguration()
    {
        return $this->buttonConfiguration;
    }

    /**
     * initializes all actions
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeAction(): void
    {
        $this->settings = $this->mergeSettings();
        $this->contentObject = $this->configurationManager->getContentObject();
        if ($this->request->hasArgument(SI::OVERWRITE_DEMAND)) {
            $this->session->set(
                'tx_t3events_overwriteDemand',
                serialize($this->request->getArgument(SI::OVERWRITE_DEMAND))
            );
        }

        if ($this->request->hasArgument(SI::RESET_DEMAND)) {
            $this->session->clean();
        }
    }

    /**
     * action list
     *
     * @param array $overwriteDemand
     * @return void
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function listAction(array $overwriteDemand = null): \Psr\Http\Message\ResponseInterface
    {
        if (!$overwriteDemand) {
            if (!$this->session->has('tx_t3events_overwriteDemand') || !is_string($this->session->get('tx_t3events_overwriteDemand')) || empty($this->session->get('tx_t3events_overwriteDemand'))) {
                throw new RuntimeException('tx_t3events_overwriteDemand is not set or is empty and also no overwriteDemand is set!');
            }
            $overwriteDemand = unserialize($this->session->get('tx_t3events_overwriteDemand'), ['allowed_classes' => false]);
        }

        $demand = $this->performanceDemandFactory->createFromSettings($this->settings);
        $this->overwriteDemandObject($demand, $overwriteDemand);
        $performances = $this->performanceRepository->findDemanded($demand);

        $templateVariables = [
            'performances' => $performances,
            SI::SETTINGS => $this->settings,
            SI::OVERWRITE_DEMAND => $overwriteDemand,
            'data' => $this->contentObject->data
        ];

        $this->emitSignal(__CLASS__, self::PERFORMANCE_LIST_ACTION, $templateVariables);
        $this->view->assignMultiple($templateVariables);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \DWenzel\T3events\Domain\Model\Performance $performance
     * @return void
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     */
    public function showAction(Performance $performance): \Psr\Http\Message\ResponseInterface
    {
        $templateVariables = [
            SI::SETTINGS => $this->settings,
            'performance' => $performance
        ];

        $this->emitSignal(__CLASS__, self::PERFORMANCE_SHOW_ACTION, $templateVariables);
        $this->view->assignMultiple($templateVariables);
        return $this->htmlResponse();
    }

    /**
     * action quickMenu
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     *
     * @todo Check if removing initialize action has any impact or breaks things!
     */
    public function quickMenuAction(): \Psr\Http\Message\ResponseInterface
    {
        if (!$this->session->has('tx_t3events_overwriteDemand') || !is_string($this->session->get('tx_t3events_overwriteDemand')) || empty($this->session->get('tx_t3events_overwriteDemand'))) {
            throw new RuntimeException('tx_t3events_overwriteDemand is not set or is empty and also no overwriteDemand is set!');
        }
        $overwriteDemand = unserialize($this->session->get('tx_t3events_overwriteDemand'), ['allowed_classes' => false]);

        // get filter options from plugin
        $filterConfiguration = [
            SI::LEGACY_KEY_GENRE => $this->settings[SI::GENRES] ?? null,
            'venue' => $this->settings[SI::VENUES] ?? null,
            'eventType' => $this->settings[SI::EVENT_TYPES] ?? null,
            'category' => $this->settings['categories'] ?? null
        ];
        $filterOptions = $this->getFilterOptions($filterConfiguration);

        $templateVariables = [
            'filterOptions' => $filterOptions,
            SI::GENRES => $filterOptions[SI::GENRES] ?? null,
            SI::VENUES => $filterOptions[SI::VENUES] ?? null,
            SI::EVENT_TYPES => $filterOptions[SI::EVENT_TYPES] ?? null,
            SI::SETTINGS => $this->settings,
            SI::OVERWRITE_DEMAND => $overwriteDemand
        ];
        $this->emitSignal(__CLASS__, self::PERFORMANCE_QUICK_MENU_ACTION, $templateVariables);
        $this->view->assignMultiple(
            $templateVariables
        );

        return $this->htmlResponse();
    }

    /**
     * Create Demand from Settings
     * This method is kept for backwards compatibility only.
     *
     * @param array $settings
     * @return \DWenzel\T3events\Domain\Model\Dto\DemandInterface
     * @deprecated Use demand factory instead
     */
    protected function createDemandFromSettings($settings)
    {
        /** @var PerformanceDemand $demand */
        return $this->performanceDemandFactory->createFromSettings($settings);
    }

    public function createSearchObject($searchRequest, $settings): Search
    {
        return $this->searchFactory->get($searchRequest, $settings);
    }
}
