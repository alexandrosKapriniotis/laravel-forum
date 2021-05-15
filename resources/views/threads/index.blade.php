@extends('layouts.app')

@section('content')
    <main class="container mx-auto mt-10">
        <div class="row">

            @if (session('status'))
                <div class="text-sm border border-t-8 rounded text-green-700 border-green-600 bg-green-100 px-3 py-4 mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="col-md-8">
                @include('threads.list')

                {{ $threads->withQueryString()->links() }}
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Trending Threads</h4>
                    </div>

                    <div class="card-body">
                        <ul>
                            @foreach($trending as $thread)
                                <li>
                                    <a href="{{ $thread['path'] }}">{{ $thread['title'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
