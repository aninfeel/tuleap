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

use Tuleap\Project\Label\IndexController;
use Tuleap\Project\Label\LabelDao;
use Tuleap\Project\Label\LabelsManagementRouter;

require_once('pre.php');

$request = HTTPRequest::instance();
session_require(array('group' => $request->get('group_id'), 'admin_flags' => 'A'));

$router = new LabelsManagementRouter(
    new IndexController(new LabelDao(), EventManager::instance())
);
$router->process($request);