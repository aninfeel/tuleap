<?php
/**
 * @copyright Copyright (c) Xerox Corporation, CodeX, Codendi 2007-2008.
 *
 * This file is licensed under the GNU General Public License version 2. See the file COPYING.
 * 
 * @author Marc Nazarian <marc.nazarian@xrce.xerox.com>
 * 
 * hudson_Widget_JobLastBuilds 
 */

require_once('HudsonJobWidget.class.php');
require_once('common/user/UserManager.class.php');
require_once('common/include/HTTPRequest.class.php');
require_once('PluginHudsonJobDao.class.php');
require_once('HudsonJob.class.php');

class hudson_Widget_JobLastBuilds extends HudsonJobWidget {
    
    var $group_id;
    
    var $job;
    var $job_url;
    var $job_id;
    
    function hudson_Widget_JobLastBuilds($owner_type, $owner_id) {
        $wlm = new WidgetLayoutManager();
        if ($owner_type == $wlm->OWNER_TYPE_USER) {
            $this->widget_id = 'myhudsonjoblastbuilds';
        } else {
            $this->widget_id = 'projecthudsonjoblastbuilds';
        }
        $this->Widget($this->widget_id);
        
        $request =& HTTPRequest::instance();
        $this->group_id = $request->get('group_id');
                
        $this->setOwner($owner_id, $owner_type);
    }
    
    function getTitle() {
        $title = '';
        if ($this->job) {
            $title .= $GLOBALS['Language']->getText('plugin_hudson', 'project_job_lastbuilds', array($this->job->getName()));
        } else {
            $title .= $GLOBALS['Language']->getText('plugin_hudson', 'project_job_lastbuilds');
        }
        return  $title;
    }
    
    function loadContent($id) {
        $sql = "SELECT * FROM plugin_hudson_widget WHERE widget_name='" . $this->widget_id . "' AND owner_id = ". $this->owner_id ." AND owner_type = '". $this->owner_type ."' AND id = ". $id;
        $res = db_query($sql);
        if ($res && db_numrows($res)) {
            $data = db_fetch_array($res);
            $this->job_id    = $data['job_id'];
            $this->content_id = $id;
            
            $jobs = $this->getAvailableJobs();
            
            if (array_key_exists($this->job_id, $jobs)) {
                $used_job = $jobs[$this->job_id];
                $this->job_url = $used_job->getUrl();
                $this->job = $used_job;
            } else {
                $this->job = null;
            }
            
        }
    }

    function getContent() {
        $html = '';
        if ($this->job != null) {
            $job = $this->job;
            
            $html .= '<ul>';
            if ($job->hasBuilds()) {
                $html .= ' <li>'.$GLOBALS['Language']->getText('plugin_hudson', 'last_build').' <a href="/plugins/hudson/?action=view_build&group_id='.$this->group_id.'&job_id='.$this->job_id.'&build_id='.$job->getLastBuildNumber().'"># '.$job->getLastBuildNumber().'</a></li>';
                $html .= ' <li>'.$GLOBALS['Language']->getText('plugin_hudson', 'last_build_success').' <a href="/plugins/hudson/?action=view_build&group_id='.$this->group_id.'&job_id='.$this->job_id.'&build_id='.$job->getLastSuccessfulBuildNumber().'"># '.$job->getLastSuccessfulBuildNumber().'</a></li>';
                $html .= ' <li>'.$GLOBALS['Language']->getText('plugin_hudson', 'last_build_failure').' <a href="/plugins/hudson/?action=view_build&group_id='.$this->group_id.'&job_id='.$this->job_id.'&build_id='.$job->getLastFailedBuildNumber().'"># '.$job->getLastFailedBuildNumber().'</a></li>';
            } else {
                $html .= ' <li>'. $GLOBALS['Language']->getText('plugin_hudson', 'widget_build_not_found') . '</li>';
            }
            $html .= '</ul>';
            $html .= $GLOBALS['Language']->getText('plugin_hudson', 'weather_report').'<img src="'.$job->getWeatherReportIcon().'" />';        
        } else {
            $html .= $GLOBALS['Language']->getText('plugin_hudson', 'widget_job_not_found');
        }
            
        return $html;
    }
    
}

?>