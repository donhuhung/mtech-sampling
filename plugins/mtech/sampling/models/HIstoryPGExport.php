<?php
namespace Mtech\Sampling\Models;

use Mtech\Sampling\Models\HistoryPG;

class HIstoryPGExport extends \Backend\Models\ExportModel
{
    public function exportData($columns, $sessionKey = null)
    {
        $history = HistoryPG::all();
        $history->each(function($history) use ($columns) {
            $history->addVisible($columns);
        });
        return $history->toArray();
    }
}