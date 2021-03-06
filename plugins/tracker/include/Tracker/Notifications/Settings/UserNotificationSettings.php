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

namespace Tuleap\Tracker\Notifications\Settings;

use Tuleap\Tracker\Notifications\GlobalNotification;

class UserNotificationSettings
{
    /**
     * @var bool
     */
    private $is_in_no_notification_at_all_mode = false;
    /**
     * @var bool
     */
    private $is_in_no_global_notification_mode = false;
    /**
     * @var bool
     */
    private $is_in_notify_on_artifact_creation_mode = false;
    /**
     * @var bool
     */
    private $is_in_notify_on_every_change_mode = false;

    public function __construct($has_unsubscribed, GlobalNotification ...$global_notifications)
    {
        if ($has_unsubscribed) {
            $this->is_in_no_notification_at_all_mode = true;
            return;
        }

        if (empty($global_notifications)) {
            $this->is_in_no_global_notification_mode = true;
            return;
        }

        foreach ($global_notifications as $global_notification) {
            if ($global_notification->isOnAllUpdates()) {
                $this->is_in_notify_on_every_change_mode = true;
                return;
            }
        }
        $this->is_in_notify_on_artifact_creation_mode = true;
    }

    /**
     * @return bool
     */
    public function isInNoNotificationAtAllMode()
    {
        return $this->is_in_no_notification_at_all_mode;
    }

    /**
     * @return bool
     */
    public function isInNoGlobalNotificationMode()
    {
        return $this->is_in_no_global_notification_mode;
    }

    /**
     * @return bool
     */
    public function isInNotifyOnArtifactCreationMode()
    {
        return $this->is_in_notify_on_artifact_creation_mode;
    }

    /**
     * @return bool
     */
    public function isInNotifyOnEveryChangeMode()
    {
        return $this->is_in_notify_on_every_change_mode;
    }
}
