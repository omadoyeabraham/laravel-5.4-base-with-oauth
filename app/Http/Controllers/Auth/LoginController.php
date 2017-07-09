<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Socialite;
use Auth;
use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the OAuth Provider.
     *
     * @param String $providerName The name of the OAuth used for authentication
     *
     * @return Response
     */
    public function redirectToProvider($providerName)
    {
        return Socialite::driver($providerName)->redirect();
    }

    /**
     * Obtain the user information from the OAuth provider.
     *
     * Check if the user already exists in our
     * database by looking up their provider_id in the database.
     * If the user exists, log them in. Otherwise, create a new user then log them in. After that
     * redirect them to the authenticated users homepage.
     *
     * @param String $providerName The name of OAuth Provider used for authentication
     *
     * @return Response
     */
    public function handleProviderCallback($providerName)
    {
        $user = Socialite::driver($providerName)->user();

        $authenticatedUser = $this->findOrCreateUser($user, $providerName);

        Auth::login($authenticatedUser, true);

        return redirect($this->redirectTo);
    }

    /**
     * If a user has registered before using social auth, return the user
     * else, create a new user object.
     *
     * @param  \Laravel\Socialite\ $user Socialite user object
     * @param   string $providerName Social auth provider
     *
     * @return  User
     */
    private function findOrCreateUser($user, $providerName)
    {
        $authenticatedUser = User::where('provider_id', $user->id)->first();

        if ($authenticatedUser) {
            return $authenticatedUser;
        }

        $newUser = User::create([
            'name'        => $user->name,
            'email'       => $user->email,
            'provider'    => $providerName,
            'provider_id' => $user->id
        ]);

        /**
         * Dispatch the newUserCreated event so the various actions that need to be carried out when a user is created are done.
         *
         */
        //event(new NewUserCreated($newUser));

        return $newUser;
    }

}
