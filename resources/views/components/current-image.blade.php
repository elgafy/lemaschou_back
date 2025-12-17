@php
    $record = $getRecord();
@endphp

@if ($record && $record->image)
<label for="desktop-image">Desktop Image:</label>

    <img src="{{ $record->image }}"
         alt="Current Image"
         style="max-width: 250px; max-height: 250px; object-fit: cover;">
@else
    <p>No image uploaded.</p>
@endif
