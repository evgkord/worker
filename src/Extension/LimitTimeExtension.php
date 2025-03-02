<?php

declare(strict_types=1);

namespace Novomirskoy\Worker\Extension;

use DateTimeImmutable;
use Novomirskoy\Worker\Context;
use Novomirskoy\Worker\EmptyExtensionTrait;
use Novomirskoy\Worker\ExtensionInterface;
use Override;

final readonly class LimitTimeExtension implements ExtensionInterface
{
    use EmptyExtensionTrait;

    public function __construct(
        private DateTimeImmutable $timeLimit,
    ) {}

    #[Override]
    public function onBeforeRunning(Context $context): void
    {
        $this->checkTime($context);
    }

    #[Override]
    public function onAfterRunning(Context $context): void
    {
        $this->checkTime($context);
    }

    #[Override]
    public function onIdle(Context $context): void
    {
        $this->checkTime($context);
    }

    private function checkTime(Context $context): void
    {
        $now = new DateTimeImmutable();

        if ($now >= $this->timeLimit) {
            $context->logger->debug(sprintf(
                '[LimitTimeExtension] Execution interrupted as limit time has passed.' .
                ' now: "%s", time-limit: "%s"',
                $now->format(DATE_ATOM),
                $this->timeLimit->format(DATE_ATOM),
            ));

            $context->interruptExecution();
        }
    }
}
