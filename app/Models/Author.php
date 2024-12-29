<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $guarded = [];

    
    public function platforms()
    {
        return $this->hasMany(Platform::class, 'author_id');
    }
}
