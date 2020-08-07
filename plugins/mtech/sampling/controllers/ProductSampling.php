<?php namespace Mtech\Sampling\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use BackendAuth;
use DB;

/**
 * Product Sampling Back-end Controller
 */
class ProductSampling extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Mtech.Sampling', 'sampling', 'productsampling');
    }
    
    public function listExtendQuery($query) {
        $user = BackendAuth::getUser();
        $userId = $user->id;
        $userGroups = $user->groups;
        $arrProject = [];
        if ($userGroups) {
            foreach ($userGroups as $group) {
                if ($group->code == "quan-ly-du-an" || $group->code == "tro-ly-du-an" || $group->code == "khach-hang") {
                    $projects = DB::table('mtech_sampling_backend_users_projects')->where('user_id',$userId)->get();                    
                    foreach($projects as $project){
                        array_push($arrProject, $project->project_id);
                    }                    
                    $query->whereIn('project_id',$arrProject);
                }
            }
        }
    }
}
