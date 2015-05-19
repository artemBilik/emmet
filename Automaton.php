<?php

namespace emmet;

class Automaton
{

    // finite state automaton states
    const SET_OPERATION      = 1;
    const GET_TAG            = 2;
    const GET_ID             = 3;
    const GET_CLASS          = 4;
    const GET_ATTR_NAME      = 5;
    const GET_ATTR_VAL       = 6;
    const GET_TEXT           = 7;
    const GET_MULTIPLICATION = 8;
    const WAIT               = 9;

    private $_state      = null;
    private $_prev_state = null;
    private $_symbol     = '';
    private $_value      = '';

    private static $_states = array(
        self::SET_OPERATION, self::GET_TAG, self::GET_ID, self::GET_CLASS, self::GET_ATTR_NAME,
        self::GET_ATTR_VAL, self::GET_TEXT, self::GET_MULTIPLICATION, self::WAIT,
    );

    public function __construct($state = self::GET_TAG)
    {

        if(in_array($state, self::_states)){
            $this->_state = $state;
        } else {
            throw new Exception('Undefined Automaton State.');
        }

    }

    public function setSymbol($symbol)
    {

        $this->_symbol = $symbol;

    }

    public function setState()
    {

        $this->_prev_state = $this->_state;
        if(in_array($this->_symbol, array('+', '^', '(', ')', '>'))){
            $this->_state = self::SET_OPERATION;
        } else {
            $this->_state = self::GET_TAG;
        }

    }

    public function addToValue()
    {

        $this->_value .= $this->_symbol;

    }

    public function getValue()
    {

        $value = $this->_value;
        $this->_value = '';
        return $value;

    }

    public function getSymbol()
    {

        return $this->_symbol;

    }


}