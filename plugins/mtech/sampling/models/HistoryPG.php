<?php

namespace Mtech\Sampling\Models;

use Model;
use Mtech\Sampling\Models\Locations;

/**
 * Model
 */
class HistoryPG extends Model {

    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_history_pg';

    /**
     * @var array Validation rules
     */
    public $rules = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'user' => 'RainLab\User\Models\User',
        'location' => ['Mtech\Sampling\Models\Locations']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];        

    public function scopeFilterByUser($query, $filter) {
        return $query->whereHas('user', function($user) use ($filter) {
                    $user->whereIn('id', $filter);
                });
    }

    public function scopeFilterByLocation($query, $filter) {
        return $query->whereHas('location', function($project) use ($filter) {
                    $project->whereIn('id', $filter);
                });
    }

}
