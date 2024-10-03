<?php

namespace DWenzel\T3events\Configuration\Plugin;

use DWenzel\T3events\Controller\EventController;
use DWenzel\T3events\Controller\PerformanceController;
use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
use DWenzel\T3extensionTools\Configuration\PluginConfigurationTrait;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Dirk Wenzel <wenzel@cps-it.de>
 *  All rights reserved
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the text file GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use DWenzel\T3events\Configuration\ExtensionConfiguration;

/**
 * Class Events
 *
 * Provides configuration for events plugin
 */
abstract class EventsQuickmenu implements PluginConfigurationInterface
{
    use PluginConfigurationTrait;

    protected static string $pluginName = 'EventsQuickmenu';

    protected static string $pluginSignature = 't3events_eventsquickmenu';

    protected static string $pluginTitle = 'LLL:EXT:t3events/Resources/Private/Language/locallang_be.xlf:plugin.events.quickmenu';

    protected static string $flexForm = 'FILE:EXT:t3events/Configuration/FlexForms/flexform_events.xml';

    protected static array $controllerActions = [
        EventController::class => 'quickMenu'
    ];

    protected static array $nonCacheableControllerActions = [
        EventController::class => 'quickMenu'
    ];

    protected static string $extensionName = ExtensionConfiguration::EXTENSION_KEY;

    protected static string $vendorExtensionName = ExtensionConfiguration::VENDOR . '.' . ExtensionConfiguration::EXTENSION_KEY;
}
