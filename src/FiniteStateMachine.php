<?php

namespace emmet;

class FiniteStateMachine
{

    // FSM STATES
    const OPERATOR        = 1;
    const TAG             = 2;
    const ID              = 3;
    const CLASS_NAME      = 4;
    const ATTR            = 5;
    const AFTER_ATTR      = 6;
    const TEXT            = 7;
    const AFTER_TEXT      = 8;
    const TEXT_NODE       = 9;
    const AFTER_TEXT_NODE = 10;
    const MULTI           = 11;
    const VARIABLE        = 12;
    const FUNC            = 13;
    const ARGS            = 14;
    const ARG_TXT         = 15;
    const ARG_VAR         = 16;
    const ERROR           = 17;
    const END             = 18;
    const HTML            = 22;

    const SAME = 19;
    const PREV = 20;
    const SKIP = 21;


    private $_state = null;
    private $_is_state_changed = false;
    private $_prev_state = null;
    public $_global_state = null;

    private static $_states = [
        self::OPERATOR, self::TAG, self::ID, self::CLASS_NAME, self::ATTR, self::AFTER_ATTR, self::TEXT, self::AFTER_TEXT, self::TEXT_NODE, self::AFTER_TEXT_NODE,
        self::MULTI, self::VARIABLE, self::FUNC, self::ARGS, self::ARG_TXT, self::ARG_VAR, self::ERROR, self::END,
    ];
    private static $_initial_states = [
        self::TAG,
    ];
    private static $_final_states = [
        self::END,
    ];
    private static $_map = [
        self::OPERATOR => [
            '+' => self::OPERATOR,
            '>' => self::OPERATOR,
            '^' => self::OPERATOR,
            '(' => self::OPERATOR,
            ')' => self::OPERATOR,
            'a' => self::TAG,
            '#' => self::ID,
            '.' => self::CLASS_NAME,
            '[' => self::ATTR,
            ']' => self::ERROR,
            '{' => self::TEXT_NODE,
            '}' => self::ERROR,
            '*' => self::MULTI,
            '`' => [self::HTML, self::VARIABLE],
            '%' => [self::HTML, self::FUNC],
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::TAG => [
            '+' => self::OPERATOR,
            '>' => self::OPERATOR,
            '^' => self::OPERATOR,
            '(' => self::ERROR,
            ')' => self::OPERATOR,
            'a' => self::SAME,
            '#' => self::ID,
            '.' => self::CLASS_NAME,
            '[' => self::ATTR,
            ']' => self::ERROR,
            '{' => self::TEXT,
            '}' => self::ERROR,
            '*' => self::MULTI,
            '`' => self::VARIABLE,
            '%' => self::FUNC,
            ' ' => self::SKIP,
            ''  => self::END,
            ',' => self::ERROR,
        ],
        self::ID => [
            '+' => self::OPERATOR,
            '>' => self::OPERATOR,
            '^' => self::OPERATOR,
            '(' => self::ERROR,
            ')' => self::OPERATOR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::CLASS_NAME,
            '[' => self::ATTR,
            ']' => self::ERROR,
            '{' => self::TEXT,
            '}' => self::ERROR,
            '*' => self::MULTI,
            '`' => self::VARIABLE,
            '%' => self::FUNC,
            ' ' => self::SKIP,
            ''  => self::END,
            ',' => self::ERROR,
        ],
        self::CLASS_NAME => [
            '+' => self::OPERATOR,
            '>' => self::OPERATOR,
            '^' => self::OPERATOR,
            '(' => self::ERROR,
            ')' => self::OPERATOR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::CLASS_NAME,
            '[' => self::ATTR,
            ']' => self::ERROR,
            '{' => self::TEXT,
            '}' => self::ERROR,
            '*' => self::MULTI,
            '`' => self::VARIABLE,
            '%' => self::FUNC,
            ' ' => self::SAME,
            ''  => self::END,
            ',' => self::ERROR,
        ],
        self::ATTR => [
            '+' => self::SAME,
            '>' => self::SAME,
            '^' => self::SAME,
            '(' => self::SAME,
            ')' => self::SAME,
            'a' => self::SAME,
            '#' => self::SAME,
            '.' => self::SAME,
            '[' => self::ERROR,
            ']' => self::AFTER_ATTR,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::SAME,
            '`' => self::VARIABLE,
            '%' => self::FUNC,
            ' ' => self::SAME,
            ''  => self::ERROR,
            ',' => self::SAME,
        ],
        self::AFTER_ATTR => [
            '+' => self::OPERATOR,
            '>' => self::OPERATOR,
            '^' => self::OPERATOR,
            '(' => self::ERROR,
            ')' => self::OPERATOR,
            'a' => self::ERROR,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::TEXT,
            '}' => self::ERROR,
            '*' => self::MULTI,
            '`' => self::ERROR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::END,
            ',' => self::ERROR,
        ],
        self::TEXT => [
            '+' => self::SAME,
            '>' => self::SAME,
            '^' => self::SAME,
            '(' => self::SAME,
            ')' => self::SAME,
            'a' => self::SAME,
            '#' => self::SAME,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::ERROR,
            '}' => self::AFTER_TEXT,
            '*' => self::SAME,
            '`' => self::VARIABLE,
            '%' => self::FUNC,
            ' ' => self::SAME,
            ''  => self::ERROR,
            ',' => self::SAME,
        ],
        self::AFTER_TEXT => [
            '+' => self::OPERATOR,
            '>' => self::OPERATOR,
            '^' => self::OPERATOR,
            '(' => self::ERROR,
            ')' => self::OPERATOR,
            'a' => self::ERROR,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::MULTI,
            '`' => self::ERROR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::END,
            ',' => self::ERROR,
        ],
        self::TEXT_NODE => [
            '+' => self::SAME,
            '>' => self::SAME,
            '^' => self::SAME,
            '(' => self::SAME,
            ')' => self::SAME,
            'a' => self::SAME,
            '#' => self::SAME,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::ERROR,
            '}' => self::AFTER_TEXT_NODE,
            '*' => self::SAME,
            '`' => self::VARIABLE,
            '%' => self::FUNC,
            ' ' => self::SAME,
            ''  => self::ERROR,
            ',' => self::SAME,
        ],
        self::AFTER_TEXT_NODE => [
            '+' => self::OPERATOR,
            '>' => self::ERROR,
            '^' => self::OPERATOR,
            '(' => self::ERROR,
            ')' => self::OPERATOR,
            'a' => self::ERROR,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::MULTI,
            '`' => self::ERROR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::END,
            ',' => self::ERROR,
        ],
        self::MULTI => [
            '+' => self::OPERATOR,
            '>' => self::OPERATOR,
            '^' => self::OPERATOR,
            '(' => self::ERROR,
            ')' => self::OPERATOR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::VARIABLE,
            '%' => self::FUNC,
            ' ' => self::SAME,
            ''  => self::END,
            ',' => self::ERROR,
        ],
        self::VARIABLE => [
            '+' => self::ERROR,
            '>' => self::SAME,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::PREV,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::FUNC => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ARGS,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::ERROR,
            '%' => self::PREV,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::ARGS => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::FUNC,
            'a' => self::ARG_TXT,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::ARG_VAR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::SKIP,
        ],
        self::ARG_TXT => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::FUNC,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::ERROR,
            '%' => self::ERROR,
            ' ' => self::SAME,
            ''  => self::ERROR,
            ',' => self::ARGS,
        ],
        self::ARG_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::ARGS,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::ERROR => [],
        self::END => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::ERROR,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::ERROR,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::HTML => [
            '+' => self::OPERATOR,
            '>' => self::OPERATOR,
            '^' => self::OPERATOR,
            '(' => self::ERROR,
            ')' => self::OPERATOR,
            'a' => self::ERROR,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::MULTI,
            '`' => self::ERROR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::END,
            ',' => self::ERROR,
        ],
    ];
    private static $_alphabet = [
        '+', '>', '^', '(', ')', 'a', '#', '.', '[', ']', '{', '}', '*', '`', '%', ' ', '', ','
    ];

