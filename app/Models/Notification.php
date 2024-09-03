<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'user_id', 'from'];

    public function user(){
        return $this->belongsTo(User::class);
    }
    
}
