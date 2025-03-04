<?php

namespace App\Models;

use App\LogsModelChanges;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use LogsModelChanges;

    protected $table = 'categories';

    protected $fillable = [
        'code',
        'name',
        'description'
    ];
}
