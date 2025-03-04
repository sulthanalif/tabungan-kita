@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('img/avatar.jpeg') }}" class="logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
