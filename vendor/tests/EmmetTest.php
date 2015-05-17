<?php
namespace artem_c\emmet\test;

use \artem_c\emmet\Emmet as Emmet;

require_once __DIR__ . '/../src/Emmet.php';

class EmmetTest extends \PHPUnit_Framework_TestCase
{

    private $_emmet_string = 'div>p>span+a>span';

    /**
     *  @expectedException \artem_c\emmet\EmmetException
     */
    public function testConstruct()
    {

        $reflection_class = new \ReflectionClass('\artem_c\emmet\Emmet');
        $emmet_property = $reflection_class->getProperty('_emmet_string');
        $emmet_property->setAccessible(true);

        $tree_property = $reflection_class->getProperty('_tree');
        $tree_property->setAccessible(true);

        $data_property = $reflection_class->getProperty('_data');
        $data_property->setAccessible(true);

        $emmet = new Emmet($this->_emmet_string);
        $this->assertEquals($this->_emmet_string, $emmet_property->getValue($emmet));

        $this->assertInstanceOf('\artem_c\emmet\Tree', $tree_property->getValue($emmet));
        $this->assertTrue($tree_property->getValue($emmet)->isRoot());

        $this->assertInstanceOf('\artem_c\emmet\Data', $data_property->getValue($emmet));


        new Emmet(new \stdClass());

    }

    public function testGetCheckDocumentation()
    {

        $emmet = new Emmet($this->_emmet_string);

        $ref = new \ReflectionClass('\artem_c\emmet\Emmet');
        $method = $ref->getMethod('getCheckTheDocumentation');
        $method->setAccessible(true);

        $this->assertEquals('Check the documentation for the right syntax to use near "div&gt;<strong style="color:red;">p&gt;span+a&gt;span</strong>".', $method->invoke($emmet, 10));

    }

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

