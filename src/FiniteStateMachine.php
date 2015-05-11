<?php

namespace emmet;

class FiniteStateMachine
{

    // FSM STATES
    const OPERATOR        = 0;

    const TAG         = 10;
    const TAG_VAR     = 11;
    const TAG_FUNC    = 12;
    const TAG_ARGS    = 13;
    const TAG_ARG_TXT = 14;
    const TAG_ARG_VAR = 15;

    const ID         = 20;
    const ID_VAR     = 21;
    const ID_FUNC    = 22;
    const ID_ARGS    = 23;
    const ID_ARG_TXT = 24;
    const ID_ARG_VAR = 25;

    const CLASS_NAME         = 30;
    const CLASS_NAME_VAR     = 31;
    const CLASS_NAME_FUNC    = 32;
    const CLASS_NAME_ARGS    = 33;
    const CLASS_NAME_ARG_TXT = 34;
    const CLASS_NAME_ARG_VAR = 35;
    
    const ATTR         = 40;
    const ATTR_VAR     = 41;
    const ATTR_FUNC    = 42;
    const ATTR_ARGS    = 43;
    const ATTR_ARG_TXT = 44;
    const ATTR_ARG_VAR = 45;
    const AFTER_ATTR   = 46;
    
    const TEXT         = 50;
    const TEXT_VAR     = 51;
    const TEXT_FUNC    = 52;
    const TEXT_ARGS    = 53;
    const TEXT_ARG_TXT = 54;
    const TEXT_ARG_VAR = 55;
    const AFTER_TEXT   = 56;
    
    const TEXT_NODE         = 60;
    const TEXT_NODE_VAR     = 61;
    const TEXT_NODE_FUNC    = 62;
    const TEXT_NODE_ARGS    = 63;
    const TEXT_NODE_ARG_TXT = 64;
    const TEXT_NODE_ARG_VAR = 65;
    const AFTER_TEXT_NODE   = 66;
    
    const MULTI         = 70;
    const MULTI_VAR     = 71;
    const MULTI_FUNC    = 72;
    const MULTI_ARGS    = 73;
    const MULTI_ARG_TXT = 74;
    const MULTI_ARG_VAR = 75;

    const HTML         = 80;
    const HTML_VAR     = 81;
    const HTML_FUNC    = 82;
    const HTML_ARGS    = 83;
    const HTML_ARG_TXT = 84;
    const HTML_ARG_VAR = 85;
    
    const ERROR = 90;
    const END   = 91;
    const SAME  = 92;
    const PREV  = 93;
    const SKIP  = 94;


    private $_state = null;
    private $_is_state_changed = false;
    private $_prev_state = null;

    private static $_states = [

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
            '`' => self::HTML_VAR,
            '%' => self::HTML_FUNC,
            ' ' => self::SKIP,
            ''  => self::END,
            ',' => self::ERROR,
        ],
// MAP FOR TAG
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
            '`' => self::TAG_VAR,
            '%' => self::TAG_FUNC,
            ' ' => self::SKIP,
            ''  => self::END,
            ',' => self::ERROR,
        ],
        self::TAG_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::TAG,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::TAG_FUNC => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::TAG_ARGS,
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
            '%' => self::TAG,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::TAG_ARGS => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::TAG_FUNC,
            'a' => self::TAG_ARG_TXT,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::TAG_ARG_VAR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::SKIP,
        ],
        self::TAG_ARG_TXT => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::TAG_FUNC,
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
            ',' => self::TAG_ARGS,
        ],
        self::TAG_ARG_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::TAG_ARGS,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        
