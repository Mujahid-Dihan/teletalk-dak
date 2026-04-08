<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileMovement extends Model
{
    use HasFactory;

    protected $fillable = ['dak_file_id', 'user_id', 'from_department_id', 'to_department_id', 'action', 'remarks'];

    public function user() { return $this->belongsTo(User::class); }
    public function fromDepartment() { return $this->belongsTo(Department::class, 'from_department_id'); }
    public function toDepartment() { return $this->belongsTo(Department::class, 'to_department_id'); }
    public function dakFile() { return $this->belongsTo(DakFile::class); }
}