        return [
            // testing operators
            [
                'div+div>p>a+span^+p+(p>span)^+div+(div+div)>p^+((div>p)>p)',
                '<div></div><div><p><a></a><span></span></p><p></p><p><span></span></p></div><div></div><div><p></p></div><div></div><div><p></p><p></p></div>',
            ],
            [
                'div+div>p>a+span^+p+(p>span)^+div+(div+div)>p+((div>p)>p)',
                '<div></div><div><p><a></a><span></span></p><p></p><p><span></span></p></div><div></div><div><p></p><div><p></p><p></p></div></div><div></div>',
            ],
            [
                '(div>a)>p^+div',
                '<div><a></a><p></p></div><div></div>'
            ],
            // testing multiplication
            [
                'a*2+img*`var`%func(2)%+hr*%func(`var`)%`test`+table>tr*`test`>td*%func(`test`)%',
                '<a></a><a></a><img /><img /><hr /><hr /><table><tr><td></td><td></td></tr><tr><td></td><td></td></tr></table>',
                ['var' => 0, 'test' => 2],
                ['func' => function ($i) { return $i; }],
            ],
            [
                'div*2>p{`var[$]`}+{`var[$]`}^+div*2>p{`var[$]`}*2+{`var[$]`}',
                '<div><p>first</p>first</div><div><p>second</p>second</div><div><p>first</p><p>second</p>first</div><div><p>first</p><p>second</p>second</div>',
                ['var' => ['first', 'second']],
            ],
            // testing tags
            [
                'div+div`var`%func(`var`,test)%+div%func(test,var)%`var`+d`var`+d%func(test,`var`)%+div',
                '<div></div><divvarvartest></divvarvartest><divtestvarvar></divtestvarvar><dvar></dvar><dtestvar></dtestvar><div></div>',
                ['var' => 'var'],
                ['func' => function($a, $b){ return $a . $b ; }]
            ],
            [
                'a#id+img.class+td[attr]+dt{text}+mp*2',
                '<a id="id"></a><img class="class" /><td attr="attr"></td><dt>text</dt><mp></mp><mp></mp>',
                [],[]
            ],
            // testing id
            [
                'abr#id+hr#id`var`id+span#id%func(`var`, test)%id+input#`var`%func(test, `var`)%+ul#%func()%`var`',
                '<abr id="id"></abr><hr id="idvarid" /><span id="idvartestid"></span><input id="vartestvar" /><ul id="var"></ul>',
                ['var' => 'var'],
                ['func' => function($a = '', $b = ''){ return $a . $b ; }]
            ],
            [
                'h1#id.class+ruby#id[attr]+ol#id{text}+var#id*2',
                '<h1 id="id" class="class"></h1><ruby id="id" attr="attr"></ruby><ol id="id">text</ol><var id="id"></var><var id="id"></var>',
            ],
            // testing class
            [
                'header.class+section.class1 class2 class3+div.class1 `var` %func(`var`, test)% class2+div.`var`+footer.%func(test, `var`)%',
                '<header class="class"></header><section class="class1 class2 class3"></section><div class="class1 var vartest class2"></div><div class="var"></div><footer class="testvar"></footer>',
                ['var' => 'var'],
                ['func' => function($a = '', $b = ''){ return $a . $b ; }]
            ],
            [
                'header.class1 class2[attr]+footer.class{text}+div.class*2',
                '<header class="class1 class2" attr="attr"></header><footer class="class">text</footer><div class="class"></div><div class="class"></div>'
            ],
            // testing text
            [
                'header{text `var` %func(`var`,text)% text}',
                '<header>text var vartext text</header>',
                ['var' => 'var'],
                ['func' => function($a = '', $b = ''){ return $a . $b ; }]
            ],
            [
                'header{text}*2+section{text}',
                '<header>text</header><header>text</header><section>text</section>'
            ],
            // testing text node
            [
                '{press }+a{here}+{ `var` %func(continue)%}+{ more}*2',
                'press <a>here</a> to continue more more',
                ['var' => 'to'],
                ['func' => function($phrase) { return $phrase; } ]
            ],
            // testing html
            [
                '`var`>%func(test, `arg`)%>`var`+`arg`*2',
                'var testargvar argarg',
                ['var' => 'var {{value}}', 'arg' => 'arg'],
                ['func' => function($a, $b, $value) { return $a. $b.$value; }],
            ],

        ];

    }
    /**
     * @dataProvider readMeEmmetStrProvider
     */
    public function testReadMe($emmet, $html, $data = [], $functions = [])
    {

        Emmet::addFunctions($functions);
        $this->assertEquals((new Emmet($emmet))->create($data), $html);

    }

    public function readMeEmmetStrProvider()
    {

        return [
            [
                'div>p>span+a>img[src=img.jpg]',
                '<div><p><span></span><a><img src="img.jpg" /></a></p></div>',
            ],
            [
                'tr>td{`value`}',
                '<tr><td>value</td></tr>',
                ['value' => 'value'],
            ],
            [
                'table#myTable>tbody>tr.myTr*`tr_cnt`>td.title{`data[$][title]`}+td{`data[$][value]`}',
                '<table id="myTable"><tbody><tr class="myTr"><td class="title">t1</td><td>v1</td></tr><tr class="myTr"><td class="title">t2</td><td>v2</td></tr><tr class="myTr"><td class="title">t3</td><td>v3</td></tr></tbody></table>',
                ['data' => [['title' => 't1', 'value' => 'v1'], ['title' => 't2', 'value' => 'v2'], ['title' => 't3', 'value' => 'v3']], 'tr_cnt' => 3]

            ],
            [
                'a+span',
                '<a></a><span></span>',
            ],
            [
                'a>span',
                '<a><span></span></a>'
            ],
            [
                'p>a>img^+span',
                '<p><a><img /></a><span></span></p>'
            ],
            [
                '(div>p+a)+div',
                '<div><p></p><a></a></div><div></div>'
            ],
            [
                '(div>p>a>span)>p',
                '<div><p><a><span></span></a></p><p></p></div>'
            ],
            [
                'div>(div>p)^+div',
                '<div><div><p></p></div></div><div></div>'
            ],
            [
                'div+h1',
                '<div></div><h1></h1>'
            ],
            [
                'div#myDiv>span',
                '<div id="myDiv"><span></span></div>'
            ],
            [
                'div.class1+div.class1 class2',
                '<div class="class1"></div><div class="class1 class2"></div>'
            ],
            [
                'option[value=12 selected]',
                '<option value="12" selected="selected"></option>'
            ],
            [
                'p{some text}',
                '<p>some text</p>',
            ],
            [
                'p*2',
                '<p></p><p></p>'
            ],
            [
                'p+{ some text }+a',
                '<p></p> some text <a></a>'
            ],
            [
                'p+{ some text }*2',
                '<p></p> some text  some text ',
            ],
            [
                'p.`info_class`{`information`}+span',
                '<p class="info">some information for user</p><span></span>',
                [ 'information' => 'some information for user', 'info_class' => 'info']
            ],
            [
                'ul>li{`ul[$]`}*2',
                '<ul><li>1</li><li>2</li></ul>',
                ['ul' => [1,2,3]],
            ],
            [
                'p{%funcName()%}',
                '<p>funcName</p>',
                [],
                ['funcName' => function() { return 'funcName';}]
            ],
            [
                'p{%funcName(some text)%}',
                '<p> some text </p>',
                [],
                ['funcName' => function($arg) { return ' ' . $arg . ' '; }],
            ],
            [
                'p{%funcName(`arg`)%}',
                '<p> arg value </p>',
                ['arg' => 'arg value'],
                ['funcName' => function($arg) { return ' ' . $arg . ' '; }],
            ],
            [
                'p{%func(`a`, b, `c`)%}',
                '<p>aaabccc</p>',
                ['a' => 'aaa', 'c' => 'ccc'],
                ['func' => function($a, $b, $c) { return $a.$b.$c; }],
            ],
            [
                'div>header{%infoHeader()%}+section{some info}',
                '<div><header>Information header</header><section>some info</section></div>',
                [],
                ['infoHeader' => 'Information header']
            ],
            [
                'p#identifier_`$`{the value of node is %getValue(`value[$]`)%, the number of node is `$`}*%count(`value`)%',
                '<p id="identifier_0">the value of node is 0, the number of node is 0</p><p id="identifier_1">the value of node is 10, the number of node is 1</p><p id="identifier_2">the value of node is 20, the number of node is 2</p><p id="identifier_3">the value of node is 30, the number of node is 3</p><p id="identifier_4">the value of node is 40, the number of node is 4</p><p id="identifier_5">the value of node is 50, the number of node is 5</p>',
                ['value' => [0,10,20,30,40,50]],
                ['count' => function($value) { return count($value); }, 'getValue' => function($value) { return $value; }]
            ],
            [
                'div>`htmlVar`+%htmlFunction()%',
                '<div>variable html nodefunction html node</div>',
                ['htmlVar' => 'variable html node'],
                ['htmlFunction' => function(){ return 'function html node'; }],
            ],
            [
                'div+`myP`>a+span',
                '<div></div><p class="myP"><a></a><span></span></p>',
                ['myP' => '<p class="myP">{{value}}</p>']
            ],
            [
                'div>%oneMoreP()%>`myP`>a+a',
                '<div><p class="one more p"><p class="myP"><a></a><a></a></p></p></div>',
                ['myP' => '<p class="myP">{{value}}</p>'],
                ['oneMoreP' => '<p class="one more p">{{value}}</p>']
            ],
            [
                'div>%func(first, `second`)%>`second`+a',
                '<div>first second second<a></a></div>',
                ['second' => 'second'],
                ['func' => function($first, $second, $value) { return $first . ' '.$second.' '.$value; }]
            ],
        ];

    }

}
