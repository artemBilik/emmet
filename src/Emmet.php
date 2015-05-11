<?php

namespace emmet;

use emmet\FiniteStateMachine as FSM;
require_once __DIR__ . DIRECTORY_SEPARATOR . "Node.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "FiniteStateMachine.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "PolishNotation.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "Data.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "EmmetException.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "Value.php";

class Emmet
{

    private $_tree = null;
    private $_emmet_string = '';
    private $_data = null;

    /**
     * @param string $emmet_string
     * @throws EmmetException
     */
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

    /**
     * parse emmet string
     * set self::_tree
     * @return null
     */
    private function build()
    {

        try {
            $emmet_string = 'root>' . $this->_emmet_string;
            $pn = new PolishNotation();
            $fsm = new FSM(FSM::TAG);
            $node = new Node(Node::ROOT);
            $value = new Value($this->_data);
            $str = '';
            $i = 0;
            $length = strlen($emmet_string) - 1;

            while (FSM::END !== $fsm->getState()) {
                if ($i > $length) {
                    $symbol = '';
                } else {
                    $symbol = $emmet_string[$i];
                }

                if ('/' === $symbol) {
                    if ($i === $length) {
                        $i++;
                        continue;
                    } else {
                        $str .= $emmet_string[++$i];
                        ++$i;
                        continue;
                    }
                }
                
                

                if (FSM::ERROR === $fsm->getState()) {
                    $this->throwException('There was an error in your Emmet string. ' . $this->getCheckTheDocumentation($i));
                }

                $fsm->setState($symbol);

                if ($fsm->isStateChanged()) {

                    $state = $fsm->getState();
                    $prev_state = $fsm->getPrevState();
                    $state_num = intval($state / 10);
                    $prev_state_num = intval($prev_state / 10);

                    switch ($prev_state) {
                        case FSM::OPERATOR:
                            $prev_sym = $emmet_string[$i - 2];
                            if (($prev_sym !== '^' && $prev_sym !== ')')&& '(' !== $str) {
                                $pn->setOperand($node);
                                $node = new Node();
                            }
                            $pn->setOperator($str);
                            break;


                        case FSM::TAG:
                            $value->addText($str);
                            if(1 !== $state_num){
                                $node->setTag($value);
                                $value = new Value($this->_data);
                            }
                            break;
                        case FSM::TAG_VAR:
                            $value->addVariable($str);
                            break;
                        case FSM::TAG_FUNC:
                            if(FSM::TAG !== $state){
                                $value->addFunction($str);
                            }
                            break;
                        case FSM::TAG_ARGS:
                            break;
                        case FSM::TAG_ARG_TXT:
                            $value->addArgument($str, Value::TXT);
                            break;
                        case FSM::TAG_ARG_VAR:
                            $value->addArgument($str, Value::VARIABLE);
                            break;
                        
                        case FSM::ID:
                            $value->addText($str);
                            if(2 !== $state_num){
                                $node->addAttributes($value);
                                if(!(3 === $state_num || 4 === $state_num)){
                                    $value = new Value($this->_data);
                                }
                            }
                            break;
                        case FSM::ID_VAR:
                            $value->addVariable($str);
                            break;
                        case FSM::ID_FUNC:
                            if(FSM::ID !== $state){
                                $value->addFunction($str);
                            }
                            break;
                        case FSM::ID_ARGS:
                            break;
                        case FSM::ID_ARG_TXT:
                            $value->addArgument($str, Value::TXT);
                            break;
                        case FSM::ID_ARG_VAR:
                            $value->addArgument($str, Value::VARIABLE);
                            break;
                        
                        
                        case FSM::CLASS_NAME:
                            $value->addText($str);
                            if(3 !== $state_num){
                                $node->addAttributes($value);
                                if(4 !== $state_num){
                                    $value = new Value($this->_data);
                                }
                            }
                            break;
                        case FSM::CLASS_NAME_VAR:
                            $value->addVariable($str);
                            break;
                        case FSM::CLASS_NAME_FUNC:
                            if(FSM::CLASS_NAME !== $state){
                                $value->addFunction($str);
                            }
                            break;
                        case FSM::CLASS_NAME_ARGS:
                            break;
                        case FSM::CLASS_NAME_ARG_TXT:
                            $value->addArgument($str, Value::TXT);
                            break;
                        case FSM::CLASS_NAME_ARG_VAR:
                            $value->addArgument($str, Value::VARIABLE);
                            break;
                        
                        
                        case FSM::ATTR:
                            $value->addText($str);
                            break;
                        case FSM::ATTR_VAR:
                            $value->addVariable($str);
                            break;
                        case FSM::ATTR_FUNC:
                            if(FSM::ATTR !== $state){
                                $value->addFunction($str);
                            }
                            break;
                        case FSM::ATTR_ARGS:
                            break;
                        case FSM::ATTR_ARG_TXT:
                            $value->addArgument($str, Value::TXT);
                            break;
                        case FSM::ATTR_ARG_VAR:
                            $value->addArgument($str, Value::VARIABLE);
                            break;
                        case FSM::AFTER_ATTR:
                            $node->addAttributes($value);
                            $value = new Value($this->_data);
                            break;
                        
                        
                        
                        case FSM::TEXT:
                            $value->addText($str);
                            break;
                        case FSM::TEXT_VAR:
                            $value->addVariable($str);
                            break;
                        case FSM::TEXT_FUNC:
                            if(FSM::TEXT !== $state){
                                $value->addFunction($str);
                            }
                            break;
                        case FSM::TEXT_ARGS:
                            break;
                        case FSM::TEXT_ARG_TXT:
                            $value->addArgument($str, Value::TXT);
                            break;
                        case FSM::TEXT_ARG_VAR:
                            $value->addArgument($str, Value::VARIABLE);
                            break;
                        case FSM::AFTER_TEXT:
                            $node->setValue($value);
                            $value = new Value($this->_data);
                            break;
                        
                        
                        case FSM::TEXT_NODE:
                            $value->addText($str);
                            break;
                        case FSM::TEXT_NODE_VAR:
                            $value->addVariable($str);
                            break;
                        case FSM::TEXT_NODE_FUNC:
                            if(FSM::TEXT_NODE !== $state){
                                $value->addFunction($str);
                            }
                            break;
                        case FSM::TEXT_NODE_ARGS:
                            break;
                        case FSM::TEXT_NODE_ARG_TXT:
                            $value->addArgument($str, Value::TXT);
                            break;
                        case FSM::TEXT_NODE_ARG_VAR:
                            $value->addArgument($str, Value::VARIABLE);
                            break;
                        case FSM::AFTER_TEXT_NODE:
                            $node->setType(Node::TEXT_NODE);
                            $node->setValue($value);
                            $value = new Value($this->_data);
                            break;
                        
                        
                        case FSM::MULTI:
                            $value->addText($str);
                            if(7 !== $state_num){
                                $node->setMultiplication($value->getToSet());
                                if(7 !== $state_num){
                                    $value = new Value($this->_data);
                                }
                            }
                            break;
                        case FSM::MULTI_VAR:
                            $value->addVariable($str);
                            break;
                        case FSM::MULTI_FUNC:
                            if(FSM::MULTI !== $state){
                                $value->addFunction($str);
                            }
                            break;
                        case FSM::MULTI_ARGS:
                            break;
                        case FSM::MULTI_ARG_TXT:
                            $value->addArgument($str, Value::TXT);
                            break;
                        case FSM::MULTI_ARG_VAR:
                            $value->addArgument($str, Value::VARIABLE);
                            break;
                        
                        
                        case FSM::HTML:
                            if(!$value->isEmpty()){
                                $node->setType(Node::HTML);
                                $node->setValue($value);
                                $value = new Value($this->_data);
                            }
                            break;
                        case FSM::HTML_VAR:
                            $value->addVariable($str);
                            break;
                        case FSM::HTML_FUNC:
                            if(FSM::HTML !== $state){
                                $value->addFunction($str);
                            }
                            break;
                        case FSM::HTML_ARGS:
                            break;
                        case FSM::HTML_ARG_TXT:
                            $value->addArgument($str, Value::TXT);
                            break;
                        case FSM::HTML_ARG_VAR:
                            $value->addArgument($str, Value::VARIABLE);
                            break;
                        default:
                            throw new \Exception('Unhandled Finite State Machine State. ' . $this->getCheckTheDocumentation($i));
                            break;
                    }
                    if((
                            FSM::OPERATOR === $state || FSM::TAG === $state || FSM::HTML_ARG_TXT === $state ||
                            FSM::TEXT_ARG_TXT === $state || FSM::TEXT_NODE_ARG_TXT === $state ||
                            FSM::TAG_ARG_TXT === $state || FSM::ID_ARG_TXT === $state ||
                            FSM::CLASS_NAME_ARG_TXT === $state || FSM::ATTR_ARG_TXT === $state)
                        && ('`' !== $symbol && '%' !== $symbol)
                    ){
                        $str = $symbol;
                    } elseif(FSM::ID === $state && 2 !== $prev_state_num){
                        $str = ' id=';
                    } elseif(FSM::CLASS_NAME === $state && (3 !== $prev_state_num || FSM::CLASS_NAME === $prev_state)){
                        $str = ' class=';
                    } elseif(FSM::ATTR === $state && (4 !== $prev_state_num)){
                        $str = ' ';
                    } else {
                        $str = '';
                    }
                } else {
                    if(FSM::SKIP !== $fsm->getState()){
                        $str .= $symbol;
                    }
                }
                ++$i;
            }

            if (FSM::END === $fsm->getState() && FSM::OPERATOR !== $fsm->getPrevState()) {
                $pn->setOperand($node);
            }

            $tree = $pn->generateTree();
            if ($tree instanceof Node) {
                $this->_tree = $tree;
            } else {
                throw new \Exception($tree);
            }
        } catch(\Exception $e){
            $this->throwException($e->getMessage());
        }

    }


    /**
     * @param array $data
     * Create an html string from self::_tree
     * @return string
     */
    public function create(array $data = [])
    {

        try{
            $this->_data->setData($data);
            return $this->_tree->getHtml();
        } catch(\Exception $e){
            $this->throwException($e->getMessage());
        }

    }

    /**
     * @param $i
     * @return string
     */
    private function getCheckTheDocumentation($i)
    {

        $i = $i - 6;
        return  'Check the documentation for the right syntax to use near "' . htmlspecialchars(substr($this->_emmet_string, 0, $i)) . '<strong style="color:red;">' . htmlspecialchars(substr($this->_emmet_string, $i)) . '</strong>".';

    }

    public static function addFunctions(array $functions)
    {

        Data::setFunctions($functions);

    }

    /**
     * drop self::_tree
     */
    public function __destruct()
    {

        if($this->_tree instanceof Tree) {
            $this->_tree->drop();
        }

    }

    /**
     * @param string $message
     * @throws EmmetException
     */
    private function throwException($message)
    {

        throw new EmmetException($message);

    }

}