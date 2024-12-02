<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'honorary_title',
        'name',
        'position',
        'category',
        'image_path',
        'email',
        'social_facebook',
        'social_researchgate',
        'social_scholar',
    ];
}
