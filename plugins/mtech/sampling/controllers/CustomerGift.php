<?php

namespace Mtech\Sampling\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use BackendAuth;
use DB;
use Mtech\Sampling\Models\Locations;
use Mtech\Sampling\Models\CustomerGifts;
use Mtech\API\Classes\HelperClass;
use Illuminate\Support\Facades\Redirect;

/**
 * Customer Gift Back-end Controller
 */
class CustomerGift extends Controller {

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

        BackendMenu::setContext('Mtech.Sampling', 'sampling', 'customergift');
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
        foreach (\Session::get('widget', []) as $name => $item) {
            if (str_contains($name, 'Filter')) {
                $filter = @unserialize(@base64_decode($item));
                if ($filter) {
                    array_push($filters, $filter);
                }
            }
        }
        foreach ($filters as $index => $filter) {
            if ($index == 0) {
                $arrCustomers = [];
                $arrGifts = [];
                $arrLocations = [];
                $customers = isset($filter['scope-customer']) ? $filter['scope-customer'] : '';
                $gifts = isset($filter['scope-gift']) ? $filter['scope-gift'] : '';
                $locations = isset($filter['scope-location']) ? $filter['scope-location'] : '';
                if ($customers) {
                    foreach ($customers as $key => $value) {
                        array_push($arrCustomers, $key);
                    }
                }
                if ($gifts) {
                    foreach ($gifts as $key => $value) {
                        array_push($arrGifts, $key);
                    }
                }
                if ($locations) {
                    foreach ($locations as $key => $value) {
                        array_push($arrLocations, $key);
                    }
                }
            }
        }
        $query = CustomerGifts::where('id','>',0);

        if ($arrCustomers) {
            $query->whereIn('customer_id', $arrCustomers);
        }
        if ($arrGifts) {
            $query->whereIn('gift_id', $arrGifts);
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
        $customerGifts = $query->get();		
        $destinationPathTemp = storage_path('app/media/temp/');
        $destinationPath = storage_path('app/media/');
        foreach ($customerGifts as $item) {
            if ($item->customer) {
                $phone = $item->customer->phone;

                //Get File Avatar
                $avatar = $item->customer->file_name_avatar;
				if($avatar && $avatar !="null"){
					$filePathAvatar = explode($phone, $avatar);
					$oldFileAvatar = $destinationPath . $avatar;
					$newFileAvatar = $destinationPathTemp . $avatar;
					if (!file_exists($newFileAvatar)) {
						@mkdir($destinationPathTemp . $filePathAvatar[0], 0777, true);
						if (file_exists($oldFileAvatar)) {
							$allowExport = true;							
							copy($oldFileAvatar, $newFileAvatar);
						}
					}
				}                

                //Get File Bill
                $bill = $item->customer->file_name_bill;
				if($bill && $bill !="null"){
					$filePathBill = explode($phone, $bill);
					$oldFileBill = $destinationPath . $bill;
					$newFileBill = $destinationPathTemp . $bill;
					if (!file_exists($newFileBill)) {
						@mkdir($destinationPathTemp . $filePathBill[0], 0777, true);
						if (file_exists($oldFileBill)) {
							$allowExport = true;
							copy($oldFileBill, $newFileBill);
						}
					}
				}                
            }
        }
        if ($allowExport) {
            $zipFileName = "customer.zip";
            HelperClass::downloadZip($destinationPathTemp, $zipFileName);
            HelperClass::deleteDir($destinationPathTemp);
        }
        return Redirect::to('/backend/mtech/sampling/customergift');
    }

}
