<?php

namespace DWenzel\T3events\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use DWenzel\T3events\Domain\Model\Dto\ModuleData;
use DWenzel\T3events\Service\ModuleDataStorageService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;

/**
 * Class ModuleDataTrait
 * Provides functionality for backend module controller
 *
 * @package DWenzel\T3events\Controller
 */
trait ModuleDataTrait
{
    protected ?ModuleData $moduleData = null;
    protected ModuleDataStorageService $moduleDataStorageService;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @return array
     * @todo Check, if this is replaced completely now!
     */
//    abstract public function mergeSettings();

    /**
     * @return string
     */
    abstract public function getModuleKey();

    /**
     * injects the module data storage service
     *
     * @param ModuleDataStorageService $service
     */
    public function injectModuleDataStorageService(ModuleDataStorageService $service): void
    {
        $this->moduleDataStorageService = $service;
    }

    /**
     * initializes all action methods
     */
    public function initializeAction(): void
    {
        $this->pageUid = (int)$GLOBALS['TYPO3_REQUEST']->getQueryParams()['id'];
        // @todo: Replace this!
        $this->settings = $this->mergeSettings();
    }

    /**
     * Reset action
     * Resets all module data and forwards the request to the list action
     */
    public function resetAction(): void
    {
        $this->moduleData = GeneralUtility::makeInstance(ModuleData::class);
        $this->moduleDataStorageService->persistModuleData($this->moduleData, $this->getModuleKey());
        //@todo: This works different now!
//        $this->forward('list');
    }

    /**
     * @return ModuleData
     */
    public function getModuleData()
    {
        return $this->moduleData;
    }

    /**
     * @param ModuleData $moduleData
     */
    public function setModuleData(ModuleData $moduleData): void
    {
        $this->moduleData = $moduleData;
    }

}
