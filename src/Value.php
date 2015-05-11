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
    /**
     * @param string $text
     *
     *    * Add textNode to self::_value
     *
     * @return bool
     */
    public function addText($text)
    {

        $this->_value[] = [
            'type'  => self::TXT,
            'value' => $text,
        ];
        return true;

    }
    /**
     * @param string $function_name
     *
     *    * Add functionNode to self::_value
     *
     * @return bool
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
    /**
     * @param string $arg_value
     * @param self::TXT|self::VARIABLE $arg_type
     *
     *    * Add argument to last self::_value function
     *    * throw Exception if last self::_value is not a function
     *
     * @return bool
     * @throws \Exception
     */
    public function addArgument($arg_value, $arg_type)
    {

        if(empty($this->_value) || !in_array($arg_type, [self::VARIABLE, self::TXT])){
            $this->throwException('There is no function to add an argument "' . $arg_value . '".');
        }
        $func = &$this->_value[count($this->_value) - 1];
        if(self::FUNC !== $func['type']){
            $this->throwException('There is no function to add an argument "' . $arg_value . '".');
        }
        $func['args'][] = [
            'type'  => $arg_type,
            'value' => $arg_value,
        ];
        return true;

    }
    private function throwException($message)
    {

        throw new \Exception($message);

    }

    /**
     * @param string $variable
     *
     *  * Add variableNode to self::_value
     *
     * @return null
     */
    public function addVariable($variable)
    {

        $this->_value[] = [
            'type'  => self::VARIABLE,
            'name'  => $variable,
        ];

    }
    /**
     *    * Return $this if self::_value has function or variable
     *    * And return $text if self::_value has only text
     * @return $this|string
     */
    public function getToSet()
    {

        $string = '';
        foreach($this->_value as $item){
            if(in_array($item['type'], [self::VARIABLE, self::FUNC])){
                return $this;
            } else {
                $string .= $this->getNodeValue($item, 0, '');
            }
        }
        return $string;
    }
    /**
     * @param int $number
     * @param string $value
     *    * return handled self::_value
     * @return string
     */
    public function get($number, $value)
    {

        $string = '';
        foreach($this->_value as $item){
            $string .= $this->getNodeValue($item, $number, $value);
        }
        return $string;

    }
    /**
     * @param array $node
     * @param int $number
     * @param string $value
     *    * Get Node value in depends of type(text, function, variable)
     * @return mixed|string
     */
    private function getNodeValue($node, $number, $value)
    {

        switch($node['type']){
            case self::TXT:
                return $node['value'];
            case self::VARIABLE:
                return $this->_data->get($node['name'], $number, $value);
            case self::FUNC:
                return $this->_data->func($node['name'], $node['args'], $value, $number);
            default:
                return '';
        }

    }


}