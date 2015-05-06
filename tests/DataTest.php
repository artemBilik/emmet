<?php
namespace emmet\test;

use \emmet\Data as Data;

require_once __DIR__ . '/../src/Data.php';

class DataTest extends \PHPUnit_Framework_TestCase
{

    private $_data = null;

    protected function setData()
    {

        $this->_data = new Data();

        $object = new \stdClass();
        $object->title = 'title';
        $object->name = 'selectName';
        $object->data =[0,1,2,3];

        $this->_data->setData([
            'array_index' => [0,1,2,3,4,5],
            'array_assoc' => ['zero' => 0, 'one' => 1, 'object' => $object],
            'string' => 'where is the line?',
        ]);

        Data::setFunctions([
           'select' => function($name, $selected, $value){
               return 'select';
           },
        ]);

    }
    /**
     * @dataProvider variableProvider
     */

    public function testGet($get, $expected)
    {

        $this->assertTrue($get === $expected);

    }

    public function testFunc()
    {



    }

    public function variableProvider()
    {

        $this->setData();
        return [
            [$this->_data->get('array_index'), [0,1,2,3,4,5]],
            [$this->_data->get('array_index[2]'), 2],
            [$this->_data->get('array_index[$]', 1), 1],
            [$this->_data->get('array_assoc[zero]'), 0],
            [$this->_data->get('array_assoc[object]->title'), 'title'],
            [$this->_data->get('array_assoc[object]->data[$]'), 1],
            [$this->_data->get('array_assoc[object]->data[3]'), 3],
            [$this->_data->get('string'), 'where is the line?'],
            [$this->_data->get('string{$}', 7), 's'],
            [$this->_data->get('string{10}'), 'h'],

        ];

    }

}
