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
 * Interface for render element types
 */
interface ElementInterface
{

    /**
     * @return string
     */
    function getId();

    /**
     * @return array [x, y]
     */
    function getPos();

    /**
     * @return array [width, height]
     */
    function getSize();

    /**
     * @return string
     */
    function getParent();

    /**
     * @return string
     */
    function getPosref();

    /**
     * @param int $x
     * @param int $y
     */
    function setPos($x, $y);

    /**
     * @param int $width
     * @param int $height
     */
    function setSize($width, $height);

    /**
     * @param string $posref
     */
    function setPosref($posref);

    /**
     * @return array [x, y, w, h]
     */
    function getBbox();

    /**
     * Draw element
     *
     * @param resource $canvas
     */
    function drawAt($canvas);
}
