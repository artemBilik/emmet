<?php
namespace emmet\test;

use \emmet\TextNode as TextNode;

require_once __DIR__ . '/../src/Element.php';

class TextNodeTest extends \PHPUnit_Framework_TestCase
{

    private $_test_value = 'test value';
    private $_test_multi = 3;
    public function testValue()
    {

        $el = new TextNode();
        $this->assertTrue('' === $el->getValue());

        $el->setValue($this->_test_value);
        $this->assertEquals($this->_test_value, $el->getValue());

        $el->setValue($this->_test_value);
        $this->assertEquals($this->_test_value, $el->getValue());

        return $el;

    }
    /**
     * @depends testValue
     */
    public function testHtml($el)
    {

        $this->assertEquals($this->_test_value, $el->getHtml());
        $el->setMultiplication($this->_test_multi);
        $this->assertEquals(str_repeat($this->_test_value, $this->_test_multi), $el->getHtml());

    }

}
