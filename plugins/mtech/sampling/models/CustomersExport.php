<?php
namespace Mtech\Sampling\Models;

use Mtech\Sampling\Models\Customers;

class CustomersExport extends \Backend\Models\ExportModel
{
    public function exportData($columns, $sessionKey = null)
    {
        $history = Customers::all();
        $history->each(function($history) use ($columns) {
            $history->addVisible($columns);
        });
        return $history->toArray();
    }
}