<?php

namespace App\Console\Commands;

use App\Events\JobRatingEvent;
use App\Models\Role;
use App\Models\Shortlist;
use App\Models\Work\WorkCategory;
use App\Models\Work\WorkLevel;
use App\Repositories\WorkRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonthlyShortlist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shortlist:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create monthly shortlist';


    public $workRepository;
    /**
     * MonthlyShortlist constructor.
     * @param WorkRepository $workRepository
     */
    public function __construct(WorkRepository $workRepository)
    {
        parent::__construct();
        $this->workRepository = $workRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $categories = WorkCategory::active()->contest()->get();
        foreach ($categories as $category) {
            $works = $this->workRepository->getThreeDayPrizedWorksToMonthlyShortlist($category);
            $shortlist = Shortlist::create(['type' => Shortlist::MONTHLY_SHORTLIST, 'category_id' => $category->id]);
            if (count($works)) {
                $works->each(function ($work) use ($shortlist) {
                    $work->update(['level_id' => WorkLevel::where('name', WorkLevel::LEVEL_SHORTLIST)->value('id')]);
                    $work->user->increment('total_shortlists');
                    $work->user->roles()->sync([Role::where('role', Role::SHORT_LIST)->value('id') => ['expires' => Carbon::now()->addMonth()]], false);
                    $shortlist->works()->attach($work->id, ['awarded_rating' => $work->overall_rating_sum]);
                });
                $this->info("Shortlists was created.");
            } else {
                $this->error("Not works to add.");
            }
        }
    }
}
