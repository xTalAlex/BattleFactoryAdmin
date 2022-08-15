<?php

namespace App\Models;

use Throwable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Squad extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        //'country_flag',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function setCountryAttribute($value)
    {
        $country = null;
        try {
            country($value);
            $country = $value;
        } catch (Throwable $e) {
            report($e);
        }
        $this->attributes['country'] = $country;
    }

    public function getCountryFlagAttribute()
    {
        $flag = null;

        if($this->country)
            try {
                $flag = country($this->country)->getFlag();
            } catch (Throwable $e) {
                report($e);
            }
        return $flag;
    }
}
