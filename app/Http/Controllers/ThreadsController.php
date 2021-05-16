<?php

namespace App\Http\Controllers;

use App\Inspections\Spam;
use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;
use App\Filters\ThreadFilters;
use App\Rules\Spamfree;
use App\Trending;
use Carbon\Carbon;
use Exception;
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
use Illuminate\Support\Facades\Redis;
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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Spam $spam
     * @return Application|Redirector|RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request,Spam $spam)
    {
        $this->validate($request, [
            'title' => 'required', new Spamfree,
            'body' => 'required', new Spamfree,
            'channel_id' => 'required|exists:channels,id'
        ]);

        $thread = Thread::create([
            'user_id' => Auth::id(),
            'channel_id' => $request->channel_id,
            'title' => $request->title,
            'body'  => $request->body
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
        $key = sprintf("users.%s.visits.%s",auth()->id(),$thread->id);

        cache()->forever($key, Carbon::now());

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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Thread $thread
     * @return Response
     */
    public function update(Request $request, Thread $thread)
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
