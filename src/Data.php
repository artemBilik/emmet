<?php
namespace artem_c\emmet;

Data::setDefaultFunctions();
class Data
{


    const VALUE = '{{value}}';

    private $_data = [];

    private static $_functions = [];

    /**
     * @param array $data
     */
    public function setData(array $data)
    {

        $this->_data = $data;

    }
    /**
     * @param string $variable
     * @param int$number
     * @param string $added_value
     *
     * Find the value of the variable in array self::_data
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($variable, $number, $added_value)
    {

        if('$' === $variable){
            if(isset($this->_data[$number])){
                return $this->_data[$number];
            }
            return $number;
        }
        $memory = $this->_data;
        $variable .= ';';
        $state = '[';
        $value = '';
        for($i = 0, $length = strlen($variable); $i < $length; ++$i){
            $symbol = $variable[$i];
            if('$' === $symbol){
                $symbol = $number;
            }

            if((('[' === $symbol || ']' === $symbol || '{' === $symbol || '}' === $symbol  || '.' === $symbol || ';' === $symbol))){
                if('' !== $value){
                    if($state === '.'){
                        if(isset($memory->$value)){
                            $memory = $memory->$value;
                        } else {
                            $this->throwException('Cann\'t find the variable "' . substr($variable, 0, strlen($variable) - 1) . '".');
                        }
                    } elseif('{' === $state){
                        if(isset($memory[$value])){
                            $memory = $memory[$value];
                        } else {
                            $this->throwException('Cann\'t find the variable "' . substr($variable, 0, strlen($variable) - 1) . '".');
                        }
                    } else {
                        if(isset($memory[$value])){
                            $memory = $memory[$value];
                        } else {
                            $this->throwException('Cann\'t find the variable "' . substr($variable, 0, strlen($variable) - 1) . '".');
                        }
                    }
                }

                $value = '';
                $state = $symbol;

                continue;
            }  else {
                $value .= $symbol;
            }
        }

        if(null !== $added_value && is_string($memory)){
            $memory = str_replace(self::VALUE, $added_value , $memory);
        }
        return $memory;

    }

    /**
     * @param string $name
     * @param array $args
     * @param string $value
     * @param int $number
     *
     * call the function from self::$_functions
     *
     * @return mixed
     * @throws \Exception
     */
    public function func($name, array $args = [], $value = null, $number = 0)
    {

        if(array_key_exists($name, self::$_functions)){
            if(is_string(self::$_functions[$name])){
                $result = self::$_functions[$name];
                if($value && '' !== $value){
                    $result = str_replace(self::VALUE, $value, $result);
                }
                return $result;
            } elseif(self::$_functions[$name] instanceof \Closure){
                $args_for_call = [];
                foreach($args as $arg){
                    if(is_array($arg) && array_key_exists('type', $arg) && Value::VARIABLE === $arg['type']){
                        $args_for_call[] = $this->get($arg['value'], $number, null);
                    } else {
                        if(is_array($arg) && array_key_exists('value', $arg)){
                            $args_for_call[] = $arg['value'];
                        } else {
                            $args_for_call[] = $arg;
                        }
                    }
                }
                if($value){
                    $args_for_call[] = $value;
                }

                return call_user_func_array(self::$_functions[$name], $args_for_call);
            } else {
                $this->throwException('Function "' . $name .'" must be a callable or a string.');
            }
        } else {
            $this->throwException('Function "' . $name . '" doesn\'t exists in functions list.');
        }

    }

    /**
     * @param array $functions
     */
    public static function setFunctions(array $functions)
    {

        self::$_functions = array_merge(self::$_functions, $functions);

    }
    public static function setDefaultFunctions()
    {

        self::setFunctions([
            'count' => function($array_to_count, $mode = \COUNT_NORMAL){
                return count($array_to_count, $mode);
            },
            'concat' => function(){
                $string = '';
                foreach(func_get_args() as $arg){
                    $string .= $arg;
                }
                return $string;
            },
            'select' => function($name, $selected, array $data, array $html_options = []) {
                $options = '';
                foreach($data as $key => $option){
                    $options .= Node::closingElement('option', ['value' => $key] + (($key == $selected) ? ['selected' => 'selected'] : []), $option);
                }
                return Node::closingElement('select', (['name' => $name] + $html_options), $options);
            }
        ]);

    }

    /**
     * @param string $message
     * @throws \Exception
     */
    private function throwException($message)
    {

        throw new \Exception($message);

    }

}
