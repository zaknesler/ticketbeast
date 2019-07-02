@component('mail::message')
# New Message

{{ $attendeeMessage->body }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
