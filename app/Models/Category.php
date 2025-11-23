<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'color'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public static function createCategory($name, $color)
    {
        return self::create([
            'name' => $name,
            'color' => $color,
        ]);
    }
}
