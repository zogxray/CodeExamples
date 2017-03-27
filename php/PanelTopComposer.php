<?php

namespace App\Http\ViewComposers;

use App\Repositories\UserRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class PanelTopComposer
{
    private $userRepository;

    const CACHE_TIME = 480;

    /**
     * PanelTopComposer constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        $bottom_masters = Cache::remember('bottom_masters', self::CACHE_TIME, function() {
            return $this->userRepository->getTopTwelveMasters();
        });

        $view->with('bottom_masters', $bottom_masters);
    }
}