<?php

namespace App\Models\Work;

use App\Models\Admin;
use App\Models\Color;
use App\Models\Comment;
use App\Models\Currency;
use App\Models\GeneratingDiplomas;
use App\Models\JudgeRatings;
use App\Models\Option;
use App\Models\Producer\Producer;
use App\Models\Rating;
use App\Models\Shortlist;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ImageRepository;
use App\WorkGame\WorkGame;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Prize;

class Work extends Model
{
    use SoftDeletes;
    protected $table = 'works';
    protected $dates = ['created_at', 'updated_at', 'nominee_at'];

    //...

    /**
     * @param $query
     * @return mixed
     */
    public function scopeContest($query)
    {
        return $query->whereHas('category', function ($q) {
            $q->contest();
        })->notPrisedAndShortlisted();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeGallery($query)
    {
        return $query->whereHas('category', function ($q) {
            $q->gallery();
        })->notPrisedAndShortlisted();
    }

    /**
     * @param $query
     * @param $category
     * @param $user
     * @return mixed
     */
    public function scopeRandomWorksForBattles($query, $category, $user)
    {
        return $query->where('works.category_id', $category->id)
                ->where('works.user_id', '!=', $user->id)
                ->where(function ($query) use ($user) {
                    $query->where(function ($query) use ($user) {
                        $query->doesntHave('couplePoints');
                    }) ->orWhere(function ($query) use ($user) {
                        $query->whereDoesntHave('couplePoints', function ($q) use ($user) {
                            $q->where('work_couple_points.user_id', '=', $user->id);
                        });
                    });
                });
    }

    public function scopeOrderByRatingSum($query)
    {
        return $query->orderBy(DB::raw('
                            (
                                select COUNT(id)::float
                                FROM master_vote_points wmp
                                WHERE wmp.target_work_id = works.id
                            ) +
                            COALESCE((
                                select COUNT(id)::float
                                FROM work_couple_points wcp_one
                                WHERE wcp_one.work_id = works.id
                                AND wcp_one.vote = true
                            ) * 10 / NULLIF((
                                select COUNT(id)::float
                                FROM work_couple_points wcp_two
                                WHERE wcp_two.work_id = works.id
                            ), 0), 0)
                            '), 'DESC');
    }

    //...
}