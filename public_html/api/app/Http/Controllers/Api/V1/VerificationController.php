<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

/**
 * @group Auth endpoints
 */
class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    //use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @authenticated
     * @urlParam hash required Email verification hash. Example: eyJpdiI6IjROTEh4Vjdyc085T1poTjlJa2hKNUE9PSIsInZhbHVlIjoiQ0lQOHVSblFvd0xROEtpRkNLd1pSUT09IiwibWFjIjoiMmRkNTNmNzFiZThkMjI3NzE3NGExY2FhYTRkMGI1ZjExODU1YTM5MzYzZTQyODNhYjQxOTIxNjU3ZTUxYWI5MSJ9
     * @response status=202 scenario="Email has been verified" {}
     * @response status=204 scenario="Email already verified" {}
     * @response status=400 scenario="Wrong link" {
     *     "message": "The link is wrong"
     * }
     * @response status=400 scenario="Unauthenticated" {
     *     "message": "Unauthenticated."
     * }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     *   @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        $response = [];
        try {
            if (Crypt::decrypt($request->hash) != $request->user()->getKey()) {
                abort(400, 'The link is wrong');
            }
        } catch (DecryptException $e) {
            abort(400, 'The link is wrong');
        }

        if ($request->user()->hasVerifiedEmail()) {
            $user = User::findOrFail($request->user()->id);
            $user->user_roles = $user->roles->pluck('name');
            $response = [
                'success' => true,
                'user' => $user,
                'message' => "You have already verified your account.",
            ];
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
            $user = User::findOrFail($request->user()->id);
            $user->user_roles = $user->roles->pluck('name');
            $response = [
                'success' => true,
                'user' => $user,
                'message' => "Successfully verified your account.",
            ];
        }
        return response()->json($response, 202);
    }

    /**
     * Resend the email verification notification.
     *
     * @authenticated
     * @response status=202 scenario="Success" {}
     * @response status=400 scenario="Unauthenticated" {
     *     "message": "Unauthenticated."
     * }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if (!$request->user()) {
            $user = Auth::user();
        } else {
            $user = $request->user();
        }
        if ($user->hasVerifiedEmail()) {

            return response()->json(['user' => $user, 'success'=> true], 202);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['user' => $user, 'success'=> true], 202);
    }
}
