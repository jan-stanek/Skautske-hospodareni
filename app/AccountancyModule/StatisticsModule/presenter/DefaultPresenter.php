<?php

declare(strict_types=1);

namespace App\AccountancyModule\StatisticsModule;

use Model\StatisticsService;
use function date;

class DefaultPresenter extends BasePresenter
{
    /** @var StatisticsService */
    private $statService;

    public function __construct(StatisticsService $statService)
    {
        parent::__construct();
        $this->statService = $statService;
    }


    public function renderDefault(?int $year = null) : void
    {
        if ($year === null) {
            $year = (int) date('Y');
        }
        $unitTree = $this->unitService->getTreeUnder($this->unitService->getDetailV2($this->unitId));
        $data     = $this->statService->getEventStatistics($unitTree, $year);

        $this->template->setParameters([
            'unitTree' => $unitTree,
            'data' => $data,
        ]);
    }
}
