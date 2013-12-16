<?php

namespace Bmaker\Element;

/**
 * Class ImageElementTest
 */
class ImageElementTest extends \PHPUnit_Framework_TestCase
{
    public function testSetImage()
    {
        $element = new ImageElement('test', 0, 0, 0, 0, 'root', 'tl tl');
        $this->assertEquals(array(0, 0, 0, 0), $element->getBbox());

        $imgFile = dirname(dirname(__DIR__)).'/_files/fyros_logo.png';
        $img = imagecreatefrompng($imgFile);
        $this->assertInternalType('resource', $img);

        $element->setImage($img);
        $this->assertEquals(array(0, 0, 50, 50), $element->getBbox());
    }

    public function testSetInvalidImage()
    {
        $element = new ImageElement('test', 0, 0, 0, 0, 'root', 'tl tl');
        $this->assertEquals(array(0, 0, 0, 0), $element->getBbox());

        $element->setImage(false);
        $this->assertEquals(array(0, 0, 1, 1), $element->getBbox());
    }
}
