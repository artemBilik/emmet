<?php
namespace artem_c\emmet\test;

use \artem_c\emmet\Node as Node;

require_once __DIR__ . '/../src/Node.php';

class TreeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Exception
     * testing isRoot and setRoot methods
     */
    public function testRoot()
    {

        $root_element = new Node(Node::ROOT);
        $this->assertTrue($root_element->isRoot(), 'Element set to Root is not a root.');

        $child_element = new Node();
        $child_element->addTo($root_element);
        $child_element->setType(Node::ROOT);

    }

    /**
     * testing hasParent and getParent methods
     */
    public function testParent()
    {

        $parent_element = new Node();
        $this->assertFalse($parent_element->hasParent(), 'New Element has parent element.');
        $this->assertNull($parent_element->getParent(), 'Parent element of the new Element is not null.');

        $child_element = new Node();
        $child_element->addTo($parent_element);
        $this->assertTrue($child_element->hasParent(), 'Added Element has not parent element.');
        $this->assertTrue($parent_element === $child_element->getParent(), 'Parent element of the added element is not the element which it was added.');

    }

    public function testAddTo()
    {

        $parent = new Node();
        $child = new Node();
        $this->assertNull($child->getParent(), 'testAddTo. Parent element is not null.');
        $this->assertNull($parent->getFirstChild('testAddTo. First child of the parent element is not null.'));
        $child->addTo($parent);
        $this->assertTrue($parent === $child->getParent('testAddTo. Parents elements do not match.'));
        $this->assertTrue($child === $parent->getFirstChild(), 'testAddTo. Child elements do not match.');

    }

    public function testAddAndSibling()
    {

        $parent      = new Node();
        $child_one   = new Node();
        $child_two   = new Node();
        $child_three = new Node();
        $child_four  = new Node();

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

        $element = new Node();
        $parent  = new Node();
        $sibling = new Node();

        $element->addTo($parent);
        $element->addSibling($sibling);

        $this->assertTrue($parent === $sibling->getParent(), 'testAddSibling. Parent elements do not match.');
        $this->assertTrue($sibling === $element->getRightSibling(), 'testAddSibling. Sibling elements do not match.');

        $sibling_one = new Node();
        $element->addSibling($sibling_one);

        $this->assertTrue($parent === $sibling_one->getParent(), 'testAddSibling.nextSibling. Parent elements do not match.');
        $this->assertTrue($sibling === $element->getRightSibling('testAddSibling.nextSibling. Sibling elements do not match. sibling element.'));
        $this->assertTrue($sibling_one === $sibling->getRightSibling(), 'testAddSibling.nextSibling. Sibling elements do not match sibling_on sibling.');

    }

}
