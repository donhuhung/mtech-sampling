<?php

namespace Mtech\Sampling\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Mtech\Sampling\Models\Projects;
use Mtech\Sampling\Models\Locations;
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
class CampaignRealGift extends ReportWidgetBase {

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
       
    }

}
