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
 * RenderInterface
 */
interface RenderInterface
{

    /**
     * @abstract
     *
     * @param ElementInterface $element
     */
    public function add(ElementInterface $element);

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * Set output size
     * Clamped to min/max size
     *
     * @param int $width
     * @param int $height
     */
    public function setSize($width, $height);

    /**
     * @return array [width, height]
     */
    public function getSize();

    /**
     * Set background color and image
     *
     * @param string $color hex string as #RRGGBB
     * @param string $image image file name
     */
    public function setBackground($color, $image);

    /**
     * @return array [color, filename]
     */
    public function getBackground();

    /**
     * Show or hide ryzom.com 'link' on output
     *
     * @param boolean $show
     */
    public function setRyzomLink($show);

    /**
     * @return string posref
     */
    public function getRyzomLink();
}
