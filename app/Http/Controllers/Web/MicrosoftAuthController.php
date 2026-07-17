<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class MicrosoftAuthController extends Controller
{
    /**
     * Redirect the user to the Microsoft authentication page.
     */
    public function redirectToMicrosoft()
    {
        return Socialite::driver('azure')->redirect();
    }

    /**
     * Obtain the user information from Microsoft.
     */
    public function handleMicrosoftCallback(Request $request)
    {
        try {
            $microsoftUser = Socialite::driver('azure')->user();
            
            // Find existing account by email
            $account = Account::where('email', $microsoftUser->getEmail())->first();

            if ($account) {
                // If account is inactive, deny access
                if (!$account->is_active) {
                    return redirect()->route('login')->withErrors(['email' => 'Your account is inactive. Please contact HR.']);
                }

                // Update microsoft_id and avatar if they are not set yet
                if (empty($account->microsoft_id)) {
                    $account->update([
                        'microsoft_id' => $microsoftUser->getId(),
                        // Microsoft graph avatar might not always be available, but we can set it if provided
                        'avatar' => $account->avatar ?? $microsoftUser->getAvatar()
                    ]);
                }

                // Log the user in
                Auth::login($account, true); // remember me = true
                
                // Update last login time
                if (method_exists($account, 'updateLastLogin')) {
                    $account->updateLastLogin();
                }

                $request->session()->regenerate();

                // Set default company session
                session(['current_company_id' => 'c5751b07-35c6-4f06-9442-51b3fe8b0347']);

                return redirect()->intended(route('dashboard'));
            } else {
                // User does not exist in our system
                return redirect()->route('login')->withErrors(['email' => 'No account found with this email ('.$microsoftUser->getEmail().'). Please contact HR to create your account first.']);
            }
            
        } catch (Exception $e) {
            \Log::error('Microsoft OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['email' => 'Failed to login with Microsoft. Please try again.']);
        }
    }
}
