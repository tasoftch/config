<?php

namespace TASoft\Config\Compiler\Factory;
use TASoft\Config\Config;

/**
 * Configuration factories can be used to dynamically create specific configuration on compile time.
 * The config factory is created and then asked for the real configuration to compile.
 *
 * PLEASE NOTE: You can not use config factories directly without compiler! The class Config won't handle it.
 *
 * @package TASoft\Config
 */
interface FactoryInterface
{
    /**
     * Called by the compiler to obtain the real configuration
     *
     * @return Config
     */
    public function toConfig(): Config;
}