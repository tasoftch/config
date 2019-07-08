<?php

namespace TASoft\Config\Compiler\Factory;


use TASoft\Config\Config;

abstract class AbstractLiveConfigFactory extends AbstractConfigFactory
{
    /**
     * Directly allows in config files to create dynamic configuration.
     *
     * @param iterable|NULL $configuration
     */
    public function __construct(iterable $configuration = NULL)
    {
        if($configuration) {
            $this->setConfig( new Config( $configuration ) );
        }
    }
}