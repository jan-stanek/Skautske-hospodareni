<?php

declare(strict_types=1);

namespace Model\Cashbook\Handlers\Cashbook;

use Model\Cashbook\Cashbook\PaymentMethod;
use Model\Cashbook\CashbookNotFound;
use Model\Cashbook\CategoryNotFound;
use Model\Cashbook\Commands\Cashbook\AddChitToCashbook;
use Model\Cashbook\Repositories\CategoryRepository;
use Model\Cashbook\Repositories\ICashbookRepository;

final class AddChitToCashbookHandler
{
    /** @var ICashbookRepository */
    private $cashbooks;

    /** @var CategoryRepository */
    private $categories;

    public function __construct(ICashbookRepository $cashbooks, CategoryRepository $categories)
    {
        $this->cashbooks  = $cashbooks;
        $this->categories = $categories;
    }

    /**
     * @throws CashbookNotFound
     * @throws CategoryNotFound
     */
    public function handle(AddChitToCashbook $command) : void
    {
        $cashbook = $this->cashbooks->find($command->getCashbookId());
        $category = $this->categories->find($command->getCategoryId(), $cashbook->getId(), $cashbook->getType());

        $cashbook->addChit($command->getBody(), $category, PaymentMethod::get(PaymentMethod::CASH));

        $this->cashbooks->save($cashbook);
    }
}
