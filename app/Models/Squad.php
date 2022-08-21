<?php

namespace App\Models;

use Throwable;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Squad extends Model
{
    use HasFactory;
    use Searchable;

    protected $guarded = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        //'country_flag',
    ];

    public function scopeFeatured($query)
    {
        return $query->where('featued', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function setCountryAttribute($value)
    {
        $country = null;
        try {
            country($value);
            $country = Str::lower($value);
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

    public function rankValue()
    {
        $value = 0;
        
        if($this->rank)
        {
            $i = 0;
            $squad_ranks = config('battlefactory.squad_ranks');
            foreach($squad_ranks as $key=>$squad_rank)
            {
                $i++;
                if($this->rank == $key)
                    $value = $i;
            }
        }

        return $value;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
 
        // Customize the data array...
        $array['rank_value'] = $this->rankValue();
 
        return $array;
    }
}
