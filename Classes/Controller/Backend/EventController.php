<?php

namespace DWenzel\T3events\Controller\Backend;

/**
 * This file is part of the TYPO3 CMS project.
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

use DWenzel\T3events\Controller\ModuleDataTrait;
use DWenzel\T3events\Controller\NotificationRepositoryTrait;
use DWenzel\T3events\Controller\NotificationServiceTrait;
use DWenzel\T3events\Domain\Factory\Dto\EventDemandFactory;
use DWenzel\T3events\Domain\Model\Dto\ButtonDemand;
use DWenzel\T3events\Domain\Model\Dto\Search;
use DWenzel\T3events\Domain\Model\Dto\SearchFactory;
use DWenzel\T3events\Domain\Repository\AudienceRepository;
use DWenzel\T3events\Domain\Repository\CompanyRepository;
use DWenzel\T3events\Domain\Repository\EventRepository;
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Event\BackendEventControllerListActionWasExecuted;
use DWenzel\T3events\Service\FilterOptionsService;
use DWenzel\T3events\Service\ModuleDataStorageService;
use DWenzel\T3events\Service\TranslationService;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use DWenzel\T3events\Utility\SettingsUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class EventController
 */
class EventController extends ActionController
{
    use BackendViewTrait;
    use FormTrait;
    use ModuleDataTrait;
    use NotificationRepositoryTrait;
    use NotificationServiceTrait;

    public const EXTENSION_KEY = 't3events';

    protected $buttonConfiguration = [
        [
            ButtonDemand::TABLE_KEY => SI::TABLE_EVENTS,
            ButtonDemand::LABEL_KEY => 'button.newAction.event',
            ButtonDemand::ACTION_KEY => 'new',
            ButtonDemand::ICON_KEY => 'ext-t3events-event',
            ButtonDemand::OVERLAY_KEY => 'overlay-new',
            ButtonDemand::ICON_SIZE_KEY => Icon::SIZE_SMALL
        ]
    ];

    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly VenueRepository $venueRepository,
        private readonly AudienceRepository $audienceRepository,
        private readonly SearchFactory $searchFactory,
        private readonly CompanyRepository $companyRepository,
        private readonly EventDemandFactory $eventDemandFactory,
        private readonly EventRepository $eventRepository,
        private readonly EventTypeRepository $eventTypeRepository,
        private readonly GenreRepository $genreRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
        private readonly TranslationService $translationService,
        private readonly FilterOptionsService $filterOptionsService,
        private readonly SettingsUtility $settingsUtility,
        protected ModuleDataStorageService $moduleDataStorageService
    ) {
    }

    /**
     * Load and persist module data
     */
    public function processRequest(RequestInterface $request): ResponseInterface
    {
        $this->moduleData = $this->moduleDataStorageService->loadModuleData($this->getModuleKey());

        try {
            $response = parent::processRequest($request);
            $this->moduleDataStorageService->persistModuleData($this->moduleData, $this->getModuleKey());
            // @todo: Check if this still works, because The StopActionException is deprecated.
        } catch (StopActionException $stopActionException) {
            $this->moduleDataStorageService->persistModuleData($this->moduleData, $this->getModuleKey());
            throw $stopActionException;
        }

        return $response;
    }

    /**
     * @return void
     */
    public function initializeNewAction(): void
    {

        $configuration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        if (!empty($configuration[SI::PERSISTENCE][SI::STORAGE_PID])) {
            $this->pageUid = $configuration[SI::PERSISTENCE][SI::STORAGE_PID];
        }

        if (!empty($configuration[SI::SETTINGS][SI::PERSISTENCE][SI::STORAGE_PID])) {
            $this->pageUid = $configuration[SI::SETTINGS][SI::PERSISTENCE][SI::STORAGE_PID];
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
    public function listAction($overwriteDemand = null): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $demand = $this->eventDemandFactory->createFromSettings($this->settings);

        if ($overwriteDemand === null) {
            $overwriteDemand = $this->moduleData->getOverwriteDemand();
        } else {
            $this->moduleData->setOverwriteDemand($overwriteDemand);
        }

        $demand->overwriteDemandObject($overwriteDemand, $this->settings);
        $this->moduleData->setDemand($demand);

        $events = $this->eventRepository->findDemanded($demand);

        if (($events instanceof QueryResultInterface && !$events->count())
            || !count($events)
        ) {
            $this->addFlashMessage(
                $this->translationService->translate('message.noEventFound.text'),
                $this->translationService->translate('message.noEventFound.title'),
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::WARNING
            );
        }

        $configuration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        $templateVariables = [
            SI::EVENTS => $events,
            SI::DEMAND => $demand,
            SI::OVERWRITE_DEMAND => $overwriteDemand,
            'filterOptions' => $this->filterOptionsService->getFilterOptions($this->settings[SI::FILTER] ?? []),
            SI::STORAGE_PID => $configuration[SI::PERSISTENCE][SI::STORAGE_PID] ?? null,
            SI::SETTINGS => $this->settings,
            SI::MODULE => SI::ROUTE_EVENT_MODULE
        ];

        /** @var BackendEventControllerListActionWasExecuted $eventControllerListActionWasCalled */
        $eventControllerListActionWasCalled = $this->eventDispatcher->dispatch(new BackendEventControllerListActionWasExecuted($templateVariables));

        $this->view->assignMultiple($eventControllerListActionWasCalled->getTemplateVariables());
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    /**
     * Redirect to new record form
     */
    public function newAction(): ResponseInterface
    {
        return $this->redirectToCreateNewRecord(SI::TABLE_EVENTS);
    }

    /**
     * @return ConfigurationManagerInterface
     */
    public function getConfigurationManager()
    {
        return $this->configurationManager;
    }

    public function createSearchObject($searchRequest, $settings): Search
    {
        return $this->searchFactory->get($searchRequest, $settings);
    }

    public function overrideSettings(array $settings = []): void
    {
        $this->settings = $settings;
    }
}
