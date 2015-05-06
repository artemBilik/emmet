<?php
namespace emmet;

class Data
{

    const TXT = 1;
    const VARIABLE = 2;

    private $_data = [];

    private static $_functions = [];

    public function setData(array $data)
    {

        $this->_data = $data;

    }

    public function get($variable, $number = 1)
    {

        $memory = $this->_data;
        $variable .= ';';
        $state = '[';
        $value = '';
        for($i = 0, $length = strlen($variable); $i < $length; ++$i){
            $symbol = $variable{$i};
            if('$' === $symbol){
                $symbol = $number;
            }
            if(('[' === $symbol || '-' === $symbol || ']' === $symbol || '{' === $symbol || '}' === $symbol || ';' === $symbol) && '' !== $value){
                if($state === '>'){
                    if(isset($memory->$value)){
                        $memory = $memory->$value;
                    } else {
                        return false;
                    }
                } elseif('{' === $state){
                    if(isset($memory{$value})){
                        $memory = $memory{$value};
                    } else {
                        return false;
                    }
                } else {
                    if(isset($memory[$value])){
                        $memory = $memory[$value];
                    } else {
                        return false;
                    }
                }
                $value = '';
                $state = $symbol;
                continue;
            } elseif('>' === $symbol){
                $value = '';
                $state = $symbol;
                continue;
            } else {
                $value .= $symbol;
            }
        }
        return $memory;

    }

    public function func($name, array $args = [], $value = null, $number = 1)
    {



    }

    public static function setFunctions(array $functions)
    {



    }

}