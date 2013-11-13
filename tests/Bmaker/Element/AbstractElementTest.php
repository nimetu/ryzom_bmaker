<?php

namespace Bmaker\Element;

/**
 * Abstract element setter/getter test
 */
class AbstractElementTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        /** @var $elm AbstractElement */
        $elm = $this->getMockForAbstractClass(
            '\Bmaker\Element\AbstractElement',
            array(
                'id',
                1,
                2,
                3,
                4,
                'parent',
                'posref'
            )
        );
        $this->assertEquals('id', $elm->getId());
        $this->assertEquals(array(1, 2), $elm->getPos());
        $this->assertEquals(array(3, 4), $elm->getSize());
        $this->assertEquals('parent', $elm->getParent());
        $this->assertEquals('posref', $elm->getPosref());
    }

    public function testSetPos()
    {
        /** @var $elm AbstractElement */
        $elm = $this->getMockForAbstractClass(
            '\Bmaker\Element\AbstractElement',
            array(
                'id',
                1,
                2,
                3,
                4,
                'parent',
                'posref'
            )
        );
        $this->assertEquals(array(1, 2), $elm->getPos());

        $elm->setPos(3, 4);
        $this->assertEquals(array(3, 4), $elm->getPos());
    }

    public function testSetPosref()
    {
        /** @var $elm AbstractElement */
        $elm = $this->getMockForAbstractClass(
            '\Bmaker\Element\AbstractElement',
            array(
                'id',
                1,
                2,
                3,
                4,
                'parent',
                'posref'
            )
        );
        $this->assertEquals('posref', $elm->getPosref());

        $elm->setPosref('tl tl');
        $this->assertEquals('tl tl', $elm->getPosref());
    }
}
