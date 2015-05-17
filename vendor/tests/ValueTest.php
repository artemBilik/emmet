<?php
namespace emmet\test;

use \emmet\Value as Value;
use \emmet\Data as Data;

require_once __DIR__ . '/../src/Value.php';
require_once __DIR__ . '/../src/Data.php';


class ValueTest extends \PHPUnit_Framework_TestCase
{

    public function testAddText()
    {

        $value = new Value(new Data());
        $first_text = 'first_text';
        $value->addText($first_text);
        $this->assertEquals($first_text, $value->get(0, null));

        $second_text = 'second_text';
        $value->addText($second_text);
        $this->assertEquals($value->get(0, null),$first_text.$second_text);

    }

    public function testAddFunc()
    {

        Data::setFunctions([
            'func' => function(){
                return 'func';
            },
        ]);

        $value = new Value(new Data());
        $value->addFunction('func');
        $this->assertEquals($value->get(0, null), 'func');

    }
    /**
     * @expectedException \Exception
     */
    public function testAddArgument()
    {

        Data::setFunctions([
            'func' => function($var, $txt){
                return $var . $txt;
            },
        ]);

        $data = new Data();
        $var = 'var';
        $txt = 'txt';
        $data->setData(['var' => $var]);

        $value = new Value($data);
        $value->addFunction('func');
        $value->addArgument('var', Value::VARIABLE);
        $value->addArgument($txt, Value::TXT);

        $this->assertEquals($value->get(0, null), $var.$txt);

        $value->addText('text');
        $value->addArgument('var', Value::VARIABLE);

    }

    public function testAddVariable()
    {

        $data = new Data();
        $data->setData(['var' => ['value', 'val={{value}}']]);

        $value = new Value($data);
        $value->addVariable('var[$]');

        $this->assertEquals($value->get(1,'value'), 'val=value');

    }

    public function testGetToSet()
    {

        $value = new Value(new Data());

        $value->addText('text one');
        $this->assertEquals($value->getToSet(), 'text one');
        $value->addText('text two');
        $this->assertEquals($value->getToSet(), 'text onetext two');
        $value->addFunction('func');
        $this->assertTrue($value->getToSet() === $value);

        $value = new Value(new Data());
        $this->assertEquals($value->getToSet(), '');
        $value->addVariable('var', Value::VARIABLE);
        $this->assertTrue($value->getToSet() === $value);

    }

}