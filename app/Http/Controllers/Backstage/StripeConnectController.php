<?php

namespace App\Http\Controllers\Backstage;

use Zttp\Zttp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StripeConnectController extends Controller
{
    /**
     * Redirect the user to the Stripe authorization page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authorizeRedirect()
    {
        $url = vsprintf('%s?%s', [
            'https://connect.stripe.com/oauth/authorize',
            http_build_query([
                'response_type' => 'code',
                'scope' => 'read_write',
                'client_id' => config('services.stripe.client_id'),
            ]),
        ]);

        return redirect($url);
    }

    /**
     * The endpoint that Stripe will redirect the user back to after authorization.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(Request $request)
    {
        $accessTokenResponse = Zttp::asFormParams()->post('https://connect.stripe.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $request->code,
            'client_secret' => config('services.stripe.secret'),
        ])->json();

        $request->user()->update([
            'stripe_account_id' => $accessTokenResponse['stripe_user_id'],
            'stripe_access_token' => $accessTokenResponse['access_token'],
        ]);

        return redirect(route('backstage.concerts.index'));
    }
}
