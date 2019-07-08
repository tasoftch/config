<?php

namespace TASoft\Config\Compiler\Factory;


use TASoft\Config\Config;

abstract class AbstractConfigFactory implements FactoryInterface
{
    /** @var Config */
    private $config;

    /**
     * @inheritDoc
     */
    public function toConfig(): Config
    {
        return $this->getConfig();
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        if(!$this->config) {
            $this->config = new Config();

        }

        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    /**
     * Called on demand to create the real configuration
     *
     * @param Config $configuration
     * @return void
     */
    abstract protected function makeConfiguration(Config $configuration);
}