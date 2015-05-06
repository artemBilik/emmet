<?php

namespace emmet;

class Value
{

    const TXT = 1;
    const VARIABLE = 2;
    const FUNC = 3;

    private $_data       = [];
    private $_var_getter = null;

    private static $_config = [];


    public function __construct(callable $var_getter)
    {

        $this->_var_getter = $var_getter;

    }
    // INTERFACE
    /*
     * Add textNode to self::_data
     */
    public function addText($text)
    {

        $this->_data[] = [
            'type'  => self::TXT,
            'value' => $text,
        ];
        return true;

    }
    /*
     * Add functionNode to self::_data
     */
    public function addFunction($function_name)
    {

        $this->_data[] = [
            'type' => self::FUNC,
            'name' => $function_name,
            'args' => [],
        ];
        return true;

    }
    /*
     * Add argument to last self::_data function
     * throw Exception if last self::_data is not a function
     */
    public function addArgument($arg_value, $arg_type)
    {

        if(empty($this->_data) || !in_array($arg_type, [self::VARIABLE, self::TXT])){
            return false;
        }
        $func = $this->_data[count($this->_data) - 1];
        if(self::FUNC !== $func['type']){
            return false;
        }
        $func['args'][] = [
            'type'  => $arg_type,
            'value' => $arg_value,
        ];
        return true;

    }
    /*
     * Add variableNode to self::_data
     */
    public function addVariable($variable)
    {

        $this->_data[] = [
            'type'  => self::VARIABLE,
            'name'  => $variable,
        ];

    }
    /*
     * Return $this if self::_data has function or variable
     * And return $text if self::_data has only text
     */
    public function get()
    {

        $string = '';
        foreach($this->_data as $item){
            if(in_array($item['type'], [self::VARIABLE, self::FUNC])){
                return $this;
            } else {
                $string .= $this->getNodeValue($item);
            }
        }
        return $string;
    }
    /*
     * return handled self::_data
     */
    public function __toString()
    {

        $string = '';
        foreach($this->_data as $item){
            $string .= $this->getNodeValue($item);
        }
        return $string;

    }
    /*
     * Includes array with functions config
     * add it to global self::$_config
     */
    public function includeConfig(array $config)
    {}


    // SERVICE
    /*
     * Get Node value in depends of type(text, function, variable)
     */
    private function getNodeValue($node)
    {

        switch($node['type']){
            case self::TXT:
                return $node['value'];
            case self::VARIABLE:
                return $this->getVariable($node['name']);
            case self::FUNC:
                return $this->callFunc($node['name'], $node['args']);
            default:
                return '';
        }

    }
    /*
     * Get Variable value from self::_var_getter
     */
    private function getVariable($variable)
    {}
    /*
     * call function with args
     * return func($args)
     */
    private function callFunc($func, array $args)
    {}

}