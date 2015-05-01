<?php
namespace emmet\test;

use \emmet\Element as Element;

require_once __DIR__ . '/../src/Element.php';

class ElementTest extends \PHPUnit_Framework_TestCase
{

    public function testTag()
    {

        $el = new Element();
        $el->setTag('div');
        $this->assertEquals('div', $el->getTag());

    }

    public function testAttributes()
    {

        $el = new Element();
        $el->setTag('input');
        $el->addAttributes('class=test_class id=test_id checked type=hidden');
        $this->assertEquals(['class' => 'test_class', 'id' => 'test_id', 'checked' => 'checked', 'type' => 'hidden'], $el->getAttributes());
        $el->addAttributes('class=new_class id=new_id type=checkbox');
        $this->assertEquals(['class' => 'test_class new_class', 'id' => 'new_id', 'checked' => 'checked', 'type' => 'checkbox'], $el->getAttributes());

    }

    public function testValue()
    {

        $el = new Element();
        $test_value = 'test_value';
        $el->setValue($test_value);
        $this->assertEquals($test_value, $el->getFirstChild()->getValue());

    }

    public function testHtml()
    {

        $div = new Element();
        $div->setTag('div');
        $div->addAttributes('id=parent');

        $p = new Element();
        $p->setTag('p');
        $p->addAttributes('class=child');
        $p->setValue('test_value');

        $hr = new Element();
        $hr->setTag('hr');
        $hr->addAttributes('color=black');
        $hr->setValue('value');

        $p->addTo($div);
        $p->addSibling($hr);

        $this->assertEquals('<div id="parent"><p class="child">test_value</p><hr color="black" /></div>',$div->getHtml());

    }

}
