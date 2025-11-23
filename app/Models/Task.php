<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    //
    protected $fillable = [
        'title','description','due_date','priority','category_id','status'
    ];
    protected $casts = [
            'due_date' => 'datetime',
        ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
