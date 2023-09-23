<?php

namespace Octopy\Impersonate\Tests\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method   static create(string[] $array)
 */
class Post1 extends Model
{
    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @var string[]
     */
    protected $fillable = [
        'title', 'user_id',
    ];
}
