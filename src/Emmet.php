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
            $attrs_states = [FSM::ID, FSM::CLASS_NAME, FSM::ATTR];
            $states_to_save = [FSM::OPERATOR, FSM::ARG_TXT, FSM::TAG];

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
                        $value .= $emmet_string[++$i];
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
                    $global_state = $fsm->getGlobalState();

                    switch ($prev_state) {
                        case FSM::TAG:
                            if('' !== $str && $str !== ' '){
                                $value->addText($str);
                            }
                            $node->setTag($value);
                            break;
                        case FSM::OPERATOR:
                            $prev_sym = $emmet_string[$i - 2];
                            if (($prev_sym !== '^' && $prev_sym !== ')')&& '(' !== $str) {
                                $pn->setOperand($node);
                                $node = new Node();
                            }
                            $pn->setOperator($str);
//                            if (true !== ($pn_operator_status = $pn->setOperator($str))) {
//                                $this->throwException($pn_operator_status . ' ' . $this->getCheckTheDocumentation($i));
//                            }
                            break;
                        case FSM::ID:
                            if('' !== $str){
                                $value->addText(' id='.$str);
                            }
                            $node->addAttributes($value);
                            break;
                        case FSM::CLASS_NAME:
                            $value->addText(' class='.$str);
                            $node->addAttributes($value);
                            break;
                        case FSM::ATTR:
                            if('' !== $str){
                                $value->addText(' '.$str);
                            }
                            $node->addAttributes($value);
                            break;
                        case FSM::AFTER_ATTR:
                            break;
                        case FSM::TEXT:
                            if('' !== $str){
                                $value->addText($str);
                            }
                            $node->setValue($value);
                            break;
                        case FSM::AFTER_TEXT:
                            break;
                        case FSM::TEXT_NODE:
                            $node->setType(Node::TEXT_NODE);
                            $value->addText($str);
                            $node->setValue($value);
                            break;
                        case FSM::AFTER_TEXT_NODE:
                            break;
                        case FSM::MULTI:
                            $node->setMultiplication($str);
                            break;
                        case FSM::VARIABLE:
                            $value->addVariable($str);
                            break;
                        case FSM::FUNC:
                            if('' !== $str && ' ' !== $str) {
                                $value->addFunction($str);
                            }
                            break;
                        case FSM::ARGS:
                            break;
                        case FSM::ARG_TXT:
                            if('' !== $str){
                                $value->addArgument($str, Value::TXT);
                            }
                            break;
                        case FSM::ARG_VAR:
                            if('' !== $str){
                                $value->addArgument($str, Value::VARIABLE);
                            }
                            break;
                        case FSM::HTML:
                            $node->setType(Node::HTML);
                            $node->setValue($value);
                            break;
                        default:
                            throw new \Exception('Unhandled Finite State Machine State. ' . $this->getCheckTheDocumentation($i));
                            break;
                    }
                        //echo getStateName($state) . ' - ' . getStateName($prev_state) . '<br>';
                    if(
                        (in_array($state, $attrs_states) && in_array($prev_state, $attrs_states)) ||
                        ($prev_state === FSM::VARIABLE || $prev_state === FSM::FUNC) ||
                        ((FSM::VARIABLE === $state || FSM::FUNC === $state) && FSM::HTML !== $global_state && FSM::TEXT !== $global_state ) ||
                        (FSM::ARG_TXT === $state || FSM::ARGS === $state || FSM::ARG_VAR === $state) ||
                        (FSM::ARG_TXT === $prev_state || FSM::ARGS === $prev_state || FSM::ARG_VAR === $prev_state)
                    ){
                    } else {
                        $value = new Value($this->_data);
                    }


                    if(($state == FSM::OPERATOR || $state === FSM::TAG || $state === FSM::ARG_TXT) && $symbol !== '`' && $symbol !== '%'){
                        $str = $symbol;
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