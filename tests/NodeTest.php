<?php
namespace emmet\test;

use \emmet\TextNode as TextNode;
use \emmet\Element as Element;

require_once __DIR__ . '/../src/Element.php';

class NodeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Exception
     * testing isRoot and setRoot methods
     */
    public function testRoot()
    {

        $root_element = new Element();
        $this->assertFalse($root_element->isRoot(), 'New Element is root.');
        $root_element->setRoot();
        $this->assertTrue($root_element->isRoot(), 'Element set to Root is not a root.');

        $child_element = new Element();
        $child_element->addTo($root_element);
        $child_element->setRoot();

    }

    /**
     * testing hasParent and getParent methods
     */
    public function testParent()
    {

        $parent_element = new Element();
        $this->assertFalse($parent_element->hasParent(), 'New Element has parent element.');
        $this->assertNull($parent_element->getParent(), 'Parent element of the new Element is not null.');

        $child_element = new Element();
        $child_element->addTo($parent_element);
        $this->assertTrue($child_element->hasParent(), 'Added Element has not parent element.');
        $this->assertTrue($parent_element === $child_element->getParent(), 'Parent element of the added element is not the element which it was added.');

    }

    public function testAddTo()
    {

        $parent = new Element();
        $child = new Element();
        $this->assertNull($child->getParent(), 'testAddTo. Parent element is not null.');
        $this->assertNull($parent->getFirstChild('testAddTo. First child of the parent element is not null.'));
        $child->addTo($parent);
        $this->assertTrue($parent === $child->getParent('testAddTo. Parents elements do not match.'));
        $this->assertTrue($child === $parent->getFirstChild(), 'testAddTo. Child elements do not match.');

    }

    public function testAddAndSibling()
    {

        $parent      = new Element();
        $child_one   = new Element();
        $child_two   = new Element();
        $child_three = new Element();
        $child_four  = new Element();

        $child_one->addTo($parent);
        $child_one->addSibling($child_two);
        $child_three->addTo($parent);
        $child_one->addSibling($child_four);

        $this->assertTrue($parent->getFirstChild() === $child_one);
        $this->assertTrue($child_one->getRightSibling() === $child_two);
        $this->assertTrue($child_two->getRightSibling() === $child_three);
        $this->assertTrue($child_three->getRightSibling() === $child_four);

        $this->assertTrue($parent === $child_one->getParent());
        $this->assertTrue($parent === $child_two->getParent());
        $this->assertTrue($parent === $child_three->getParent());
        $this->assertTrue($parent === $child_four->getParent());
    }

    public function testAddSibling()
    {

        $element = new Element();
        $parent  = new Element();
        $sibling = new Element();

        $element->addTo($parent);
        $element->addSibling($sibling);

        $this->assertTrue($parent === $sibling->getParent(), 'testAddSibling. Parent elements do not match.');
        $this->assertTrue($sibling === $element->getRightSibling(), 'testAddSibling. Sibling elements do not match.');

        $sibling_one = new Element();
        $element->addSibling($sibling_one);

        $this->assertTrue($parent === $sibling_one->getParent(), 'testAddSibling.nextSibling. Parent elements do not match.');
        $this->assertTrue($sibling === $element->getRightSibling('testAddSibling.nextSibling. Sibling elements do not match. sibling element.'));
        $this->assertTrue($sibling_one === $sibling->getRightSibling(), 'testAddSibling.nextSibling. Sibling elements do not match sibling_on sibling.');

    }

    public function testMultiplication()
    {

        $element = new Element();
        $this->assertEquals(1,$element->getMultiplication());

        $element->setMultiplication('12');
        $this->assertEquals(12, $element->getMultiplication());


        $element->setMultiplication(-1);
        $this->assertEquals(1, $element->getMultiplication());


        $element->setMultiplication('4asdf');
        $this->assertEquals(4, $element->getMultiplication());



    }
}
