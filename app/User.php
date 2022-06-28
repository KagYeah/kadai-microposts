<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * このユーザが所有する投稿。（Micropostモデルとの関係を定義）
     */
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    /**
     * このユーザがフォロー中のユーザ。（Userモデルとの関係を定義）
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    /**
     * このユーザをフォロー中のユーザ。（Userモデルとの関係を定義）
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    /**
     * このユーザがfavoriteしているmicropost。（Micropostモデルとの関係を定義）
     */
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    /**
     * このユーザーに関係するモデルの件数をロード
     */
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
    }
    
    /**
     * $userIdで指定されたユーザをフォローする。
     * 
     * @param  int $usereId
     * @return bool
     */
    public function follow($userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            return false;
        } else {
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    /**
     * $userIdで指定されたユーザをアンフォローする。
     * 
     * @param  int $usereId
     * @return bool
     */
    public function unfollow($userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            $this->followings()->detach($userId);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 指定された$userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     * 
     * @param  int $userId
     * @return bool
     */
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    /**
     * このユーザとフォロー中ユーザの投稿に絞り込む
     */
    public function feed_microposts()
    {
        $userIds = $this->followings()->pluck('users.id')->toArray();
        $userIds[] = $this->id;
        
        return Micropost::whereIn('user_id', $userIds);
    }
    
    /**
     * $postIdで指定された投稿をfavoriteする。
     * 
     * @param  int $postId
     * @return bool
     */
    public function favorite($postId)
    {
        if ($this->is_favoriting($postId)) {
            return false;
        } else {
            $this->favorites()->attach($postId);
            return true;
        }
    }
    
    /**
     * $postIdで指定された投稿をunfavoriteする。
     * 
     * @param  int $postId
     * @return bool
     */
    public function unfavorite($postId)
    {
        if ($this->is_favoriting($postId)) {
            $this->favorites()->detach($postId);
            return true;
        } else {
            return false;
        }
    }
    
    public function is_favoriting($postId)
    {
        return $this->favorites()->where('micropost_id', $postId)->exists();
    }
    
    public function favorite_microposts()
    {
        $postIds = $this->favorites()->pluck('microposts.id')->toArray();
        
        return Micropost::whereIn('micropost_id', $postIds);
    }
}
