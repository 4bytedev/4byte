<?php

namespace Packages\Entry\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryDislike extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;

    public $primaryKey = null;

    protected $fillable = [
        'user_id',
        'entry_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }
}
