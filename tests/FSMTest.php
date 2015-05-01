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
        $this->assertEquals(FSM::GET_TAG, $fsm->getState());
        new FSM(FSM::SET_OPERATOR);

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
            // get attr errors
            ['root>[attr'],
            ['root>[attr]div'],
            ['root>[attr].class'],
            ['root>[attr]#id'],
            // get text errors
            ['root>{text'],
            ['root>{text}div'],
            ['root>{text}.class'],
            ['root>{text}#id'],
            ['root>{text}[attr]'],
            // get multiplications errors
            ['root>*32a'],
            ['root>*2.class'],
            ['root>*2#id'],
            ['root>*2[attr]'],
            ['root>*2{text}'],
        ];

    }
//[$this->getProcessEmmet('root>[attr')],
//[$this->getProcessEmmet('root>[attr]p')],

    public function emmetProvider()
    {

        return [
            // set_operator testing
            [$this->getProcessEmmet('div+()^>div'), 'div'.FSM::GET_TAG.'+'.FSM::SET_OPERATOR.'('.FSM::SET_OPERATOR.')'.FSM::SET_OPERATOR.'^'.FSM::SET_OPERATOR.'>'.FSM::SET_OPERATOR.'div'.FSM::GET_TAG],
            [$this->getProcessEmmet('div>#id+.class+([attr=attr]^+span{text})>{text node}+span*2+span+*2'), 'div'.FSM::GET_TAG.'>'.FSM::SET_OPERATOR.'#id'.FSM::GET_ID.'+'.FSM::SET_OPERATOR.'.class'.FSM::GET_CLASS.'+'.FSM::SET_OPERATOR.'('.FSM::SET_OPERATOR.'[attr=attr'.FSM::GET_ATTR.']'.FSM::WAIT_AFTER_ATTR.'^'.FSM::SET_OPERATOR.'+'.FSM::SET_OPERATOR.'span'.FSM::GET_TAG.'{text'.FSM::GET_TEXT.'}'.FSM::WAIT_AFTER_TEXT.')'.FSM::SET_OPERATOR.'>'.FSM::SET_OPERATOR.'{text node'.FSM::GET_TEXT_NODE.'}'.FSM::WAIT_AFTER_TEXT_NODE.'+'.FSM::SET_OPERATOR.'span'.FSM::GET_TAG.'*2'.FSM::GET_MULTIPLICATION.'+'.FSM::SET_OPERATOR.'span'.FSM::GET_TAG.'+'.FSM::SET_OPERATOR.'*2'.FSM::GET_MULTIPLICATION],
            // get_tag testing
            [$this->getProcessEmmet('div+div#id+div.class+div[attr]+div{text}+div*2+#id#id'), 'div'.FSM::GET_TAG.'+'.FSM::SET_OPERATOR.'div'.FSM::GET_TAG.'#id'.FSM::GET_ID.'+'.FSM::SET_OPERATOR.'div'.FSM::GET_TAG.'.class'.FSM::GET_CLASS.'+'.FSM::SET_OPERATOR.'div'.FSM::GET_TAG.'[attr'.FSM::GET_ATTR.']'.FSM::WAIT_AFTER_ATTR.'+'.FSM::SET_OPERATOR.'div'.FSM::GET_TAG.'{text'.FSM::GET_TEXT.'}'.FSM::WAIT_AFTER_TEXT.'+'.FSM::SET_OPERATOR.'div'.FSM::GET_TAG.'*2'.FSM::GET_MULTIPLICATION.'+'.FSM::SET_OPERATOR.'#id'.FSM::GET_ID.'#id'.FSM::GET_ID],
            // get_id testing
            [$this->getProcessEmmet('root>#id+#id.class+#id[attr]+#id{text}+#id`$`*21'), 'root'.FSM::GET_TAG.'>'.FSM::SET_OPERATOR.'#id'.FSM::GET_ID.'+'.FSM::SET_OPERATOR.'#id'.FSM::GET_ID.'.class'.FSM::GET_CLASS.'+'.FSM::SET_OPERATOR.'#id'.FSM::GET_ID.'[attr'.FSM::GET_ATTR.']'.FSM::WAIT_AFTER_ATTR.'+'.FSM::SET_OPERATOR.'#id'.FSM::GET_ID.'{text'.FSM::GET_TEXT.'}'.FSM::WAIT_AFTER_TEXT.'+'.FSM::SET_OPERATOR.'#id`$`'.FSM::GET_ID.'*21'.FSM::GET_MULTIPLICATION],
            // get_class testing
            [$this->getProcessEmmet('root>.class#id+.class.class+.class[attr]+.class{text}+.class*12+.class+.class'), 'root'.FSM::GET_TAG.'>'.FSM::SET_OPERATOR.'.class'.FSM::GET_CLASS.'#id'.FSM::GET_ID.'+'.FSM::SET_OPERATOR.'.class'.FSM::GET_CLASS.'.class'.FSM::GET_CLASS.'+'.FSM::SET_OPERATOR.'.class'.FSM::GET_CLASS.'[attr'.FSM::GET_ATTR.']'.FSM::WAIT_AFTER_ATTR.'+'.FSM::SET_OPERATOR.'.class'.FSM::GET_CLASS.'{text'.FSM::GET_TEXT.'}'.FSM::WAIT_AFTER_TEXT.'+'.FSM::SET_OPERATOR.'.class'.FSM::GET_CLASS.'*12'.FSM::GET_MULTIPLICATION.'+'.FSM::SET_OPERATOR.'.class'.FSM::GET_CLASS.'+'.FSM::SET_OPERATOR.'.class'.FSM::GET_CLASS],
            // get_attr testing
            [$this->getProcessEmmet('root>[attr=attr attr attr=attr]+[attr]{text}+[attr]*2'), 'root'.FSM::GET_TAG.'>'.FSM::SET_OPERATOR.'[attr=attr attr attr=attr'.FSM::GET_ATTR.']'.FSM::WAIT_AFTER_ATTR.'+'.FSM::SET_OPERATOR.'[attr'.FSM::GET_ATTR.']'.FSM::WAIT_AFTER_ATTR.'{text'.FSM::GET_TEXT.'}'.FSM::WAIT_AFTER_TEXT.'+'.FSM::SET_OPERATOR.'[attr'.FSM::GET_ATTR.']'.FSM::WAIT_AFTER_ATTR.'*2'.FSM::GET_MULTIPLICATION],
            // get_test testing
            [$this->getProcessEmmet('root>p{text}+p{text}*2'), 'root'.FSM::GET_TAG.'>'.FSM::SET_OPERATOR.'p'.FSM::GET_TAG.'{text'.FSM::GET_TEXT.'}'.FSM::WAIT_AFTER_TEXT.'+'.FSM::SET_OPERATOR.'p'.FSM::GET_TAG.'{text'.FSM::GET_TEXT.'}'.FSM::WAIT_AFTER_TEXT.'*2'.FSM::GET_MULTIPLICATION],
            // get_multiplication testing
            [$this->getProcessEmmet('root>div*2+*2'), 'root'.FSM::GET_TAG.'>'.FSM::SET_OPERATOR.'div'.FSM::GET_TAG.'*2'.FSM::GET_MULTIPLICATION.'+'.FSM::SET_OPERATOR.'*2'.FSM::GET_MULTIPLICATION],
        ];

    }

    protected function getProcessEmmet($emmet_string)
    {

        $fsm = new FSM(FSM::GET_TAG);
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