<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // A department can have many files originating from it
    public function originatingFiles()
    {
        return $this->hasMany(DakFile::class, 'origin_department_id');
    }

    // A department can currently hold many files
    public function currentFiles()
    {
        return $this->hasMany(DakFile::class, 'current_department_id');
    }
}
