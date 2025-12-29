@extends('layouts.app')

@section('content')
    <h1 class="mb-10 text-2xl">Write a Review for "{{ $book->title }}"</h1>

    <form method="POST" action ="{{ route('books.reviews.store', $book) }}" class="mb-6">
        @csrf

        <div class="mb-4">
            <label for="rating" class="block mb-2 font-bold">Rating:</label>
            <select name="rating" id="rating" class="input w-full">
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ $i }}
                    </option>
                @endfor
            </select>
            @error('rating')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="review" class="block mb-2 font-bold">Review:</label>
            <textarea name="review" id="review" rows="4" class="input w-full">{{ old('review') }}</textarea>
            @error('review')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn">Submit Review</button>
    </form>
