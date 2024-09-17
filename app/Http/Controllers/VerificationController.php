<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;

class VerificationController extends Controller
{
    /**
     * Verify the user's email address.
     *
     * @param  Request  $request
     * @param  string  $id
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = \App\Models\User::findOrFail($id);

        // Verifikasi hash email
        if (! hash_equals((string) $hash, sha1($user->email))) {
            return response()->json(['message' => 'Invalid verification link.'], 400);
        }

        // Tandai email sebagai terverifikasi
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // Beri respons sukses tanpa melakukan redirect ke route login
        return response()->json(['message' => 'Your email has been verified!'], 200);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent.']);
    }
}
