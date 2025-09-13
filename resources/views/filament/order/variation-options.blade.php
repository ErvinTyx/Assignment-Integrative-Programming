@php
    $record = $getRecord();

    // If $record is an array (Filament repeater), extract IDs; otherwise use accessor on the model.
    if (is_array($record)) {
        $ids = $record['variation_type_option_ids'] ?? null;
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }
        $ids = (array) $ids;
        $variationOptions = \App\Models\VariationTypeOption::with('variationType')
            ->whereIn('id', $ids)
            ->get();
    } else {
        // $record is an Eloquent model -> use accessor (which should return a Collection)
        $variationOptions = collect($record->variationOptions ?? []);
    }
@endphp

@if ($variationOptions->isNotEmpty())
    <ul class="list-disc ml-4">
        @foreach ($variationOptions as $option)
            <li>{{ $option->variationType->name ?? 'Type' }}: {{ $option->name }}</li>
        @endforeach
    </ul>
@else
    <span class="text-gray-500">No Variations</span>
@endif
