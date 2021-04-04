<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $guarded = [];
    /**
     * Get all of the tags for the post.
     */
    public function interests()
    {
        return $this->morphToMany(Interest::class, 'interestable');
    }

}
