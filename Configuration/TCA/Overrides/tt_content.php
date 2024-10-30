<?php

\DWenzel\T3events\Configuration\ExtensionConfiguration::registerPlugins();

// Todo TYPO3 12 - Check if we should do it this way or we keep using \DWenzel\T3events\Configuration\ExtensionConfiguration::registerPlugins();
// use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// (static function (): void {
//     $pluginSignature = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
//         'T3events',
//         'Events',
//         'Events',
//     );

//     $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
//     ExtensionManagementUtility::addPiFlexFormValue(
//         $pluginSignature,
//         'FILE:EXT:t3events/Configuration/FlexForms/flexform_events.xml'
//     );

//     $pluginSignature = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
//         'T3events',
//         'EventsDetail',
//         'Events Detail',
//     );

//     $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
//     ExtensionManagementUtility::addPiFlexFormValue(
//         $pluginSignature,
//         'FILE:EXT:t3events/Configuration/FlexForms/flexform_events.xml'
//     );

//     $pluginSignature = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
//         'T3events',
//         'EventsQuickmenu',
//         'Events Quickmenu',
//     );

//     $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
//     ExtensionManagementUtility::addPiFlexFormValue(
//         $pluginSignature,
//         'FILE:EXT:t3events/Configuration/FlexForms/flexform_eventsquickmenu.xml'
//     );

//     $pluginSignature = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
//         'T3events',
//         'Performances',
//         'Performances',
//     );

//     $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
//     ExtensionManagementUtility::addPiFlexFormValue(
//         $pluginSignature,
//         'FILE:EXT:t3events/Configuration/FlexForms/flexform_events.xml'
//     );

//     $pluginSignature = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
//         'T3events',
//         'PerformancesDetail',
//         'Performances Detail',
//     );

//     $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
//     ExtensionManagementUtility::addPiFlexFormValue(
//         $pluginSignature,
//         'FILE:EXT:t3events/Configuration/FlexForms/flexform_events.xml'
//     );

//     $pluginSignature = \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
//         'T3events',
//         'PerformancesQuickmenu',
//         'Performances Quickmenu',
//     );

//     $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
//     ExtensionManagementUtility::addPiFlexFormValue(
//         $pluginSignature,
//         'FILE:EXT:t3events/Configuration/FlexForms/flexform_eventsquickmenu.xml'
//     );
// })();

$temporaryColumns = [
    'tx_t3events_event' => [
        'config' => [
            'type' => 'passthrough',
            'foreign_table' => 'tx_t3events_domain_model_event'
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    $temporaryColumns
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    'tx_t3events_event'
);
