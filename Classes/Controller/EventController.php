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

use DWenzel\T3events\Domain\Factory\Dto\EventDemandFactory;
use DWenzel\T3events\Domain\Model\Event;
use DWenzel\T3events\Domain\Repository\EventRepository;
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Event\EventControllerListActionWasExecuted;
use DWenzel\T3events\Event\EventControllerQuickMenuActionWasExecuted;
use DWenzel\T3events\Event\EventControllerShowActionWasExecuted;
use DWenzel\T3events\Service\TranslationService;
use DWenzel\T3events\Session\SessionInterface;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use DWenzel\T3events\Utility\SettingsUtility;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class EventController
 *
 * @package DWenzel\T3events\Controller
 */
class EventController extends AbstractActionController
{
    public function __construct(
        private readonly VenueRepository $venueRepository,
        private readonly EventDemandFactory $eventDemandFactory,
        private readonly EventRepository $eventRepository,
        private readonly GenreRepository $genreRepository,
        private readonly EventTypeRepository $eventTypeRepository,
        private readonly SessionInterface $session,
        private readonly TranslationService $translationService,
        private readonly SettingsUtility $settingsUtility
    ) {
        // @todo: Check if the following line is neccessary:
        $this->session->setNamespace(self::class);
    }

    /**
     * initializes all actions
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeAction(): void
    {
        $this->settings = $this->settingsUtility->mergeSettings($this->settings, $this->actionMethodName, $this);
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
    public function listAction($overwriteDemand = null): ResponseInterface
    {
        $demand = $this->eventDemandFactory->createFromSettings($this->settings);
        $demand->overwriteDemandObject($overwriteDemand, $this->settings);

        $events = $this->eventRepository->findDemanded($demand);

        /** @var QueryResultInterface $events */
        if (
            !$events->count()
            && !$this->settings['hideIfEmptyResult']
        ) {
            $this->addFlashMessage(
                $this->translationService->translate('tx_t3events.noEventsForSelectionMessage'),
                $this->translationService->translate('tx_t3events.noEventsForSelectionTitle'),
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::WARNING
            );
        }

        $templateVariables = [
            'events' => $events,
            'demand' => $demand,
            SI::SETTINGS => $this->settings,
            SI::OVERWRITE_DEMAND => $overwriteDemand,
            'data' => $this->request->getAttribute('currentContentObject')->data
        ];

        /** @var EventControllerListActionWasExecuted $eventControllerListActionWasExecuted */
        $eventControllerListActionWasExecuted = $this->eventDispatcher->dispatch(new EventControllerListActionWasExecuted($templateVariables));

        $this->view->assignMultiple($eventControllerListActionWasExecuted->getTemplateVariables());
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
    public function showAction(Event $event): ResponseInterface
    {
        $templateVariables = [
            SI::SETTINGS => $this->settings,
            'event' => $event
        ];

        /** @var EventControllerShowActionWasExecuted $eventControllerShowActionWasExecuted */
        $eventControllerShowActionWasExecuted = $this->eventDispatcher->dispatch(new EventControllerShowActionWasExecuted($templateVariables));

        $this->view->assignMultiple($eventControllerShowActionWasExecuted->getTemplateVariables());
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
    public function quickMenuAction(): ResponseInterface
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

        /** @var EventControllerQuickMenuActionWasExecuted $eventControllerQuickMenuActionWasExecuted */
        $eventControllerQuickMenuActionWasExecuted = $this->eventDispatcher->dispatch(new EventControllerQuickMenuActionWasExecuted($templateVariables));
        $this->view->assignMultiple(
            $eventControllerQuickMenuActionWasExecuted->getTemplateVariables()
        );
        return $this->htmlResponse();
    }
}
