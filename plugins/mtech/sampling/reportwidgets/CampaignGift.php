<?php

namespace Mtech\Sampling\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Mtech\Sampling\Models\Projects;
use Mtech\Sampling\Models\Locations;
use Mtech\Sampling\Models\LocationGift;
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
class CampaignGift extends ReportWidgetBase {

    /**
     * Renders the widget.
     */
    public function render() {
        try {
            $this->loadData();
        } catch (Exception $ex) {
            $this->vars['error'] = $ex->getMessage();
        }

        return $this->makePartial('widget');
    }

    public function defineProperties() {
        return [
            'title' => [
                'title' => 'backend::lang.dashboard.widget_title_label',
                'default' => e(trans('rainlab.googleanalytics::lang.widgets.title_traffic_sources')),
                'type' => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error'
            ],
            'reportSize' => [
                'title' => 'rainlab.googleanalytics::lang.widgets.traffic_sources_report_size',
                'default' => '150',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'rainlab.googleanalytics::lang.widgets.traffic_sources_report_size_validation'
            ],
            'center' => [
                'title' => 'rainlab.googleanalytics::lang.widgets.traffic_sources_center',
                'type' => 'checkbox'
            ],
        ];
    }

    protected function loadData() {
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
                    foreach ($projects as $index => $project) {
                        $projectId = $project->id;
                        $locations = Locations::where('project_id', $projectId)->get();
                        $totalGiftInventory = 0;
                        foreach ($locations as $location) {
                            $locationGifts = LocationGift::where('location_id', $location->id)->get();
                            foreach ($locationGifts as $item) {
                                $totalGiftInventory += $item->total_gift - $item->gift_inventory;
                                $kpi = $item->total_gift;
                            }
                        }
                        $data[$index]['project_name'] = $project->project_name;
                        $data[$index]['project_id'] = $projectId;
                        $data[$index]['kpi'] = $kpi;
                        $data[$index]['totalGiftInventory'] = $totalGiftInventory > 0 ? $totalGiftInventory : $kpi;
                    }
                } else {
                    $projects = Projects::where('status', 1)->get();
                    foreach ($projects as $index => $project) {
                        $projectId = $project->id;
                        $locations = Locations::where('project_id', $projectId)->get();
                        $totalGiftInventory = 0;
                        foreach ($locations as $location) {
                            $locationGifts = LocationGift::where('location_id', $location->id)->get();
                            foreach ($locationGifts as $item) {
                                $totalGiftInventory += $item->total_gift - $item->gift_inventory;
                                $kpi = $item->total_gift;
                            }
                        }
                        $data[$index]['project_name'] = $project->project_name;
                        $data[$index]['project_id'] = $projectId;
                        $data[$index]['kpi'] = $kpi;
                        $data[$index]['totalGiftInventory'] = $totalGiftInventory > 0 ? $totalGiftInventory : $kpi;
                    }
                }
            }
        }
        $this->vars['rows'] = $data;
    }

}
