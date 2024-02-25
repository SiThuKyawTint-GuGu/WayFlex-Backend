<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelDiscount extends Model
{
    use HasFactory;
    protected $fillable = [
        "level_id",
        "discount_percentage"
    ];

    public function level(){
        return $this->belongsTo(Level::class);
    }
}
