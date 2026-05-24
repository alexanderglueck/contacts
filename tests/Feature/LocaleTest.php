<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_set_locale_middleware_applies_the_users_locale()
    {
        $user = create(User::class, ['locale' => 'de']);

        $this->actingAs($user)->get(route('home'));

        $this->assertSame('de', App::getLocale());
    }

    #[Test]
    public function it_falls_back_to_app_default_when_user_locale_is_not_in_allowlist()
    {
        $user = create(User::class, ['locale' => 'fr']);

        $this->actingAs($user)->get(route('home'));

        // 'fr' is intentionally not in config('app.available_locales');
        // SetLocale should leave App::getLocale() at the default rather
        // than honor an out-of-allowlist value.
        $this->assertSame(config('app.locale'), App::getLocale());
    }

    #[Test]
    public function the_profile_update_persists_the_locale_change()
    {
        $user = $this->createUser();

        $this->put(route('user_settings.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'locale' => 'de',
        ])->assertRedirect();

        $this->assertSame('de', $user->fresh()->locale);
    }

    #[Test]
    public function the_profile_update_rejects_a_locale_outside_the_allowlist()
    {
        $user = $this->createUser();

        $this->put(route('user_settings.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'locale' => 'pirate',
        ])->assertSessionHasErrors('locale');

        // Whatever the locale was before — null on a freshly-factoried
        // user, the table default 'en' after a round-trip — it must not
        // be the out-of-allowlist value we just tried to submit.
        $this->assertNotSame('pirate', $user->fresh()->locale);
    }
}
