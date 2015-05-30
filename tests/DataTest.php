<?php
namespace artem_c\emmet\test;

use \artem_c\emmet\Data as Data;
use \artem_c\emmet\Value as Value;

require_once __DIR__ . '/../src/Data.php';
require_once __DIR__ . '/../src/Value.php';

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
            'array_assoc' => ['zero' => 0, 'one' => '23{{value}}', 'object' => $object],
            'string' => 'where is the line?',
            'name' => 'testName'
        ]);

        Data::setFunctions([
            'select_for_test' => function($name, $selected, $values){
                $value = '';
                foreach($values as $key => $item){
                    $value .= '<option value="' . $key . '"' . (($key === $selected) ? ' selected>' : '>') . $item . '</option>';
                }
               return '<select name="' . $name .'">' .$value.'</select>';
            },
            'message' => '<div class="message">{{value}}</div>'
        ]);

    }
    /**
     * @dataProvider variableProvider
     */

    public function testGet($get, $expected)
    {

        $this->assertTrue($get === $expected);

    }

    /**
     * @dataProvider funcProvider
     */
    public function testFunc($get, $expected)
    {

        $this->assertTrue($get === $expected);

    }
    public function testDefaultFunc()
    {

        $data = new Data();

        $concat = $data->func('concat', ['My', ' name', ' is', ' Artem!']);
        $this->assertEquals($concat, 'My name is Artem!');

        $test_array = [1,2,3,4,5];
        $this->assertEquals(count($test_array), $data->func('count', [$test_array]));

        $select = '<select name="Test[test]" class="class1 class2"><option value="0" selected="selected">null</option><option value="1">one</option><option value="2">two</option></select>';
        //file_put_contents('select', $data->func('select', ['Test[test]', 0, ['null', 'one', 'two'], ['class' => 'class1 class2']]));
        $this->assertEquals($select, $data->func('select', ['Test[test]', 0, ['null', 'one', 'two'], ['class' => 'class1 class2']]));
    }


    public function funcProvider()
    {

        $this->setData();

        return [
            [$this->_data->func('select_for_test', [
                ['value' => 'array_assoc[object].name', 'type' => Value::VARIABLE,],
                ['type' => Value::TXT, 'value' => 2],
                ['type' => Value::VARIABLE, 'value' => 'array_assoc[object].data'],
            ]), '<select name="selectName"><option value="0">0</option><option value="1">1</option><option value="2" selected>2</option><option value="3">3</option></select>'],
            [
                $this->_data->func('message', [], 'message'),
                '<div class="message">message</div>'
            ],
        ];


    }

    public function variableProvider()
    {

        $this->setData();
        return [
            [$this->_data->get('array_index[2]', 0, ''), 2],
            [$this->_data->get('array_index[$]', 1, ''), 1],
            [$this->_data->get('array_assoc[zero]', 0, ''), 0],
            [$this->_data->get('array_assoc[object].title', 0, ''), 'title'],
            [$this->_data->get('array_assoc[object].title{1}', 0, ''), 'i'],
            [$this->_data->get('array_assoc[object].data[$]', 1, ''), 1],
            [$this->_data->get('array_assoc[object].data[3]', 0, ''), 3],
            [$this->_data->get('string', 0, ''), 'where is the line?'],
            [$this->_data->get('string{$}', 7, ''), 's'],
            [$this->_data->get('string{10}', 0, ''), 'h'],
            [$this->_data->get('array_assoc[one]{1}', 0, ''), '3'],
            [$this->_data->get('array_assoc[one]', 0, 'added_value'), '23added_value'],
            [$this->_data->get('array_index', 1, 'awd'), [0,1,2,3,4,5]],
        ];

    }

}

