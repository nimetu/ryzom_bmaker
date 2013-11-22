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

namespace Bmaker\Helper;

use RyzomExtra\GuildIconBuilder;
use RyzomExtra\GuildIconRenderer;

/**
 * Factory class for GuildIconRenderer
 */
class GuildIconHelper
{
    /** @var string */
    protected $dataPath;

    /**
     * @param string $dataPath
     */
    public function __construct($dataPath = false)
    {
        $this->dataPath = $dataPath;
    }

    /**
     * @param string $icon
     * @param string $size
     *
     * @return resource
     */
    public function render($icon, $size = 'b')
    {
        $iconBuilder = new GuildIconBuilder($icon);
        $icon = new GuildIconRenderer($iconBuilder, $this->dataPath);
        $icon->setSize($size);

        return $icon->output();
    }

}
