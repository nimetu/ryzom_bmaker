<?php

namespace Bmaker\Translator;

/**
 * Translator
 */
class TranslatorTest extends \PHPUnit_Framework_TestCase
{

    public function testTrans()
    {
        $trans = new Translator('en');

        $trans->register(array('test' => 'test-en'));
        $trans->register(array('test2' => 'test2-en'), 'en');
        $trans->register(array('test' => 'test-fr'), 'fr');

        $this->assertEquals('en', $trans->getLang());
        $this->assertEquals('test-en', $trans->trans('test'));
        $this->assertEquals('test2-en', $trans->trans('test2'));
        $this->assertEquals('test-fr', $trans->trans('test', array(), 'fr'));
    }
}
