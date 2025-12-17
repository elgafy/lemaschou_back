@php
    $record = $getRecord();
@endphp

@if ($record && $record->image_mobile)
<label for="mobile-image">Mobile Image:</label>

    <img src="{{ $record->image_mobile }}"
         alt="Current Image"
         style="max-width: 250px; max-height: 250px; object-fit: cover;">
@else
    <p>No image uploaded.</p>
@endif
