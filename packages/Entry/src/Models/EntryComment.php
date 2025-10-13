<?php

namespace Packages\Entry\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'parent_id',
        'entry_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(EntryComment::class, 'parent_id');
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }
}
