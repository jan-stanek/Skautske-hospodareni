<?php

declare(strict_types=1);

namespace Model\Payment;

class Group
{

    /** @var int */
    private $id;

    /** @var string */
    private $type;

    /** @var int */
    private $unitId;

    /** @var int */
    private $skautisId;

    /** @var string|NULL */
    private $name;

    /** @var float|NULL */
    private $defaultAmount;

    /** @var \DateTimeImmutable|NULL */
    private $dueDate;

    /** @var int|NULL */
    private $constantSymbol;

    /** @var string */
    private $state = self::STATE_OPEN;

    /** @var \DateTimeImmutable|NULL */
    private $createdAt;

    /** @var \DateTimeImmutable|NULL */
    private $lastPairing;

    /** @var string */
    private $emailTemplate;

    const STATE_OPEN = 'open';

    /**
     * Group constructor.
     * @param string|NULL $type
     * @param int $unitId
     * @param int|NULL $skautisId
     * @param string $name
     * @param float|NULL $defaultAmount
     * @param \DateTimeImmutable|NULL $dueDate
     * @param int|NULL $constantSymbol
     * @param \DateTimeImmutable $createdAt
     * @param string $emailTemplate
     */
    public function __construct(
        ?string $type,
        int $unitId,
        ?int $skautisId,
        string $name,
        ?float $defaultAmount,
        ?\DateTimeImmutable $dueDate,
        ?int $constantSymbol,
        \DateTimeImmutable $createdAt,
        string $emailTemplate)
    {
        $this->type = $type;
        $this->unitId = $unitId;
        $this->skautisId = $skautisId;
        $this->name = $name;
        $this->defaultAmount = $defaultAmount;
        $this->dueDate = $dueDate;
        $this->constantSymbol = $constantSymbol;
        $this->createdAt = $createdAt;
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|NULL
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getUnitId(): int
    {
        return $this->unitId;
    }

    /**
     * @return int|NULL
     */
    public function getSkautisId(): ?int
    {
        return $this->skautisId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float|NULL
     */
    public function getDefaultAmount(): ?float
    {
        return $this->defaultAmount;
    }

    /**
     * @return \DateTimeImmutable|NULL
     */
    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->dueDate;
    }

    /**
     * @return int|NULL
     */
    public function getConstantSymbol(): ?int
    {
        return $this->constantSymbol;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return \DateTimeImmutable|NULL
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getEmailTemplate(): string
    {
        return $this->emailTemplate;
    }

    /**
     * Update last pairing time
     * @param \DateTimeImmutable $at
     */
    public function updateLastPairing(\DateTimeImmutable $at): void
    {
        $this->lastPairing = $at;
    }

    /**
     * @return \DateTimeImmutable|NULL
     */
    public function getLastPairing(): ?\DateTimeImmutable
    {
        return $this->lastPairing;
    }

}