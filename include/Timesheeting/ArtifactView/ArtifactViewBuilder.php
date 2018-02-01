<?php
/**
 * Copyright (c) Enalean, 2018. All Rights Reserved.
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

namespace Tuleap\Timesheeting\ArtifactView;

use Codendi_Request;
use CSRFSynchronizerToken;
use PFUser;
use timesheetingPlugin;
use Tracker_Artifact;
use Tuleap\Timesheeting\Admin\TimesheetingEnabler;
use Tuleap\Timesheeting\Permissions\PermissionsRetriever;
use Tuleap\Timesheeting\Time\TimeRetriever;

class ArtifactViewBuilder
{
    /**
     * @var timesheetingPlugin
     */
    private $plugin;

    /**
     * @var TimesheetingEnabler
     */
    private $timesheeting_enabler;

    /**
     * @var PermissionsRetriever
     */
    private $permissions_retriever;

    /**
     * @var TimeRetriever
     */
    private $time_retriever;

    public function __construct(
        TimesheetingPlugin $plugin,
        TimesheetingEnabler $timesheeting_enabler,
        PermissionsRetriever $permissions_retriever,
        TimeRetriever $time_retriever
    ) {
        $this->plugin                = $plugin;
        $this->timesheeting_enabler  = $timesheeting_enabler;
        $this->permissions_retriever = $permissions_retriever;
        $this->time_retriever        = $time_retriever;
    }

    /**
     * @return ArtifactView | null
     */
    public function build(PFUser $user, Codendi_Request $request, Tracker_Artifact $artifact)
    {
        $tracker = $artifact->getTracker();
        $project = $tracker->getProject();

        if (! $this->plugin->isAllowed($project->getId())) {
            return null;
        }

        if (! $this->timesheeting_enabler->isTimesheetingEnabledForTracker($tracker)) {
            return null;
        }

        $user_can_add_time = $this->permissions_retriever->userCanAddTimeInTracker($user, $tracker);

        if (! $user_can_add_time &&
            ! $this->permissions_retriever->userCanSeeAggregatedTimesInTracker($user, $tracker)
        ) {
            return null;
        }

        $csrf                 = new CSRFSynchronizerToken($artifact->getUri());
        $times_for_user       = $this->time_retriever->getTimesForUser($user, $artifact);
        $time_presenters      = $this->getTimePresenters($times_for_user);
        $formatted_total_time = $this->getFormattedTotalTime($times_for_user);

        $presenter = new ArtifactViewPresenter(
            $artifact,
            $csrf,
            $time_presenters,
            $formatted_total_time,
            $user_can_add_time
        );

        return new ArtifactView($artifact, $request, $user, $presenter);
    }

    /**
     * @return array
     */
    private function getTimePresenters(array $times_for_user)
    {
        $presenters = array();

        foreach ($times_for_user as $time) {
            $presenters[] = $time->getAsPresenter();
        }

        return $presenters;
    }

    private function getFormattedTotalTime(array $times_for_user)
    {
        $total_minutes = 0;
        foreach ($times_for_user as $time) {
            $total_minutes += $time->getMinutes();
        }

        $hours   = floor($total_minutes / 60);
        $minutes = $total_minutes % 60;

        return str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutes, 2, "0", STR_PAD_LEFT);
    }
}