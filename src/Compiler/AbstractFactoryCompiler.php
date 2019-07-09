<?php

namespace TASoft\Config\Compiler;


use TASoft\Config\Compiler\Factory\FactoryInterface;
use TASoft\Config\Config;

/**
 * The factory compiler is designed to resolve config factories.
 *
 * @package TASoft\Config\Compiler
 */
abstract class AbstractFactoryCompiler extends AbstractCompiler
{
    /**
     * @inheritDoc
     */
    protected function mergeConfiguration(Config $collected, Config $new)
    {
        $iterator = function(Config $cfg) use (&$iterator) {
            foreach($cfg->toArray(false) as $key => $value) {
                if($value instanceof Config)
                    $cfg[$key] = $iterator($value);
                elseif($value instanceof FactoryInterface) {
                    $cfg[$key] = $iterator($value->toConfig());
                }
            }
            return $cfg;
        };
        parent::mergeConfiguration($collected, $iterator($new));
    }

}