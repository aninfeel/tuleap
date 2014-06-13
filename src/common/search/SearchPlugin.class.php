<?php
/**
 * Copyright (c) Enalean, 2014. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

class Search_SearchPlugin {

    const RESULTS_PER_QUERY = 25;

    /**
     * @var EventManager
     */
    private $event_manager;


    public function __construct(EventManager $event_manager) {
        $this->event_manager = $event_manager;
    }

    public function search(Search_SearchQuery $query) {
        $results = null;
        $params = array(
            'query'              => $query,
            'words'              => $query->getWords(),
            'offset'             => $query->getOffset(),
            'nbRows'             => self::RESULTS_PER_QUERY,
            'type_of_search'     => $query->getTypeOfSearch(),
            'project'            => $query->getProject(),
            'group_id'           => $query->getProject()->getId(),
            'results'            => &$results,
        );

        $this->event_manager->processEvent(Event::SEARCH_TYPE, $params);

        return $results;
    }
}
