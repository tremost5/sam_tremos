<?php

namespace App\Http\Controllers;

use App\Models\FacebookAccount;
use App\Models\FacebookPage;
use App\Services\MetaFacebookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class FacebookController extends Controller
{
    public function index(MetaFacebookService $metaService)
    {
        $account = FacebookAccount::query()->where('user_id', Auth::id())->first();
        $pages = FacebookPage::query()->where('user_id', Auth::id())->get();
        $metaConfigured = $metaService->hasCredentials(Auth::user());

        return view('facebook.index', compact('account', 'pages', 'metaConfigured'));
    }

    public function connect(MetaFacebookService $metaService)
    {
        $meta = $metaService->getConfigForUser(Auth::user());
        if (empty($meta['app_id']) || empty($meta['app_secret']) || empty($meta['redirect_uri'])) {
            return redirect()->route('facebook.index')->with('error', 'Meta API belum dikonfigurasi.');
        }

        $params = http_build_query([
            'client_id' => $meta['app_id'],
            'redirect_uri' => $meta['redirect_uri'],
            'scope' => 'pages_manage_posts,pages_read_engagement,pages_show_list',
            'response_type' => 'code',
            'state' => bin2hex(random_bytes(16)),
        ]);

        return redirect('https://www.facebook.com/'.config('services.meta.graph_version', 'v19.0').'/dialog/oauth?'.$params);
    }

    public function callback(Request $request)
    {
        $code = $request->get('code');
        if (! $code) {
            return redirect()->route('facebook.index')->with('error', 'Facebook OAuth gagal.');
        }

        $meta = app(MetaFacebookService::class)->getConfigForUser(Auth::user());
        if (empty($meta['app_id']) || empty($meta['app_secret']) || empty($meta['redirect_uri'])) {
            return redirect()->route('facebook.index')->with('error', 'Meta API belum dikonfigurasi.');
        }

        $response = \Illuminate\Support\Facades\Http::asForm()->post('https://graph.facebook.com/'.$meta['graph_version'].'/oauth/access_token', [
            'client_id' => $meta['app_id'],
            'client_secret' => $meta['app_secret'],
            'redirect_uri' => $meta['redirect_uri'],
            'code' => $code,
        ]);

        $payload = $response->json();
        if (! isset($payload['access_token'])) {
            return redirect()->route('facebook.index')->with('error', 'Facebook OAuth gagal mendapatkan access token.');
        }

        $account = FacebookAccount::query()->updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'provider' => 'facebook',
                'access_token' => Crypt::encryptString($payload['access_token']),
                'token_expires_at' => now()->addSeconds($payload['expires_in'] ?? 3600),
                'status' => 'connected',
            ]
        );

        $pageResponse = \Illuminate\Support\Facades\Http::get('https://graph.facebook.com/'.$meta['graph_version'].'/me/accounts', [
            'access_token' => $payload['access_token'],
        ]);

        $pageData = $pageResponse->json()['data'] ?? [];
        foreach ($pageData as $page) {
            FacebookPage::query()->updateOrCreate([
                'facebook_id' => $page['id'],
                'user_id' => Auth::id(),
            ], [
                'name' => $page['name'] ?? 'Facebook Page',
                'access_token' => Crypt::encryptString($page['access_token'] ?? $payload['access_token']),
                'category' => $page['category'] ?? null,
                'permissions' => json_encode($page['tasks'] ?? []),
            ]);
        }

        return redirect()->route('facebook.index')->with('success', 'Koneksi Facebook berhasil dibuat.');
    }

    public function disconnect()
    {
        FacebookAccount::query()->where('user_id', Auth::id())->delete();
        FacebookPage::query()->where('user_id', Auth::id())->delete();

        return redirect()->route('facebook.index')->with('success', 'Koneksi Facebook berhasil diputus.');
    }

    public function selectPage(Request $request, int $page)
    {
        $selected = FacebookPage::query()
            ->where('user_id', Auth::id())
            ->findOrFail($page);

        FacebookPage::query()->where('user_id', Auth::id())->update(['selected' => false]);
        $selected->update(['selected' => true]);

        return redirect()->route('facebook.index')->with('success', 'Facebook Page berhasil dipilih.');
    }
}
