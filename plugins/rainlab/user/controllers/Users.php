<?php namespace RainLab\User\Controllers;

use Auth;
use Lang;
use Flash;
use Response;
use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;
use RainLab\User\Models\MailBlocker;
use RainLab\User\Models\Settings as UserSettings;
use DB;

class Users extends Controller
{
    /**
     * @var array Extensions implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
		\Backend\Behaviors\RelationController::class
    ];

    /**
     * @var array `FormController` configuration.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var array `ListController` configuration.
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var array `RelationController` configuration, by extension.
     */
    public $relationConfig = 'config_relation.yaml';

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['rainlab.users.access_users'];

    /**
     * @var string HTML body tag class
     */
    public $bodyClass = 'compact-container';

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('RainLab.User', 'user', 'users');
        SettingsManager::setContext('RainLab.User', 'settings');
    }

    public function index()
    {
        $this->addJs('/plugins/rainlab/user/assets/js/bulk-actions.js');

        $this->asExtension('ListController')->index();
    }

    /**
     * {@inheritDoc}
     */
    public function listInjectRowClass($record, $definition = null)
    {
        if ($record->trashed()) {
            return 'strike';
        }

        if (!$record->is_activated) {
            return 'disabled';
        }
    }

    public function listExtendQuery($query)
    {
        $user = BackendAuth::getUser();
        $userId = $user->id;        
        $userGroups = $user->groups;
        $arrUsers = [];
        if ($userGroups) {
            foreach ($userGroups as $group) {
                if ($group->code == "quan-ly-du-an" || $group->code == "tro-ly-du-an" || $group->code == "khach-hang") {
                    $projects = DB::table('mtech_sampling_backend_users_projects')->where('user_id',$userId)->get();                    
                    foreach($projects as $project){
                        array_push($arrUsers, $project->user_id);
                    }                       
                    $query->where('id',2);
                    $query->withTrashed();
                }
            }
        }
    }

    public function formExtendQuery($query)
    {
        $query->withTrashed();
    }

    /**
     * Display username field if settings permit
     */
    public function formExtendFields($form)
    {
        /*
         * Show the username field if it is configured for use
         */
        if (
            UserSettings::get('login_attribute') == UserSettings::LOGIN_USERNAME &&
            array_key_exists('username', $form->getFields())
        ) {
            $form->getField('username')->hidden = false;
        }
    }

    public function formAfterUpdate($model)
    {
        $blockMail = post('User[block_mail]', false);
        if ($blockMail !== false) {
            $blockMail ? MailBlocker::blockAll($model) : MailBlocker::unblockAll($model);
        }
    }

    public function formExtendModel($model)
    {
        $model->block_mail = MailBlocker::isBlockAll($model);

        $model->bindEvent('model.saveInternal', function() use ($model) {
            unset($model->attributes['block_mail']);
        });
    }

    /**
     * Manually activate a user
     */
    public function preview_onActivate($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        $model->attemptActivation($model->activation_code);

        Flash::success(Lang::get('rainlab.user::lang.users.activated_success'));

        if ($redirect = $this->makeRedirect('update-close', $model)) {
            return $redirect;
        }
    }
    
    /**
     * Manually Block a user
     */
    public function preview_onBlockUser($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);
        $model->is_activated = 0;

        $model->save();

        Flash::success(Lang::get('rainlab.user::lang.users.activated_success'));

        if ($redirect = $this->makeRedirect('update-close', $model)) {
            return $redirect;
        }
    }

    /**
     * Manually unban a user
     */
    public function preview_onUnban($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        $model->unban();

        Flash::success(Lang::get('rainlab.user::lang.users.unbanned_success'));

        if ($redirect = $this->makeRedirect('update-close', $model)) {
            return $redirect;
        }
    }

    /**
     * Display the convert to registered user popup
     */
    public function preview_onLoadConvertGuestForm($recordId)
    {
        $this->vars['groups'] = UserGroup::where('code', '!=', UserGroup::GROUP_GUEST)->get();

        return $this->makePartial('convert_guest_form');
    }

    /**
     * Manually convert a guest user to a registered one
     */
    public function preview_onConvertGuest($recordId)
    {
        $model = $this->formFindModelObject($recordId);

        // Convert user and send notification
        $model->convertToRegistered(post('send_registration_notification', false));

        // Remove user from guest group
        if ($group = UserGroup::getGuestGroup()) {
            $model->groups()->remove($group);
        }

        // Add user to new group
        if (
            ($groupId = post('new_group')) &&
            ($group = UserGroup::find($groupId))
        ) {
            $model->groups()->add($group);
        }

        Flash::success(Lang::get('rainlab.user::lang.users.convert_guest_success'));

        if ($redirect = $this->makeRedirect('update-close', $model)) {
            return $redirect;
        }
    }

    /**
     * Impersonate this user
     */
    public function preview_onImpersonateUser($recordId)
    {
        if (!$this->user->hasAccess('rainlab.users.impersonate_user')) {
            return Response::make(Lang::get('backend::lang.page.access_denied.label'), 403);
        }

        $model = $this->formFindModelObject($recordId);

        Auth::impersonate($model);

        Flash::success(Lang::get('rainlab.user::lang.users.impersonate_success'));
    }

    /**
     * Force delete a user.
     */
    public function update_onDelete($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        $model->forceDelete();

        Flash::success(Lang::get('backend::lang.form.delete_success'));

        if ($redirect = $this->makeRedirect('delete', $model)) {
            return $redirect;
        }
    }

    /**
     * Perform bulk action on selected users
     */
    public function index_onBulkAction()
    {
        if (
            ($bulkAction = post('action')) &&
            ($checkedIds = post('checked')) &&
            is_array($checkedIds) &&
            count($checkedIds)
        ) {

            foreach ($checkedIds as $userId) {
                if (!$user = User::withTrashed()->find($userId)) {
                    continue;
                }

                switch ($bulkAction) {
                    case 'delete':
                        $user->forceDelete();
                        break;

                    case 'activate':
                        $user->attemptActivation($user->activation_code);
                        break;

                    case 'deactivate':
                        $user->delete();
                        break;

                    case 'restore':
                        $user->restore();
                        break;

                    case 'ban':
                        $user->ban();
                        break;

                    case 'unban':
                        $user->unban();
                        break;
                }
            }

            Flash::success(Lang::get('rainlab.user::lang.users.'.$bulkAction.'_selected_success'));
        }
        else {
            Flash::error(Lang::get('rainlab.user::lang.users.'.$bulkAction.'_selected_empty'));
        }

        return $this->listRefresh();
    }
}
