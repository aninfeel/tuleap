<?php
/**
 * Copyright (c) Enalean, 2014. All rights reserved
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/
 */

require_once 'MediawikiGroupPresenter.class.php';

class MediawikiAdminPresenter {

    private $project;
    private $groups_permissions;

    /** @var Boolean */
    private $is_default_mapping;

    private $is_compatibility_view_enabled = true;

    public function __construct(Project $project, array $groups_permissions, $is_default_mapping, array $options) {
        $this->project                       = $project;
        $this->groups_permissions            = $groups_permissions;
        $this->is_default_mapping            = $is_default_mapping;
        $this->is_compatibility_view_enabled = $enable_compatibility_view = (bool) isset($options['enable_compatibility_view']) ? $options['enable_compatibility_view'] : 0;
    }

    public function admin_title() {
        return $GLOBALS['Language']->getText('plugin_mediawiki', 'admin_title');
    }

    public function can_show_options() {
        return forge_get_config('enable_compatibility_view', 'mediawiki');
    }

    public function is_compatibility_enabled_value() {
        return $this->is_compatibility_view_enabled;
    }

    public function compatibility_view_text() {
        return $GLOBALS['Language']->getText('plugin_mediawiki', 'compatibility_view_text');
    }

    public function title() {
        return $GLOBALS['Language']->getText('plugin_mediawiki', 'group_mapping_title');
    }

    public function help_intro() {
        return $GLOBALS['Language']->getText('plugin_mediawiki', 'group_mapping_help_intro', Config::get('sys_name'));
    }

    public function help_link() {
        return $GLOBALS['Language']->getText('plugin_mediawiki', 'group_mapping_help_link', $this->getMWUrl('Special:ListGroupRights'));
    }

    public function help_project() {
        $type = $this->project->isPublic() ? 'public' : 'private';
        return $GLOBALS['Language']->getText('plugin_mediawiki', 'group_mapping_help_project_'.$type);
    }

    private function getMWUrl($page) {
        return MEDIAWIKI_BASE_URL . '/wiki/' . $this->project->getUnixName(). '/index.php/' . $page;
    }

    public function route() {
        return MEDIAWIKI_BASE_URL . '/forge_admin?' . http_build_query(array(
            'group_id' => $this->project->getID(),
            'action'   => 'save'
        ));
    }

    public function groups_permissions() {
        return $this->groups_permissions;
    }

    public function submit_label() {
        return $GLOBALS['Language']->getText('plugin_mediawiki', 'save_changes');
    }

    public function or_string() {
        return $GLOBALS['Language']->getText('plugin_mediawiki', 'or_string');
    }

    public function restore_label() {
        return $GLOBALS['Language']->getText('plugin_mediawiki', 'restore_defaults');
    }

    public function options_title() {
        return $GLOBALS['Language']->getText('plugin_mediawiki', 'options_title');
    }
}
