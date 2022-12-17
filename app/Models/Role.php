<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\Responsibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'company_id'
    ];

    /**
     * Get all of the responsibilites for the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function responsibilities()
    {
        return $this->hasMany(Responsibility::class);
    }

    /**
     * Get all of the employees for the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}
