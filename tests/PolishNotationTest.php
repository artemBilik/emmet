<?php
namespace emmet\test;

use \emmet\Element;

require_once __DIR__ . '/../src/Element.php';
require_once __DIR__ . '/PolishNotation.php';

class PolishNotationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider infixProvider
     */
    public function testInfixToPostfixTransform($infix, $postfix)
    {


        $this->assertEquals($this->transformPostfix($postfix), $this->transformInfinx($infix));

    }
    /**
     * @dataProvider infixErrorProvider
     * @expectedException Exception
     */
    public function testInfixErrors($infix)
    {

        $this->transformInfinx($infix);

    }
    /**
     * @dataProvider postfixErrorProvider
     * @expectedException Exception
     */
    public function testPostfixErrors($postfix)
    {

        $pn = new PolishNotation();
        $pn->setOutput($postfix);
        if(is_string($message = $pn->generateTree())){
            throw new \Exception($message);
        }

    }

    public function postfixErrorProvider()
    {

        return [
            [ explode(' ', 'root > div + +'), ],
            [ explode(' ', 'root > div div div +'), ],
            [ explode(' ', 'root div div div + >'), ],
            [ explode(' ', 'root div div div > ^^^'), ],
            [ explode(' ', 'root div div div + ( +'), ],
            [ explode(' ', 'root div div div + ) +'), ],
        ];

    }

    public function infixErrorProvider()
    {

        return [
            [
                explode(' ', 'root > div > div > div ^ ^ ^ ^ ^ ^ '),
            ],
            [
                explode(' ', 'root > (div > div ) ) > div'),
            ],
            [
                explode(' ', 'root > div + ( ( div + div )'),
            ],
        ];

    }

    public function infixProvider()
    {

        return [
            // 'root>div+div'  'root div div + >'
            [
                explode(' ', 'root > div + div'),
                explode(' ', 'root div div + >'),
            ],
            // 'root>div>div'    'root div div > > '
            [
                 explode(' ', 'root > div > div'),
                 explode(' ', 'root div div > >'),
            ],
            // 'root>div>div+div>div^^+div'   'root div div div div > ^ + > ^ div + >'
            [
                 explode(' ', 'root > div > div + div > div ^ ^ + div'),
                 explode(' ', 'root div div div div > ^ + > ^ div + >'),
            ],
            // 'root>(div>div>div>div)>div'  'root div div div div > > > ^ div > > '
            [
                 explode(' ', 'root > ( div > div > div > div ) > div'),
                 explode(' ', 'root div div div div > > > ^ div > >'),
            ],
            // 'root>(div+div)+div' 'root div div + div + >'
            [
                 explode(' ', 'root > ( div + div ) + div'),
                 explode(' ', 'root div div + div + >'),
            ],
            // 'root>div>(div>div>div^+div)>div^>div' 'root div div div div > ^ div + > div > ^ div > > > '
            [
                 explode(' ', 'root > div > ( div > div > div ^ + div ) > div ^ > div'),
                 explode(' ', 'root div div div div > ^ div + > div > ^ div > > >'),
            ],
            // 'root>div>div>(div>div>div)^^+div' 'root div div div div div > > > ^ > ^ div + >'
            [
                 explode(' ', 'root > div > div > ( div > div > div ) ^ ^ + div'),
                 explode(' ', 'root div div div div div > > > ^ > ^ div + >'),
            ],
        ];

    }

    private function transformInfinx(array $collection)
    {

        $pn = new PolishNotation();
        foreach($collection as $item){
            if(!in_array($item, array('+', '>', '^', '(', ')'))){
                $el = new Element();
                if('root' === $item){
                    $el->setRoot();
                }
                $el->setTag($item);
                $pn->setOperand($el);
            } else {
                if( true !== ($message = $pn->setOperator($item))){
                    throw new \Exception($message);
                }
            }
        }

        if(is_string(($output = $pn->endOutput()))){
            throw new \Exception($message);
        }
        return $pn->getOutput();
        
    }

    private function transformPostfix(array $collection)
    {

        $postfix = array();
        foreach($collection as $item){
            if(!$this->isOperator($item)){
                $el = new Element();
                if('root' === $item){
                    $el->setRoot();
                }
                $el->setTag($item);
                $postfix[] = $el;
            } else {
                $postfix[] = $item;
            }
        }
        return $postfix;

    }

    private function isOperator($str)
    {

        return in_array($str, array('+', '>', '^', '(', ')'));

    }

}
