<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    protected $enum = [
        'status' => ['pending', 'processing', 'failed', 'completed'],
    ];
    
    protected $attributes = [
        'status' => 'pending',
    ];
    
}
