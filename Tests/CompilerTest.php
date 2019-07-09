<?php
/**
 * CompilerTest.php
 * config
 *
 * Created on 2019-07-08 19:16 by thomas
 */

use PHPUnit\Framework\TestCase;
use TASoft\Config\Compiler\Factory\AbstractLiveConfigFactory;
use TASoft\Config\Compiler\Source\FileGroupSource;
use TASoft\Config\Compiler\Source\FileSource;
use TASoft\Config\Compiler\StandardCompiler;
use TASoft\Config\Compiler\StandardFactoryCompiler;
use TASoft\Config\Config;
use TASoft\Config\FileConfig;

class CompilerTest extends TestCase
{
    public function testStandardCompiler() {
        $compiler = new StandardCompiler(__DIR__ . "/Outputs/std-compiler.php");

        $src = new FileGroupSource();
        $src->addSourceFile(__DIR__ . "/Inputs/simple.config.php");
        $src->addSourceFile(__DIR__ . "/Inputs/nested.config.php");

        $compiler->setSource($src);

        $compiler->compile();

        $this->assertFileExists(__DIR__ . "/Outputs/std-compiler.php");
        $config = new FileConfig(__DIR__ . "/Outputs/std-compiler.php");

        $this->assertArrayHasKey("services", $config);
        $this->assertArrayHasKey("test", $config);
        $this->assertArrayHasKey("thomas", $config);
        $this->assertArrayHasKey("number", $config);
        $this->assertArrayHasKey("bool", $config);

        unlink(__DIR__ . "/Outputs/std-compiler.php");
    }

    public function testFactoryCompiler() {
        $compiler = new StandardFactoryCompiler(__DIR__ . "/Outputs/factory-compiler.php");
        $compiler->setSource( new FileSource(__DIR__ . "/Inputs/factory.config.php") );

        $compiler->compile();

        $this->assertFileExists(__DIR__ . "/Outputs/factory-compiler.php");
        $config = new FileConfig(__DIR__ . "/Outputs/factory-compiler.php");

        $this->assertArrayHasKey("test", $config["simple"]);
        $this->assertArrayHasKey("factory", $config["simple"]);

        $this->assertEquals(529, $config["simple"]["num1"]);
        $this->assertEquals(9, $config["simple"]["num2"]);
        $this->assertEquals(65025, $config["simple"]["num3"]);


    }
}

class LiveTestConfigFactory extends AbstractLiveConfigFactory {

    protected function makeConfiguration(Config $configuration): Config
    {
        $config = new Config();

        foreach($configuration as $key => $value) {
            if(is_numeric($value))
                $value *= $value;
            $config[$key] = $value;
        }
        $config["factory"] = 'done';
        return $config;
    }
}
