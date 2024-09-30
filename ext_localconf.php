<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

\DWenzel\T3events\Configuration\ExtensionConfiguration::configurePlugins();
// Modify flexform values
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass']['t3events'] =
    \DWenzel\T3events\Hooks\BackendUtility::class;

/** @var \TYPO3\CMS\Core\Information\Typo3Version $typo3Version */
$typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);

if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger($typo3Version->getVersion()) >= 8005000) {
    // Modify flexform fields since core 8.5 via formEngine: Inject a data provider
    // between TcaFlexPrepare and TcaFlexProcess
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord']
    [\DWenzel\T3events\DataProvider\Form\EventPluginFormDataProvider::class] = [
        'depends' => [
            \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexPrepare::class,
        ],
        'before' => [
            \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexProcess::class,
        ],
    ];


    /** @noinspection PhpUnhandledExceptionInspection */
    \DWenzel\T3events\Configuration\ExtensionConfiguration::registerIcons();
}


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:t3events/Configuration/TSconfig/PageTSconfig.ts">');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][\DWenzel\T3events\Update\LegacyFileFieldsUpdateWizard::IDENTIFIER] = \DWenzel\T3events\Update\LegacyFileFieldsUpdateWizard::class;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['t3eventsLegendPeriodConstraints'] = [
    'nodeName' => 't3eventsLegendPeriodConstraints',
    'priority' => 40,
    'class' => \DWenzel\T3events\Configuration\PeriodConstraintLegendFormElement::class,
];

// @todo: We need to find a better way to do this!
if (isset($GLOBALS['TSFE'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\DWenzel\T3events\Session\SessionInterface::class] = [
        'className' => \DWenzel\T3events\Session\Typo3Session::class
    ];
} else {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\DWenzel\T3events\Session\SessionInterface::class] = [
        'className' => \DWenzel\T3events\Session\Typo3BackendSession::class
    ];
}


// Register a node in ext_localconf.php
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1610471915] = [
    'nodeName' => 'periodConstraintLegend',
    'priority' => 40,
    'class' => \DWenzel\T3events\Form\Element\PeriodConstraintLegendNode::class,
];
