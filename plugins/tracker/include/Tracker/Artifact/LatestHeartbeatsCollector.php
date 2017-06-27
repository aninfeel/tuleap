<?php
/**
 * Copyright (c) Enalean, 2017. All Rights Reserved.
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

namespace Tuleap\Tracker\Artifact;

use PFUser;
use Tracker_ArtifactDao;
use Tracker_ArtifactFactory;
use Tuleap\Glyph\GlyphFinder;
use Tuleap\Project\HeartbeatsEntry;
use Tuleap\Project\HeartbeatsEntryCollection;

class LatestHeartbeatsCollector
{
    /**
     * @var Tracker_ArtifactDao
     */
    private $dao;
    /**
     * @var Tracker_ArtifactFactory
     */
    private $factory;
    /**
     * @var GlyphFinder
     */
    private $glyph_finder;

    public function __construct(Tracker_ArtifactDao $dao, Tracker_ArtifactFactory $factory, GlyphFinder $glyph_finder)
    {
        $this->dao          = $dao;
        $this->factory      = $factory;
        $this->glyph_finder = $glyph_finder;
    }

    public function collect(HeartbeatsEntryCollection $collection)
    {
        $artifacts = $this->dao->searchLatestUpdatedArtifactsInProject(
            $collection->getProject()->getID(),
            $collection::NB_MAX_ENTRIES
        );
        foreach ($artifacts as $row) {
            $artifact = $this->factory->getInstanceFromRow($row);
            if (! $artifact->userCanView($collection->getUser())) {
                $collection->thereAreActivitiesUserCannotSee();
                continue;
            }

            $collection->add(
                new HeartbeatsEntry(
                    $artifact->getLastUpdateDate(),
                    $artifact->getXRef(),
                    $artifact->getUri(),
                    $artifact->getTitle(),
                    $artifact->getTracker()->getColor(),
                    $this->glyph_finder->get('tuleap-tracker')->getInlineString()
                )
            );
        }
    }
}
