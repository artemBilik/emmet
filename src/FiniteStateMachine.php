<?php

namespace emmet;

class FiniteStateMachine
{

    // FSM STATES
    const SET_OPERATOR         = 1;
    const GET_TAG              = 2;
    const GET_ID               = 3;
    const GET_CLASS            = 4;
    const GET_ATTR             = 5;
    const WAIT_AFTER_ATTR      = 6;
    const GET_TEXT             = 7;
    const WAIT_AFTER_TEXT      = 8;
    const ERROR                = 9;
    const GET_MULTIPLICATION   = 10;
    const GET_TEXT_NODE        = 11;
    const WAIT_AFTER_TEXT_NODE = 12;
    const END                  = 13;

    private $_state = null;
    private $_is_state_changed = false;
    private $_prev_state = null;

    private static $_states = array(
        self::SET_OPERATOR, self::GET_TAG, self::GET_ID, self::GET_CLASS, self::GET_ATTR, self::WAIT_AFTER_ATTR, self::GET_TEXT,
        self::WAIT_AFTER_TEXT, self::ERROR, self::GET_MULTIPLICATION, self::GET_TEXT_NODE, self::WAIT_AFTER_TEXT_NODE, self::END
    );
    private static $_initial_states = array(
        self::GET_TAG,
    );
    //@todo check final states
    private static $_final_states = array(
        self::END,
    );
    private static $_map = array(
        self::SET_OPERATOR => array(
            'operator' => self::SET_OPERATOR,
            '#' => self::GET_ID,
            '.' => self::GET_CLASS,
            '[' => self::GET_ATTR,
            '*' => self::GET_MULTIPLICATION,
            '{' => self::GET_TEXT_NODE,
            '' => self::END,
            'another' => self::GET_TAG,
        ),
        self::GET_TAG => array(
            'operator' => self::SET_OPERATOR,
            '#' => self::GET_ID,
            '.' => self::GET_CLASS,
            '[' => self::GET_ATTR,
            '{' => self::GET_TEXT,
            '*' => self::GET_MULTIPLICATION,
            '' => self::END,
        ),
        self::GET_ID => array(
            'operator' => self::SET_OPERATOR,
            '#' => self::GET_ID,
            '.' => self::GET_CLASS,
            '[' => self::GET_ATTR,
            '{' => self::GET_TEXT,
            '*' => self::GET_MULTIPLICATION,
            '' => self::END,
        ),
        self::GET_CLASS => array(
            'operator' => self::SET_OPERATOR,
            '#' => self::GET_ID,
            '.' => self::GET_CLASS,
            '[' => self::GET_ATTR,
            '{' => self::GET_TEXT,
            '*' => self::GET_MULTIPLICATION,
            '' => self::END,
        ),
        self::GET_ATTR => array(
            ']' => self::WAIT_AFTER_ATTR,
            '' => self::ERROR,
        ),
        self::WAIT_AFTER_ATTR => array(
            'operator' => self::SET_OPERATOR,
            '' => self::END,
            '*' => self::GET_MULTIPLICATION,
            '{' => self::GET_TEXT,
        ),
        self::GET_TEXT => array(
            '}' => self::WAIT_AFTER_TEXT,
            '' => self::ERROR,
        ),
        self::WAIT_AFTER_TEXT => array(
            'operator' => self::SET_OPERATOR,
            '' => self::END,
            '*' => self::GET_MULTIPLICATION,
        ),
        self::GET_MULTIPLICATION => array(
            'operator' => self::SET_OPERATOR,
            '' => self::END,
        ),
        self::GET_TEXT_NODE => array(
            '}' => self::WAIT_AFTER_TEXT_NODE,
            '' => self::ERROR,
        ),
        self::WAIT_AFTER_TEXT_NODE => array(
            'operator' => self::SET_OPERATOR,
            '*' => self::GET_MULTIPLICATION,
            '' => self::END,
        ),
        self::END => array(
            'another' => self::ERROR,
        ),
    );
    private static $_operators = array(
        '>', '+', '(', ')', '^',
    );


    public function __construct($state = null)
    {

        if(null === $state){
            $state = self::GET_TAG;
        }
        if(!in_array($state, self::$_initial_states)){
            throw new \InvalidArgumentException('Undefined Initial State. Use the list of the initial states.');
        } else {
            $this->_state = $state;
        }

    }

    public function getState()
    {

        return $this->_state;

    }

    public function setState($symbol)
    {

        if(self::ERROR === $this->_state){
            $this->throwException('Finite State Machine Error.');
        }

        $symbol_type = $symbol;
        if(in_array($symbol, self::$_operators)){
            $symbol_type = 'operator';
        }

        $map = self::$_map[$this->_state];
        if(array_key_exists($symbol_type, $map)){
           $this->setNewState($map[$symbol_type]);
        } else {
            if(array_key_exists('another', $map)){
                $this->setNewState($map['another']);
            } else {
                $this->_is_state_changed = false;
            }
        }


    }

    private function setNewState($state)
    {

        $this->_prev_state       = $this->_state;
        $this->_is_state_changed = true;
        $this->_state            = $state;

    }

    public function isStateChanged()
    {

        return $this->_is_state_changed;

    }

    public function getPrevState()
    {

        return $this->_prev_state;

    }

    private function throwException($message)
    {

        throw new \Exception($message);

    }

}