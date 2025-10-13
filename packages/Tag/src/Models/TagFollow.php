<?php

namespace Packages\Tag\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagFollow extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;

    public $primaryKey = null;

    protected $fillable = [
        'user_id',
        'tag_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
