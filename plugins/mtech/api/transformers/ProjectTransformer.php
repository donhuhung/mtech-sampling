<?php
namespace Mtech\API\Transformers;

use Carbon\Carbon;
use League\Fractal;
use Mtech\Sampling\Models\Projects;

class ProjectTransformer extends Fractal\TransformerAbstract
{

    public function transform(Projects $project)
    {        
        return [
            'id'               => (int) $project->id,
            'project_name'        => (string) $project->project_name,
            'start_date'         => $project->start_date,                        
            'end_date'            => $project->end_date,            
            'allow_choose_gift'   => $project->allow_choose_gift,  
            'number_receive_gift'  => $project->number_receive_gift,
            'createdAt' => Carbon::parse($project->created_at)->format('Y-m-d'),
        ];
    }
}
