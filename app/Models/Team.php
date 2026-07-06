<?php

namespace App\Models;

use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role') // that makes the role column to be returned alogside the team and user data when we access the relationship
            ->withTimestamps();
    }
}
