<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imagem extends Model
{
    use HasFactory;
    protected $table = 'imagens';

    protected $fillable = [
        'imagem',
        'post_id',
    ];

    public $timestamps = false;

    
    public function post(){
        return $this->belongsTo(Post::class, 'id', 'post_id');
    }
}
