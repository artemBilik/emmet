<?php

namespace emmet;

class Value
{

    private $_data = [];
    private $_var_getter = null;
    private static $_config = [];


    // INTERFACE
    /*
     * Add textNode to self::_data
     */
    public function addText($text)
    {}
    /*
     * Add functionNode to self::_data
     */
    public function addFunction($function_name)
    {}
    /*
     * Add argument to last self::_data function
     * throw Exception if last self::_data is not a function
     */
    public function addArgument($arg_value, $arg_type)
    {}
    /*
     * Add variableNode to self::_data
     */
    public function addVariable($variable)
    {}
    /*
     * Return $this if self::_data has function or variable
     * And return $text if self::_data has only text
     */
    public function get()
    {}
    /*
     * return handled self::_data
     */
    public function __toString()
    {

        return '';

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
    {}
    /*
     * Get Variable value from self::_var_getter
     */
    private function getVariable($variable)
    {}
    /*
     * call function with args
     * return func($args)
     */
    private function callFunc($func, $args)
    {}
}