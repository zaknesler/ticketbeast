@component('mail::message')
# Your Invitation

You have been invited to join Ticketbeast! Please click the button below to set up your account.

@component('mail::button', ['url' => route('invitations.show', $invitation->code)])
Create account
@endcomponent
@endcomponent
