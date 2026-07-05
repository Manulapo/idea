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

    public static function statusCounts()
    {
        /** @var User $user */
        $user = Auth::user();
        // select the count of each statuses
        $count = $user->ideas()
            ->selectRaw('status,count(*) as count') // select status and their count from DB
            ->groupBy('status') // group all the results by statuses
            ->pluck('count', 'status'); // take the count value and assign the "status" key

        // return a collection
        return collect(IdeaStatus::cases())->mapWithKeys(fn ($status) => [
            $status->value => $count->get($status->value, 0),
        ])->put('all', $user->ideas()->count());
    }

    public function formattedDescription(): Attribute
    {
        // this is a getter for the description attribute, it allows you to format the description before returning it. in this case, we are just returning the description as is, but you can add any formatting you want here.
        return Attribute::get(fn ($value, $attributes) => str($attributes['description'])->markdown());
    }
}
