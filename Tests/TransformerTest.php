<?php
/**
 * TransformerTest.php
 * Config
 *
 * Created on 11.04.18 20:30 by thomas
 */

use PHPUnit\Framework\TestCase;
use TASoft\Config\Transformer\Callback;
use TASoft\Config\Transformer\Literal;
use TASoft\Config\Transformer\Regex;
use TASoft\Config\Transformer\RegexCallback;

class TransformerTest extends TestCase
{

    public function testRegularExpressions() {
        $regex = new Regex();

        $regex->addRegularExpression("/(\d+)/i", "Num: \$1");
        $regex->addRegularExpression("/^(https|http)/i", "https-ta");


        $this->assertEquals("Num: 45", $regex->getTransformedValue(45));
        $this->assertEquals("https-ta://www.tasoft.ch", $regex->getTransformedValue('http://www.tasoft.ch'));
        $this->assertEquals("This is a https protocol on port Num: 90.", $regex->getTransformedValue('This is a https protocol on port 90.'));
    }


    public function testLiteral() {
        $literal = new Literal();

        $literal->addLiteral('test', 'TASoft');

        $this->assertEquals("TASoft", $literal->getTransformedValue("test"));
        $this->assertEquals("Miau", $literal->getTransformedValue('Miau'));
    }

    public function testCallback() {
        $callback =  new Callback(function($value) {
            return $value + $value;
        });

        $this->assertEquals(44, $callback->getTransformedValue(22));

        $callback = new RegexCallback("/^(\w+)\.tasoft\.ch$/i", function($matches) {
            return "admin-{$matches[1]}";
        });

        $this->assertEquals("www.google.ch", $callback->getTransformedValue('www.google.ch'));
        $this->assertEquals("admin-www", $callback->getTransformedValue('www.tasoft.ch'));
        $this->assertEquals("admin-dev", $callback->getTransformedValue('dev.tasoft.ch'));

    }
}
