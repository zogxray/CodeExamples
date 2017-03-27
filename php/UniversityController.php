<?php

namespace App\Http\Controllers\Front\University;

use App\Http\Requests\UpdateUniversityRequest;
use App\Http\Requests\UpdateUniversityVideoRequest;
use App\Models\Comment;
use App\Http\Requests\StoreUniversityRequest;
use App\Models\Currency;
use App\Models\University\University;
use App\Models\University\UniversityCategories;
use App\Models\University\UniversityGallery;
use App\Models\User;
use App\Repositories\ImageRepository;
use App\Repositories\UniversityRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UniversityController extends Controller
{

    private $universityRepository;
    private $imageRepository;

    /**
     * UniversityController constructor.
     * @param ImageRepository $imageRepository
     * @param UniversityRepository $universityRepository
     */
    public function __construct(
        ImageRepository $imageRepository, UniversityRepository $universityRepository
    )
    {
        $this->universityRepository = $universityRepository;
        $this->imageRepository = $imageRepository;
    }

//  ...

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function universities(Request $request) {
        $categories = $this->universityRepository->getCategories();
        $universities = University::with(['user', 'language'])
            ->filter($request)
            ->has('lessons')
            ->orderBy('id', 'DESC')
            ->paginate(University::PER_PAGE);
        $authors = User::whereIn('users.id', $universities->pluck('user_id')->unique()->toArray())->get();
        if ($request->ajax()) {
            return response()->json(compact(
                'categories',
                'universities'
            ));
        }

        return view('front.university.index', compact(
            'categories',
            'universities',
            'authors'
        ));
    }
}
