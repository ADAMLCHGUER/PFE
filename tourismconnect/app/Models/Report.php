<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'report_type', 'data', 'report_date'];

    protected $casts = [
        'data' => 'array',
        'report_date' => 'date',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}