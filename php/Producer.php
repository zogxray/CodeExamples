<?php

namespace App\Models\Producer;

use App\Models\Admin;
use App\Models\Advert;
use App\Models\Country;
use App\Models\User;
use App\Models\Work\Work;
use App\Repositories\ImageRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

class Producer extends Model
{
    use SoftDeletes;
    protected $table = 'producers';

    protected $fillable = [
        'views',
        'slug',
        'image',
        'banner',
        'product',
        'title',
        'about',
        'is_international',
        'is_active',
        'country_id',
        'owner_id',
    ];

    public $images = [
        'image' => [
            'square',
            'avatar'
        ],
        'banner' => [
            'banner'
        ],
        'product' => [
            'square'
        ]
    ];

    const PER_PAGE = 12;
    const MASTERS_PER_PAGE = 28;
    const PER_PAGE_IN_MASTER = 16;

    protected $appends = [
        'url',
        'works_count',
        'avatar_image_url',
        'square_product_url',
        'square_image_url',
        'banner_banner_url',
        'original_image_url',
        'original_product_url',
        'original_banner_url',
    ];

    /**
     * @return mixed
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(ProducerCategory::class, 'producer_to_category', 'producer_id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function works()
    {
        return $this->belongsToMany(Work::class, 'work_master_to_producer', 'producer_id', 'work_id');
    }

    public function masters()
    {
        return $this->belongsToMany(User::class, 'work_master_to_producer', 'producer_id', 'master_id')
            ->selectRaw('(
                        SELECT count(work_master_to_producer.id) 
                        FROM work_master_to_producer
                        WHERE work_master_to_producer.master_id = users.id
                        AND work_master_to_producer.producer_id = ' . $this->id. '
                        ) as uses')
            ->groupBy(['users.id', 'work_master_to_producer.master_id', 'work_master_to_producer.producer_id'])
            ->orderBy('users.total_rating', 'DESC')
            ->orderBy('uses', 'DESC');
    }

    public function simpleMasters()
    {
        return $this->belongsToMany(User::class, 'work_master_to_producer', 'producer_id', 'master_id');
    }

    public function posters()
    {
        return $this->belongsToMany(Advert::class, 'poster_to_producer', 'producer_id', 'poster_id');
    }


    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query, $request, $category)
    {
        return $query->where(function ($query) use ($request, $category) {
            if ($request->has('title') && !empty($request->get('title'))) {
                $query->where('slug', 'LIKE', '%'.str_slug($request->get('title'), '-').'%');
            }
            if ($request->has('category') && !empty($request->get('category'))) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('producer_categories.id', (int)$request->get('category'));
                });
            }
            if ($category) {
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('producer_categories.id', $category->id);
                });
            }
        });
    }

    public function scopeOrderByMasters($query) {
        return $query->orderBy(DB::raw('
                            (
                                select COUNT(DISTINCT master_id)
                                FROM work_master_to_producer wmtp
                                WHERE wmtp.producer_id = producers.id
                            )
                            '), 'DESC');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return route('producer::producer', $this->slug);
    }


    public function getWorksCountAttribute()
    {
        return $this->works()->count();
    }

    public function getWinnersCountAttribute()
    {
        return $this->works()->prisedOrShortlisted()->count();
    }

    public function getAvatarImageUrlAttribute()
    {
        return ImageRepository::getImageOrHolder($this, 'image', 'avatar', 'rectangular_picture_holder');
    }

    public function getSquareImageUrlAttribute()
    {
        return ImageRepository::getImageOrHolder($this, 'image', 'square', 'square_picture_holder');
    }

    public function getSquareProductUrlAttribute()
    {
        return ImageRepository::getImageOrHolder($this, 'product', 'square', 'square_picture_holder');
    }

    public function getBannerBannerUrlAttribute()
    {
        return ImageRepository::getImageOrHolder($this, 'banner', 'banner', 'rectangular_picture_holder');
    }


    public function getOriginalImageUrlAttribute()
    {
        return ImageRepository::getImageOrHolder($this, 'image', 'original', 'rectangular_picture_holder');
    }

    public function getOriginalProductUrlAttribute()
    {
        return ImageRepository::getImageOrHolder($this, 'product', 'original', 'rectangular_picture_holder');
    }

    public function getOriginalBannerUrlAttribute()
    {
        return ImageRepository::getImageOrHolder($this, 'banner', 'original', 'rectangular_picture_holder');
    }

}
