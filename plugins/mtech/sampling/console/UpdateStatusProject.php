<?php

namespace Mtech\Sampling\Console;

use Illuminate\Console\Command;
use Mtech\Sampling\Models\Projects;
use DB;

class UpdateStatusProject extends Command {

    /**
     * @var string The console command name.
     */
    protected $name = 'sampling:update_status_project';

    /**
     * @var string The console command description.
     */
    protected $description = 'No description provided yet...';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle() {
        $projects = Projects::where('status',1)->get();
        if ($projects) {
            foreach ($projects as $project) {
                $now = date('Y-m-d H:i:s');
                $endDate = $project->end_date;
                if($now > $endDate){
                    $project->status = 0;
                    $project->save();
                }
            }
        }
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments() {
        return [
        ];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions() {
        return [];
    }

}
