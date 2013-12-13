<?php

require_once __DIR__.'/../vendor/autoload.php';

// guild emblems
$guildDataPath = __DIR__.'/../vendor/nimetu/ryzom_extra/resources/guild-icon';

// atys date
$atysDate = new \RyzomExtra\AtysDateTime('atys');
$atysDate->setGameCycle(0);

// character xml
$xml = simplexml_load_file(__DIR__.'/api-xml/apihomin-faction-test.xml');

$trans = new \Bmaker\Translator\Translator('en');

$tpl = new Bmaker\Template($trans);
$tpl->setGuildIconHelper(new Bmaker\Helper\GuildIconHelper());
$tpl->setFactionHelper(new Bmaker\Helper\FactionLogoHelper());

$charApiHelper = new Bmaker\Helper\CharacterApiHelper($trans);
$charApiHelper->setCharacter($xml->character);

$tpl->registerKeywords(new Bmaker\Helper\AtysDateHelper($atysDate));
$tpl->registerKeywords($charApiHelper);

// load template
$template = simplexml_load_file(__DIR__.'/../resources/default.xml');

// final image
$image = new Bmaker\Render\ImageRenderer();
$image->setType('jpg');

// render
$tpl->render($image, $template);
file_put_contents('output_en.jpg', $image->output());

// set language for all external components ...
$trans->setLang('fr');
$atysDate->setLanguage('fr');
// ... and render again
$tpl->render($image, $template);
file_put_contents('output_fr.jpg', $image->output());

