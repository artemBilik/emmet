<?php

namespace emmet;


/*
 * @todo $ in multiplication
 */

abstract class Node
{

    protected $_first_child = null;
    protected $_right_sibling = null;
    protected $_parent = null;
    protected $_is_root = false;
    protected $_multiplication = 1;

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
        $this->_right_sibling = $sibling;
        return true;

    }

    public function getParent()
    {

        return $this->_parent;

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

        $this->_is_root = true;

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

}

class TextNode extends Node
{

    private $_value;

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

        if($this->_right_sibling){
            return str_repeat($this->_value, $this->_multiplication) . $this->_right_sibling->getHtml();
        }
        return str_repeat($this->_value, $this->_multiplication);

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

    public function addAttribute($attributes)
    {

        $attributes = explode(' ', $attributes);
        foreach($attributes as $attr){
            $attr_values = explode('=', $attr);
            if(!isset($attr_values[1])){
                $attr_values[1] = $attr_values[0];
            }
            if('class' === $attr_values[0]){
                if(isset($this->_attributes['class'])){
                    $this->_attributes['class'] += ' '.$attr_values[1];
                } else {
                    $this->_attributes['class'] = $attr_values[1];
                }
            } else {
                $this->_attributes[$attr_values[0]] = $attr_values[1];
            }
        }

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
            if($this->_first_child){
                return $this->_first_child->getHtml();
            } else {
                return '';
            }
        }
        if(in_array($this->_tag, self::$_self_closing_tags)){
            return $this->selfClosingElement();
        } else {
            $value = '';
            if($this->_first_child){
                $value .= $this->_first_child->getHtml();
            }
            $html = $this->closingElement($value);
            if($this->_right_sibling){
                $html .= $this->_right_sibling->getHtml();
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