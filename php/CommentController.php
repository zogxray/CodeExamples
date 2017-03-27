<?php

namespace App\Http\Controllers\Front;


use App\Events\JobRatingEvent;
use App\Events\WorkAttentionEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Advert;
use App\Models\Alerts;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Course\Course;
use App\Models\Event;
use App\Models\University\University;
use App\Models\Work\Work;
use App\Repositories\CommentRepository;
use App\Repositories\ImageRepository;
use App\Repositories\PointsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public $pointsRepository;
    public $commentRepository;
    public $imageRepository;
    /**
     * CommentController constructor.
     * @param PointsRepository $pointsRepository
     * @param CommentRepository $commentRepository
     */
    public function __construct(PointsRepository $pointsRepository, CommentRepository $commentRepository, ImageRepository $imageRepository)
    {
        $this->pointsRepository = $pointsRepository;
        $this->commentRepository = $commentRepository;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComments(Request $request) {
        $parent_id = $request->get('parent_id');
        $parent_type = $request->get('parent_type');
        $comment_type = $request->get('comment_type');
        switch ($comment_type) {
            case 'questions':
                $type = 'questions';
                break;
            case 'reviews':
                $type = 'reviews';
                break;
            case 'comments':
                $type = 'comments';
                break;
            default:
                $type = 'comments';
        }

        switch ($parent_type) {
            case 'work':
                $item = Work::findOrFail($parent_id);
                break;
            case 'blog':
                $item = Blog::findOrFail($parent_id);
                break;
            case 'comment':
                $item = Comment::findOrFail($parent_id);
                break;
            case 'university':
                $item = University::findOrFail($parent_id);
                break;
            case 'course':
                $item = Course::findOrFail($parent_id);
                break;
            case 'event':
                $item = Event::findOrFail($parent_id);
                break;
            case 'poster':
                $item = Advert::findOrFail($parent_id);
                break;
            default:
                $item = Comment::findOrFail($parent_id);
        }

        $items = $item->$type()->with('user')->with('comments.user')->paginate(Comment::PER_PAGE);
        return response()->json(compact('parent','items'));
    }

    /**
     * @param CommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeComment(CommentRequest $request)
    {
        $parent_id = (int)$request->get('parent_id');

        switch ($request->get('parent_type')) {
            case 'work':
                $item = Work::findOrFail($parent_id);
                break;
            case 'blog':
                $item = Blog::findOrFail($parent_id);
                break;
            case 'comment':
                $item = Comment::findOrFail($parent_id);
                break;
            case 'university':
                $item = University::findOrFail($parent_id);
                break;
            case 'course':
                $item = Course::findOrFail($parent_id);
                break;
            case 'event':
                $item = Event::findOrFail($parent_id);
                break;
            case 'poster':
                $item = Advert::findOrFail($parent_id);
                break;
            default:
                $item = Comment::findOrFail($parent_id);
        }

        $comment = new Comment();

        $rating = $request->has('rating') ? $request->get('rating') : null;
        $is_question = $request->has('is_question') ? true : false;

        $comment->fill(
            [
                'comment' => $request->get('comment'),
                'user_id' => Auth::user()->id,
                'is_question' => $is_question,
                'rating' => $rating
            ]
        );

        $item->comments()->save($comment);

        if($request->has('image'))
        {
            $image = json_decode(json_encode($request->get('image')), FALSE);
            $unique = md5(Carbon::now());

            if($image && $image->original && $image->cropped)
            {
                $this->imageRepository->imageStore($comment, $image->original, $image->cropped, $unique, 'image');
                $comment->update([
                    'image' => $unique,
                ]);
            }
        }

        $comment->load(['user', 'comments']);
        Auth::user()->increment('total_comments');

        return response()->json(compact('comment'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParentRating(Request $request)
    {
        $parent_id = (int)$request->get('parent_id');

        switch ($request->get('parent_type')) {
            case 'university':
                $item = University::findOrFail($parent_id)->rating;
                break;
            case 'course':
                $item = Course::findOrFail($parent_id)->rating;
                break;
            default:
                $item = Comment::findOrFail($parent_id)->rating;
        }

        return response()->json(compact('item'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $comment = Comment::where(['id' => $id, 'user_id' => $user->id])->firstOrFail();
        if($comment->comments) {
            foreach($comment->comments as $child) {
                $child->delete();
            }
        }
        $comment->delete();
        $messages = [
            'deleted' => trans('works.commentDelete'),
        ];
        return response()->json(['success' => true, 'messages' => $messages]);
    }
}
