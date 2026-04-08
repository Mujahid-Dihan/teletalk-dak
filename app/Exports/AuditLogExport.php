<?php

namespace App\Exports;

use App\Models\FileMovement;
use Maatwebsite\Excel\Concerns\FromCollection;

class AuditLogExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return FileMovement::all();
    }
}
