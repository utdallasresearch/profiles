<?php

namespace App;

use App\Profile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\Models\Media;

class ProfileData extends Model implements HasMedia, Auditable
{
    use HasFactory, HasAudits, HasMediaTrait;

    /** @var string The database table used by the model */
    protected $table = 'profile_data';

    /** @var array attribute casting map */
    protected $casts = [
        'public' => 'boolean',
        'data' => 'array'
    ];

    /** @var array mass-assignment fillable attributes */
    protected $fillable = [
        'profile_id',
        'type',
        'data',
        'sort_order',
        'public'
    ];

    /////////////////////
    // General Methods //
    /////////////////////

    /**
     * Get the attributes that can be exposed via the API.
     *
     * @return array
     */
    public static function apiAttributes()
    {
        return ['profile_id', 'type', 'data', 'sort_order'];
    }

    /**
     * Registers media conversions.
     *
     * @param  Media|null $media
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->registerImageThumbnails($media, 'thumb', 150);
        $this->registerImageThumbnails($media, 'medium', 350);
    }

    /**
     * Registers image thumbnails.
     *
     * @param  Media|null $media
     * @param  string     $name       Name of the thumbnail
     * @param  int        $size       Max dimension in pixels
     * @param  string     $collection Name of the collection for the thumbnails
     * @return Spatie\MediaLibrary\Conversion\Conversion
     */
    protected function registerImageThumbnails(Media $media = null, $name, $size, $collection = 'images')
    {
        return $this->addMediaConversion($name)->width($size)->height($size)->performOnCollections($collection);
    }

    ///////////////////////////////////
    // Mutators & Virtual Attributes //
    ///////////////////////////////////

    public function getImageAttribute()
    {
        return $this->getFirstMedia('images');
    }

    /**
     * Get the image URL. ($this->image_url)
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return url($this->getFirstMediaUrl('images', 'medium') ?: '/img/default.png');
    }

    /**
     * Get profile data as if it was a natural attribute.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute('data')[$key] ?? parent::__get($key);
    }

    /**
     * Check if profile data attribute isset
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return !is_null($this->getAttribute('data')[$key] ?? null) || parent::__isset($key);
    }

    //////////////////
    // Query Scopes //
    //////////////////

    /**
     * Query scope for information
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeInformation($query)
    {
        return $query->where('type', 'information');
    }

    /**
     * Query scope for preparation
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopePreparation($query)
    {
        return $query->where('type', 'preparation');
    }

    /**
     * Query scope for awards
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeAwards($query)
    {
        return $query->where('type', 'awards');
    }

    /**
     * Query scope for
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeAreas($query)
    {
        return $query->where('type', 'areas');
    }

    /**
     * Query scope for activities
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeActivities($query)
    {
        return $query->where('type', 'activities');
    }

    /**
     * Query scope for news
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeNews($query)
    {
        return $query->where('type', 'news');
    }

    /**
     * Query scope for appointments
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeAppointments($query)
    {
        return $query->where('type', 'appointments')->orderby('data->start_date', 'DESC')->orderby('sort_order', 'DESC');
    }

    /**
     * Query scope for publications
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopePublications($query)
    {
        return $query->where('type', 'publications')->orderby('data->year', 'DESC')->orderby('sort_order', 'DESC');
    }

    /**
     * Query scope for affiliations
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeAffiliations($query)
    {
        return $query->where('type', 'affiliations');
    }

    /**
     * Query scope for support
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeSupport($query)
    {
        return $query->where('type', 'support');
    }

    /**
     * Query scope for projects
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeProjects($query)
    {
        return $query->where('type', 'projects');
    }

    /**
     * Query scope for presentations
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopePresentations($query)
    {
        return $query->where('type', 'presentations');
    }

    /**
     * Query scope for additionals
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeAdditionals($query)
    {
        return $query->where('type', 'additionals');
    }

    public function scopePublic($query){
        return $query->where('public', 1);
    }

    ///////////////
    // Relations //
    ///////////////

    /**
     * This belongs to one profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

}
