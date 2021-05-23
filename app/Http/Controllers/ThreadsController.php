<?php

namespace App\Http\Controllers;

use App\Inspections\Spam;
use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;
use App\Filters\ThreadFilters;
use App\Rules\Recaptcha;
use App\Rules\Spamfree;
use App\Trending;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ThreadsController extends Controller
{
    /**
     * ThreadsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index','show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Channel $channel
     * @param ThreadFilters $filters
     * @param Trending $trending
     * @return Application|Factory|View|Response
     */
    public function index(Channel $channel, ThreadFilters $filters,Trending $trending)
    {
        $threads = $this->getThreads($filters, $channel);

        if (request()->wantsJson()){
            return $threads;
        }

        return view('threads.index',[
            'threads'  => $threads,
            'trending' => $trending->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * @param Channel $channel
     * @param Thread $thread
     * @throws AuthorizationException
     */
    public function update(Channel $channel, Thread $thread)
    {
        $this->authorize('update',$thread);

        $thread->update(request()->validate([
            'title' => 'required',
            'body'  => 'required'
        ]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Recaptcha $recaptcha
     * @return Application|Redirector|RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request,Recaptcha $recaptcha)
    {
        $this->validate($request, [
            'title' => 'required', new Spamfree,
            'body' => 'required', new Spamfree,
            'channel_id' => 'required|exists:channels,id',
            'g-recaptcha-response'  => 'required',$recaptcha
        ]);

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response'  => $request->input('g-recaptcha-response'),
            'remoteip'  => request()->ip()
        ]);

        $thread = Thread::create([
            'user_id' => Auth::id(),
            'channel_id' => $request->channel_id,
            'title' => $request->title,
            'body'  => $request->body,
            'slug'  => $request->title
        ]);

        return redirect($thread->path())
            ->with('flash','Your thread has been published');
    }

    /**
     * Display the specified resource.
     *
     * @param  $channelId
     * @param Thread $thread
     * @param Trending $trending
     * @return Application|Factory|View
     * @throws Exception
     */
    public function show($channelId,Thread $thread,Trending $trending)
    {
        if(auth()->check()){
            $key = sprintf("users.%s.visits.%s",auth()->id(),$thread->id);

            cache()->forever($key, Carbon::now());
        }

        $trending->push($thread);

        $thread->increment('visits');

        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Thread $thread
     * @return Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $channel
     * @param Thread $thread
     * @return Application|ResponseFactory|RedirectResponse|Response|Redirector
     * @throws Exception
     */
    public function destroy($channel,Thread $thread)
    {
        $this->authorize('update',$thread);

        $thread->delete();

        if (request()->wantsJson()){
            return response([],204);
        }

        return redirect('/threads');
    }

    /**
     * @param ThreadFilters $filters
     * @param Channel $channel
     * @return mixed
     */
    public function getThreads(ThreadFilters $filters, Channel $channel)
    {
        $threads = Thread::latest()->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        return $threads->paginate(5);
    }


}
