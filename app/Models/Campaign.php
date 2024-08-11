<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'file_path', 'user_id', 'total_contacts', 'processed_contacts'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
