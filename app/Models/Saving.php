<?php

namespace App\Models;

use App\LogsModelChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Saving extends Model
{
    use SoftDeletes, LogsModelChanges;

    protected $table= 'savings';

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'description',
        'date',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
