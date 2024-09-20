<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Symbol extends Model
{
    use HasFactory;

    protected $appends = ['icon'];


    public function getIconAttribute()
    {
        if ($this->attributes['icon']) {
            return asset($this->attributes['icon']);
        } else {
            return $this->attributes['icon'];
        }
    }
}
