{{-- Spam trap for public forms (see App\Support\SpamGuard). Hidden from
     people and screen readers; bots that fill every field get filtered. --}}
<div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;">
    <label for="{{ \App\Support\SpamGuard::FIELD }}">Website</label>
    <input type="text" id="{{ \App\Support\SpamGuard::FIELD }}" name="{{ \App\Support\SpamGuard::FIELD }}" value="" tabindex="-1" autocomplete="off">
</div>
<input type="hidden" name="{{ \App\Support\SpamGuard::TIME_FIELD }}" value="{{ time() }}">
