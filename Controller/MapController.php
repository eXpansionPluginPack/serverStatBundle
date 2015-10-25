<?php
/**
 * @author      Oliver de Cramer (oliverde8 at gmail.com)
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007
 *
 * PHP version 5.3 and above
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

namespace MlExpansion\ServerStatsBundle\Controller;


use Manialib\Formatting\String as MlString;
use oliverde8\MPDedicatedServerBundle\Service\DedicatedServer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MapController extends Controller {

    public function indexAction($page, $nPerPage)
    {
        $limit = ($page - 1) * $nPerPage . ', ' . $nPerPage;

        $query = "SELECT m.*, AVG(r.record_avgScore) as avg_time, COUNT(record_id) as nb_player " .
            "FROM exp_maps m " .
            "LEFT JOIN exp_records r ON r.record_challengeuid = m.challenge_uid " .
            "GROUP BY m.challenge_id " .
            "ORDER BY challenge_addtime DESC LIMIT $limit";

        $connection = $this->getDoctrine()->getManager()->getConnection();
        $statement = $connection->prepare($query);
        $statement->execute();
        $maps = $statement->fetchAll();


        foreach ($maps as &$map) {
            $string = new MlString($map["challenge_name"]);
            $map["challenge_name_formatted"] = $string->toHtml();
            $map["avg_time_formatted"] = DedicatedServer::formatPastTime(((int) ($map['avg_time']/1000)), 5, 0);
        }

        return $this->render(
            'MlExpansionServerStatsBundle:Map:index.html.twig',
            array('maps' => $maps));
    }
}