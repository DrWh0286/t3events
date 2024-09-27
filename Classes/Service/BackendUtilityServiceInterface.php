<?php

declare(strict_types=1);

namespace DWenzel\T3events\Service;

interface BackendUtilityServiceInterface
{
    public function getRecord($table, $uid, $fields = '*', $where = '', $useDeleteClause = true): ?array;

    public function getRecordTitle($table, $row, $prep = false, $forceResult = true): ?string;
}
