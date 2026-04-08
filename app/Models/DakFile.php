<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DakFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_id',
        'subject',
        'priority',
        'status',
        'origin_department_id',
        'current_department_id',
        'physical_location',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A file belongs to the department that created it
    public function originDepartment()
    {
        return $this->belongsTo(Department::class, 'origin_department_id');
    }

    // A file is currently sitting in a specific department
    public function currentDepartment()
    {
        return $this->belongsTo(Department::class, 'current_department_id');
    }

    // A file has many movement records (The Audit Trail)
    public function movements()
    {
        return $this->hasMany(FileMovement::class)->orderBy('created_at', 'desc');
    }
}
