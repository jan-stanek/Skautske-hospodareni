<?php

declare(strict_types=1);

namespace Model\Cashbook\Handlers;

use Cake\Chronos\Date;
use CommandHandlerTest;
use Helpers;
use Model\Cashbook\Cashbook;
use Model\Cashbook\Cashbook\Amount;
use Model\Cashbook\Cashbook\CashbookId;
use Model\Cashbook\Cashbook\CashbookType;
use Model\Cashbook\Commands\Cashbook\MoveChitsToDifferentCashbook;
use Model\Cashbook\Repositories\ICashbookRepository;

final class MoveChitsToDifferentCashbookHandlerTest extends CommandHandlerTest
{
    private const TARGET_CASHBOOK_ID = '2';
    private const SOURCE_CASHBOOK_ID = '1';

    /** @var ICashbookRepository */
    private $cashbooks;

    public function testMovingChits() : void
    {
        $sourceCashbookId = CashbookId::fromString(self::SOURCE_CASHBOOK_ID);
        $targetCashbookId = CashbookId::fromString(self::TARGET_CASHBOOK_ID);

        $type = CashbookType::get(CashbookType::EVENT);
        $this->cashbooks->save(new Cashbook($targetCashbookId, $type));
        $sourceCashbook = new Cashbook($sourceCashbookId, $type);
        $categoryId     = 123;
        $category       = Helpers::mockChitItemCategory($categoryId);

        for ($i = 0; $i < 3; $i++) {
            $sourceCashbook->addChit(
                new Cashbook\ChitBody(null, new Date(), null),
                Cashbook\PaymentMethod::get(Cashbook\PaymentMethod::CASH),
                [new Cashbook\ChitItem(new Amount('100'), $category, 'test')],
                Helpers::mockCashbookCategories($categoryId)
            );
        }

        $this->cashbooks->save($sourceCashbook);

        $this->commandBus->handle(
            new MoveChitsToDifferentCashbook([1, 3], $sourceCashbookId, $targetCashbookId)
        );

        $this->entityManager->clear();

        $sourceCashbook = $this->cashbooks->find($sourceCashbookId);
        $targetCashbook = $this->cashbooks->find($targetCashbookId);

        $this->assertCount(1, $sourceCashbook->getChits());
        $this->assertCount(2, $targetCashbook->getChits());
    }

    /**
     * @return string[]
     */
    protected function getTestedEntites() : array
    {
        return [
            Cashbook::class,
            Cashbook\Chit::class,
            Cashbook\ChitItem::class,
        ];
    }

    protected function _before() : void
    {
        $this->tester->useConfigFiles([__DIR__ . '/MoveChitsToDifferentCashbookHandlerTest.neon']);
        parent::_before();
        $this->cashbooks = $this->tester->grabService(ICashbookRepository::class);
    }
}
