<?php

namespace emmet;

class Value
{

    const TXT = 1;
    const VARIABLE = 2;
    const FUNC = 3;

    private $_value      = [];
    private $_data = null;

    private static $_config = [];


    public function __construct(Data $data)
    {

        $this->_data = $data;

    }
    // INTERFACE
    /*
     * Add textNode to self::_value
     */
    public function addText($text)
    {

        $this->_value[] = [
            'type'  => self::TXT,
            'value' => $text,
        ];
        return true;

    }
    /*
     * Add functionNode to self::_value
     */
    public function addFunction($function_name)
    {

        $this->_value[] = [
            'type' => self::FUNC,
            'name' => $function_name,
            'args' => [],
        ];
        return true;

    }
    /*
     * Add argument to last self::_value function
     * throw Exception if last self::_value is not a function
     */
    public function addArgument($arg_value, $arg_type)
    {

        if(empty($this->_value) || !in_array($arg_type, [self::VARIABLE, self::TXT])){
            return false;
        }
        $func = $this->_value[count($this->_value) - 1];
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
     * Add variableNode to self::_value
     */
    public function addVariable($variable)
    {

        $this->_value[] = [
            'type'  => self::VARIABLE,
            'name'  => $variable,
        ];

    }
    /*
     * Return $this if self::_value has function or variable
     * And return $text if self::_value has only text
     */
    public function get()
    {

        $string = '';
        foreach($this->_value as $item){
            if(in_array($item['type'], [self::VARIABLE, self::FUNC])){
                return $this;
            } else {
                $string .= $this->getNodeValue($item);
            }
        }
        return $string;
    }
    /*
     * return handled self::_value
     */
    public function __toString()
    {

        $string = '';
        foreach($this->_value as $item){
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
                return $this->_data->get($node['name']);
            case self::FUNC:
                return $this->_data->func($node['name'], $node['args']);
            default:
                return '';
        }

    }


}