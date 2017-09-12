<?php


namespace Model\DTO\Travel;

use Nette\StaticClass;
use Model\Travel\Contract as ContractEntity;

final class ContractFactory
{

    use StaticClass;

    public static function create(ContractEntity $contract): Contract
    {
        return new Contract(
            $contract->getId(),
            $contract->getPassenger(),
            $contract->getUnitId(),
            $contract->getUnitRepresentative(),
            $contract->getSince(),
            $contract->getUntil(),
            $contract->getTemplateVersion()
        );
    }

}