    private static $_global_states = [
        self::TAG, self::ID, self::CLASS_NAME, self::ATTR, self::TEXT, self::TEXT_NODE, self::MULTI,
    ];


    public function __construct($state = null)
    {

        if(null === $state){
            $state = self::TAG;
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

        if(!in_array($symbol, self::$_alphabet)){
            $key = 'a';
        } else {
            $key = $symbol;
        }

        $state = self::$_map[$this->_state][$key];

        $this->_is_state_changed = false;

        if(is_array($state)){
            $this->setNewState($state[1]);
            $this->_global_state = $state[0];
        } else {
            switch($state){
                case self::SAME:
                    break;
                case self::SKIP:
                    break;
                case self::PREV:
                    $this->setNewState($this->_global_state);
                    break;
                default:
                    $this->setNewState($state);
                    break;
            }
        }
        return true;
    }

    private function setNewState($state)
    {

        $this->_prev_state       = $this->_state;
        $this->_is_state_changed = true;
        $this->_state            = $state;
        if(in_array($state, self::$_global_states)){
            $this->_global_state = $state;
        }

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

    public function checkMap()
    {

        foreach(self::$_states as $state){
            if(!array_key_exists($state, self::$_map)){
                return false;
            }
        }

        foreach(self::$_map as $key => $map){
            if($key === self::ERROR) { continue; }
            foreach(self::$_alphabet as $symbol){
                if(!array_key_exists($symbol, $map)){
                    return false;
                }
            }
        }
        return true;

    }

    public static function getMap()
    {

        return self::$_map;

    }

}