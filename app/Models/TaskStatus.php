<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;
    protected $fillabled = ['name', 'disabled'];
    protected $table = 'status';
}
