<?php
namespace emmet\test;

use \emmet\Emmet as Emmet;

require_once __DIR__ . '/../src/Emmet.php';

class EmmetTest extends \PHPUnit_Framework_TestCase
{

    private $_emmet_string = 'div>p>span+a>span';

    /**
     *  @expectedException \emmet\EmmetException
     */
    public function testConstruct()
    {

        $reflection_class = new \ReflectionClass('\emmet\Emmet');
        $emmet_property = $reflection_class->getProperty('_emmet_string');
        $emmet_property->setAccessible(true);

        $tree_property = $reflection_class->getProperty('_tree');
        $tree_property->setAccessible(true);

        $data_property = $reflection_class->getProperty('_data');
        $data_property->setAccessible(true);

        $emmet = new Emmet($this->_emmet_string);
        $this->assertEquals($this->_emmet_string, $emmet_property->getValue($emmet));

        $this->assertInstanceOf('\emmet\Tree', $tree_property->getValue($emmet));
        $this->assertTrue($tree_property->getValue($emmet)->isRoot());

        $this->assertInstanceOf('\emmet\Data', $data_property->getValue($emmet));


        new Emmet(new \stdClass());

    }

    public function testGetCheckDocumentation()
    {

        $emmet = new Emmet($this->_emmet_string);

        $ref = new \ReflectionClass('\emmet\Emmet');
        $method = $ref->getMethod('getCheckTheDocumentation');
        $method->setAccessible(true);

        $this->assertEquals('Check the documentation for the right syntax to use near "div&gt;<strong style="color:red;">p&gt;span+a&gt;span</strong>".', $method->invoke($emmet, 4));

    }

    /**
     * @todo test create of emmet string
     */

    /**
     * @dataProvider emmetStrProvider
     */
    public function testCreate($emmet, $html, $data = [], $functions = [])
    {

        Emmet::addFunctions($functions);
        $this->assertEquals((new Emmet($emmet))->create($data), $html);

    }

    public function emmetStrProvider()
    {

        // @todo add ' ' to emmet string
        return [
            [
                'div>p^+div',
                '<div ><p ></p></div><div ></div>'
            ],
            [
                'div#myDiv.class1.class2+div',
                '<div id="myDiv" class="class1 class2" ></div><div ></div>'
            ],
            [
                'form>input[type=checkbox selected name=myCheckBox value=`some_value`]+input[type=submit value=Сохранить]',
                '<form ><input type="checkbox" selected="selected" name="myCheckBox" value="checkboxValue" /><input type="submit" value="Сохранить" /></form>',
                ['some_value' => 'checkboxValue'],
            ],
            [
                'p{%getText(text)%}+{%getText(more text)%}',
                '<p >text</p>more text',
                [],
                ['getText' => function($text) { return $text; }]
            ],
            [
                'p*2>{text}*2',
                '<p >texttext</p><p >texttext</p>',
            ],
            [
                '%func(`html`, break)%>`var[asd]`>span{span}',
                'htmlbreakdas<span >span</span>',
                [
                    'html' => 'html',
                    'var' => ['asd' => 'das{{value}}']
                ],
                ['func' => function($html, $break, $value){
                    return $html . $break . $value;
                },]
            ],
            [
                'div+(div>p>a^>span+span)>div^+a',
                '<div ></div><div ><p ><a ></a><span ></span><span ></span></p><div ></div></div><a ></a>',
            ],
            [
                'tag+div#id+a.class+dt[attr]+ul{text}+ol*3+`var`+%func()%+div',
                '<tag ></tag><div id="id" ></div><a class="class" ></a><dt attr="attr" ></dt><ul >text</ul><ol ></ol><ol ></ol><ol ></ol>varfunc<div ></div>',
                ['var' => 'var'],
                ['func' => 'func']
            ],
            [
                'tag#id>tag.class+tag[attr]+tag{text}+tag*2+ta`var`g+ta%func()%g+tag`var`+tag%func()%',
                '<tag id="id" ><tag class="class" ></tag><tag attr="attr" ></tag><tag >text</tag><tag ></tag><tag ></tag><taasdfg ></taasdfg><tafuncg ></tafuncg><tagasdf ></tagasdf><tagfunc ></tagfunc></tag>',
                ['var' => 'asdf'],
                ['func' => 'func']
            ],
            [
                'b#id.class>table#id[attr]>tr#id{text}>td#id*2>abr#as`var`+title#as%func()%',
                '<b id="id" class="class" ><table id="id" attr="attr" ><tr id="id" >text<td id="id" ><abr id="asvaar" ></abr><title id="asfunc" ></title></td><td id="id" ><abr id="asvaar" ></abr><title id="asfunc" ></title></td></tr></table></b>',
                ['var' => 'vaar'],
                ['func' => function() { return 'func'; }, ]
            ],
//            [
//                'br.class+header.class[attr]+section.class1.class2.class3+md.class{text}+h1.class*2+h4.`var`+h5.%func()%',
//                '<br class="class" /><header class="class" attr="attr" ></header><section class="class1 class2 class4" ></section><md class="class" >text</md><h1 class="class" ></h1><h1 class="class" ></h1><h4 class="var" ></h4><h5 class="func" ></h5>',
//                ['var' => 'var'],
//                ['func' => function() { return 'func'; }, ]
//            ],
//            [
//                '[attr=attr attr]+[attr]{text}+[attr]*8+[attr=`var` %func()% attr=attr]'
//            ],
//            [
//                'div{text}+div{text}*8+div{text `var` %func()% asdf}',
//            ],
//            [
//                '{text `var` %func()% text}*8+{text}+div'
//            ],
//            [
//                '*8+*7asdf'
//            ],
//            [
//                'div`var[$]{$}->title`'
//            ],
//            [
//                '{%select(select name, `values`, `selected`)%}+{%span(value of span)%}'
//            ],
//            [
//                '`var`+%func()%>`var`^+(`var`*8)+%func()%'
//            ],
        ];

    }

}

