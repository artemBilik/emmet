<?php

namespace emmet;


/*
 * @todo $ in multiplication
 */

abstract class Node
{

    private $_first_child = null;
    private $_right_sibling = null;
    private $_parent = null;
    private $_is_root = false;
    private $_multiplication = 1;

    abstract function getHtml();

    public function addTo(Node $parent)
    {
        if($this->hasParent()){
            $this->_parent->addTo($parent);
        } else {
            $this->_parent = $parent;
            if (null === $parent->_first_child) {
                $parent->_first_child = $this;
            } else {
                $left_sibling = $parent->_first_child;
                while (null !== $left_sibling->_right_sibling) {
                    $left_sibling = $left_sibling->_right_sibling;
                }
                $left_sibling->_right_sibling = $this;
            }
        }

    }

    public function addSibling(Node $sibling)
    {

        if(null !== $this->_right_sibling){
            return $this->_right_sibling->addSibling($sibling);
        }
        $sibling->_parent = $this->_parent;
        $this->_right_sibling = $sibling;
        return true;

    }

    public function getParent()
    {

        return $this->_parent;

    }

    public function getFirstChild()
    {

        return $this->_first_child;

    }
    public function getRightSibling()
    {

        return $this->_right_sibling;

    }
    public function hasParent()
    {

        return (null === $this->_parent ? false : true);

    }

    public function drop()
    {

        if($this->_first_child){
            $this->_first_child->drop();
            $this->_first_child = null;
        }
        if($this->_right_sibling){
            $this->_right_sibling->drop();
            $this->_right_sibling = null;
        }

    }
    public function setRoot()
    {
        if(!$this->hasParent()){
            $this->_is_root = true;
        } else {
            throw new \Exception('You can not set to root element which has a parent element.');
        }

    }

    public function isRoot()
    {

        return $this->_is_root;

    }

    public function setMultiplication($number)
    {

        $number = intval($number);
        $this->_multiplication = ($number < 1) ? 1 : $number;

    }

    public function getMultiplication()
    {

        return $this->_multiplication;

    }

}

class TextNode extends Node
{

    private $_value = '';

    public function setValue($value)
    {

        $this->_value = $value;

    }

    public function getValue()
    {

        return $this->_value;

    }

    public function getHtml()
    {

        if($this->getRightSibling()){
            return str_repeat($this->_value, $this->getMultiplication()) . $this->getRightSibling()->getHtml();
        }
        return str_repeat($this->_value, $this->getMultiplication());

    }

}

class Element extends Node
{

    private $_tag = '';
    private $_attributes = array();

    private static $_self_closing_tags = array(
        'hr', 'br', 'input', 'link', 'meta',
    );

    public function setTag($tag_name)
    {

        $this->_tag = $tag_name;

    }

    public function getTag()
    {

        return $this->_tag;

    }

    public function addAttributes($attributes)
    {

        $attributes = explode(' ', $attributes);
        foreach($attributes as $attr){
            $attr_values = explode('=', $attr);
            if(!isset($attr_values[1])){
                $attr_values[1] = $attr_values[0];
            }
            if('class' === $attr_values[0]){
                if(isset($this->_attributes['class'])){
                    $this->_attributes['class'] .= ' '.$attr_values[1];
                } else {
                    $this->_attributes['class'] = $attr_values[1];
                }
            } else {
                $this->_attributes[$attr_values[0]] = $attr_values[1];
            }
        }

    }

    public function getAttributes()
    {

        return $this->_attributes;

    }

    public function setValue($value)
    {

        $tn = new TextNode;
        $tn->setValue($value);
        $tn->addTo($this);

    }

    public function getHtml()
    {

        if($this->isRoot()){
            if($this->getFirstChild()){
                return $this->getFirstChild()->getHtml();
            } else {
                return '';
            }
        }
        if(in_array($this->_tag, self::$_self_closing_tags)){
            return $this->selfClosingElement();
        } else {
            $value = '';
            if($this->getFirstChild()){
                $value .= $this->getFirstChild()->getHtml();
            }
            $html = $this->closingElement($value);
            if($this->getRightSibling()){
                $html .= $this->getRightSibling()->getHtml();
            }
            return $html;
        }

    }

    private function closingElement($value)
    {

        return "<" . $this->_tag . $this->createAttributesList() . ">" . $value . "</" . $this->_tag . ">";


    }

    private function selfClosingElement()
    {

        return "<" . $this->_tag . $this->createAttributesList() . " />";

    }

    private function createAttributesList()
    {

        $attributes = '';
        foreach($this->_attributes as $attr => $value){
            $attributes .= " " . mb_strtolower($attr) . "=\"" . $value . "\"";
        }
        return $attributes;

    }

}