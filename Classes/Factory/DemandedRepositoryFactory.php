<?php

declare(strict_types=1);

namespace DWenzel\T3events\Factory;

use DWenzel\T3events\Domain\Repository\DemandedRepositoryInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DemandedRepositoryFactory
{
    public function getDemandedRepositoryImplementationByKey(string $key): DemandedRepositoryInterface
    {
        $repositoryName = ucfirst($key) . 'Repository';

        $demandedRepositoryInterface = new \ReflectionClass(DemandedRepositoryInterface::class);
        $namespace = $demandedRepositoryInterface->getNamespaceName();

        $repositoryFQCN = $namespace . '\\' . $repositoryName;
        $repository = null;

        if (class_exists($repositoryFQCN)) {
            $repository = GeneralUtility::makeInstance($repositoryFQCN);
        }

        return $repository instanceof DemandedRepositoryInterface
            ? $repository
            : throw new NoDemandedRepositoryFoundForKeyException(
                'The class ' . $repository . ' does not implement DemandedRepositoryInterface!'
            );
    }
}
