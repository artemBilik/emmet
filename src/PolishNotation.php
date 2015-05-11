<?php

namespace emmet;

class PolishNotation
{

    private $_output = array();
    private $_stack  = array();

    /**
     * @param char $operator
     * @return bool
     * @throws \Exception
     */
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
                        $this->throwException('Incorrectly placed brackets.');
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
                        $this->throwException('The "^" operator is excess.');
                    }
                    $this->_output[] = $operator;
                } while('>' !== $operator);
                $this->_output[] = '^';
                return true;
            default:
                $this->throwException('Undefined operator "' . $operator . '".');
        }

    }
    /**
     * @param Node $operand
     */
    public function setOperand(Node $operand)
    {

        $this->_output[] = $operand;

    }
    /**
     * @return array
     * @throws \Exception
     */
    private function endOutput()
    {

        while(true){
            $operator = array_shift($this->_stack);
            if(null === $operator){
                break;
            } elseif('(' === $operator){
                $this->throwException('Incorrectly placed brackets.');
            } else {
                $this->_output[] = $operator;
            }
        }

        return $this->_output;

    }
    /**
     * @return Node
     * @throws \Exception
     */
    public function generateTree()
    {
        $this->endOutput();
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
                        $this->throwException('the number of operands less than operations.');
                    }
                } elseif('+' === $el){
                    $right = array_shift($this->_stack);
                    $left = array_shift($this->_stack);
                    if($left instanceof Node && $right instanceof Node){
                        $left->addSibling($right);
                        array_unshift($this->_stack, $left);
                    } else {
                        $this->throwException('the number of operands less than operations.');
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
                                $this->throwException('You are out of the tree. Check "^" operator.');
                            }
                        }
                    } else {
                        $this->throwException('the number of operands less than operations.');
                    }
                } else {
                    $this->throwException('Undefined Operation. "'.$el.'".');
                }
            }
        }

        $res = array_shift($this->_stack);
        while($res->hasParent()){
            $res = $res->getParent();
        }
        return $res;

    }
    /**
     * @param array $output
     */
    protected function setOutput(array $output)
    {

        $this->_output = $output;

    }
    /**
     * @return array
     */
    public function getOutput()
    {

        return $this->endOutput();

    }
    /**
     * @param string $message
     * @throws \Exception
     */
    private function throwException($message)
    {

        throw new \Exception($message);

    }


}