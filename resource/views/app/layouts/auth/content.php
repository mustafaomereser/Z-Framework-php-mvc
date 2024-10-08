<?php if (\zFramework\Core\Facades\Auth::check()) : ?>
    <div class="d-flex align-items-center gap-2">
        <span><?= \zFramework\Core\Facades\Auth::user()['username'] ?></span>
        <button class="btn btn-sm btn-outline-light border" onclick="$.system.signout(this);">{{ _l('lang.signout') }}</button>
    </div>
<?php else : ?>
    <button class="btn btn-sm btn-outline-light border" data-modal="{{ route('auth-form') }}">{{ _l('lang.signin') }}</button>
<?php endif ?>