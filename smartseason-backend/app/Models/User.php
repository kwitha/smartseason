<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens,HasFactory,Notifiable;
    protected $fillable =['name','email','password','role'];
    protected $hidden =['password','remember_token'];
    protected $cast =['email_verified_at' => 'datetime',
                       'password'=>'hashed'];
            
    public function isAdmin():bool{return $this->role ==='admin';}
    public function isAgent():bool{return $this->role ==='agent';}

    public function assignments():HasMany
    {
      return $this->hasMany(FieldAssignment::class,'agent_id');
    }
     
    public function fieldupdates():HasMany
    {
        return $this->hasMany(FieldUpdate::class,'agent_id');
    }

    public function createdFields():HasMany
    {
        return $this->hasMany(Field::class,'created_by');
    }
     
}
