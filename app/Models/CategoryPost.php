<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryPost extends Model
{
    //  defult - category_posts
    //  to tell the laravel to use category_post table
    protected $table = 'category_post';
    protected $fillable = ['category_id', 'post_id']; // allow mass assignment for saving records using create()
    public $timestamps = false;
    
    # CATEGORYPOST - CATEGORY
    # to get the detailes of the category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
