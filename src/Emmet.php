<?php

namespace emmet;

use emmet\FiniteStateMachine as FSM;
require_once __DIR__ . DIRECTORY_SEPARATOR . "Element.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "FiniteStateMachine.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "PolishNotation.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "Data.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "EmmetException.php";

class Emmet extends FiniteStateMachine
{

    private $_tree = null;
    private $_emmet_string = '';
    private $_data = null;

    public function __construct($emmet_string)
    {

        if(is_string($emmet_string)) {
            $this->_emmet_string = (string)$emmet_string;
        } else {
            $this->throwException('Emmet::__construct(string emmet_string). emmet_string is not a string.');
        }
        $this->_data = new Data();
        $this->build();

    }

    /*
     * Parse emmet string;
     * create an element tree;
     */
    private function build()
    {

        $element = new Element();
        $element->setRoot();
        $this->_tree = $element;

    }

    /*
     * Create an html string
     */
    public function create($data = [])
    {

        return $this->_tree->getHtml(function($variable) use($data){
            extract($data);
            return eval('return $'.$variable . ';');
        });

    }

    private function getCheckTheDocumentation($i)
    {

        return  'Check the documentation for the right syntax to use near "' . htmlspecialchars(substr($this->_emmet_string, 0, $i)) . '<strong style="color:red;">' . htmlspecialchars(substr($this->_emmet_string, $i)) . '</strong>".';

    }

    public function __destruct()
    {

        $this->_tree->drop();

    }

    private function throwException($message)
    {

        throw new EmmetException($message);

    }

}






//private function build()
//{
//
//    $emmet_string        = 'root>'.$this->_emmet_string;
//
//    $fsm     = new FSM(FSM::GET_TAG);
//    $pn      = new PolishNotation();
//    $element = new Element();
//    $element->setRoot();
//    $value   = '';
//    $i       = 0;
//    $length = strlen($emmet_string) - 1;
//
//    while(FSM::END !== $fsm->getState()){
//        if($i > $length){
//            $symbol = '';
//        } else {
//            $symbol = $emmet_string[$i];
//        }
//
//        if('/' === $symbol){
//            if($i === $length){
//                $i++;
//                continue;
//            } else {
//                $value .= $emmet_string[++$i];
//                ++$i;
//                continue;
//            }
//        }
//
//        if(FSM::ERROR === $fsm->getState()){
//            $this->throwException('There was an error in your Emmet string. ' . $this->getCheckTheDocumentation($emmet_string, $i));
//        }
//        $fsm->setState($symbol);
//        if($fsm->isStateChanged()){
//            switch($fsm->getPrevState()){
//                case FSM::GET_TAG:
//                    $element->setTag($value);
//                    break;
//                case FSM::SET_OPERATOR:
//                    if(!in_array($emmet_string[$i - 2], array('^', ')')) && '(' !== $value){
//                        $pn->setOperand($element);
//                        $element = new Element();
//                    }
//                    if(true !== ($pn_operator_status = $pn->setOperator($value))){
//                        $this->throwException($pn_operator_status.' '.$this->getCheckTheDocumentation($emmet_string, $i));
//                    }
//                    break;
//                case FSM::GET_ID:
//                    $element->addAttributes('id='.substr($value, 1));
//                    break;
//                case FSM::GET_CLASS:
//                    $element->addAttributes('class='.substr($value, 1));
//                    break;
//                case FSM::GET_ATTR:
//                    $element->addAttributes(substr($value, 1));
//                    break;
//                case FSM::GET_TEXT:
//                    $element->setValue(substr($value,1));
//                    break;
//                case FSM::GET_MULTIPLICATION:
//                    $element->setMultiplication(substr($value,1));
//                    break;
//                case FSM::GET_TEXT_NODE:
//                    $element = new TextNode();
//                    $element->setValue(substr($value,1));
//                    break;
//                case FSM::WAIT_AFTER_ATTR:
//                    break;
//                case FSM::WAIT_AFTER_TEXT_NODE:
//                    break;
//                case FSM::WAIT_AFTER_TEXT:
//                    break;
//                default:
//                    $this->throwException('Unhandled Finite State Machine State. '.$this->getCheckTheDocumentation($emmet_string, $i));
//                    break;
//            }
//            if(FSM::END === $fsm->getState() && FSM::SET_OPERATOR !== $fsm->getPrevState()){
//                $pn->setOperand($element);
//            }
//            $value = $symbol;
//        } else {
//            $value .= $symbol;
//        }
//        ++$i;
//    }
//
//    $tree = $pn->generateTree();
//    if($tree instanceof Node){
//        $this->_tree = $tree;
//    } else {
//        $this->throwException($tree);
//    }
//
//}