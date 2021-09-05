<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Photo extends Model
{
    use HasFactory;
    //プライマリキーの型
    protected $keyType = 'string';
    //IDの桁数
    const ID_LENGTH = 12;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! Arr::get($this->attributes, 'id')) {
            $this->setId();
        }
    }

    private function setId()
    {
        $this->attributes['id'] = $this->getRandamId();
    }

    private function getRandamId()
    {
        $characters = array_merge(
            range(0, 9),
            range('a', 'z'),
            range('A', 'Z'),
            ['-','_']
        );
        $length = count($characters);

        $id = "";
        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }
    /**
     * リレーションシップ　－　usersテーブル
     */
    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id', 'users');
    }
    /**
     * リレーションシップ　－　commnetsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Models\Comment')->orderBy('id', 'desc');
    }
    /**
     * リレーションシップ　－　usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany('App\Models\User', 'likes')->withTimestamps();
    }
    /**
     * アクセサ - likes_count
     * @return int
     */
    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }
    /**
     * アクセサ liked_by_user
     * @return boolean
     */
    public function getLikedByUserAttribute()
    {
        if (Auth::guest()) {
            return false;
        }
        return $this->likes->contains(function ($user) {
            return $user->id === Auth::user()->id;
        });
    }
    /**
     * アクセサ - url
     * @return string
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->attributes['filename']);
    }
    /** JSONに含める属性 */
    protected $appends = [
        'url','likes_count','liked_by_user',
    ];
    protected $visible = [
        'id','owner','url','comments','likes_count','liked_by_user',
    ];
    protected $perPage=5;
}
