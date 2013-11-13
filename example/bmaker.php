<?php

require_once __DIR__.'/../vendor/autoload.php';

// guild emblems
$guildDataPath = __DIR__.'/../vendor/nimetu/ryzom_extra/resources/guild-icon';

// atys date
$atysDate = new \RyzomExtra\AtysDateTime('atys');
$atysDate->setGameCycle(0);

// character xml
$xml = simplexml_load_file(__DIR__.'/api-xml/apihomin-public.xml');

// trnslation, faction icons, backgrounds, fonts etc
$dataPath = __DIR__.'/../resources';
$lang = array();
$lang['en'] = include $dataPath.'/lang_en.php';
$lang['fr'] = include $dataPath.'/lang_fr.php';
$lang['de'] = include $dataPath.'/lang_de.php';

$trans = new \Bmaker\Translator\Translator('en');
$trans->register($lang['en']);
$trans->register($lang['fr'], 'fr');
$trans->register($lang['de'], 'de');

// setup template engline
$fonts = array(
    'ryzom.ttf' => __DIR__.'/../resources/fonts/ryzom.ttf',
    'basic.ttf' => __DIR__.'/../resources/fonts/basic.ttf',
);
$images = array(
    'banner_bg.png' => __DIR__.'/../resources/background/banner_bg.png',
    'banner_bg_fyros.png' => __DIR__.'/../resources/background/banner_bg_fyros.png',
    'banner_bg_tryker.png' => __DIR__.'/../resources/background/banner_bg_tryker.png',
    'banner_bg_matis.png' => __DIR__.'/../resources/background/banner_bg_matis.png',
    'banner_bg_zorai.png' => __DIR__.'/../resources/background/banner_bg_zorai.png',
);

$tpl = new Bmaker\Template($trans);
$tpl->setGuildIconHelper(new Bmaker\Helper\GuildIconHelper($guildDataPath));
$tpl->setFactionHelper(new Bmaker\Helper\FactionLogoHelper($dataPath));

$tpl->registerKeywords(new Bmaker\Helper\AtysDateHelper($atysDate));
$tpl->registerKeywords(new Bmaker\Helper\CharacterApiHelper($xml->character, $trans));

$tpl->registerFonts($fonts);
$tpl->registerImages($images);

// load template
$template = simplexml_load_file($dataPath.'/default.xml');

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

