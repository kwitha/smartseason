<?php

namespace App\Models;

use Carbon\Carbon; //a datetime library used by laravel
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    Use HasFactory;
    protected $fillable=[
        'name','crop_type','planting_date','stage','created_by'
    ];

    protected $appends = ['status'];//exposes computed data
    protected $cast = ['planting_date'=>'date'];
    //computed status never stored in the database
    //completed : stage is 'harvested'
    //at-risk:no updates for more than 14 days
    //active : everthing else
    public function getStatusAttribute (): string
    {
        if($this->stage ==='harvested')
            {
                return 'completed';
            }

        if(Carbon::parse($this->updated_at)->diffInDays(now())>14)
            {
                return 'at_risk';
            }    

            return 'active';

    }

    public function creator ():BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
     public function assignments():HasMany
     {
        return $this->hasMany(FieldAssignment::class);
     }
     public function updates():HasMany
     {
        return $this->hasMany(Fieldupdate::class);
     }
}