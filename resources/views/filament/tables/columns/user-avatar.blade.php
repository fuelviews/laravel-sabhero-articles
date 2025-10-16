@php
    $record = $getState();
@endphp

<div style="display: flex; justify-content: center; align-items: center; width: 100%; margin-right: 2rem;">
    <img
        src="{{ $record->avatar }}"
        alt="{{ $record->{config('sabhero-articles.user.columns.name')} }}"
        style="width: 1.75rem; height: 1.75rem; border-radius: 9999px;">
</div>
