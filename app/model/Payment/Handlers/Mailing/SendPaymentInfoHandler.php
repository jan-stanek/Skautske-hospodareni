<?php

declare(strict_types=1);

namespace Model\Event\Handlers\Mailing;

use Model\Payment\Commands\Mailing\SendPaymentInfo;
use Model\Payment\EmailType;
use Model\Payment\InvalidSmtp;
use Model\Payment\MailingService;
use Model\Payment\Payment\State;
use Model\Payment\PaymentClosed;
use Model\Payment\Repositories\IPaymentRepository;

final class SendPaymentInfoHandler
{
    /** @var IPaymentRepository */
    private $payments;

    /** @var MailingService */
    private $mailingService;

    public function __construct(IPaymentRepository $payments, MailingService $mailingService)
    {
        $this->payments       = $payments;
        $this->mailingService = $mailingService;
    }

    /**
     * @throws InvalidSmtp
     */
    public function __invoke(SendPaymentInfo $command) : void
    {
        $payment = $this->payments->find($command->getPaymentId());

        if ($payment->isClosed()) {
            throw new PaymentClosed();
        }

        $this->mailingService->sendEmail($payment->getId(), EmailType::get(EmailType::PAYMENT_INFO));

        if ($payment->getState()->equalsValue(State::SENT)) {
            return;
        }

        $payment->markSent();

        $this->payments->save($payment);
    }
}
