<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contatos extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
        'pic',
        'tag_id',
        'user_id',
    ];

    public function tag()
    {
        return $this->belongsTo(Tags::class, 'tag_id', 'id');
    }

}
