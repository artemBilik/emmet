<?php

namespace artem_c\emmet;

abstract class Tree
{
    /**
     * Don't forget to call self::drop() method after usage
     */
    private $_first_child   = null;
    private $_right_sibling = null;
    private $_parent        = null;
    private $_is_root       = false;

    /**
     * @param Tree $parent
     */
    public function addTo(Tree $parent)
    {
        if($this->_is_root){
            $this->throwException('You cann\'t add Root node to another node');
        }
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

    /**
     * @param Tree $sibling
     * @return bool
     */
    public function addSibling(Tree $sibling)
    {

        if(null !== $this->_right_sibling){
            return $this->_right_sibling->addSibling($sibling);
        }
        $sibling->_parent = $this->_parent;
        $this->_right_sibling = $sibling;
        return true;

    }

    /**
     * @return Tree|null
     */
    public function getParent()
    {

        return $this->_parent;

    }

    /**
     * @return Tree|null
     */
    public function getFirstChild()
    {

        return $this->_first_child;

    }

    /**
     * @return Tree|null
     */
    public function getRightSibling()
    {

        return $this->_right_sibling;

    }

    /**
     * @return Tree|null
     */
    public function hasParent()
    {

        return (null === $this->_parent ? false : true);

    }

    /**
     *   * flush it out
     */
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

    /**
     * @throws \Exception
     */
    protected function setRoot()
    {
        if(!$this->hasParent()){
            $this->_is_root = true;
        } else {
            $this->throwException('You can not set to root element which has a parent element.');
        }

    }

    /**
     * @return bool
     */
    public function isRoot()
    {

        return $this->_is_root;

    }

    /**
     * @param string $message
     * @throws \Exception
     */
    protected function throwException($message)
    {

        throw new \Exception($message);

    }

}

class Node extends Tree
{

    const TAG       = 1;
    const ROOT      = 2;
    const TEXT_NODE = 3;
    const HTML      = 4;

    private $_type = null;
    private $_tag = '';
    private $_value = '';
    private $_attributes = '';
    private $_id = '';
    private $_class = '';
    private $_multiplication = 1;
    private $_number = 0;
    private $_minimized = false;

    private static $_self_closing_tags  = '%hr%br%input%link%meta%img%';

    public function __construct($type = self::TAG)
    {

        $this->setType($type);

    }

    /**
     * @param self::TAG|self::ROOT|self::TEXT_NODE|self::HTML $type
     * @throws \Exception
     */
    public function setType($type)
    {

        if(self::TAG === $type || self::TEXT_NODE === $type || self::HTML === $type){
            $this->_type = $type;
        } elseif(self::ROOT === $type){
            $this->_type = $type;
            $this->setRoot();
        } else {
            $this->throwException('Undefined type "' . $type .'" in Node::setType($type)');
        }

    }

    /**
     * @param Value $tag
     */
    public function setTag(Value $tag)
    {

        $this->_tag = $tag;

    }

    /**
     * @param Value $attributes
     */
    public function addAttributes(Value $attributes)
    {

        $this->_attributes = $attributes;

    }
    /**
     * @param Value $id
     */
    public function addId(Value $id)
    {

        $this->_id = $id;

    }
    /**
     * @param Value $class
     */
    public function addClass(Value $class)
    {

        $this->_class = $class;

    }

    /**
     * @param Value $value
     */
    public function setValue(Value $value)
    {

        if(self::TEXT_NODE === $this->_type){
            $this->_value = $value;
        } elseif(self::TAG === $this->_type){
            $tn = new self(self::TEXT_NODE);
            $tn->setValue($value);
            $tn->addTo($this);
        } elseif(self::HTML){
            $this->_value = $value;
        }

    }

