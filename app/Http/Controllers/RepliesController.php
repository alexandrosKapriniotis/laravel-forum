<?php

namespace App\Http\Controllers;

use App\Http\Forms\CreatePostForm;
use App\Models\Reply;
use App\Inspections\Spam;
use App\Models\Thread;
use App\Rules\Spamfree;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RepliesController extends Controller
{
    /**
     * RepliesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth',['except' => 'index']);
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    /**
     * @param $channelId
     * @param Thread $thread
     * @param CreatePostForm $form
     * @return Application|ResponseFactory|Model|Response
     * @throws Exception
     */
    public function store($channelId, Thread $thread, CreatePostForm $form)
    {
        if ($thread->locked){
            return response('Thread is locked',422);
        }

        return $thread->addReply([
            'body' => request()->input('body'),
            'user_id' => auth()->id()
        ])->load('owner');
    }

    /**
     * @param Reply $reply
     * @return Application|ResponseFactory|RedirectResponse|Response
     * @throws Exception
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update',$reply);

        $reply->delete();

        if (request()->expectsJson()){
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }

    /**
     * Update a thread's reply
     * @param Reply $reply
     * @param Spam $spam
     * @return bool|Application|ResponseFactory|Response
     * @throws AuthorizationException
     */
    public function update(Reply $reply,Spam $spam)
    {
        $this->authorize('update', $reply);

        try {
            $this->validate(request(), ['body' => 'required', new Spamfree]);

            return $reply->update(['body' => request('body')]);
        } catch (Exception $e) {
            return response(
                'Sorry, your reply could not be saved at this time.', 422
            );
        }

    }
}
