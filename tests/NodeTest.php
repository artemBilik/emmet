<?php
namespace emmet\test;

use \emmet\Node as Node;
use \emmet\Value as Value;
use \emmet\Data as Data;

require_once __DIR__ . '/../src/Node.php';
require_once __DIR__ . '/../src/Value.php';
require_once __DIR__ . '/../src/Data.php';

class NodeTest extends \PHPUnit_Framework_TestCase
{


    public function testSetType()
    {

        $node = new Node();

        $class = new \ReflectionClass('\emmet\Node');
        $type = $class->getProperty('_type');
        $type->setAccessible(true);

        $node->setType(Node::TAG);
        $this->assertTrue($type->getValue($node) === Node::TAG);
        $node->setType(Node::TEXT_NODE);
        $this->assertTrue($type->getValue($node) === Node::TEXT_NODE);
        $node->setType(Node::HTML);
        $this->assertTrue($type->getValue($node) === Node::HTML);
        $node->setType(Node::ROOT);
        $this->assertTrue($type->getValue($node) === Node::ROOT);

    }

    /**
     * @expectedException \Exception
     */
    public function testWrongType()
    {

        $node = new Node();
        $node->setType(123);

    }
    /**
     * @expectedException \Exception
     */
    public function testSetRoot()
    {

        $node = new Node(Node::ROOT);
        $this->assertTrue($node->isRoot());

        $child_node = new Node(Node::TAG);
        $child_node->addTo($node);

        $child_node->setType(Node::HTML);
        $child_node->setType(Node::ROOT);

    }
    /**
     * @expectedException \Exception
     */
    public function testRootAddToRoot()
    {

        $node = new Node(Node::TAG);
        $child = new Node(Node::ROOT);
        $child->addTo($node);

    }

    public function testTagHtml()
    {

        $node = new Node(Node::ROOT);

        $child = new Node(Node::TAG);
        $sibling = new Node(Node::TAG);

        $child->addTo($node);
        $child->addSibling($sibling);

        $value = new Value(new Data());
        $value->addText('text node of the child node');
        $child->setValue($value);

        $value = new Value(new Data());
        $value->addText('one more text node of the child node');
        $child->setValue($value);

        $value = new Value(new Data());
        $value->addText('header');
        $child->setTag($value);

        $hr = new Node(Node::TAG);
        $value = new Value(new Data());
        $value->addText('hr');
        $hr->setTag($value);

        $value = new Value(new Data());
        $value->addText('alone');
        $hr->addAttributes($value);

        $hr->addTo($child);

        $value = new Value(new Data());
        $value->addText('class=headerClass1 id=headerId color=red class=headerClass2');
        $child->addAttributes($value);


        $value = new Value(new Data());
        $value->addText('text node of the sibling node');
        $sibling->setValue($value);

        $value = new Value(new Data());
        $value->addText('section');
        $sibling->setTag($value);

        $value = new Value(new Data());
        $value->addText('alone');
        $sibling->addAttributes($value);

        $value = new Value(new Data());
        $value->addText('2');

        $sibling->setMultiplication($value);


        $html = '<header class="headerClass1 headerClass2" id="headerId" color="red">text node of the child nodeone more text node of the child node<hr alone="alone" /></header><section alone="alone">text node of the sibling node</section><section alone="alone">text node of the sibling node</section>';
        $this->assertEquals($node->getHtml(), $html);

    }

    public function testTextNodeHtml()
    {

        $node = new Node(Node::ROOT);

        $tn = new Node(Node::TEXT_NODE);
        $value = new Value(new Data());
        $value->addText('text node');
        $tn->setValue($value);
        $value  = new Value(new Data());
        $value->addText('2');
        $tn->setMultiplication($value);

        $this->assertEquals($tn->getHtml(), 'text nodetext node');

    }

    public function testHtmlHtml()
    {

        $html = new Node(Node::HTML);

        $data = new Data();
        $data->setData(['html' => 'html{{value}}']);
        $value = new Value($data);
        $value->addVariable('html');
        $html->setValue($value);

        $node = new Node(Node::TAG);
        $value = new Value(new Data());
        $value->addText('hr');
        $node->setTag($value);
        $node->addTo($html);

        $this->assertEquals($html->getHtml(), 'html<hr />');

    }

}