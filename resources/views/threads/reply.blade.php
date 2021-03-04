<div class="card mt-2 mb-2">
    <div class="card-header">
        <a href="#" class="card-title">
            {{ $reply->owner->name }}
        </a> said {{ $reply->created_at->diffForHumans() }}...
    </div>

    <div class="card-body">
        {{ $reply->body }}
    </div>
</div>
