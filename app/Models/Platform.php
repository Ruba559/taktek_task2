<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $guarded = [];

    public function author()
    {
        return $this->belongsTo(Author::class );
    }
}