    /**
     * @param $number
     */
    public function setMultiplication(Value $number)
    {

        $this->_multiplication = $number;

    }
    public function getNumber()
    {

        return $this->get('multiplication');

    }
    private function setNumber($number)
    {

        $this->_number = intval($number);

    }
    public function getHtml($number = 0)
    {

        $this->setNumber($number);
        if(self::TAG === $this->_type){
            return $this->getHtmlForTag($number);
        } elseif(self::HTML === $this->_type){
            return $this->getHtmlForHtml($number);
        } elseif(self::TEXT_NODE === $this->_type) {
            return $this->getHtmlForTextNode($number);
        } elseif(self::ROOT === $this->_type) {
            return $this->getHtmlForRoot();
        } else {
            return '';
        }

    }

    private function getHtmlForTag($number)
    {

        $result = '';
        $multiplication = $this->get('multiplication');
        for($i = 0; $i < $multiplication; $i++){
            if(1 < $multiplication) {
                $this->setNumber($i);
            }
            $tag = $this->get('tag');
            if(false !== strpos(self::$_self_closing_tags, '%'.$tag.'%')){ // @todo add ' ' $tag throw tests
                $result .= $this->selfClosingElement($tag);
            } else {
                $value = '';
                $first_child = $this->getFirstChild();
                if($first_child){
                    $value .= $first_child->getHtml($this->_number);
                }
                $html = $this->closingElement($tag, $value);
                $result .= $html;
            }
        }

        $right_sibling = $this->getRightSibling();
        if($right_sibling){
            $result .= $right_sibling->getHtml($number);
        }

        return $result;

    }
    private function getHtmlForHtml($number)
    {

        $result = '';
        $multiplication = $this->get('multiplication');
        for($i = 0; $i < $multiplication; ++$i){
            if(1 < $multiplication){
                $this->setNumber($i);
            }
            $value = '';
            $first_child = $this->getFirstChild();
            if($first_child){
                $first_child->setNumber($i);
                $value .= $first_child->getHtml($this->_number);
            }

            $html = $this->get('value', $value);
            $result .= $html;

        }
        $right_sibling = $this->getRightSibling();
        if($right_sibling){
            $result .= $right_sibling->getHtml($number);
        }
        return $result;

    }
    private function getHtmlForTextNode($number)
    {

        $result = '';
        $multiplication = $this->get('multiplication');
        for($i = 0; $i < $multiplication; ++$i){
            if(1 < $multiplication){
                $this->setNumber($i);
            }

            $result .= $this->get('value');
        }
        $right_sibling = $this->getRightSibling();
        if($right_sibling){
            return $result . $right_sibling->getHtml($number);
        }

        return $result;

    }
    private function getHtmlForRoot()
    {

        $first_child = $this->getFirstChild();
        if(null != $first_child){
            return $first_child->getHtml(0);
        }

    }

    private function closingElement($tag, $value)
    {

        return "<" . $tag . $this->getAttributes() . ">" . $value . "</" . $tag . ">";

    }

    private function selfClosingElement($tag)
    {

        return "<" . $tag . $this->getAttributes() . " />";

    }

    private function getAttributes()
    {

        $attributes = [];
        $id = $this->get('id');
        if($id){
            $attributes['id'] = $id;
        }
        $class = $this->get('class');
        if($class){
            $attributes['class'] = $class;
        }
        foreach(explode(' ', $this->get('attributes')) as $attr){
            $attr = explode('=', $attr);
            if('class' === $attr[0]){
                if(isset($attributes['class'])){
                    $attributes['class'] .= ' '. ((isset($attr[1])) ? $attr[1] : '');
                } else {
                    $attributes['class'] = ((isset($attr[1])) ? $attr[1] : '');
                }
            } elseif('' === $attr[0]) {
                continue;
            } else {
                $attributes[$attr[0]] = (isset($attr[1])) ? $attr[1] : $attr[0];
            }
        }

        $attr_str = '';
        foreach($attributes as $key => $attr){
            $attr_str .= ' ' . $key . '="' . $attr . '"';
        }
        return $attr_str;

    }

    private function get($var, $value = null)
    {


        $var = '_'.$var;
        if($this->$var instanceof Value){
            $value = $this->$var->get($this->_number, $value);
            if(false === $this->_minimized){
                $this->$var = $this->$var->getToSet();
                $this->_minimized = true;
            }
            return $value;
        } else {
            return $this->$var;
        }

    }

}