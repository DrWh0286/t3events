<?php

$GLOBALS['TCA']['tx_t3events_domain_model_event']['columns']['categories'] = [
    'config' => [
       'type' => 'category'
    ]
 ];
 
 \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_t3events_domain_model_event', 'categories');