<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Worker portal pages with the admin API faked.
 */
class PortalTest extends TestCase
{
    private const ADMIN = 'https://admin.test';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.admin.url' => self::ADMIN, 'services.admin.key' => 'secret-key']);
    }

    private function profile(array $over = []): array
    {
        return array_replace_recursive([
            'id' => 7, 'passport_number' => 'AA1234567', 'prefix' => 'Mr', 'name' => 'MYO MIN AUNG', 'full_name' => 'Mr MYO MIN AUNG',
            'sex' => 'male', 'sex_label' => 'ชาย', 'birthday' => '1997-12-06', 'birthday_text' => '06/12/1997', 'age_text' => '28 ปี 10 เดือน', 'labour_number' => 'L-1',
            'nationality' => ['code' => 'myanmar', 'name_th' => 'พม่า', 'name_en' => 'Myanmar'],
            'status' => ['enabled' => true, 'job' => 'job', 'active' => true, 'label_th' => 'ทำงาน', 'label_en' => 'Employed', 'job_date_start' => '2025-01-01', 'job_date_start_text' => '01/01/2025', 'resign_date' => null, 'escape_date' => null],
            'employer' => ['id' => 1, 'name' => 'แสงลินฟู้ดส์', 'name_en' => 'Saeng Lin Foods', 'type' => 'อาหาร', 'type_en' => 'Food', 'phone' => '02-123-4567', 'contact' => 'คุณเก๋', 'address' => ['line' => '123/45', 'district' => 'คลองเตย', 'amphur' => 'คลองเตย', 'province' => 'กรุงเทพมหานคร', 'zipcode' => '10110', 'text_th' => '123/45 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพมหานคร 10110', 'text_en' => '123/45 Sukhumvit Rd, Khlong Toei, Bangkok 10110']],
            'agency' => ['id' => 1, 'name' => 'TOP MANPOWER'],
            'passport' => ['number' => 'AA1234567', 'date_start' => null, 'date_start_text' => '-', 'date_end' => '2028-12-15', 'date_end_text' => '15/12/2028', 'days_left' => 450, 'level' => 'ok', 'warn_days' => 90],
            'visa' => ['number' => 'V-1', 'date_in' => null, 'date_in_text' => '-', 'date_start' => null, 'date_start_text' => '-', 'date_end' => null, 'date_end_text' => '-', 'days_left' => null, 'level' => 'none', 'warn_days' => 90],
            'work_permit' => ['number' => '1234-5678-90', 'labour_number' => '', 'date_start' => '2025-01-01', 'date_start_text' => '01/01/2025', 'date_end' => '2026-12-31', 'date_end_text' => '31/12/2026', 'days_left' => 65, 'level' => 'ok', 'warn_days' => 30],
            'ninety_day' => ['tm_number' => '', 'date_start' => null, 'date_start_text' => '-', 'date_end' => '2026-12-15', 'date_end_text' => '15/12/2026', 'days_left' => 45, 'level' => 'ok', 'warn_days' => 15],
            'alerts' => [['type' => 'ninety_day', 'label_th' => 'รายงานตัว 90 วัน', 'label_en' => '90-Day Report', 'date_end' => '2026-12-15', 'date_end_text' => '15/12/2026', 'days_left' => 10, 'level' => 'warning']],
            'documents' => [
                ['key' => '12', 'name' => 'Passport', 'file_name' => 'AA1234567_Passport.pdf', 'ext' => 'pdf', 'is_image' => false, 'size' => 120000, 'size_text' => '117 KB', 'updated_at' => null],
                ['key' => 'legacy-labour_file_work', 'name' => 'Work Permit', 'file_name' => 'AA1234567_Work.jpg', 'ext' => 'jpg', 'is_image' => true, 'size' => 0, 'size_text' => '-', 'updated_at' => null],
            ],
            'generated_at' => '2026-10-27T10:00:00+07:00',
        ], $over);
    }

    /** Http::fake() keeps the first stub registered per URL, so overrides are merged in before faking. */
    private function loginOk(array $overrides = []): void
    {
        Http::fake(array_replace([
            self::ADMIN . '/api/portal/login' => Http::response(['ok' => true, 'labour_id' => 7, 'profile' => $this->profile()]),
            self::ADMIN . '/api/portal/labours/7' => Http::response(['ok' => true, 'profile' => $this->profile()]),
            self::ADMIN . '/api/portal/labours/7/documents/12/link' => Http::response(['ok' => true, 'key' => '12', 'name' => 'Passport', 'file_name' => 'x.pdf', 'url' => self::ADMIN . '/portal-file/7/12?expires=1&signature=abc', 'expires_at' => '2026-10-27T10:15:00+07:00']),
            self::ADMIN . '/api/portal/labours/7/documents/nope/link' => Http::response(['message' => 'document_not_found'], 404),
        ], $overrides));
        $this->post('/login', ['passport' => 'aa1234567', 'password' => '06121997'])->assertRedirect('/');
    }

    public function test_guest_is_sent_to_login_and_login_page_renders()
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/documents')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Log in')->assertSee('Passport number or name')->assertSee('myominoo');
    }

    public function test_login_sends_passport_and_birthday_to_the_admin_api_with_the_shared_key()
    {
        $this->loginOk();

        Http::assertSent(function ($req) {
            return $req->url() === self::ADMIN . '/api/portal/login'
                && $req->hasHeader('X-Portal-Key', 'secret-key')
                && $req['passport'] === 'AA1234567'
                && $req['password'] === '06121997';
        });

        $home = $this->get('/');
        $home->assertOk()
            ->assertDontSee('identity-icon has-photo')
            ->assertSee('Company Name')->assertSee('Saeng Lin Foods')->assertDontSee('แสงลินฟู้ดส์')
            ->assertSee('Mr MYO MIN AUNG')
            ->assertSee('Active')
            ->assertSee('AA1234567')
            ->assertSee('15/12/2028')
            ->assertSee('1234-5678-90')
            ->assertSee('450')
            ->assertSee('Sukhumvit Rd')
            ->assertSee('90-Day Report'); // alert

        // The cached profile is reused: no second /labours/7 call right after login.
        Http::assertNotSent(fn ($req) => $req->url() === self::ADMIN . '/api/portal/labours/7');
        $this->get('/?refresh=1')->assertOk();
        Http::assertSent(fn ($req) => $req->url() === self::ADMIN . '/api/portal/labours/7');

        $this->get('/login')->assertRedirect('/');
    }

    public function test_wrong_birthday_shows_a_bilingual_error_and_stays_on_login()
    {
        Http::fake([self::ADMIN . '/api/portal/login' => Http::response(['message' => 'x', 'errors' => ['passport' => ['invalid_credentials']]], 422)]);

        $res = $this->from('/login')->post('/login', ['passport' => 'AA1234567', 'password' => '01011990']);
        $res->assertRedirect('/login')->assertSessionHasErrors('passport');
        $this->assertStringContainsString('is incorrect', session('errors')->first('passport'));
        $this->get('/')->assertRedirect('/login');
    }

    public function test_name_login_is_forwarded_as_typed_and_ambiguous_names_get_a_clear_message()
    {
        Http::fake([self::ADMIN . '/api/portal/login' => Http::sequence()
            ->push(['ok' => true, 'labour_id' => 7, 'profile' => $this->profile()])
            ->push(['message' => 'x', 'errors' => ['passport' => ['ambiguous_name']]], 422),
        ]);

        $this->post('/login', ['passport' => 'myo min aung', 'password' => '06121997'])->assertRedirect('/');
        Http::assertSent(fn ($req) => $req['passport'] === 'MYOMINAUNG');

        $this->post('/logout');
        $this->post('/login', ['passport' => 'myo min aung', 'password' => '06121997'])
            ->assertRedirect()->assertSessionHasErrors(['passport' => 'Several workers share this name, please log in with your passport number']);
    }

    public function test_disabled_account_and_unreachable_admin()
    {
        Http::fake([self::ADMIN . '/api/portal/login' => Http::sequence()
            ->push(['errors' => ['passport' => ['account_disabled']]], 422)
            ->push('down', 500)->push('down', 500)]);
        $this->from('/login')->post('/login', ['passport' => 'BB1', 'password' => '01011990'])->assertSessionHasErrors('passport');
        $this->assertStringContainsString('disabled', session('errors')->first('passport'));

        $this->from('/login')->post('/login', ['passport' => 'BB1', 'password' => '01011990'])->assertSessionHasErrors('passport');
        $this->assertStringContainsString('Cannot reach', session('errors')->first('passport'));
    }

    public function test_documents_page_and_opening_a_document_redirects_to_the_signed_link()
    {
        $this->loginOk();

        $this->get('/documents')->assertOk()->assertSee('Passport')->assertSee('AA1234567_Passport.pdf')->assertSee('Work Permit')->assertSee('117 KB');
        $this->get('/documents/12')->assertRedirect(self::ADMIN . '/portal-file/7/12?expires=1&signature=abc');
        $this->get('/documents/nope')->assertRedirect('/documents');
        $this->get('/documents/../etc')->assertNotFound();
    }

    public function test_photo_from_the_admin_api_is_shown_on_home_and_profile()
    {
        $this->loginOk([self::ADMIN . '/api/portal/login' => Http::response(['ok' => true, 'labour_id' => 7, 'profile' => $this->profile(['photo_url' => self::ADMIN . '/portal-file/7/photo?expires=1&signature=p'])])]);
        $this->get('/')->assertOk()->assertSee('identity-icon has-photo')->assertSee('/portal-file/7/photo?expires=1&amp;signature=p', false);
        $this->get('/profile')->assertOk()->assertSee('identity-icon has-photo');
    }

    public function test_profile_page_and_logout()
    {
        $this->loginOk();

        $this->get('/profile')->assertOk()->assertSee('06/12/1997')->assertSee('TOP MANPOWER')->assertSee('Myanmar');
        $this->post('/logout')->assertRedirect('/login');
        $this->get('/')->assertRedirect('/login');
    }

    public function test_worker_disabled_after_login_is_logged_out_on_next_refresh()
    {
        $this->loginOk([self::ADMIN . '/api/portal/labours/7' => Http::response(['message' => 'account_disabled'], 403)]);

        $this->get('/?refresh=1')->assertRedirect('/login');
        $this->get('/')->assertRedirect('/login');
    }

    public function test_repeated_failures_are_throttled_locally()
    {
        Http::fake([self::ADMIN . '/api/portal/login' => Http::response(['errors' => ['passport' => ['invalid_credentials']]], 422)]);
        for ($i = 0; $i < 8; $i++) {
            $this->post('/login', ['passport' => 'AA1234567', 'password' => '01011990']);
        }
        $this->from('/login')->post('/login', ['passport' => 'AA1234567', 'password' => '01011990'])->assertSessionHasErrors('passport');
        $this->assertStringContainsString('Too many attempts', session('errors')->first('passport'));
        Http::assertSentCount(8);
    }

    public function test_language_switch_is_remembered_and_burmese_strings_render()
    {
        $this->loginOk();

        // English by default; ?lang=my switches and is remembered (session + cookie) for later pages.
        $this->get('/')->assertOk()->assertSee('Work Permit')->assertSee('lang="en"', false);
        $this->get('/?lang=my')->assertOk()->assertSee('lang="my"', false)->assertSee(__('Work Permit', [], 'my'))->assertCookie('portal_lang', 'my');
        $this->get('/documents')->assertOk()->assertSee('lang="my"', false)->assertSee(__('My Documents', [], 'my'));

        // Unknown codes are ignored: the remembered language stays.
        $this->get('/?lang=xx')->assertOk()->assertSee('lang="my"', false);
        $this->get('/?lang=en')->assertOk()->assertSee('lang="en"', false);
    }

    public function test_business_type_is_translated_into_burmese_once_and_cached()
    {
        config(['services.google.translate_key' => 'g-test']);
        Cache::flush();
        $this->loginOk(['translation.googleapis.com/*' => Http::response(['data' => ['translations' => [['translatedText' => 'အစားအစာ']]]])]);

        // English page: no Google call, English text.
        $this->get('/profile')->assertOk()->assertSee('Food')->assertDontSee('အစားအစာ');
        Http::assertNotSent(fn ($req) => str_contains($req->url(), 'translation.googleapis.com'));

        // Burmese page: translated value with the English under it; second view served from cache.
        $this->get('/profile?lang=my')->assertOk()->assertSee('အစားအစာ')->assertSee('sub-en')->assertSee('Food');
        $this->get('/profile')->assertOk()->assertSee('အစားအစာ');
        $this->assertSame(1, Http::recorded(fn ($req) => str_contains($req->url(), 'translation.googleapis.com'))->count());
    }

    public function test_translate_command_fills_lang_file_from_google()
    {
        config(['services.google.translate_key' => 'k']);
        $dir = sys_get_temp_dir() . '/portal-lang-' . uniqid();
        mkdir($dir);
        app()->useLangPath($dir);
        file_put_contents($dir . '/en.json', json_encode(['Home' => 'Home', 'Log out' => 'Log out']));

        Http::fake(['translation.googleapis.com/*' => Http::response(['data' => ['translations' => [['translatedText' => 'ပင်မ'], ['translatedText' => 'ထွက်ရန်']]]])]);

        $this->artisan('lang:translate', ['--locale' => ['my']])->assertSuccessful();
        $out = json_decode(file_get_contents($dir . '/my.json'), true);
        $this->assertSame(['Home' => 'ပင်မ', 'Log out' => 'ထွက်ရန်'], $out);
        Http::assertSent(fn ($r) => $r['target'] === 'my' && $r['q'] === ['Home', 'Log out'] && str_contains($r->url(), 'key=k'));
    }
}
