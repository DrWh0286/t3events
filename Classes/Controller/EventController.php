<?php

namespace DWenzel\T3events\Controller;

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

use DWenzel\T3events\Domain\Model\Dto\Search;
use DWenzel\T3events\Domain\Model\Dto\SearchFactory;
use DWenzel\T3events\Domain\Model\Event;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use RuntimeException;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Class EventController
 *
 * @package DWenzel\T3events\Controller
 */
class EventController extends ActionController
{
    use DemandTrait, EventDemandFactoryTrait,
        EventRepositoryTrait, EntityNotFoundHandlerTrait,
        EventTypeRepositoryTrait, FilterableControllerTrait,
        GenreRepositoryTrait, SessionTrait,
        SettingsUtilityTrait, TranslateTrait;

    private SearchFactory $searchFactory;

    public function __construct(private readonly VenueRepository $venueRepository, Dispatcher $dispatcher, SearchFactory $searchFactory)
    {
        $this->signalSlotDispatcher = $dispatcher;
        $this->searchFactory = $searchFactory;
    }

    const EVENT_QUICK_MENU_ACTION = 'quickMenuAction';
    const EVENT_LIST_ACTION = 'listAction';
    const EVENT_SHOW_ACTION = 'showAction';

    /**
     * initializes all actions
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeAction(): void
    {
        $this->settings = $this->mergeSettings();
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
    public function listAction($overwriteDemand = null): \Psr\Http\Message\ResponseInterface
    {
        if (!$overwriteDemand) {
            if (!$this->session->has('tx_t3events_overwriteDemand') || !is_string($this->session->get('tx_t3events_overwriteDemand')) || empty($this->session->get('tx_t3events_overwriteDemand'))) {
                throw new RuntimeException('tx_t3events_overwriteDemand is not set or is empty and also no overwriteDemand is set!');
            }
            $overwriteDemand = unserialize($this->session->get('tx_t3events_overwriteDemand'), ['allowed_classes' => false]);
        }

        $demand = $this->eventDemandFactory->createFromSettings($this->settings);
        $this->overwriteDemandObject($demand, $overwriteDemand);
        $events = $this->eventRepository->findDemanded($demand);

        /** @var QueryResultInterface $events */
        if (
            !$events->count()
            && !$this->settings['hideIfEmptyResult']
        ) {
            $this->addFlashMessage(
                $this->translate('tx_t3events.noEventsForSelectionMessage'),
                $this->translate('tx_t3events.noEventsForSelectionTitle'),
                FlashMessage::WARNING
            );
        }

        $templateVariables = [
            'events' => $events,
            'demand' => $demand,
            SI::SETTINGS => $this->settings,
            SI::OVERWRITE_DEMAND => $overwriteDemand,
            'data' => $this->configurationManager->getContentObject()->data
        ];

        $this->emitSignal(__CLASS__, self::EVENT_LIST_ACTION, $templateVariables);
        $this->view->assignMultiple($templateVariables);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \DWenzel\T3events\Domain\Model\Event $event
     * @return void
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function showAction(Event $event): \Psr\Http\Message\ResponseInterface
    {
        $templateVariables = [
            SI::SETTINGS => $this->settings,
            'event' => $event
        ];
        $this->emitSignal(__CLASS__, self::EVENT_SHOW_ACTION, $templateVariables);
        $this->view->assignMultiple($templateVariables);
        return $this->htmlResponse();
    }

    /**
     * action quickMenu
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     *
     * @todo Check, if removed initializeQuickMenuAction() breaks something.
     */
    public function quickMenuAction(): \Psr\Http\Message\ResponseInterface
    {
        // get session data
        $overwriteDemand = unserialize($this->session->get('tx_t3events_overwriteDemand'), ['allowed_classes' => false]);

        // get filter options from plugin
        $genres = $this->genreRepository->findMultipleByUid($this->settings[SI::GENRES], 'title');
        $venues = $this->venueRepository->findMultipleByUid($this->settings[SI::VENUES], 'title');
        $eventTypes = $this->eventTypeRepository->findMultipleByUid($this->settings[SI::EVENT_TYPES], 'title');

        $templateVariables = [
            SI::GENRES => $genres,
            SI::VENUES => $venues,
            SI::EVENT_TYPES => $eventTypes,
            SI::SETTINGS => $this->settings,
            SI::OVERWRITE_DEMAND => $overwriteDemand
        ];

        $this->emitSignal(__CLASS__, self::EVENT_QUICK_MENU_ACTION, $templateVariables);
        $this->view->assignMultiple(
            $templateVariables
        );
        return $this->htmlResponse();
    }

    public function createSearchObject($searchRequest, $settings): Search
    {
        return $this->searchFactory->get($searchRequest, $settings);
    }
}
