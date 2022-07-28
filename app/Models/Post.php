<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'banner',
        'description',
        'category',
        'images'
    ];
    public $keyType = 'string';
    protected $hidden = [
        'category_id'
    ];

    public $timestamps = false;

    public function imagens(){
        return $this->hasMany(Imagem::class);
    }

    public function category(){
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}

