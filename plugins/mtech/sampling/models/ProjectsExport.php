<?php
namespace Mtech\Sampling\Models;

use Mtech\Sampling\Models\Projects;

class LocationsExport extends \Backend\Models\ExportModel
{
    public function exportData($columns, $sessionKey = null)
    {
        $history = Projects::all();
        $history->each(function($history) use ($columns) {
            $history->addVisible($columns);
        });
        return $history->toArray();
    }
}