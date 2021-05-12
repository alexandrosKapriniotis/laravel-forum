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
use Illuminate\Http\Response;

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
     * @return Model
     * @throws Exception
     */
    public function store($channelId, Thread $thread, CreatePostForm $form): Model
    {
        $reply = Reply::create([
            'body' => request()->input('body'),
            'user_id' => auth()->id(),
            'thread_id' => $thread->id
        ]);

        $reply = $thread->addReply($reply);

        return $reply->load('owner');
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
