<?php

namespace Bmaker\Helper;

/**
 * CharacterApi keywords
 */
class CharacterApiHelperTest extends \PHPUnit_Framework_TestCase
{

    public function testGet()
    {
        $xml = simplexml_load_string(
            '
	    <character>
			<name>CaseSensitive</name>
			<shard>shard</shard>
			<race>tryker</race>
			<cult>karavan</cult>
			<civ>zorai</civ>
			<titleid>title0001</titleid>
			<guild>
			    <name>Guild Name</name>
			    <icon>1234567890</icon>
			</guild>
		</character>
		'
        );

        $trans = $this->getMock('Bmaker\Translator\TranslatorInterface');
        $trans->expects($this->any())
            ->method('trans')
            ->with($this->equalTo('title0001.title'))
            ->will($this->returnValue('Title #1'));

        $ca = new CharacterApiHelper($xml, $trans);
        $this->assertEquals('CaseSensitive', $ca->get('name'));
        $this->assertEquals('Shard', $ca->get('shard'));
        $this->assertEquals('Guild Name', $ca->get('guild'));
        $this->assertEquals('Tryker', $ca->get('race'));
        $this->assertEquals('1234567890', $ca->get('guild_logo'));
        $this->assertEquals('tryker', $ca->get('race_logo'));
        $this->assertEquals('karavan', $ca->get('cult_logo'));
        $this->assertEquals('zorai', $ca->get('civ_logo'));
        $this->assertEquals('Title #1', $ca->get('title'));
        $this->assertEquals('Title #1', $ca->get('titleid'));
        $this->assertEquals('title0001', $ca->get('titleid', array('translate' => false)));
        $this->assertNull($ca->get('non-exist'));
    }


    public function testGetGuild()
    {
        $xml = simplexml_load_string(
            '
		<character>
			<name>Name</name>
		</character>'
        );

        $trans = $this->getMock('Bmaker\Translator\TranslatorInterface');

        $ca = new CharacterApiHelper($xml, $trans);
        $this->assertEquals('Name', $ca->get('name'));
        $this->assertEquals('', $ca->get('guild'));
        $this->assertEquals('', $ca->get('guild_logo'));
        $this->assertNull($ca->get('non-exist'));
    }


    public function testStatusOnline()
    {
        $xml = simplexml_load_string(
            '
		<character>
			<played lastlogin="11" lastlogout="10">1</played>
		</character>
		'
        );

        $trans = $this->getMock('Bmaker\Translator\TranslatorInterface');
        $trans->expects($this->any())
            ->method('trans')
            ->with($this->equalTo('uiOnline'))
            ->will($this->returnValue('Online'));

        $ca = new CharacterApiHelper($xml, $trans);
        $this->assertEquals('Online', $ca->get('status'));
        $this->assertEquals('Online', $ca->get('last_seen'));
        $this->assertEquals('Thu, 01 Jan 1970 00:00:11', $ca->get('last_seen_status'));
    }


    public function testStatusOffline()
    {
        $xml = simplexml_load_string(
            '
		<character>
			<played lastlogin="10" lastlogout="11">1</played>
		</character>'
        );

        $trans = $this->getMock('Bmaker\Translator\TranslatorInterface');
        $trans->expects($this->any())
            ->method('trans')
            ->with($this->equalTo('uiOffline'))
            ->will($this->returnValue('Offline'));

        $ca = new CharacterApiHelper($xml, $trans);
        $this->assertEquals('Offline', $ca->get('status'));
        $this->assertEquals('Thu, 01 Jan 1970 00:00:11', $ca->get('last_seen'));
    }


    public function testGetDaysPlayedSingle()
    {
        $xml = simplexml_load_string(
            '
		<character>
            <played>172799</played>
		</character>'
        );

        $trans = $this->getMock('Bmaker\Translator\TranslatorInterface');
        $trans->expects($this->any())
            ->method('trans')
            ->with($this->equalTo('uiDayPlayed'))
            ->will($this->returnValue('1 day'));

        $ca = new CharacterApiHelper($xml, $trans);
        $this->assertEquals('1 day 23:59:59', $ca->get('played_time'));
    }


    public function testGetDaysPlayedMulti()
    {
        $xml = simplexml_load_string(
            '
		<root>
            <played>259199</played>
		</root>'
        );

        $trans = $this->getMock('Bmaker\Translator\TranslatorInterface');
        $trans->expects($this->any())
            ->method('trans')
            ->with($this->equalTo('uiDaysPlayed'))
            ->will($this->returnValue('2 days'));

        $ca = new CharacterApiHelper($xml, $trans);
        $this->assertEquals('2 days 23:59:59', $ca->get('played_time'));
    }
}
