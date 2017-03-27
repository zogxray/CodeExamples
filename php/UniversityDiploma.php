<?php

namespace App\Models\University;

use App\Models\User;
use App\Repositories\ImageRepository;
use Illuminate\Database\Eloquent\Model;

class UniversityDiploma extends Model
{
    protected $table = 'university_diplomas';
    protected $fillable = [
        'university_id',
        'user_id',
        'image',
    ];

    public $appends = ['original_diploma_url', 'small_diploma_url'];

    public $images = [
        'image' => [
            'smallUniversityDiploma',
        ]
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getOriginalDiplomaUrlAttribute()
    {
        return ImageRepository::getImageOrHolder($this, 'image', 'original', 'poster_image_holder');
    }

    public function getSmallDiplomaUrlAttribute()
    {
        return ImageRepository::getImageOrHolder($this, 'image', 'small', 'poster_image_holder');
    }

}
