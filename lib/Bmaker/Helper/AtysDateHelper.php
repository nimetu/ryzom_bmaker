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

use RyzomExtra\AtysDateTime;

/**
 * Registers atys date related variables
 */
class AtysDateHelper implements KeywordsCollection
{

    /** @var AtysDateTime */
    protected $atysDate;

    /**
     * @param AtysDateTime $atysDate
     */
    public function __construct(AtysDateTime $atysDate)
    {
        $this->atysDate = $atysDate;
    }

    /**
     * @param string $id
     * @param array $params
     *
     * @return mixed
     */
    public function get($id, array $params = array())
    {
        // translate by default
        $translate = !isset($params['translate']) || $params['translate'] === true;

        switch ($id) {
            case 'atys:date':
                $result = $this->atysDate->toDateString();
                break;
            case 'atys:time':
                if ($translate) {
                    // return string '00h' instead int 0
                    $result = $this->atysDate->toTimeString();
                } else {
                    // return int
                    $result = $this->atysDate->getHours();
                }
                break;
            case 'atys:datetime':
                $result = $this->atysDate->formatDate(true);
                break;
            case 'atys:year':
                $result = $this->atysDate->getYear();
                break;
            case 'atys:season':
                if ($translate) {
                    // return season name
                    $result = $this->atysDate->getSeasonName();
                } else {
                    // return season index
                    $result = $this->atysDate->getSeason();
                }
                break;
            default:
                $result = null;
        }

        return $result;
    }
}
