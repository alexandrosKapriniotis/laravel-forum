@extends('layouts.app')

@section('content')
    <main class="container mx-auto mt-10">
        <div class="row">

            @if (session('status'))
                <div class="text-sm border border-t-8 rounded text-green-700 border-green-600 bg-green-100 px-3 py-4 mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="col-md-8 col-md-offset-2">
                <header class="font-semibold bg-gray-200 text-gray-700 py-5 px-6 sm:py-6 sm:px-8 sm:rounded-t-md">
                    Forum threads
                </header>

                @foreach($threads as $thread)
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ $thread->path() }}">
                                {{ $thread->title }}
                            </a>
                        </div>

                        <div class="card-body">
                            {{ $thread->body }}
                        </div>
                    </div>
                <hr>
                @endforeach
            </div>
        </div>
    </main>
@endsection
