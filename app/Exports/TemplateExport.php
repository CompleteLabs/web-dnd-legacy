<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateExport implements WithHeadings
{
     /**
    * @return \Illuminate\Support\Collection
    */

    public function headings(): array
    {
        return [
            'nama_lengkap',
            'username',
            'role',
            'area',
            'divisi',
            'dr',
            'wn',
            'wr',
            'mn',
            'mr',
            'approval',
            'password',
        ];
    }
}
