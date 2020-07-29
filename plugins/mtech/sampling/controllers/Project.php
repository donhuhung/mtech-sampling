<?php

namespace Mtech\Sampling\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use BackendAuth;

/**
 * Project Back-end Controller
 */
class Project extends Controller {

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

        BackendMenu::setContext('Mtech.Sampling', 'sampling', 'project');
    }

    public function listExtendQuery($query) {
        $user = BackendAuth::getUser();
        $userId = $user->id;
        $userGroups = $user->groups;
        if ($userGroups) {
            foreach ($userGroups as $group) {
                if ($group->code == "quan-ly-du-an" || $group->code == "tro-ly-du-an" || $group->code == "khach-hang") {
                    $query->whereHas('usersBackend', function ( $q ) use ($userId) {
                        $q->where('user_id', $userId);
                    });
                }
            }
        }
    }

}
