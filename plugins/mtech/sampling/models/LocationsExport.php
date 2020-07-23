<?php
namespace Mtech\Sampling\Models;

use Mtech\Sampling\Models\Locations;

class LocationsExport extends \Backend\Models\ExportModel
{
    public function exportData($columns, $sessionKey = null)
    {
        $history = Locations::all();
        $history->each(function($history) use ($columns) {
            $history->addVisible($columns);
        });
        return $history->toArray();
    }
}