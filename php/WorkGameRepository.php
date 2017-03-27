<?php

namespace App\Repositories;

use App\Models\Course\Course;
use App\Models\JudgeRatings;
use App\Models\Prize;
use App\Models\Producer\Producer;
use App\Models\Shortlist;
use App\Models\User;
use App\Models\Work\Work;
use App\Models\Work\WorkCategory;
use App\Models\Work\WorkLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WorkGameRepository
{
    const PAGES_WORK_PER_PAGE = Work::PAGES_WORK_PER_PAGE;
    const HOME_WORK_PER_PAGE = Work::HOME_WORK_PER_PAGE;
    const PROFILE_WORK_PER_PAGE = Work::PROFILE_WORK_PER_PAGE;
    const PROFILE_COURSE_PER_PAGE = Course::PROFILE_COURSE_PER_PAGE;

    /**
     * @return \App\Models\Work\Work
     */
    public function getMonthlyMasteryWinner($category)
    {
        return Work::where('created_at', '>', Carbon::now()->subMonth()->toDateTimeString())
            ->where('category_id', $category->id)
            ->orderByMastery()
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * @return \App\Models\Work\Work
     */
    public function getMonthlyDesignWinner($category)
    {
        return Work::where('created_at', '>', Carbon::now()->subMonth()->toDateTimeString())
            ->where('category_id', $category->id)
            ->orderByDesign()
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * @param $master
     * @param int $take
     * @return \App\Models\Work\Work
     */
    public function getVotedWorksForMaster($master, $take = self::PROFILE_WORK_PER_PAGE)
    {
        return Work::with(Work::getWorkRelations())
            ->where('user_id', '!=', $master->id)
            ->where(function ($query) use ($master) {
                $query->whereHas('customer_votes', function ($query) use ($master) {
                    $query->where('user_id', $master->id);
                });
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($take);
    }

    /**
     * @param $school
     * @param int $take
     * @return \App\Models\Work\Work
     */
    public function getWinnersWorksForSchool($school, $take = self::PROFILE_WORK_PER_PAGE)
    {
        $learners_ids = DB::table('school_to_learner')
            ->select('learner_id')
            ->where('status', User::STATUS_MUTUALLY)
            ->where('school_id', '=', $school->id);

        $teachers_ids = DB::table('school_to_teacher')
            ->select('teacher_id')
            ->where('status', User::STATUS_MUTUALLY)
            ->where('school_id', '=', $school->id)
            ->union($learners_ids)
            ->pluck('teacher_id');

        return Work::with(Work::getWorkRelations())
            ->whereIn('user_id', $teachers_ids)
            ->where(function ($query) {
                $query->has('prizes')->orHas('shortlists');
            })
            ->orderByRatingSum()
            ->paginate($take);
    }

   //...

}