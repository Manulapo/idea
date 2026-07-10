<?php

declare(strict_types=1);

namespace App\Models;

use App\IdeaStatus;
use Database\Factories\IdeaFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Idea extends Model
{
    /** @use HasFactory<IdeaFactory> */
    use HasFactory;

    // protected casting = it allows you to automatically convert attributes to a specific data type when you access them. For example, if you have a JSON column in your database, you can cast it to an array or object when you retrieve it from the database.
    protected $casts = [
        'links' => AsArrayObject::class,
        'status' => IdeaStatus::class,
    ];

    // this appends the status attribute to the model's array and JSON representations, so that it can be accessed like any other attribute. (even for factories)
    protected $attributes = [
        'status' => IdeaStatus::PENDING->value,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public static function statusCounts(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        $teamIds = $user->teams()->pluck('teams.id');

        $visibleIdeas = Idea::where(function ($query) use ($user, $teamIds) {
            $query->where('user_id', $user->id); // only include ideas created by the user

            if ($teamIds->isNotEmpty()) {
                $query->orWhereIn('team_id', $teamIds); // only include ideas from teams the user is a member of
            }
        });

        $countsByStatus = (clone $visibleIdeas)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status'); // this is where actually we run the query and get the counts by status as a collection where the keys are the status values and the values are the counts.

        return collect(IdeaStatus::cases())
            ->mapWithKeys(fn ($status) => [
                $status->value => (int) $countsByStatus->get($status->value, 0),
            ])
            ->put('all', (clone $visibleIdeas)->count()) // this is where we run the query again to get the total count of visible ideas and add it to the collection with the key 'all'.
            ->put('my-ideas', $user->ideas()->count());
    }

    public function formattedDescription(): Attribute
    {
        // this is a getter for the description attribute, it allows you to format the description before returning it. in this case, we are just returning the description as is, but you can add any formatting you want here.
        return Attribute::get(fn ($value, $attributes) => str($attributes['description'])->markdown());
    }
}
