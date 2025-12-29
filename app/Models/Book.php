<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

use function Symfony\Component\Clock\now;

class Book extends Model
{
    //
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, $title): Builder | QueryBuilder
    {
        return $query->where('title', 'like', "% {$title}%");
    }

    public function scopeWithReviewCount(Builder $query, $from = null, $to = null): Builder | QueryBuilder
    {
        return $query->withCount([
            'reviews' => function (Builder $q) use ($from, $to) {
                $this->dateRangeFilter($q, $from, $to);
            }
        ]);
    }

    public function scopeWithAvgRating(Builder $query, $from = null, $to = null): Builder | QueryBuilder
    {
        return $query->withAvg([
            'reviews' => function (Builder $q) use ($from, $to) {
                $this->dateRangeFilter($q, $from, $to);
            }
        ], 'rating');
    }

    public function scopePopular(Builder $query, $from = null, $to = null): Builder | QueryBuilder
    {
        return $query->withReviewCount()
            ->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder | QueryBuilder
    {
        return $query->withAvgRating()->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $minreviews): Builder | QueryBuilder
    {
        return $query->having('reviews_count', '>=', $minreviews);
    }

    private function dateRangeFilter(Builder $query, $from = null, $to = null): Builder
    {
        if ($from && !$to) {
            $query->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
        return $query;
    }

    public function scopePopularLastMonth(Builder $query): Builder | QueryBuilder
    {
        return $query->popular(Carbon::now()->subMonth(), now())
            ->highestRated(Carbon::now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopePopularLast6Months(Builder $query): Builder | QueryBuilder
    {
        return $query->popular(Carbon::now()->subMonths(6), now())
            ->highestRated(Carbon::now()->subMonths(6), now())
            ->minReviews(5);
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder | QueryBuilder
    {
        return $query->highestRated(Carbon::now()->subMonth(), now())
            ->popular(Carbon::now()->subMonth(), now())
            ->minReviews(5);
    }

    public function scopeHighestRatedLast6Months(Builder $query): Builder | QueryBuilder
    {
        return $query->highestRated(Carbon::now()->subMonths(6), now())
            ->popular(Carbon::now()->subMonths(6), now())
            ->minReviews(5);
    }


    protected static function booted()
    {
        // Invalidate cache for the associated book when a new review is created
        static::updated(fn(Book $book) => cache()->forget('book:' . $book->id));
        static::deleted(fn(Book $book) => cache()->forget('book:' . $book->id));
    }
}

// \App\Models\Book::popular('2024-01-01','2024-04-20')->get();
