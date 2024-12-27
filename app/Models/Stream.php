<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'is_live',
        'stream_status',
        'stream_date',
        'stream_time',
        'stream_url'
    ];
}
