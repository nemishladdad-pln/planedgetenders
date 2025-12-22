<?php
/**
 * User: Pallavi Dighe
 * Date: 10/09/2023
 * Time: 01:09 AM
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\Fluent\Concerns\Has;
//use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Services\Auth\UserOtpService;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FAQRCode\Google2FA as Google2FAQR;


/**
 * Class AuthController
 *
 * @author  Pallavi <pals.ver@gmail.com>
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    public function __construct(protected UserOtpService $userOtpService)
    {
        //$this->middleware(['auth:api']);
    }
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|string|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ]
        ]);

        /** @var User $user */
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
        $token = $user->createToken('main')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            //'email' => 'required|email|string|exists:users,email', // This will give error message of email specific
            'email' => 'required|email|string',
            'password' => [
                'required',
            ],
            'remember' => 'boolean'
        ]);
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        if (!Auth::attempt($credentials, $remember)) {
            return response([
                'message' => 'The provided credentials are not correct.'
            ], 422);
        }

        $user = Auth::user();

                // First, check if user has 2fa enabled. If yes, then we need to call otp service.
        if ($user->google2fa_enabled && !$request->otp) {

            $this->userOtpService->index($user);
            return response()->json([
                'error' => true,
                'user' => $user,
                'google2fa_enabled' => $user->google2fa_enabled,
                'message' => 'Please check your inbox, we have sent you an otp. Please enter it.'
            ]);
        }

        if ($user->google2fa_enabled && $request->otp) {
            if (!$this->userOtpService->verify($request, $user)) {
                return response()->json([
                    'error' => true,
                    'user' => $user,
                    'google2fa_enabled' => $user->google2fa_enabled,
                    'message' => 'The provided otp is incorrect.'
                ]);
            }
        }
        //This line is added to manage roles in .vue files.
        $user->user_roles = $user->roles->pluck('name');
        $token = $user->createToken('main')->plainTextToken;

        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties(['customProperty' => 'customValue'])
            ->log($user->name.' has logged in.');

        return response()->json([
            'error' => false,
            'user' => $user,
            'token' => $token,
            'google2fa_enabled' => $user->google2fa_enabled,
            'user_permissions' => [
                'roles' => auth()->user()->getRoleNames(),
                'permissions' => auth()->user()->getAllPermissions()->pluck('name'),
            ]
        ]);
    }

    public function logout()
    {
        /** @var User $user */
        $user = Auth::user();
        // Revoke the token that was used to authenticate the current request...
        $user->currentAccessToken()->delete();

        return response([
            'success' => true
        ]);
    }

    public function forgot_password(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', '=', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Provided email address does not exists.'], 404);
        }
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response($status === Password::RESET_LINK_SENT
                ? ['success' => true]
                : ['success' => false, 'errors' => ['email' => [__($status)]]]);
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password)
            {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return response($status === Password::PASSWORD_RESET
                    ? ['success' => true]
                    : ['success' => false, 'errors' => ['email' => [__($status)]]]);
    }

}


