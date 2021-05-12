@extends('layouts.app')

@section('content')
    <main class="container mx-auto mt-10">
        <div class="row">

            @if (session('status'))
                <div class="text-sm border border-t-8 rounded text-green-700 border-green-600 bg-green-100 px-3 py-4 mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="col-md-8 col-md-offset-2 mt-4">
                @forelse($threads as $thread)
                    <div class="card">
                        <div class="card-header">
                            <div class="level">
                                <h4 class="flex">
                                    <a href="{{ $thread->path() }}">
                                        @if (auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                                           <strong>
                                               {{ $thread->title }}
                                           </strong>
                                        @else
                                            {{ $thread->title }}
                                        @endif
                                    </a>
                                </h4>

                                <a href="{{ $thread->path() }}" style="margin-left: 5px">
                                    {{ $thread->replies_count }} {{ \Illuminate\Support\Str::plural('reply',$thread->replies_count) }}
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            {{ $thread->body }}
                        </div>
                    </div>
                <hr>
                @empty
                    <p>There no relevant results at this time</p>
                @endforelse
            </div>
        </div>
    </main>
@endsection
