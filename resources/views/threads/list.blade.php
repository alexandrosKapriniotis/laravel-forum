@forelse($threads as $thread)
    <div class="card">
        <div class="card-header">
            <div class="level">
                <div class="flex">
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

                    <h5>Posted By: <a href="#">{{ $thread->creator->name }}</a></h5>
                </div>

                <a href="{{ $thread->path() }}" style="margin-left: 5px">
                    {{ $thread->replies_count }} {{ \Illuminate\Support\Str::plural('reply',$thread->replies_count) }}
                </a>
            </div>
        </div>

        <div class="card-body">
            {{ $thread->body }}
        </div>

        <div class="card-footer">
            {{ $thread->visits }} Visits
        </div>
    </div>
    <hr>
@empty
    <p>There no relevant results at this time</p>
@endforelse