// MAP FOR ID
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
            '`' => self::ID_VAR,
            '%' => self::ID_FUNC,
            ' ' => self::SKIP,
            ''  => self::END,
            ',' => self::ERROR,
        ],
        self::ID_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::ID,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::ID_FUNC => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ID_ARGS,
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
            '%' => self::ID,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::ID_ARGS => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ID_FUNC,
            'a' => self::ID_ARG_TXT,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::ID_ARG_VAR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::SKIP,
        ],
        self::ID_ARG_TXT => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ID_FUNC,
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
            ',' => self::ID_ARGS,
        ],
        self::ID_ARG_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::ID_ARGS,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
   // MAP FOR CLASS NAME     
        
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
            '`' => self::CLASS_NAME_VAR,
            '%' => self::CLASS_NAME_FUNC,
            ' ' => self::SAME,
            ''  => self::END,
            ',' => self::ERROR,
        ],
        self::CLASS_NAME_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::CLASS_NAME,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::CLASS_NAME_FUNC => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::CLASS_NAME_ARGS,
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
            '%' => self::CLASS_NAME,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::CLASS_NAME_ARGS => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::CLASS_NAME_FUNC,
            'a' => self::CLASS_NAME_ARG_TXT,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::CLASS_NAME_ARG_VAR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::SKIP,
        ],
        self::CLASS_NAME_ARG_TXT => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::CLASS_NAME_FUNC,
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
            ',' => self::CLASS_NAME_ARGS,
        ],
        self::CLASS_NAME_ARG_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::CLASS_NAME_ARGS,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        
        // MAP FOR ATTR
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
            '`' => self::ATTR_VAR,
            '%' => self::ATTR_FUNC,
            ' ' => self::SAME,
            ''  => self::ERROR,
            ',' => self::SAME,
        ],
        self::ATTR_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::ATTR,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::ATTR_FUNC => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ATTR_ARGS,
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
            '%' => self::ATTR,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::ATTR_ARGS => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ATTR_FUNC,
            'a' => self::ATTR_ARG_TXT,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::ATTR_ARG_VAR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::SKIP,
        ],
        self::ATTR_ARG_TXT => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ATTR_FUNC,
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
            ',' => self::ATTR_ARGS,
        ],
        self::ATTR_ARG_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::ATTR_ARGS,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
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
        
        // MAP FRO TEXT
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
            '`' => self::TEXT_VAR,
            '%' => self::TEXT_FUNC,
            ' ' => self::SAME,
            ''  => self::ERROR,
            ',' => self::SAME,
        ],
        self::TEXT_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::TEXT,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::TEXT_FUNC => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::TEXT_ARGS,
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
            '%' => self::TEXT,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::TEXT_ARGS => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::TEXT_FUNC,
            'a' => self::TEXT_ARG_TXT,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::TEXT_ARG_VAR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::SKIP,
        ],
        self::TEXT_ARG_TXT => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::TEXT_FUNC,
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
            ',' => self::TEXT_ARGS,
        ],
        self::TEXT_ARG_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::TEXT_ARGS,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
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
            '`' => self::TEXT_NODE_VAR,
            '%' => self::TEXT_NODE_FUNC,
            ' ' => self::SAME,
            ''  => self::ERROR,
            ',' => self::SAME,
        ],
        self::TEXT_NODE_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::TEXT_NODE,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::TEXT_NODE_FUNC => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::TEXT_NODE_ARGS,
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
            '%' => self::TEXT_NODE,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::TEXT_NODE_ARGS => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::TEXT_NODE_FUNC,
            'a' => self::TEXT_NODE_ARG_TXT,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::TEXT_NODE_ARG_VAR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::SKIP,
        ],
        self::TEXT_NODE_ARG_TXT => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::TEXT_NODE_FUNC,
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
            ',' => self::TEXT_NODE_ARGS,
        ],
        self::TEXT_NODE_ARG_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::TEXT_NODE_ARGS,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
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
        
        // MAP FOR MULTI
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
            '`' => self::MULTI_VAR,
            '%' => self::MULTI_FUNC,
            ' ' => self::SAME,
            ''  => self::END,
            ',' => self::ERROR,
        ],
        self::MULTI_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::MULTI,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::MULTI_FUNC => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::MULTI_ARGS,
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
            '%' => self::MULTI,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::MULTI_ARGS => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::MULTI_FUNC,
            'a' => self::MULTI_ARG_TXT,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::MULTI_ARG_VAR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::SKIP,
        ],
        self::MULTI_ARG_TXT => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::MULTI_FUNC,
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
            ',' => self::MULTI_ARGS,
        ],
        self::MULTI_ARG_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::MULTI_ARGS,
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
        self::HTML_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::HTML,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::HTML_FUNC => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::HTML_ARGS,
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
            '%' => self::HTML,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        self::HTML_ARGS => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::HTML_FUNC,
            'a' => self::HTML_ARG_TXT,
            '#' => self::ERROR,
            '.' => self::ERROR,
            '[' => self::ERROR,
            ']' => self::ERROR,
            '{' => self::ERROR,
            '}' => self::ERROR,
            '*' => self::ERROR,
            '`' => self::HTML_ARG_VAR,
            '%' => self::ERROR,
            ' ' => self::SKIP,
            ''  => self::ERROR,
            ',' => self::SKIP,
        ],
        self::HTML_ARG_TXT => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::HTML_FUNC,
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
            ',' => self::HTML_ARGS,
        ],
        self::HTML_ARG_VAR => [
            '+' => self::ERROR,
            '>' => self::ERROR,
            '^' => self::ERROR,
            '(' => self::ERROR,
            ')' => self::ERROR,
            'a' => self::SAME,
            '#' => self::ERROR,
            '.' => self::SAME,
            '[' => self::SAME,
            ']' => self::SAME,
            '{' => self::SAME,
            '}' => self::SAME,
            '*' => self::ERROR,
            '`' => self::HTML_ARGS,
            '%' => self::ERROR,
            ' ' => self::ERROR,
            ''  => self::ERROR,
            ',' => self::ERROR,
        ],
        
    ];
    private static $_alphabet = [
        '+', '>', '^', '(', ')', 'a', '#', '.', '[', ']', '{', '}', '*', '`', '%', ' ', '', ','
    ];

    /**
     * @param int $state
     * @throws \Exception
     */
    public function __construct($state = 0)
    {

        if(0 === $state){
            $state = self::TAG;
        }
        if(!in_array($state, self::$_initial_states)){
            $this->throwException('FiniteStateMachine::__construct(int state = null). Undefined Initial State. Use the list of the initial states.');
        } else {
            $this->setNewState($state);
        }

    }

    /**
     * @return int|null
     */
    public function getState()
    {

        return $this->_state;

    }

    /**
     * @param char $symbol
     * @return bool
     * @throws \Exception
     */
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

        switch($state){
            case self::SAME:
                break;
            case self::SKIP:
                break;
            default:
                $this->setNewState($state);
                break;
        }

        return true;
    }

    /**
     * @param int $state
     */
    private function setNewState($state)
    {

        $this->_prev_state       = $this->_state;
        $this->_is_state_changed = (self::SKIP === $state) ? false : true;
        $this->_state            = $state;

    }

    /**
     * @return bool
     */
    public function isStateChanged()
    {

        return $this->_is_state_changed;

    }

    /**
     * @return int
     */
    public function getPrevState()
    {

        return $this->_prev_state;

    }

    /**
     * @param string $message
     * @throws \Exception
     */
    private function throwException($message)
    {

        throw new \Exception($message);

    }

    /**
     * check is map is correct
     * self::_map should have all self::_states
     * self::_map[state] should have all self::_alphabet
     * @return bool
     */
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