@extends('layouts.app')

@section('content')
    <thread-view :thread="{{ $thread }}" :initial-replies-count="{{ $thread->replies_count }}" inline-template>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="level">
                                <img src="{{ asset($thread->creator->avatar_path) }}" alt="{{ $thread->creator->name }}" width="25" height="25">

                                <span class="flex">
                                    <a href="/profiles/{{ $thread->creator->name }}">
                                        {{ $thread->creator->name }} posted:
                                    </a>
                                </span>

                                @can ('update', $thread)
                                    <form action="{{ $thread->path() }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link">DELETE THREAD</button>
                                    </form>
                                @endcan
                            </div>

                            {{ $thread->title }}
                        </div>

                        <div class="card-body">
                            {{ $thread->body }}
                        </div>
                    </div>

                    <replies @added="repliesCount++" @removed="repliesCount--" ></replies>

                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <p>
                                This thread was published {{ $thread->created_at->diffForHumans() }} by
                                <a href="#">{{ $thread->creator->name }}</a>, and currently
                                has <span v-text="repliesCount"></span> {{ \Illuminate\Support\Str::plural('comment',$thread->replies_count) }}.
                            </p>

                            <subscribe-button :active="{{ $thread->isSubscribedTo ? 'true' : 'false' }}" v-if="signedIn"></subscribe-button>

                            <button class="btn btn-default" v-if="authorize('isAdmin')" @click="toggleLock" v-text="locked ? 'Unlock' : 'Lock' ">Lock</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection
