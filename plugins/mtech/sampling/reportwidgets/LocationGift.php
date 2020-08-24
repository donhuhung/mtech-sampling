<?php

namespace Mtech\Sampling\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Mtech\Sampling\Models\Projects;
use Mtech\Sampling\Models\Locations;
use Mtech\Sampling\Models\LocationGift As LocationGiftModel;
use ApplicationException;
use Exception;
use BackendAuth;
use DB;

/**
 * CampaignGift widget.
 *
 * @package backend
 * @author Mtech
 */
class LocationGift extends ReportWidgetBase {

    /**
     * Renders the widget.
     */
    public function render() {
        try {
            $this->loadData();
        } catch (Exception $ex) {
            $this->vars['error'] = $ex->getMessage();
        }
        $this->addCss('/plugins/mtech/sampling/assets/css/bootstrap.min.css');
        $this->addJs('/plugins/mtech/sampling/assets/js/jquery-1.12.4.min.js');
        $this->addJs('/plugins/mtech/sampling/assets/js/canvasjs.min.js');

        return $this->makePartial('widget');
    }

    public function defineProperties() {
        return [
            'title' => [
                'title' => 'Compare Gift Province',
                'default' => e(trans('rainlab.googleanalytics::lang.widgets.title_traffic_sources')),
                'type' => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error'
            ],
        ];
    }

    protected function loadData() {        
        $data = [];
        $districtID = 0;
        $i = 0;
        $user = BackendAuth::getUser();
        $userId = $user->id;
        $userGroups = $user->groups;
        $arrProject = [];
        $data = [];
        if ($userGroups) {
            foreach ($userGroups as $group) {     
                if ($group->code == "quan-ly-du-an" || $group->code == "tro-ly-du-an" || $group->code == "khach-hang") {
                    $projects = DB::table('mtech_sampling_backend_users_projects')->where('user_id', $userId)->get();
                    foreach ($projects as $project) {
                        array_push($arrProject, $project->project_id);
                    }                    
                    $projects = Projects::whereIn('id', $arrProject)->where('status', 1)->get();    
                }
                else{
                    $projects = Projects::where('status', 1)->get();
                }
            }      
        }
        foreach ($projects as $project) {
            $projectID = $project->id;            
            $locations = Locations::where('project_id', $projectID)->get();            
            foreach ($locations as $index => $location) {
                $locationGifts = LocationGiftModel::where('location_id', $location->id)->get();
                foreach ($locationGifts as $item) {
                    $kpi = $item->total_gift;
                    $totalGiftInventory = ($item->total_gift - $item->gift_inventory);
                }                
                $data[$projectID][$index]['province_id'] = $location->district->province->id;
                $data[$projectID][$index]['province_name'] = $location->district->province->name;
                $data[$projectID][$index]['kpi'] = $kpi;
                $data[$projectID][$index]['realTime'] = $totalGiftInventory;                
                $data[$projectID][$index]['project_id'] = $projectID;                
                $data[$projectID][$index]['project_name'] = $project->project_name;                
            }
            /*if ($data) {
                $data = $this->group_by($data, 'province_id');
                foreach ($data as $subProvinces) {
                    $kpi = 0;
                    $realTime = 0;
                    foreach ($subProvinces as $index => $province) {
                        $kpi += $province['kpi'];
                        $realTime += $province['realTime'];
                    }
                    $result[$i]['province_id'] = $subProvinces[$index]['province_id'];
                    $result[$i]['province_name'] = $subProvinces[$index]['province_name'];
                    $result[$i]['kpi'] = $kpi;
                    $result[$i]['realTime'] = $realTime;
                    $i++;
                }
            } else {
                $result = $data;
            }*/
        }        
        $this->vars['rows'] = $data;
    }

    protected function group_by($array, $key) {
        $return = array();
        foreach ($array as $val) {
            $return[$val[$key]][] = $val;
        }
        return $return;
    }

}
