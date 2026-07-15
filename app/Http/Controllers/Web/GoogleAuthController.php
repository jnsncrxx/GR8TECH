<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Find existing account by email
            $account = Account::where('email', $googleUser->getEmail())->first();

            if ($account) {
                // If account is inactive, deny access
                if (!$account->is_active) {
                    return redirect()->route('login')->withErrors(['email' => 'Your account is inactive. Please contact HR.']);
                }

                // Update google_id and avatar if they are not set yet
                if (empty($account->google_id)) {
                    $account->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar()
                    ]);
                }

                // Log the user in
                Auth::login($account, true); // remember me = true
                
                // Update last login time
                if (method_exists($account, 'updateLastLogin')) {
                    $account->updateLastLogin();
                }

                $request->session()->regenerate();

                // Set default company session (matching AuthController behavior)
                session(['current_company_id' => 'c5751b07-35c6-4f06-9442-51b3fe8b0347']);

                return redirect()->intended(route('dashboard'));
            } else {
                // User does not exist in our system
                return redirect()->route('login')->withErrors(['email' => 'No account found with this email ('.$googleUser->getEmail().'). Please contact HR to create your account first.']);
            }
            
        } catch (Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['email' => 'Failed to login with Google. Please try again.']);
        }
    }
}
