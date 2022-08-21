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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active_members' => 'integer',
        'requires_approval' => 'boolean',
        'verified' => 'boolean',
        'featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


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

    public function setCodeAttribute($value)
    {
        if($value[0] != '#')  $value = '#'.$value;
        $this->attributes['code'] = Str::upper($value);
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

    public function setActiveMembersAttribute($value)
    {
        if(!$value)  $value = 1;
        if($value > 30) $value = 30;
        $this->attributes['active_members'] = $value;
    }

    public function setRequiresApprovalAttribute($value)
    {
        $this->attributes['requires_approval'] = ($value ?? false);
    }

    public function setFeaturedAttribute($value)
    {
        $this->attributes['featured'] = ($value ?? false);
    }

    public function setVerifiedAttribute($value)
    {
        $this->attributes['verified'] = ($value ?? false);
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

    public function rankLabel()
    {
        return $this->rank ? config('battlefactory.squad_ranks')[$this->rank] : null;
    }

    public function countryName()
    {
        return $this->country ? country($this->country)->getName() : null;
    }

    public function nameWords()
    {
        return array_filter(preg_split('/(?=[A-Z])/', $this->name), fn($word) => $word!='' );
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
        $array['rank_label'] = $this->rankLabel();
        $array['country_name'] = $this->countryName();
        $array['name_words'] = $this->nameWords();
        $array['rank_value'] = $this->rankValue();
 
        return $array;
    }
}
