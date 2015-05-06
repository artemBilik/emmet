<?php
namespace emmet\test;

use \emmet\FiniteStateMachine as FSM;

require_once __DIR__ . '/../src/FiniteStateMachine.php';


class FSMTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInit()
    {

        $fsm = new FSM();
        $this->assertEquals(FSM::TAG, $fsm->getState());
        $this->assertTrue($fsm->checkMap(), 'Map is not correct.');
        new FSM(FSM::OPERATOR);

    }


    /**
     * @dataProvider emmetProvider
     */

    public function testFull($process, $expected)
    {

        $this->assertEquals($expected, $process);

    }
    /**
     * @dataProvider errorsProvider
     * @expectedException Exception
     *
     */
    public function testErrors($emmet)
    {

        $this->getProcessEmmet($emmet);

    }

    public function errorsProvider()
    {

        return [
            // get operator errors
            ['root>]'],
            ['root+}'],
            ['root+,'],
            // get tag errors
            ['root+div('],
            ['root+div]'],
            ['root+div}'],
            ['root+div,'],
            // get id errors
            ['root>#id('],
            ['root>#id#'],
            ['root>#id]'],
            ['root>#id}'],
            ['root>#id,'],
            // get class errors
            ['root>.class('],
            ['root>.class#'],
            ['root>.class]'],
            ['root>.class}'],
            ['root>.class,'],
            // get attr errors
            ['root>[attr[]'],
            ['root>[attr'],
            ['root>[attr]('],
            ['root>[attr]a'],
            ['root>[attr]#'],
            ['root>[attr].'],
            ['root>[attr]['],
            ['root>[attr]]'],
            ['root>[attr]}'],
            ['root>[attr]`'],
            ['root>[attr]%'],
            ['root>[attr],'],
            // get text errors
            ['root>{text{}'],
            ['root>{text'],
            ['root>{text}('],
            ['root>{text}a'],
            ['root>{text}#'],
            ['root>{text}.'],
            ['root>{text}['],
            ['root>{text}]'],
            ['root>{text}]{'],
            ['root>{text}]}'],
            ['root>{text}]`'],
            ['root>{text}]%'],
            ['root>{text}],'],
            // get text node errors
            ['root>{text{}'],
            ['root>{text'],
            ['root>{text}>'],
            ['root>{text}('],
            ['root>{text}a'],
            ['root>{text}#'],
            ['root>{text}.'],
            ['root>{text}['],
            ['root>{text}]'],
            ['root>{text}{'],
            ['root>{text}}'],
            ['root>{text}`'],
            ['root>{text}%'],
            ['root>{text},'],
            // get multi errors
            ['root>*8('],
            ['root>*8#'],
            ['root>*8.'],
            ['root>*8['],
            ['root>*8]'],
            ['root>*8{'],
            ['root>*8}'],
            ['root>*8*'],
            ['root>*8,'],
            // get variable errors
            ['root>`var+`'],
            ['root>`var^`'],
            ['root>`var(`'],
            ['root>`var)`'],
            ['root>`var#`'],
            ['root>`var.`'],
            ['root>`var*`'],
            ['root>`var%`'],
            ['root>`var `'],
            ['root>`var'],
            ['root>`var,`'],
            // get func errors
            ['root>%func+()%'],
            ['root>%func>()%'],
            ['root>%func^()%'],
            ['root>%func)()%'],
            ['root>%func#()%'],
            ['root>%func.()%'],
            ['root>%func[()%'],
            ['root>%func]()%'],
            ['root>%func{()%'],
            ['root>%func}()%'],
            ['root>%func*()%'],
            ['root>%func`()%'],
            ['root>%func()'],
            ['root>%fun,c()'],
            // get args errors
            ['root>%func(a,+)%'],
            ['root>%func(a,^)%'],
            ['root>%func(a,>)%'],
            ['root>%func(a,))%'],
            ['root>%func(a,#)%'],
            ['root>%func(a,.)%'],
            ['root>%func(a,[)%'],
            ['root>%func(a,])%'],
            ['root>%func(a,{)%'],
            ['root>%func(a,})%'],
            ['root>%func(a,*)%'],
            ['root>%func(a,%)%'],
            ['root>%func(a,'],
            // get args txt errors
            ['root>%func(a+)%'],
            ['root>%func(a^)%'],
            ['root>%func(a>)%'],
            ['root>%func(a))%'],
            ['root>%func(a#)%'],
            ['root>%func(a.)%'],
            ['root>%func(a[)%'],
            ['root>%func(a])%'],
            ['root>%func(a{)%'],
            ['root>%func(a})%'],
            ['root>%func(a*)%'],
            ['root>%func(a%)%'],
            ['root>%func(a`)%'],
            ['root>%func(a'],
            // get args var errors
            ['root>%func(a, `var+`)%'],
            ['root>%func(a, `var>`)%'],
            ['root>%func(a, `var^`)%'],
            ['root>%func(a, `var(`)%'],
            ['root>%func(a, `var)`)%'],
            ['root>%func(a, `var#`)%'],
            ['root>%func(a, `var.`)%'],
            ['root>%func(a, `var*`)%'],
            ['root>%func(a, `var%`)%'],
            ['root>%func(a, `var `)%'],
            ['root>%func(a, `var,`)%'],
            ['root>%func(a, `var,'],
            // get html errors
            ['root>%func()%('],
            ['root>%func()%d'],
            ['root>%func()%#'],
            ['root>%func()%.'],
            ['root>%func()%['],
            ['root>%func()%]'],
            ['root>%func()%{'],
            ['root>%func()%}'],
            ['root>%func()%`'],
            ['root>%func()%%'],
            ['root>%func()%,'],

        ];

    }

    public function emmetProvider()
    {

        return [
            // testing operator
            [$this->getProcessEmmet('root>+>()^+tag+#id+.class+[attr]+{text}+*8+`var`+%func()%+div'), 'root'.FSM::TAG.'>'.FSM::OPERATOR.'+'.FSM::OPERATOR.'>'.FSM::OPERATOR.'('.FSM::OPERATOR.')'.FSM::OPERATOR.'^'.FSM::OPERATOR.'+'.FSM::OPERATOR.'tag'.FSM::TAG.'+'.FSM::OPERATOR.'#id'.FSM::ID.'+'.FSM::OPERATOR.'.class'.FSM::CLASS_NAME.'+'.FSM::OPERATOR.'[attr'.FSM::ATTR.']'.FSM::AFTER_ATTR.'+'.FSM::OPERATOR.'{text'.FSM::TEXT_NODE.'}'.FSM::AFTER_TEXT_NODE.'+'.FSM::OPERATOR.'*8'.FSM::MULTI.'+'.FSM::OPERATOR.'`var'.FSM::VARIABLE.'`'.FSM::HTML.'+'.FSM::OPERATOR.'%func'.FSM::FUNC.'('.FSM::ARGS.')'.FSM::FUNC.'%'.FSM::HTML.'+'.FSM::OPERATOR.'div'.FSM::TAG],
            // testing tag
            [$this->getProcessEmmet('root>tag#id>tag.class+tag[attr]+tag{text}+tag*8+ta`var`g+ta%func()%g+tag`var`+tag%func()%'), 'root'.FSM::TAG.'>'.FSM::OPERATOR.'tag'.FSM::TAG.'#id'.FSM::ID.'>'.FSM::OPERATOR.'tag'.FSM::TAG.'.class'.FSM::CLASS_NAME.'+'.FSM::OPERATOR.'tag'.FSM::TAG.'[attr'.FSM::ATTR.']'.FSM::AFTER_ATTR.'+'.FSM::OPERATOR.'tag'.FSM::TAG.'{text'.FSM::TEXT.'}'.FSM::AFTER_TEXT.'+'.FSM::OPERATOR.'tag'.FSM::TAG.'*8'.FSM::MULTI.'+'.FSM::OPERATOR.'ta'.FSM::TAG.'`var'.FSM::VARIABLE.'`g'.FSM::TAG.'+'.FSM::OPERATOR.'ta'.FSM::TAG.'%func'.FSM::FUNC.'('.FSM::ARGS.')'.FSM::FUNC.'%g'.FSM::TAG.'+'.FSM::OPERATOR.'tag'.FSM::TAG.'`var'.FSM::VARIABLE.'`'.FSM::TAG.'+'.FSM::OPERATOR.'tag'.FSM::TAG.'%func'.FSM::FUNC.'('.FSM::ARGS.')'.FSM::FUNC.'%'.FSM::TAG],
            // testing id
            [$this->getProcessEmmet('root>#id.class>#id[attr]>#id{text}>#id*8>#as`var`sd+#as%func()%sd'),'root'.FSM::TAG.'>'.FSM::OPERATOR.'#id'.FSM::ID.'.class'.FSM::CLASS_NAME.'>'.FSM::OPERATOR.'#id'.FSM::ID.'[attr'.FSM::ATTR.']'.FSM::AFTER_ATTR.'>'.FSM::OPERATOR.'#id'.FSM::ID.'{text'.FSM::TEXT.'}'.FSM::AFTER_TEXT.'>'.FSM::OPERATOR.'#id'.FSM::ID.'*8'.FSM::MULTI.'>'.FSM::OPERATOR.'#as'.FSM::ID.'`var'.FSM::VARIABLE.'`sd'.FSM::ID.'+'.FSM::OPERATOR.'#as'.FSM::ID.'%func'.FSM::FUNC.'('.FSM::ARGS.')'.FSM::FUNC.'%sd'.FSM::ID],
            // testing class
            [$this->getProcessEmmet('root>.class+.class[attr]+.class1 class2.class3+.class{text}+.class*8+.`var`as+.%func()%sd'), 'root'.FSM::TAG.'>'.FSM::OPERATOR.'.class'.FSM::CLASS_NAME.'+'.FSM::OPERATOR.'.class'.FSM::CLASS_NAME.'[attr'.FSM::ATTR.']'.FSM::AFTER_ATTR.'+'.FSM::OPERATOR.'.class1 class2'.FSM::CLASS_NAME.'.class3'.FSM::CLASS_NAME.'+'.FSM::OPERATOR.'.class'.FSM::CLASS_NAME.'{text'.FSM::TEXT.'}'.FSM::AFTER_TEXT.'+'.FSM::OPERATOR.'.class'.FSM::CLASS_NAME.'*8'.FSM::MULTI.'+'.FSM::OPERATOR.'.'.FSM::CLASS_NAME.'`var'.FSM::VARIABLE.'`as'.FSM::CLASS_NAME.'+'.FSM::OPERATOR.'.'.FSM::CLASS_NAME.'%func'.FSM::FUNC.'('.FSM::ARGS.')'.FSM::FUNC.'%sd'.FSM::CLASS_NAME],
            // testing attr
            [$this->getProcessEmmet('root>[attr=attr attr]+[attr]{text}+[attr]*8+[attr=`var` %func()% attr=attr]'), 'root'.FSM::TAG.'>'.FSM::OPERATOR.'[attr=attr attr'.FSM::ATTR.']'.FSM::AFTER_ATTR.'+'.FSM::OPERATOR.'[attr'.FSM::ATTR.']'.FSM::AFTER_ATTR.'{text'.FSM::TEXT.'}'.FSM::AFTER_TEXT.'+'.FSM::OPERATOR.'[attr'.FSM::ATTR.']'.FSM::AFTER_ATTR.'*8'.FSM::MULTI.'+'.FSM::OPERATOR.'[attr='.FSM::ATTR.'`var'.FSM::VARIABLE.'` '.FSM::ATTR.'%func'.FSM::FUNC.'('.FSM::ARGS.')'.FSM::FUNC.'% attr=attr'.FSM::ATTR.']'.FSM::AFTER_ATTR],
            // testing text
            [$this->getProcessEmmet('root>div{text}+div{text}*8+div{text `var` %func()% asdf}'), 'root'.FSM::TAG.'>'.FSM::OPERATOR.'div'.FSM::TAG.'{text'.FSM::TEXT.'}'.FSM::AFTER_TEXT.'+'.FSM::OPERATOR.'div'.FSM::TAG.'{text'.FSM::TEXT.'}'.FSM::AFTER_TEXT.'*8'.FSM::MULTI.'+'.FSM::OPERATOR.'div'.FSM::TAG.'{text '.FSM::TEXT.'`var'.FSM::VARIABLE.'` '.FSM::TEXT.'%func'.FSM::FUNC.'('.FSM::ARGS.')'.FSM::FUNC.'% asdf'.FSM::TEXT.'}'.FSM::AFTER_TEXT],
            // testing text node
            [$this->getProcessEmmet('root>{text `var` %func()% text}*8+{text}+div'), 'root'.FSM::TAG.'>'.FSM::OPERATOR.'{text '.FSM::TEXT_NODE.'`var'.FSM::VARIABLE.'` '.FSM::TEXT_NODE.'%func'.FSM::FUNC.'('.FSM::ARGS.')'.FSM::FUNC.'% text'.FSM::TEXT_NODE.'}'.FSM::AFTER_TEXT_NODE.'*8'.FSM::MULTI.'+'.FSM::OPERATOR.'{text'.FSM::TEXT_NODE.'}'.FSM::AFTER_TEXT_NODE.'+'.FSM::OPERATOR.'div'.FSM::TAG],
            // testing multiplication
            [$this->getProcessEmmet('root>*8+*7asdf'), 'root'.FSM::TAG.'>'.FSM::OPERATOR.'*8'.FSM::MULTI.'+'.FSM::OPERATOR.'*7asdf'.FSM::MULTI],
            // testing variable
            [$this->getProcessEmmet('root>div`var[$]{$}->title`'), 'root'.FSM::TAG.'>'.FSM::OPERATOR.'div'.FSM::TAG.'`var[$]{$}->title'.FSM::VARIABLE.'`'.FSM::TAG],
            // testing function
            [$this->getProcessEmmet('root>{%select(select name, `values`, `selected`)%}+{%span(value of span)%}'), 'root'.FSM::TAG.'>'.FSM::OPERATOR.'{'.FSM::TEXT_NODE.'%select'.FSM::FUNC.'('.FSM::ARGS.'select name'.FSM::ARG_TXT.', '.FSM::ARGS.'`values'.FSM::ARG_VAR.'`, '.FSM::ARGS.'`selected'.FSM::ARG_VAR.'`'.FSM::ARGS.')'.FSM::FUNC.'%'.FSM::TEXT_NODE.'}'.FSM::AFTER_TEXT_NODE.'+'.FSM::OPERATOR.'{'.FSM::TEXT_NODE.'%span'.FSM::FUNC.'('.FSM::ARGS.'value of span'.FSM::ARG_TXT.')'.FSM::FUNC.'%'.FSM::TEXT_NODE.'}'.FSM::AFTER_TEXT_NODE],
            // testing html
            [$this->getProcessEmmet('root>`var`+%func()%>`var`^+(`var`*8)+%func()%'), 'root'.FSM::TAG.'>'.FSM::OPERATOR.'`var'.FSM::VARIABLE.'`'.FSM::HTML.'+'.FSM::OPERATOR.'%func'.FSM::FUNC.'('.FSM::ARGS.')'.FSM::FUNC.'%'.FSM::HTML.'>'.FSM::OPERATOR.'`var'.FSM::VARIABLE.'`'.FSM::HTML.'^'.FSM::OPERATOR.'+'.FSM::OPERATOR.'('.FSM::OPERATOR.'`var'.FSM::VARIABLE.'`'.FSM::HTML.'*8'.FSM::MULTI.')'.FSM::OPERATOR.'+'.FSM::OPERATOR.'%func'.FSM::FUNC.'('.FSM::ARGS.')'.FSM::FUNC.'%'.FSM::HTML],
        ];

    }

    protected function getProcessEmmet($emmet_string)
    {

        $fsm = new FSM(FSM::TAG);
        $process = '';
        $value = '';
        $i = 0;
        $length = strlen($emmet_string) - 1;

        while(FSM::END !== $fsm->getState()){
            if($i > $length){
                $symbol = '';
            } else {
                $symbol = $emmet_string[$i];
            }

            $fsm->setState($symbol);
            if($fsm->getState()  === FSM::ERROR){
                throw new \Exception('error');
            }

            if($fsm->isStateChanged()){
                $process .= $value.$fsm->getPrevState();
                $value = $symbol;
            } else {
                $value .= $symbol;
            }

            ++$i;
        }

        return $process;

    }

}

