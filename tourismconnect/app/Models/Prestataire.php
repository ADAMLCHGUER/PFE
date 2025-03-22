<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Prestataire extends Model
{
    use HasFactory, CrudTrait;

    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'description',
        'adresse',
        'ville',
        'pays',
        'code_postal',
        'site_web',
        'actif',
        'image'
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];
}