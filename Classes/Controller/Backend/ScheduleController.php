<?php

namespace DWenzel\T3events\Controller\Backend;

use DWenzel\T3events\Controller\ModuleDataTrait;
use DWenzel\T3events\Controller\PerformanceController;
use DWenzel\T3events\Event\ScheduleControllerListActionWasExecuted;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class ScheduleController
 */
class ScheduleController extends PerformanceController
{
    use ModuleDataTrait;
    use FormTrait;

    /**
     * Load and persist module data
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return void
     * @throws \Exception
     */
    public function processRequest(RequestInterface $request): ResponseInterface
    {
        $this->moduleData = $this->moduleDataStorageService->loadModuleData($this->getModuleKey());

        $response = parent::processRequest($request);
        $this->moduleDataStorageService->persistModuleData($this->moduleData, $this->getModuleKey());

        return $response;
    }

    /**
     * action list
     *
     * @param array $overwriteDemand
     * @return void
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function listAction(array $overwriteDemand = null): \Psr\Http\Message\ResponseInterface
    {
        $demand = $this->performanceDemandFactory->createFromSettings($this->settings);
        $filterSettings = $this->settings['filter'] ?? [];
        $filterOptions = $this->filterOptionsService->getFilterOptions($filterSettings);

        if ($overwriteDemand === null) {
            $overwriteDemand = $this->moduleData->getOverwriteDemand();
        } else {
            $this->moduleData->setOverwriteDemand($overwriteDemand);
        }

        $demand->overwriteDemandObject($overwriteDemand, $this->settings);

        $performances =  $this->performanceRepository->findDemanded($demand);

        $itemsPerPage = $this->settings['paginate']['itemsPerPage'] ?? 10;
        $maximumLinks = 5;
        $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
        $paginator = new \TYPO3\CMS\Extbase\Pagination\QueryResultPaginator($performances, $currentPage, $itemsPerPage);
        $pagination = new \DWenzel\T3events\NumberedPagination($paginator, $maximumLinks);

        $templateVariables = [
            'performances' => $performances,
            SI::OVERWRITE_DEMAND => $overwriteDemand,
            'demand' => $demand,
            SI::SETTINGS => $this->settings,
            'filterOptions' => $filterOptions,
            SI::MODULE => SI::ROUTE_SCHEDULE_MODULE,
            'pagination' => [
                'paginator' => $paginator,
                'pagination' => $pagination,
            ]
        ];

        /** @var ScheduleControllerListActionWasExecuted $performanceControllerShowActionWasExecuted */
        $performanceControllerShowActionWasExecuted = $this->eventDispatcher->dispatch(
            new ScheduleControllerListActionWasExecuted($templateVariables)
        );

        $this->view->assignMultiple($performanceControllerShowActionWasExecuted->getTemplateVariables());
        return $this->htmlResponse();
    }

    public function getModuleKey(): string
    {
        return 't3events_performance';
    }
}
