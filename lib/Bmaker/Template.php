<?php
//
// RyzomBmaker - https://github.com/nimetu/ryzom_bmaker
// Copyright (c) 2013 Meelis Mägi <nimetu@gmail.com>
//
// This file is part of RyzomBmaker.
//
// RyzomBmaker is free software; you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// (at your option) any later version.
//
// RyzomBmaker is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program; if not, write to the Free Software Foundation,
// Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
//

namespace Bmaker;

use Bmaker\Element\ImageElement;
use Bmaker\Element\PolygonElement;
use Bmaker\Element\TextElement;
use Bmaker\Helper\FactionLogoHelper;
use Bmaker\Helper\GuildIconHelper;
use Bmaker\Helper\KeywordsCollection;
use Bmaker\Render\RenderInterface;
use Bmaker\Translator\Translator;
use Bmaker\Utils\FinderInterface;

/**
 * Bmaker xml template parser
 */
class Template implements FinderInterface
{
    /** Recursion depth in xml file */
    const MAX_DEPTH = 10;

    /** possible types for variable */
    const VAR_INT = 'int';
    const VAR_DOUBLE = 'double';
    const VAR_STRING = 'string';

    /** @var RenderInterface */
    protected $render;

    /** @var array */
    protected $variables;

    /** @var KeywordsCollection[] */
    protected $keywords;

    /** @var Translator */
    protected $trans;

    /** @var string */
    protected $lang;

    /** @var array */
    protected $uiStrings;

    /** @var bool */
    protected $allowOtherwiseCondition = false;

    /** @var string */
    protected $defaultFont;

    /** @var array */
    protected $images;

    /** @var array */
    protected $fonts;

    /** @var FactionLogoHelper */
    protected $factionLogoHelper;

    /** @var GuildIconHelper */
    protected $guildIconHelper;

    /**
     * @param Translator $trans
     */
    function __construct(Translator $trans)
    {
        $this->trans = $trans;

        $this->variables = array();
        $this->keywords = array();

        $this->images = array();
        $this->fonts = array();

        $this->uiStrings = array(
            'uiname',
            'uiguild',
            'uishard',
            'uistatus',
            'uionline',
            'uioffline',
            'uiplayed',
            'uilastseen'
        );

        $this->guildIconHelper = false;

        $this->defaultFont = 'ryzom.ttf';

        $this->registerFonts(
            array(
                'ryzom.ttf' => __DIR__.'/../../resources/fonts/ryzom.ttf',
                'basic.ttf' => __DIR__.'/../../resources/fonts/basic.ttf',
            ),
            true
        );

        $this->registerImages(
            array(
                'banner_bg.png' => __DIR__.'/../../resources/background/banner_bg.png',
                'banner_bg_fyros.png' => __DIR__.'/../../resources/background/banner_bg_fyros.png',
                'banner_bg_tryker.png' => __DIR__.'/../../resources/background/banner_bg_tryker.png',
                'banner_bg_matis.png' => __DIR__.'/../../resources/background/banner_bg_matis.png',
                'banner_bg_zorai.png' => __DIR__.'/../../resources/background/banner_bg_zorai.png',
            ),
            true
        );
    }

    /**
     * Register guild icon generator
     *
     * @param GuildIconHelper $helper
     */
    public function setGuildIconHelper(GuildIconHelper $helper)
    {
        $this->guildIconHelper = $helper;
    }

    /**
     * Register faction (cult/civ) logo generator
     *
     * @param FactionLogoHelper $helper
     */
    public function setFactionHelper(FactionLogoHelper $helper)
    {
        $this->factionLogoHelper = $helper;
    }

    /**
     * Return variable value or NULL if variable not found
     *
     * @param string $id
     * @param array $params
     *
     * @return mixed
     */
    function valueLookup($id, array $params = array())
    {
        $result = null;

        $id = strtolower($id);
        if (isset($this->variables[$id])) {
            // user defined variable
            $result = $this->variables[$id];
        } else {
            $result = $this->keywordLookup($id, $params);
        }
        return $result;
    }

