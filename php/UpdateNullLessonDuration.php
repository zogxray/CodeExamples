<?php

namespace App\Console\Commands;

use App\Models\University\University;
use App\Models\University\UniversityLessons;
use Illuminate\Console\Command;
use Vinkla\Vimeo\VimeoManager;

class UpdateNullLessonDuration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lesson:duration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update null lesson duration';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $vimeoManager;

    public function __construct(VimeoManager $vimeoManager)
    {
        parent::__construct();
        $this->vimeoManager = $vimeoManager;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lessons = UniversityLessons::whereNull('duration')->get();
        $bar = $this->output->createProgressBar(count($lessons));
        foreach ($lessons as $lesson) {
            $status = $this->vimeoManager->request('/videos/'.$lesson->video, [], 'GET');
            if($status['body']['error']) {
                $this->error($status['body']['error']);
                die();
            }
            if($status['body']['status'] == 'available') {
                $lesson->update(['duration' => gmdate("H:i:s", $status['body']['duration'])]);
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info("Update lessons: ".count($lessons));
    }
}
