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

use DWenzel\T3events\Domain\Factory\Dto\PerformanceDemandFactory;
use DWenzel\T3events\Domain\Model\Dto\PerformanceDemand;
use DWenzel\T3events\Domain\Model\Dto\Search;
use DWenzel\T3events\Domain\Model\Dto\SearchFactory;
use DWenzel\T3events\Domain\Model\Performance;
use DWenzel\T3events\Domain\Repository\CategoryRepository;
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Domain\Repository\PerformanceRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Event\PerformanceControllerListActionWasExecuted;
use DWenzel\T3events\Event\PerformanceControllerQuickMenuActionWasExecuted;
use DWenzel\T3events\Event\PerformanceControllerShowActionWasExecuted;
use DWenzel\T3events\Service\FilterOptionsService;
use DWenzel\T3events\Session\SessionInterface;
use DWenzel\T3events\Utility\SettingsUtility;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Class PerformanceController
 *
 * @package DWenzel\T3events\Controller
 */
class PerformanceController extends AbstractActionController
{
    public const PERFORMANCE_LIST_ACTION = 'listAction';
    public const PERFORMANCE_QUICK_MENU_ACTION = 'quickMenuAction';
    public const PERFORMANCE_SHOW_ACTION = 'showAction';
    public const SESSION_NAME_SPACE = 'performanceController';

    /**
     * TYPO3 Content Object
     *
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObject;

    protected $buttonConfiguration = [];
    protected string $namespace = '';

    public function __construct(
        protected readonly PerformanceRepository $performanceRepository,
        protected readonly GenreRepository $genreRepository,
        protected readonly VenueRepository $venueRepository,
        protected readonly EventTypeRepository $eventTypeRepository,
        protected readonly SearchFactory $searchFactory,
        protected readonly FilterOptionsService $filterOptionsService,
        protected readonly CategoryRepository $categoryRepository,
        protected readonly PerformanceDemandFactory $performanceDemandFactory,
        protected readonly SessionInterface $session,
        protected readonly SettingsUtility $settingsUtility
    ) {
        $this->namespace = get_class($this);
        $this->session->setNamespace($this->namespace);
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
        $this->settings = $this->settingsUtility->mergeSettings($this->settings, $this->actionMethodName, $this);

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
    public function listAction(array $overwriteDemand = null): ResponseInterface
    {
        if (!$overwriteDemand) {
            if (!$this->session->has('tx_t3events_overwriteDemand') || !is_string($this->session->get('tx_t3events_overwriteDemand')) || empty($this->session->get('tx_t3events_overwriteDemand'))) {
                throw new RuntimeException('tx_t3events_overwriteDemand is not set or is empty and also no overwriteDemand is set!');
            }
            $overwriteDemand = unserialize($this->session->get('tx_t3events_overwriteDemand'), ['allowed_classes' => false]);
        }

        $demand = $this->performanceDemandFactory->createFromSettings($this->settings);
        $demand->overwriteDemandObject($overwriteDemand, $this->settings);
        $performances = $this->performanceRepository->findDemanded($demand);

        $templateVariables = [
            'performances' => $performances,
            SI::SETTINGS => $this->settings,
            SI::OVERWRITE_DEMAND => $overwriteDemand,
            'data' => $this->contentObject->data
        ];

        /** @var PerformanceControllerListActionWasExecuted $performanceControllerListActionWasExecuted */
        $performanceControllerListActionWasExecuted = $this->eventDispatcher->dispatch(
            new PerformanceControllerListActionWasExecuted($templateVariables)
        );

        $this->view->assignMultiple($performanceControllerListActionWasExecuted->getTemplateVariables());
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
    public function showAction(Performance $performance): ResponseInterface
    {
        $templateVariables = [
            SI::SETTINGS => $this->settings,
            'performance' => $performance
        ];

        /** @var PerformanceControllerShowActionWasExecuted $performanceControllerShowActionWasExecuted */
        $performanceControllerShowActionWasExecuted = $this->eventDispatcher->dispatch(
            new PerformanceControllerShowActionWasExecuted($templateVariables)
        );

        $this->view->assignMultiple($performanceControllerShowActionWasExecuted->getTemplateVariables());
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
    public function quickMenuAction(): ResponseInterface
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
        $filterOptions = $this->filterOptionsService->getFilterOptions($filterConfiguration);

        $templateVariables = [
            'filterOptions' => $filterOptions,
            SI::GENRES => $filterOptions[SI::GENRES] ?? null,
            SI::VENUES => $filterOptions[SI::VENUES] ?? null,
            SI::EVENT_TYPES => $filterOptions[SI::EVENT_TYPES] ?? null,
            SI::SETTINGS => $this->settings,
            SI::OVERWRITE_DEMAND => $overwriteDemand
        ];

        /** @var PerformanceControllerQuickMenuActionWasExecuted $performanceControllerQuickMenuActionWasExecuted */
        $performanceControllerQuickMenuActionWasExecuted = $this->eventDispatcher->dispatch(
            new PerformanceControllerQuickMenuActionWasExecuted($templateVariables)
        );

        $this->view->assignMultiple(
            $performanceControllerQuickMenuActionWasExecuted->getTemplateVariables()
        );

        return $this->htmlResponse();
    }

    /**
     * @internal Only for testing purpose!
     */
    public function setView(ViewInterface $view): void
    {
        if (!$this->view instanceof ViewInterface) {
            $this->view = $view;
        }
    }

    public function getView(): ViewInterface
    {
        return $this->view;
    }

    /**
     * @internal Only for testing purpose!
     */
    public function setRequest(RequestInterface $request): void
    {
        if (!$this->request instanceof RequestInterface) {
            $this->request = $request;
        }
    }

    /**
     * @internal Only for testing purpose!
     */
    public function overrideSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
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
}
