<?php 
defined('TYPO3') || die();

return [
    't3events_main' => [
        'labels' => 'LLL:EXT:t3events/Resources/Private/Language/locallang_mod_main.xlf',
        'iconIdentifier' => 'extensions-t3events-main',
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
        'extensionName' => 'T3events',
    ],
    't3events_event' => [
        'parent' => 't3events_main',
        'access' => 'user,group',
        'iconIdentifier' => 'extensions-t3events-event',
        'path' => '/module/t3events_main/event',
        'labels' => 'LLL:EXT:t3events/Resources/Private/Language/locallang_m1.xlf',
        'extensionName' => 'T3events',
        'controllerActions' => [
            DWenzel\T3events\Controller\Backend\EventController::class => [
                'list', 'show', 'reset', 'new'
            ],
        ],
    ],
    't3events_schedule' => [
        'parent' => 't3events_main',
        'access' => 'user,group',
        'iconIdentifier' => 'extensions-t3events-schedule',
        'path' => '/module/t3events_main/schedule',
        'labels' => 'LLL:EXT:t3events/Resources/Private/Language/locallang_m2.xlf',
        'extensionName' => 'T3events',
        'controllerActions' => [
            DWenzel\T3events\Controller\Backend\ScheduleController::class => [
                'list', 'show', 'edit', 'delete', 'reset'
            ],
        ],
    ],
];