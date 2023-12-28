<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Phrase extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'user_id'
    ];

    protected $with = ['user'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($phrase) {
            $user = Auth::user();
            if ($user) {
                $phrase->user_id = $user->id;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
