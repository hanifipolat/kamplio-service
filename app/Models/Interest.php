<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;
   # protected $visible=['id','name'];
    protected $guarded = [];
    //Interest::with(['posts','users])->find($interest->id)

    public function posts()
    {
        return $this->morphedByMany(Post::class, 'interestable');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'interestable');
    }
}
