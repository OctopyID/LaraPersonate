<?php

namespace Octopy\Impersonate\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'comment',
    ];
}
