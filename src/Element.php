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
    protected $_number = 0;

    abstract function getHtml(callable $getData);

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

    public function createValue($value, callable $getData)
    {

        $result = '';
        $variable = '';
        $get_var = false;

        for($i = 0, $length = strlen($value); $i < $length; ++$i){
            $symbol = $value{$i};
            if('$' === $value{$i}){
                $symbol = $this->_number;
            }

            if('`' === $symbol){
                if(!$get_var){
                    $get_var = true;
                } else {
                    $result .= $getData($variable);
                    $variable = '';
                    $get_var = false;
                }
            } else {
                if($get_var){
                    $variable .= $symbol;
                } else {
                    $result .= $symbol;
                }
            }
        }

        return $result;

    }

    public function setNumber($number)
    {

        $this->_number = intval($number);

    }

}

class TextNode extends Node
{

    private $_value = '';

    public function setValue($value)
    {

        $this->_value = $value;

    }

    public function getValue(callable $getData)
    {

        return $this->createValue($this->_value, $getData);

    }

    public function getHtml(callable $getData)
    {

        $result = '';
        for($i = 0; $i < $this->getMultiplication(); ++$i){
            if(1 !== $this->getMultiplication()){
                $this->setNumber($i);
            }
            $result .= $this->getValue($getData);
        }
        if($this->getRightSibling()){
            return $result . $this->getRightSibling()->getHtml($getData);
        }

        return $result;

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

    public function getHtml(callable $getData)
    {

        if($this->isRoot()){
            if($this->getFirstChild()){
                return $this->getFirstChild()->getHtml($getData);
            } else {
                return '';
            }
        }
        $result = '';
        for($i = 0; $i < $this->getMultiplication(); $i++){
            if(1 !== $this->getMultiplication()) {
                $this->setNumber($i);
            }
            if(in_array($this->_tag, self::$_self_closing_tags)){
                $result .= $this->selfClosingElement($getData);
            } else {
                $value = '';
                if($this->getFirstChild()){
                    $this->getFirstChild()->setNumber($i);
                    $value .= $this->getFirstChild()->getHtml($getData);
                }
                $html = $this->closingElement($value, $getData);
                if($this->getRightSibling()){
                    $html .= $this->getRightSibling()->getHtml($getData);
                }
                $result .= $html;
            }
        }
        return $result;


    }

    private function closingElement($value, callable $getData)
    {

        return "<" . $this->createValue($this->_tag, $getData) . $this->createAttributesList($getData) . ">" . $value . "</" . $this->createValue($this->_tag, $getData) . ">";


    }

    private function selfClosingElement(callable $getData)
    {

        return "<" . $this->createValue($this->_tag, $getData) . $this->createAttributesList($getData) . " />";

    }

    private function createAttributesList(callable $getData)
    {

        $attributes = '';
        foreach($this->_attributes as $attr => $value){
            $attributes .= " " . mb_strtolower($this->createValue($attr, $getData)) . "=\"" . $this->createValue($value, $getData) . "\"";
        }
        return $attributes;

    }

}