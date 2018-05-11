<?php
/**
 * ConfigTest.php
 * Config
 *
 * Created on 11.04.18 18:51 by thomas
 */

use TASoft\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testCreateConfig() {
        $cfg = new Config([

        ]);

        $this->assertEmpty($cfg->getKeys());
    }

    public function testNoConstructor() {
        $cfg = new Config();
        $this->assertEmpty($cfg->getKeys());

        $cfg->test = 23;
        $cfg["something"] = true;

        $this->assertNotEmpty($cfg->getKeys());
        $this->assertEquals("", $cfg->getPrefix());

        $this->assertTrue($cfg["something"]);
        $this->assertEquals(23, $cfg->test);
        $this->assertNull($cfg->noSense);
        $this->assertNull($cfg["nonexisting-index"]);
    }

    public function testChildConstructor() {
        $cfg = new Config([
            'item1' => 2,
            'item2' => 66,
            'item3' => [
                'mock',
                'bug'
            ]
        ]);


        unset($cfg->item1);
        unset($cfg['item1']);

        $this->assertTrue(isset($cfg->item2));
        $this->assertCount(2, $cfg);

        if(isset($cfg["item2"])) {
            $cfg['item2'] = NULL;
        }

        $this->assertNull($cfg['item2']);
    }

    public function testChildValues() {
        $cfg = new Config([
            'item1' => 2,
            'item2' => 66,
            'item3' => [
                'mock',
                'bug'
            ]
        ]);

        $cfg->item12 = [
            'other' => 9
        ];

        $cfg["item6"] = 0xDF;

        $this->assertTrue(isset($cfg['item12']));
        $this->assertTrue(isset($cfg->item6));
    }

    public function testMerging() {
        $cfg = new Config([
            'item1' => 2,
            'item2' => 66,
            'item3' => [
                'mock' => 5,
                'bug' => 22
            ]
        ]);

        $subCfg = $cfg->item3;
        $this->assertInstanceOf(Config::class, $subCfg);
        $this->assertEquals("item3", $subCfg->getPrefix());

        $newCfg = new Config([
            'mock' => 77,
            'thomas' => 67
        ]);

        $subCfg->merge($newCfg);
        $this->assertCount(3, $subCfg);
        $this->assertEquals(67, $subCfg->thomas);
        $this->assertEquals(77, $subCfg->mock);
    }

    public function testToArray() {
        $config = [
            'item1' => 2,
            'item2' => 66,
            'item3' => [
                'mock' => 5,
                'bug' => 22
            ]
        ];

        $cfg = new Config($config);
        $configArray = $cfg->toArray();
        $this->assertEquals($config, $configArray);
    }

    public function testSerialisation() {
        $cfg = new Config([
            'item1' => 2,
            'item2' => 66,
            'item3' => [
                'mock' => 5,
                'bug' => 22
            ]
        ]);

        $data = serialize($cfg);
        $config = unserialize($data);

        $this->assertEquals($cfg, $config);
    }
}
