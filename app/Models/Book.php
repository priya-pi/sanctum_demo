<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['id','user_id','name','description','no_of_page','author','category','price','released_year','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
