<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $visible = [
        'author','content',
    ];

    /**
     * リレーションシップ　ー　usersテーブル
     * @return Illuminate\Database\Eloquient\Relations\BelogsTo
     */
    public function author()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id', 'users');
    }
}
