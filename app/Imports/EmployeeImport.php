<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;

class EmployeeImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    
    public function model(array $row)
    {
      
        return new Employee([
            'name'  => $row[3],
            'date' => $row[5],
            'check_in' => $row[9],
            'check_out' => $row[10],
        ]);

        
    }
}
