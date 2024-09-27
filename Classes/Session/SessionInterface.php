<?php

namespace DWenzel\T3events\Session;

/**
 * Interface SessionInterface
 *
 * @package DWenzel\T3events\Session
 */
interface SessionInterface
{
    /**
     * @param string $identifier
     * @return void
     */
    public function set($identifier, mixed $value);

    /**
     * @param string $identifier
     * @return mixed
     */
    public function get($identifier);

    /**
     * @param string $identifier
     * @return mixed
     */
    public function has($identifier);

    /**
     * @return void
     */
    public function clean();

    /**
     * Sets the namespace
     *
     * @param string $namespace
     */
    public function setNamespace($namespace);
}
