<?php

namespace DWenzel\T3events\Controller;

/**
 * Class SignalTrait
 *
 * @package DWenzel\T3events\Tests\Unit\Controller
 * @deprecated Needs to be replaced by PSR-14, maybe a wrapping service (basically not needed) and constructor DI!
 * @todo: Switch to \Psr\EventDispatcher\EventDispatcherInterface everywhere and replace the trait usage with DI
 */
trait SignalTrait
{
    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * Emits signals
     *
     * @param string $class Name of the signaling class
     * @param string $name Signal name
     * @param array $arguments Signal arguments
     * @codeCoverageIgnore
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @todo Change to PSR-14!
     */
    public function emitSignal($class, $name, array &$arguments): void
    {
        /**
         * Wrap arguments into array in order to allow changing the arguments
         * count. Dispatcher throws InvalidSlotReturnException if slotResult count
         * differs.
         */
        $slotResult = $this->signalSlotDispatcher->dispatch($class, $name, [$arguments]);
        $arguments = $slotResult[0];
    }
}
