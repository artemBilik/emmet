<?php

namespace emmet;

use emmet\FiniteStateMachine as FSM;
require_once __DIR__ . DIRECTORY_SEPARATOR . "Element.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "FiniteStateMachine.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "PolishNotation.php";

class Emmet
{

    private $_tree = null;
    private $_emmet_string = '';
    /*
     * Create object's collection for generate html
     */
    public function build($emmet_string)
    {

        $this->_emmet_string = $emmet_string;
        $emmet_string        = 'root>'.$emmet_string;

        $fsm     = new FSM(FSM::GET_TAG);
        $pn      = new PolishNotation();
        $element = new Element();
        $element->setRoot();
        $value   = '';
        $i       = 0;
        $length = strlen($emmet_string) - 1;

        while(FSM::END !== $fsm->getState()){
            if($i > $length){
                $symbol = '';
            } else {
                $symbol = $emmet_string[$i];
            }
            if(FSM::ERROR === $fsm->getState()){
                $this->throwException('There was an error in your Emmet string. ' . $this->getCheckTheDocumentation($emmet_string, $i));
            }
            $fsm->setState($symbol);
            if($fsm->isStateChanged()){
                switch($fsm->getPrevState()){
                    case FSM::GET_TAG:
                        $element->setTag($value);
                        break;
                    case FSM::SET_OPERATOR:
                        if(!in_array($emmet_string[$i - 2], array('^', ')')) && '(' !== $value){
                            $pn->setOperand($element);
                            $element = new Element();
                        }
                        if(true !== ($pn_operator_status = $pn->setOperator($value))){
                            $this->throwException($pn_operator_status.' '.$this->getCheckTheDocumentation($emmet_string, $i));
                        }
                        break;
                    case FSM::GET_ID:
                        $element->addAttribute('id='.substr($value, 1));
                        break;
                    case FSM::GET_CLASS:
                        $element->addAttribute('class='.substr($value, 1));
                        break;
                    case FSM::GET_ATTR:
                        $element->addAttribute(substr($value, 1));
                        break;
                    case FSM::GET_TEXT:
                        $element->setValue(substr($value,1));
                        break;
                    case FSM::GET_MULTIPLICATION:
                        $element->setMultiplication(substr($value,1));
                        break;
                    case FSM::GET_TEXT_NODE:
                        $element = new TextNode();
                        $element->setValue(substr($value,1));
                        break;
                    case FSM::WAIT_AFTER_ATTR:
                        break;
                    case FSM::WAIT_AFTER_TEXT_NODE:
                        break;
                    case FSM::WAIT_AFTER_TEXT:
                        break;
                    default:
                        $this->throwException('Unhandled Finite State Machine State. '.$this->getCheckTheDocumentation($emmet_string, $i));
                        break;
                }
                if(FSM::END === $fsm->getState() && FSM::SET_OPERATOR !== $fsm->getPrevState()){
                    $pn->setOperand($element);
                }
                $value = $symbol;
            } else {
                $value .= $symbol;
            }
            ++$i;
        }

        $tree = $pn->generateTree();
        if($tree instanceof Node){
            $this->_tree = $tree;
        } else {
            $this->throwException($tree);
        }

    }
    /*
     * Create html by object's collection
     */
    public function generate($data)
    {

        return $this->_tree->getHtml();

    }

    /*
     * Create an html string
     */
    public function create($emmet_string, $data = array())
    {

        $this->build($emmet_string);
        return $this->generate($data);

    }

    private function getCheckTheDocumentation($emmet_string, $i)
    {

        return  'Check the documentation for the right syntax to use near "' . substr($emmet_string, 5, $i - 5) . '<strong style="color:red;">' . substr($emmet_string, $i) . '</strong>".';

    }

    public function __destruct()
    {

        $this->_tree->drop();

    }

    public function throwException($message)
    {

        echo $message; exit;

    }

}