<?php

namespace App\Imports;

use App\Models\Phases;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PhasesImport implements ToModel,WithHeadingRow
{
    private $projectId;
    public function __construct(int $projectId)
    {
        $this->projectId = $projectId;
    }

    public function model(array $row)
    {
        return new Phases([
            'projectid' => $this->projectId,
            'name' => $row[1],
            
        ]);
    }
}