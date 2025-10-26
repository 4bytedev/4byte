@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@php
$logo = App\Services\SettingsService::getSiteSettingsField('light_logo');
@endphp
@if (isset($logo))
<img src="{{ Illuminate\Support\Facades\Storage::url($logo) }}" class="logo" alt="Laravel Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
