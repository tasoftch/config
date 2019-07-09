<?php
/**
 * TransformationControllerTest.php
 * Config
 *
 * Created on 11.04.18 20:16 by thomas
 */
use PHPUnit\Framework\TestCase;
use TASoft\Config\Config;
use TASoft\Config\Controller\TransformationController;
use TASoft\Config\Transformer\TransformerInterface;

class TransformationControllerTest extends TestCase
{

    public function testAddTransformer()
    {
        /** @var TransformationController $trans */
        $trans = TransformationController::getController();
        $trans->addTransformer(new Tester);

        $cfg = new Config(['test' => 23]);
        $this->assertEquals("Thomas 123", $cfg->test);
    }

    public function testGetTransformedValue()
    {
        /** @var TransformationController $trans */
        $trans = TransformationController::getController();
        $trans->removeAllTransformers();

        $cfg = new Config(['test' => 23]);
        $this->assertEquals(23, $cfg->test);
    }

    public function testKeyedTransformer() {
        /** @var TransformationController $trans */
        $trans = TransformationController::getController();
        $trans->removeAllTransformers();

        $trans->addTransformer(new DynTransformer('Thomas'), "test");
        $trans->addTransformer(new DynTransformer('TASoft'), "test", "prefix");

        $cfg = new Config([
            "test" => 9,
            "unaffected" => 10,
            "prefix" => [
                'test' => 5
            ]
        ]);

        $this->assertEquals(10, $cfg->unaffected);
        $this->assertEquals("Thomas", $cfg->test);

        $this->assertEquals("TASoft", $cfg->prefix->test);
    }

    public function testRemoveTransformer() {
        /** @var TransformationController $trans */
        $trans = TransformationController::getController();

        $trans->removeTransformers("test", "prefix");

        $cfg = new Config([
            "test" => 9,
            "unaffected" => 10,
            "prefix" => [
                'test' => 5
            ]
        ]);

        $this->assertEquals(10, $cfg->unaffected);
        $this->assertEquals("Thomas", $cfg->test);

        $this->assertEquals(5, $cfg->prefix->test);
    }
}

class Tester implements TransformerInterface {
    public function getTransformedValue($value)
    {
        return "Thomas 123";
    }
}

class DynTransformer implements TransformerInterface {
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getTransformedValue($value)
    {
        return $this->value;
    }
}