    /**
     * Lookup reserved keyword value
     *
     * @param       $id
     * @param array $params
     *
     * @return string|null
     */
    function keywordLookup($id, array $params = array())
    {
        $result = null;
        foreach ($this->keywords as $kwManager) {
            $result = $kwManager->get($id, $params);
            if ($result !== null) {
                break;
            }
        }
        return $result;
    }

    /**
     * Register new (or overwrite previous) user defined variable
     *
     * @param string $id
     * @param string $value
     */
    function valueRegister($id, $value)
    {
        $id = strtolower($id);
        $this->variables[$id] = $value;
    }

    /**
     * Register external collection of keywords (id's and variables)
     *
     * @param KeywordsCollection $kw
     */
    function registerKeywords(KeywordsCollection $kw)
    {
        $this->keywords[] = $kw;
    }

    /**
     * Parse template and render it
     *
     * @param RenderInterface $render
     * @param \SimpleXMLElement $xml
     */
    function render(RenderInterface $render, \SimpleXMLElement $xml)
    {
        $this->render = $render;

        $type = (string)$xml['type'];
        $width = (int)$xml['w'];
        $height = (int)$xml['h'];
        $color = (string)$xml['color'];
        $image = (string)$xml['image'];
        $ryzom_link = isset($xml['ryzom_link']) ? (string)$xml['ryzom_link'] : 'tr';
        $font = strtolower((string)$xml['font']);
        if (isset($this->fonts[$font])) {
            $this->defaultFont = $this->fonts[$font];
        }

        $race = $this->valueLookup('race_logo');
        $cult = $this->valueLookup('cult_logo');

        $image = $this->getBackgroundImage($image, $race, $cult);

        $this->render->setType($type);
        $this->render->setSize($width, $height);
        $this->render->setBackground($color, $image);
        $this->render->setRyzomLink($ryzom_link);

        // add elements from template
        $this->parse($xml);

        if (!empty($ryzom_link)) {
            $this->addRyzomLink($ryzom_link, $width, $height);
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     *
     * @return mixed
     */
    function parse(\SimpleXMLElement $xml)
    {
        static $depth = 0;

        $depth++;
        if ($depth > self::MAX_DEPTH) {
            return;
        }

        foreach ($xml->children() as $node) {
            /** @var $node \SimpleXMLElement */
            $nodeName = (string)$node->getName();
            $nodeId = strtolower((string)$node['id']);
            // <var id="var0">.. content ..</var>
            switch ($nodeName) {
                case 'var':
                    $this->handleVar($nodeId, $node);
                    break;
                case 'when':
                case 'otherwise':
                    $this->handleWhenOtherwise($nodeId, $node);
                    break;
                case 'image':
                    $this->handleImage($nodeId, $node);
                    break;
                case 'poly':
                    $this->handlePoly($nodeId, $node);
                    break;
                case 'text':
                default:
                    $this->handleText($nodeId, $node);
            }
        }

        $depth--;
    }

    /**
     * @param string $nodeId
     * @param \SimpleXMLElement $node
     */
    function handleVar($nodeId, \SimpleXMLElement $node)
    {
        // register or set variable value
        $this->valueRegister($nodeId, (string)$node);
    }

    /**
     * Parse <when test="a = b"> and <otherwise> block
     *
     * @param string $nodeId
     * @param \SimpleXMLElement $node
     */
    function handleWhenOtherwise($nodeId, \SimpleXMLElement $node)
    {
        $nodeName = (string)$node->getName();
        if ($nodeName == 'when') {
            // <when> node, if there is no test expression, then ignore this block
            if (isset($node['test'])) {
                if ($this->validateExpression((string)$node['test'])) {
                    // expression returned true, so parse this block and ignore <otherwise> block
                    $this->parse($node);
                    $this->allowOtherwiseCondition = false;
                } else {
                    // ignore this block and use next <otherwise> block
                    $this->allowOtherwiseCondition = true;
                }
            }
        } else {
            if ($this->allowOtherwiseCondition) {
                // <otherwise> node
                $this->parse($node);
                $this->allowOtherwiseCondition = false;
            }
        }
    }

    /**
     * @param string $nodeId
     * @param \SimpleXMLElement $node
     */
    function handleImage($nodeId, \SimpleXMLElement $node)
    {
        /** @var $image ImageElement */
        $image = $this->createElement($nodeId, $node, 'Bmaker\Element\ImageElement');

        switch ($nodeId) {
            case 'guild_logo':
                $guildIcon = $this->valueLookup('guild_logo');
                if ($this->guildIconHelper) {
                    $im = $this->guildIconHelper->render($guildIcon);
                    $image->setImage($im);
                }
                break;
            case 'cult_logo':
            case 'civ_logo':
                $logo = $this->valueLookup($nodeId);
                if ($this->factionLogoHelper) {
                    $im = $this->factionLogoHelper->render($logo);
                    $image->setImage($im);
                }
                break;
            default:
                $imageFile = $this->varExpand((string)$node, 'string');
                $path = $this->find($imageFile);
                if ($path !== null) {
                    $image->setImage(imagecreatefrompng($path));
                }
        }
        $this->render->add($image);
    }

    /**
     * @param string $nodeId
     * @param \SimpleXMLElement $node
     */
    function handlePoly($nodeId, \SimpleXMLElement $node)
    {
        $points = array();
        foreach ($node->children() as $child) {
            $x = (int)$child['x'];
            $y = (int)$child['y'];
            $rel = isset($child['pos']) && $child['pos'] == 'rel';
            $points[] = array($x, $y, $rel);
        }

        // we need at least 3 points
        if (count($points) < 3) {
            return;
        }

        /** @var $poly PolygonElement */
        $poly = $this->createElement($nodeId, $node, 'Bmaker\Element\PolygonElement');

        $thickness = $this->getAttribute($poly, 'thickness', 1, self::VAR_INT);
        $poly->setThickness($thickness);

        $color = $this->getAttribute($poly, 'color', 'rgb(255,255,255)', self::VAR_STRING);
        $poly->setColor($color);

        $fill = $this->getAttribute($poly, 'shadow', false, self::VAR_STRING);
        if ($fill !== false) {
            $poly->setFillColor($fill);
        }

        foreach ($points as $point) {
            $poly->addPoint($point[0], $point[1], $point[2]);
        }

        $this->render->add($poly);
    }

    /**
     * @param string $nodeId
     * @param \SimpleXMLElement $node
     */
    function handleText($nodeId, \SimpleXMLElement $node)
    {
        if (in_array($nodeId, $this->uiStrings)) {
            // if node id is one of known ui string, then translate it
            $value = $this->trans->trans($nodeId);
        } else {
            // else expand node value for user variables and use that
            $nodeValue = (string)$node;
            if (!empty($nodeValue)) {
                $value = $this->varExpand($nodeValue, 'string');
            } else {
                // node does not have a value, known keyword?
                $value = $this->valueLookup($nodeId);
            }
        }

        /** @var $text TextElement */
        $text = $this->createElement($nodeId, $node, 'Bmaker\Element\TextElement');

        $size = $this->getAttribute($node, 'size', 10, self::VAR_INT);
        if ($size == 0) {
            $size = 10;
        }
        $angle = $this->getAttribute($node, 'angle', 0, self::VAR_DOUBLE);
        $font = $this->getAttribute($node, 'font', $this->defaultFont, self::VAR_STRING);
        $path = $this->getFontPath($font);
        if ($path === null) {
            $path = $this->getFontPath($this->defaultFont);
        }
        $color = $this->getAttribute($node, 'color', 'rgb(255,255,255)', self::VAR_STRING);

        $text->setText($value, $size, $angle, $path, $color);

        $color = $this->getAttribute($node, 'shadow', false, self::VAR_STRING);
        if ($color !== false) {
            $sx = $this->getAttribute($node, 'shadow_x', 1, self::VAR_INT);
            $sy = $this->getAttribute($node, 'shadow_y', 1, self::VAR_INT);
        } else {
            $color = 'rgb(0,0,0)';
            $sx = 1;
            $sy = 1;
        }
        $text->setDropShadow($sx, $sy, $color);

        $this->render->add($text);
    }

    /**
     * Get node attribute, call varExpand on it
     * If attribute is not set, return $default
     *
     * @param \SimpleXMLElement $node
     * @param string $attr
     * @param mixed $default
     * @param int $type
     *
     * @return float|int|mixed|string
     */
    function getAttribute(\SimpleXMLElement $node, $attr, $default, $type)
    {
        if (!isset($node[$attr])) {
            return $default;
        }
        return $this->varExpand($node[$attr], $type);
    }

    /**
     * $var or {$var} is expanded inside $value
     *
     * @param string $value
     * @param string $type self::VAR_* constant
     * @param array $params
     *
     * @return float|int|string
     */
    function varExpand($value, $type, array $params = array())
    {
        // match $foo or $foo:bar, optionally enclosed in {}
        if (preg_match_all('/({)?\$([a-zA-Z0-9_]+(?::[a-zA-Z0-9_]+)?)(?(1)})/', $value, $match)) {
            $replace = array();
            foreach ($match[0] as $k => $pattern) {
                $varId = strtolower($match[2][$k]);
                // lookup value (reserved or user defined)
                $varValue = $this->valueLookup($varId, $params);
                if ($varValue !== null) {
                    $replace[$pattern] = $varValue;
                }
            }
            $result = strtr($value, $replace);
        } else {
            // no variables, use string as it is
            $result = $value;
        }

        // convert to requested type
        switch ($type) {
            case self::VAR_INT:
                return (int)$result;
            case self::VAR_DOUBLE:
                return (double)$result;
            case self::VAR_STRING:
                // fall thru
            default:
                return (string)$result;
        }
    }

    /**
     * Validate string expression
     *
     * @param string $expression
     *
     * @return bool
     */
    function validateExpression($expression)
    {
        $leftRegex = '(\$[a-zA-Z0-9:_]+)';
        $rightRegex = '([^\'\\\]*(.[^\'\\\]*)*)';
        if (preg_match('/'.$leftRegex.'\s*(=|!=|<|<=|>=|>)\s*\''.$rightRegex.'\'/', $expression, $match)) {
            // expand both sides to string values
            $leftValue = strtolower($this->varExpand($match[1], self::VAR_STRING, array('translate' => false)));
            $rightValue = strtolower($this->varExpand($match[3], self::VAR_STRING, array('translate' => false)));
            switch ($match[2]) {
                case '=':
                    $result = ($leftValue == $rightValue);
                    break;
                case '!=':
                    $result = ($leftValue != $rightValue);
                    break;
                case '<':
                    $result = ($leftValue < $rightValue);
                    break;
                case '<=':
                    $result = ($leftValue <= $rightValue);
                    break;
                case '>=':
                    $result = ($leftValue >= $rightValue);
                    break;
                case '>':
                    $result = ($leftValue > $rightValue);
                    break;
                default:
                    $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Create ElementInterface element from xml node
     *
     * @param string $nodeId
     * @param \SimpleXMLElement $node
     * @param string $class
     *
     * @return mixed
     */
    function createElement($nodeId, \SimpleXMLElement $node, $class)
    {
        $x = (int)$node['x'];
        $y = (int)$node['y'];
        $w = isset($node['w']) ? (int)$node['w'] : null;
        $h = isset($node['h']) ? (int)$node['h'] : null;
        $parent = (string)$node['parent'];
        $posref = (string)$node['posref'];

        $result = new $class($nodeId, $x, $y, $w, $h, $parent, $posref);
        return $result;
    }

    /**
     * Register font paths
     *
     * @param array $fonts
     * @param bool $replace default false
     */
    function registerFonts(array $fonts, $replace = false)
    {
        if ($replace) {
            $this->fonts = $fonts;
        } else {
            $this->fonts = array_merge($this->fonts, $fonts);
        }
    }

    /**
     * Get full path for registered font
     *
     * @param string $font
     *
     * @return string|null
     */
    function getFontPath($font)
    {
        $font = strtolower($font);
        if (isset($this->fonts[$font])) {
            return $this->fonts[$font];
        }
        return null;
    }

    /**
     * Register image paths
     *
     * @param array $images
     * @param bool $clean default false
     */
    function registerImages(array $images, $replace = false)
    {
        if ($replace) {
            $this->images = $images;
        } else {
            $this->images = array_merge($this->images, $images);
        }
    }

    /**
     * Return full path for registered image
     *
     * @param string $image
     *
     * @return string|null
     */
    function find($image)
    {
        $image = strtolower($image);
        if (isset($this->images[$image])) {
            return $this->images[$image];
        }
        return null;
    }


    /**
     * Figure out which background image if available.
     *
     * Image names tried are (in order, last one matches):
     * 'image.png', 'image_race.png', 'image_cult.png' or 'image_race_cult.png'
     *
     * @param string $bgImage
     * @param string $race
     * @param string $cult
     *
     * @return string background image full path + filename
     */
    function getBackgroundImage($bgImage, $race, $cult)
    {
        // simple background image
        $filename = $bgImage;
        $pos = strrpos($filename, '.');
        if ($pos !== false) {
            $ext = substr($filename, $pos);
            $filename = substr($filename, 0, $pos);
        } else {
            $ext = '';
        }

        if (!empty($race)) {
            $path = $this->find($filename.'_'.$race.$ext);
            if ($path !== null) {
                $filename = $filename.'_'.$race;
            }
        }

        if (!empty($cult)) {
            $path = $this->find($filename.'_'.$cult.$ext);
            if ($path !== null) {
                $filename = $filename.'_'.$cult;
            }
        }

        return $this->find($filename.$ext);
    }

    /**
     * Draw 'www.ryzom.com' signature
     *
     * @param string $posref one of tl,bl,br,tr
     * @param int $width output image width
     * @param int $height output image height
     */
    function addRyzomLink($posref, $width, $height)
    {
        $font = $this->getFontPath('basic.ttf');
        $text = new TextElement('ryzom_link', 0, 0, null, null, 'root', '');
        $text->setText('www.ryzom.com', 7, 0, $font, 'rgb(255,255,255)');
        $text->setDropShadow(1, 1, 'rgb(50,50,50)');

        $bbox = $text->getBbox();
        $_w = $bbox[2];
        $_h = $bbox[3];

        switch ($posref) {
            case 'tl':
                $text->setPos(1, -2);
                $text->setPosref('TL TL');
                $points = array(
                    array(0, 0, false),
                    array(0, $_h, false),
                    array($_w, $_h, false),
                    array($_w + $_h * 2, 0, false),
                    array($width, 0, false),
                );
                break;
            case 'bl':
                $text->setPos(1, -2);
                $text->setPosref('BL BL');
                $points = array(
                    array(0, $height, false),
                    array(0, $height - $_h, false),
                    array($_w, $height - $_h, false),
                    array($_w + $_h * 2, $height - 1, false),
                    array($width, $height - 1, false),
                );
                break;
            case 'br':
                $text->setPos(-1, -2);
                $text->setPosref('BR BR');
                $points = array(
                    array(0, $height - 1, false),
                    array($width, $height, false),
                    array($width, $height - $_h, false),
                    array($width - $_w, $height - $_h, false),
                    array($width - $_w - $_h * 2, $height - 1, false),
                );
                break;
            case 'tr':
            default:
                $text->setPos(-1, -2);
                $text->setPosref('TR TR');
                $points = array(
                    array(0, 0, false),
                    array($width, 0, false),
                    array($width, $_h, false),
                    array($width - $_w, $_h, false),
                    array($width - $_w - $_h * 2, 0, false),
                );
        }
        $poly = new PolygonElement('ryzom_link_poly', 0, 0, 0, 0, 'root', 'TR TR');
        $poly->setThickness(1);
        $poly->setColor('rgb(0,0,0)');
        $poly->setFillColor('rgb(0,0,0)');
        $poly->setPolygon($points);

        $this->render->add($poly);
        $this->render->add($text);
    }
}
