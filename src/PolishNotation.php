<?php

namespace emmet;

class PolishNotation
{

    private $_output = array();
    private $_stack  = array();

    public function setOperator($operator)
    {

        switch($operator){
            case '(':
                array_unshift($this->_stack, '(');
                return true;
            case ')':
                unset($operator);
                $up = -2;
                do{
                    if(isset($operator)){
                        $this->_output[] = $operator;
                    }
                    $operator = array_shift($this->_stack);
                    if('>' === $operator){
                        $up++;
                    }
                    if(null === $operator){
                        return 'Incorrectly placed brackets.';
                    }
                } while('(' !== $operator);
                while($up > 0){
                    $this->_output[] = '^';
                    $up--;
                }
                return true;
            case '+':
                array_unshift($this->_stack, '+');
                return true;
            case '>':
                array_unshift($this->_stack, '>');
                return true;
            case '^':
                do{
                    $operator = array_shift($this->_stack);
                    if(empty($this->_stack)){
                        return 'The "^" operator is excess.';
                    }
                    $this->_output[] = $operator;
                } while('>' !== $operator);
                $this->_output[] = '^';
                return true;
            default:
                return 'Undefined operator "' . $operator . '".';
        }


    }

    public function setOperand(Node $operand)
    {

        $this->_output[] = $operand;

    }

    public function setOutput()
    {

        while(true){
            $operator = array_shift($this->_stack);
            if(null === $operator){
                break;
            } elseif('(' === $operator){
                return 'Incorrectly placed brackets.';
            } else {
                $this->_output[] = $operator;
            }
        }

        return true;

    }

    public function generateTree()
    {
        $this->setOutput();
        while(!empty($this->_output)){
            $el = array_shift($this->_output);
            if($el instanceof Node){
                array_unshift($this->_stack, $el);
            } else {
                if('>' === $el){
                    $child = array_shift($this->_stack);
                    $parent = array_shift($this->_stack);
                    if($parent instanceof Node && $child instanceof Node){
                        $child->addTo($parent);
                        array_unshift($this->_stack, $parent);
                    } else {
                        return 'the number of operands less than operations.';
                    }
                } elseif('+' === $el){
                    $right = array_shift($this->_stack);
                    $left = array_shift($this->_stack);
                    if($left instanceof Node && $right instanceof Node){
                        $left->addSibling($right);
                        array_unshift($this->_stack, $left);
                    } else {
                        return 'the number of operands less than operations.';
                    }
                } elseif('^' === $el){
                    $child = array_shift($this->_stack);
                    if($child instanceof Node){
                        $parent = $child->getParent();
                        if(null !== $parent){
                            array_unshift($this->_stack, $child->getParent());
                        } else {
                            if(!$child->isRoot()){
                                array_unshift($this->_stack, $child);
                            } else {
                                return 'You are out of the tree. Check "^" operator.';
                            }
                        }
                    } else {
                        return 'the number of operands less than operations.';
                    }
                } else {
                    return 'Undefined Operation. "'.$el.'".';
                }
            }
        }

        $res = array_shift($this->_stack);
        while($res->hasParent()){
            $res = $res->getParent();
        }
        return $res;

    }

}