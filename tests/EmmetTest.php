<?php
namespace emmet\test;

use \emmet\Emmet as Emmet;

require_once __DIR__ . '/../src/Emmet.php';

class EmmetTest extends \PHPUnit_Framework_TestCase
{

    private $_emmet_string = 'div>p>span+a>span';

    /**
     *  @expectedException \emmet\EmmetException
     */
    public function testConstruct()
    {

        $reflection_class = new \ReflectionClass('\emmet\Emmet');
        $emmet_property = $reflection_class->getProperty('_emmet_string');
        $emmet_property->setAccessible(true);

        $tree_property = $reflection_class->getProperty('_tree');
        $tree_property->setAccessible(true);

        $data_property = $reflection_class->getProperty('_data');
        $data_property->setAccessible(true);

        $emmet = new Emmet($this->_emmet_string);
        $this->assertEquals($this->_emmet_string, $emmet_property->getValue($emmet));

        $this->assertInstanceOf('\emmet\Element', $tree_property->getValue($emmet));
        $this->assertTrue($tree_property->getValue($emmet)->isRoot());

        $this->assertInstanceOf('\emmet\Data', $data_property->getValue($emmet));


        new Emmet(new \stdClass());

    }

    public function testGetCheckDocumentation()
    {

        $emmet = new Emmet($this->_emmet_string);

        $ref = new \ReflectionClass('\emmet\Emmet');
        $method = $ref->getMethod('getCheckTheDocumentation');
        $method->setAccessible(true);

        $this->assertEquals('Check the documentation for the right syntax to use near "div&gt;<strong style="color:red;">p&gt;span+a&gt;span</strong>".', $method->invoke($emmet, 4));

    }

    public function testCreate()
    {



    }

}
