<?php
namespace App\Services\Auth;

use App\Mail\SendOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FAQRCode\Google2FA as Google2FAQR;
use App\Models\User;

class UserOtpService
{
    public function __construct(protected Google2FA $google2FA, protected Google2FAQR $google2FAQR){}

    public function index($user)
    {
        $secretKey = $this->google2FA->generateSecretKey();
        $user->update(['google2fa_secret' => $secretKey]);

        $qrcode = $this->google2FAQR->getQRCodeInline(
            'meritest',
            $user->email,
            $user->google2fa_secret
        );

        $currentOtp = $this->google2FA->getCurrentOtp($secretKey);

        // $userModel = User::findOrFail($user->id);
        // $userModel->sendWhatsAppMessage();

        Mail::to($user->email)->send(new SendOtp($user, $currentOtp));
    }

    public function verify($request, $user)
    {
        if (!$this->google2FA->verifyKey($user->google2fa_secret, $request->otp)) {
            return false;
        }
        return true;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'otp' => 'required'
        ]);

        $user = auth()->user();
        if (!$this->google2FA->verifyKey($user->google2fa_secret, $request->otp)) {
            return response(null, 401);
        }
        $user->update([
            'google2fa_enabled' => true,
        ]);

    }

    public function destroy(Request $request)
    {
        /*$this->validate($request, [
            'password' => 'required'
        ]);*/

        $user = auth()->user();

        $user->update([
            'google2fa_enabled' => false,
            'google2fa_secret' => null,
        ]);
    }
}
