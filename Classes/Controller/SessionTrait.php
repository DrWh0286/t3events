<?php

namespace DWenzel\T3events\Controller;

use DWenzel\T3events\Session\SessionInterface;

/**
 * Class SessionTrait
 * Provides session handling for controllers
 *
 * @package DWenzel\T3events\Controller
 * @deprecated This needs to be solved via DI!
 * @todo: Replace with DI. Needs implementation configuration in Services.yaml, because there are two implementations
 *        for interface \DWenzel\T3events\Session\SessionInterface.
 */
trait SessionTrait
{
    /**
     * @var \DWenzel\T3events\Session\SessionInterface
     */
    protected $session;

    /**
     * namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * @param \DWenzel\T3events\Session\SessionInterface $session
     * @deprecated This needs to be solved via DI!
     */
    public function injectSession(SessionInterface $session): void
    {
        $session->setNamespace($this->namespace);
        $this->session = $session;
    }
}
