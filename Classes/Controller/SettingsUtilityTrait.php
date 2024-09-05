<?php

namespace DWenzel\T3events\Controller;

use DWenzel\T3events\Utility\SettingsUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Class SettingsUtilityTrait
 *
 * @package Controller
 * @todo: This needs another solution. Property $settings is also the property of the Controller itself - never set
 *        here,never forced to be set when the trait is use - trait is the wrong solution for this.
 * @deprecated Seems not to be used as intended anymore.
 */
trait SettingsUtilityTrait
{
    /**
     * @var \DWenzel\T3events\Utility\SettingsUtility
     */
    protected $settingsUtility;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var string
     */
    protected $actionMethodName = 'indexAction';

    /**
     * injects the settings utility
     *
     * @param \DWenzel\T3events\Utility\SettingsUtility $settingsUtility
     */
    public function injectSettingsUtility(SettingsUtility $settingsUtility): void
    {
        $this->settingsUtility = $settingsUtility;
    }

    /**
     * Merges TypoScript settings for action and controller into one array
     * @return array
     */
    public function mergeSettings()
    {
        $actionName = preg_replace('/Action$/', '', $this->actionMethodName);
        $controllerKey = $this->settingsUtility->getControllerKey($this);
        $controllerSettings = [];
        $actionSettings = [];
        if (!empty($this->settings[$controllerKey])) {
            $controllerSettings = $this->settings[$controllerKey];
        }
        $allowedControllerSettingKeys = ['search', 'notify'];
        foreach ($controllerSettings as $key => $value) {
            if (!in_array($key, $allowedControllerSettingKeys)) {
                unset($controllerSettings[$key]);
            }
        }
        if (!empty($this->settings[$controllerKey][$actionName])) {
            $actionSettings = $this->settings[$controllerKey][$actionName];
        }

        ArrayUtility::mergeRecursiveWithOverrule($controllerSettings, $actionSettings);
        ArrayUtility::mergeRecursiveWithOverrule($controllerSettings, $this->settings);
        return $controllerSettings;
    }
}
