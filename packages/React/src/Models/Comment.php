<?php

namespace Packages\React\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Packages\React\Services\ReactService;
use Packages\React\Traits\HasCacheKey;
use Packages\React\Traits\HasLikes;

class Comment extends Model
{
    use HasCacheKey;
    use HasFactory;
    use HasLikes;

    protected $fillable = [
        'content',
        'user_id',
        'parent_id',
        'commentable_type',
        'commentable_id',
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function repliesCount()
    {
        return app(ReactService::class)->getCommentRepliesCount($this->commentable_type, $this->commentable_id, $this->id);
    }
}
