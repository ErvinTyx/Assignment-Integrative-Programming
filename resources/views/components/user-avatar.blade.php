@props(['user', 'size'=>'w-20 h-20'])

@if ($user->image)
    <img src="{{ $user->imageUrl() }}" alt="{{ $user->name }}" class="{{ $size }} object-cover rounded-lg">
@else
    <img src="{{ ('default-avatar.png') }}" alt="Dummy Avatar" class="{{ $size }} object-cover rounded-lg">
@endif
