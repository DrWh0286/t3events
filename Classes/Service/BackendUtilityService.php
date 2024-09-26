<?php

declare(strict_types=1);

namespace DWenzel\T3events\Service;

use TYPO3\CMS\Backend\Utility\BackendUtility;

final class BackendUtilityService implements BackendUtilityServiceInterface
{
    public function getRecord($table, $uid, $fields = '*', $where = '', $useDeleteClause = true): ?array
    {
        return BackendUtility::getRecord($table, $uid, $fields, $where, $useDeleteClause);
    }

    public function getRecordTitle($table, $row, $prep = false, $forceResult = true): ?string
    {
        return BackendUtility::getRecordTitle($table, $row, $prep, $forceResult);
    }
}
