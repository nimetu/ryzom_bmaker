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

namespace Bmaker\Element;

/**
 * ImageElement
 */
class ImageElement extends AbstractElement
{

    /** @var string */
    protected $filename;

    /** @var resource */
    protected $image;

    /** @var int */
    protected $imWidth;

    /** @var int */
    protected $imHeight;

    /**
     * @param resource $image
     */
    function setImage($image)
    {
        if ($image) {
            $this->image = $image;
        } else {
            $this->image = imagecreatetruecolor(1, 1);
            $t = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
            imagefill($this->image, 0, 0, $t);
        }

        $this->imWidth = imagesx($this->image);
        $this->imHeight = imagesy($this->image);
    }

    /**
     * {@inheritdoc}
     */
    function getBbox()
    {
        return array(0, 0, $this->imWidth, $this->imHeight);
    }

    /**
     * {@inheritdoc}
     */
    function drawAt($canvas)
    {
        // if needed, then resize, else copy
        if ($this->width != $this->imWidth || $this->height != $this->imHeight) {
            imagecopyresampled(
                $canvas,
                $this->image,
                $this->x,
                $this->y,
                0,
                0,
                $this->width,
                $this->height,
                $this->imWidth,
                $this->imHeight
            );
        } else {
            imagecopy($canvas, $this->image, $this->x, $this->y, 0, 0, $this->width, $this->height);
        }
    }
}
