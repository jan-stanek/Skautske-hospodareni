<?php

declare(strict_types=1);

namespace Model\DTO\Cashbook;

use Model\Cashbook\Cashbook\CashbookId;
use Model\Cashbook\Cashbook\CashbookType;
use Nette\SmartObject;

/**
 * @property-read string $chitNumberPrefix
 */
class Cashbook
{
    use SmartObject;

    /** @var CashbookId */
    private $id;

    /** @var CashbookType */
    private $type;

    /** @var string|NULL */
    private $chitNumberPrefix;

    /** @var string */
    private $note;

    public function __construct(
        CashbookId $id,
        CashbookType $type,
        ?string $chitNumberPrefix,
        string $note
    ) {
        $this->id               = $id;
        $this->type             = $type;
        $this->chitNumberPrefix = $chitNumberPrefix;
        $this->note             = $note;
    }

    public function getId() : string
    {
        return $this->id->toString();
    }

    public function getType() : CashbookType
    {
        return $this->type;
    }

    public function getChitNumberPrefix() : ?string
    {
        return $this->chitNumberPrefix;
    }

    public function getNote() : string
    {
        return $this->note;
    }
}
