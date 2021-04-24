<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function noInfoIds()
    {
        return (new static())->newQuery()
            ->selectRaw('users.id')
            ->leftJoin('information as inf', 'inf.user_id', '=', 'users.id')
            ->whereNull('inf.user_id')
            ->pluck('id');
    }

    public function getEmployersByAgency($id)
    {
        return $this->newQuery()->where('agency_id', $id)->where('role', 3)->with(['information', 'employee']);
    }

    public function getAffiliatesByAgency($id)
    {
        return $this->newQuery()->where('agency_id', $id)->where('role', 5)->with(['information']);
    }

    public static function getEmployersIds()
    {
        return (new static())->newQuery()->where('role', 3)->pluck('id');
    }

    public static function getAffiliateIds()
    {
        return (new static())->newQuery()->where('role', 5)->pluck('id');
    }

    public function information()
    {
        return $this->hasOne(Information::class, 'user_id', 'id');
    }

    public function employee()
    {
        return $this->hasMany(Candidate::class, 'employer_id', 'id');
    }
}
