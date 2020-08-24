<?php

namespace Mtech\Sampling\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use BackendAuth;
use DB;
use Mtech\Sampling\Models\Locations;
use Mtech\Sampling\Models\HistoryPG As HistoryPGModel;
use Mtech\API\Classes\HelperClass;
use Illuminate\Support\Facades\Redirect;

/**
 * History P G Back-end Controller
 */
class HistoryPG extends Controller {

    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ImportExportController',
    ];
    public $importExportConfig = 'config_import_export.yaml';

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    public function __construct() {
        parent::__construct();

        BackendMenu::setContext('Mtech.Sampling', 'sampling', 'historypg');
    }

    public function listExtendQuery($query) {
        $user = BackendAuth::getUser();
        $userId = $user->id;
        $userGroups = $user->groups;
        $arrProject = [];
        $arrLocation = [];
        if ($userGroups) {
            foreach ($userGroups as $group) {
                if ($group->code == "quan-ly-du-an" || $group->code == "tro-ly-du-an" || $group->code == "khach-hang") {
                    $projects = DB::table('mtech_sampling_backend_users_projects')->where('user_id', $userId)->get();
                    foreach ($projects as $project) {
                        array_push($arrProject, $project->project_id);
                    }
                    $locations = Locations::whereIn('project_id', $arrProject)->get();
                    foreach ($locations as $location) {
                        array_push($arrLocation, $location->id);
                    }
                    $query->whereIn('location_id', $arrLocation);
                }
            }
        }
    }

    public function downloadfilezip() {
        $filters = [];
        $allowExport = false;
        $arrUsers = [];
        $arrLocations = [];
        foreach (\Session::get('widget', []) as $name => $item) {
            if (str_contains($name, 'Filter')) {
                $filter = @unserialize(@base64_decode($item));
                if ($filter) {
                    array_push($filters, $filter);
                }
            }
        }
        foreach ($filters as $index => $filter) {
            if ($index > 0) {
                $users = isset($filter['scope-user']) ? $filter['scope-user'] : '';
                $locations = isset($filter['scope-location']) ? $filter['scope-location'] : '';
                if ($users) {
                    foreach ($users as $key => $value) {
                        array_push($arrUsers, $key);
                    }
                }
                if ($locations) {
                    foreach ($locations as $key => $value) {
                        array_push($arrLocations, $key);
                    }
                }
            }
        }
		$query = HistoryPGModel::where('id','>',0);        

        if ($arrUsers) {
            $query->whereIn('user_id', $arrUsers);
        }
        $user = BackendAuth::getUser();
        $userId = $user->id;
        $userGroups = $user->groups;
        $arrProject = [];
        $arrLocation = [];
        if ($userGroups) {
            foreach ($userGroups as $group) {
                if ($group->code == "quan-ly-du-an" || $group->code == "tro-ly-du-an" || $group->code == "khach-hang") {
                    $projects = DB::table('mtech_sampling_backend_users_projects')->where('user_id', $userId)->get();
                    foreach ($projects as $project) {
                        array_push($arrProject, $project->project_id);
                    }
                    $locations = Locations::whereIn('project_id', $arrProject)->get();
                    foreach ($locations as $location) {
                        array_push($arrLocation, $location->id);
                    }
                    $query->whereIn('location_id', $arrLocation);
                }
            }
        }
        if ($arrLocations) {
            $query->whereIn('location_id', $arrLocations);
        }
        $history = $query->get();
        $destinationPathTemp = storage_path('app/media/pg/');
        $destinationPath = storage_path('app/media/');
        foreach ($history as $item) {
            if ($item->user) {
                $name = HelperClass::getAlias($item->user->name);

                //Get File Checkin
                $checkin = $item->checkin_image;
                $filePathCheckin = explode($name, $checkin);
                $oldFileCheckin = $destinationPath . $checkin;
                $newFileCheckin = $destinationPathTemp . $checkin;
                if (!file_exists($newFileCheckin)) {
                    @mkdir($destinationPathTemp . $filePathCheckin[0], 0777, true);
                    if (file_exists($oldFileCheckin)) {
                        $allowExport = true;
                        copy($oldFileCheckin, $newFileCheckin);
                    }
                }

                //Get File Checkout
                $checkout = $item->checkout_image;
                $filePathCheckout = explode($name, $checkout);
                $oldFileCheckout = $destinationPath . $checkout;
                $newFileCheckout = $destinationPathTemp . $checkout;
                if (!file_exists($newFileCheckout)) {
                    @mkdir($destinationPathTemp . $filePathCheckout[0], 0777, true);
                    if (file_exists($oldFileCheckout)) {
                        $allowExport = true;
                        copy($oldFileCheckout, $newFileCheckout);
                    }
                }
            }
        }
        if ($allowExport) {
            $zipFileName = "pg.zip";
            HelperClass::downloadZip($destinationPathTemp, $zipFileName);
            HelperClass::deleteDir($destinationPathTemp);
        }
        return Redirect::to('/backend/mtech/sampling/historypg');
    }

}
