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

namespace Bmaker\Render;

use Bmaker\Element\ElementInterface;

/**
 * Writer for PNG, JPG, GIF image
 */
class ImageRenderer extends AbstractRenderer
{
    const IMAGE_PNG = 'png';
    const IMAGE_JPEG = 'jpg';
    const IMAGE_GIF = 'gif';

    /** @var string */
    protected $type;

    /**
     * @param string $type
     */
    public function __construct($type = self::IMAGE_PNG)
    {
        parent::__construct();

        $this->type = $type;

        $this->images = array();
        $this->fonts = array();
    }

    /**
     * Find elements parent
     *
     * @param ElementInterface $elm
     *
     * @return ElementInterface|null
     */
    public function getParent(ElementInterface $elm)
    {
        $id = strtolower($elm->getParent());
        if (isset($this->elements[$id])) {
            return $this->elements[$id];
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        switch ($type) {
            case 'png':
                $this->type = self::IMAGE_PNG;
                break;
            case 'gif':
                $this->type = self::IMAGE_GIF;
                break;
            case 'jpg':
            case 'jpeg':
            default:
                $this->type = self::IMAGE_JPEG;
                break;
        }
    }

    /**
     * Prepare output image with background color and image
     *
     * @return resource
     */
    public function createBackground()
    {
        // output canvas
        $canvas = imagecreatetruecolor($this->width, $this->height);

        if ($this->bgColor !== false) {
            $filler = imagecolorallocatealpha(
                $canvas,
                $this->bgColor->getRed(),
                $this->bgColor->getGreen(),
                $this->bgColor->getBlue(),
                $this->bgColor->getAlpha()
            );
        } else {
            // transparent background when color not set
            $filler = imagecolorallocatealpha($canvas, 125, 0, 0, 127);
        }
        imagefill($canvas, 0, 0, $filler);

        $bgFile = $this->bgImage;
        if ($bgFile !== false && file_exists($bgFile)) {
            $data = file_get_contents($bgFile);
            $bg = imagecreatefromstring($data);
            if ($bg !== false) {
                $bgWidth = imagesx($bg);
                $bgHeight = imagesy($bg);
                imagecopy($canvas, $bg, 0, 0, 0, 0, $bgWidth, $bgHeight);
                imagedestroy($bg);
            }
        }

        return $canvas;
    }

    /**
     * Depending on type, return image content
     *
     * @see ImageRenderer::setType()
     *
     * @return string
     */
    public function output()
    {
        $canvas = $this->createBackground();

        // draw all elements
        foreach ($this->elements as $elm) {
            $this->placeElement($elm);
            $elm->drawAt($canvas);
        }

        ob_start();
        if ($this->type == self::IMAGE_PNG) {
            imagesavealpha($canvas, true);
            imagepng($canvas, null, 9);
        } elseif ($this->type == self::IMAGE_JPEG) {
            imagejpeg($canvas, null, 85);
        } else {
            imagesavealpha($canvas, true);
            imagegif($canvas);
        }
        return ob_get_clean();
    }

    /**
     * Calculate elements position using parent and posref
     *
     * @param ElementInterface $elm
     */
    public function placeElement(ElementInterface $elm)
    {
        // element real bbox
        $bbox = $elm->getBbox();

        // element requested pos and size
        list($nodeX, $nodeY) = $elm->getPos();
        list($nodeW, $nodeH) = $elm->getSize();

        if ($nodeW === null) {
            $nodeW = $bbox[2];
        }
        if ($nodeH === null) {
            $nodeH = $bbox[3];
        }
        // final size (only image is resized,
        // text/polygon will not be clipped or resized)
        $elm->setSize($nodeW, $nodeH);

        $parent = $this->getParent($elm);
        if (!empty($parent)) {
            list($parentX, $parentY) = $parent->getPos();
            list($parentW, $parentH) = $parent->getSize();
        } else {
            // root element
            $parentX = 0;
            $parentY = 0;
            $parentW = $this->width;
            $parentH = $this->height;
        }

        // first is parent, second is node
        $posref = explode(' ', $elm->getPosref());
        if (empty($posref)) {
            $posref = array('TL', 'TL');
        } elseif (empty($posref[1])) {
            $posref[1] = 'TL';
        }

        // modify parent x/y so that x/y points to anchor
        list($offsetX, $offsetY) = $this->evalPosref($posref[0], $parentW, $parentH);
        $parentX += $offsetX;
        $parentY += $offsetY;

        // modify element x/y so that x/y points to anchor
        list($offsetX, $offsetY) = $this->evalPosref($posref[1], $nodeW, $nodeH);
        $x = ($nodeX - $offsetX) + $parentX;
        $y = ($nodeY - $offsetY) + $parentY;

        // final position
        $elm->setPos($x, $y);
    }

    /**
     * Evaluate posref against $pw and $ph such that they
     * will point to $posref position in 0x0+PWxPH rectangle
     *
     * @param string $posref TL, TR, BL, BR, ML, MR, MM, TM, BM
     * @param int $pw
     * @param int $ph
     *
     * @return array [$pw, $ph]
     */
    public function evalPosref($posref, $pw, $ph)
    {
        switch ($posref) {
            case 'TL':
                $pw = 0;
                $ph = 0;
                break;
            case 'TR':
                // pw is same
                $ph = 0;
                break;
            case 'BL':
                $pw = 0;
                // ph is same
                break;
            case 'BR':
                // nothing
                break;
            case 'ML':
                $pw = 0;
                $ph = floor($ph / 2);
                break;
            case 'MR':
                // pw is same
                $ph = floor($ph / 2);
                break;
            case 'MM':
                $pw = floor($pw / 2);
                $ph = floor($ph / 2);
                break;
            case 'TM':
                $pw = 0;
                $ph = floor($ph / 2);
                break;
            case 'BM':
                $pw = floor($pw / 2);
                // ph is same
                break;
        }
        return array($pw, $ph);
    }

}
