<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivacyNoticeController extends Controller
{
    public function show()
    {
        return view('auth.privacy-notice', [
            'noticeVersion' => config('privacy.notice_version'),
            'lastUpdated' => config('privacy.last_updated'),
            'fullNoticeUrl' => config('privacy.full_notice_url'),
        ]);
    }

    public function accept(Request $request)
    {
        $validated = $request->validate([
            'accept_privacy_notice' => 'accepted',
        ], [
            'accept_privacy_notice.accepted' => 'Debes confirmar que has leído y aceptas el Aviso de Privacidad.',
        ]);

        $user = Auth::user();

        $user->user_ap_accepted = true;
        $user->user_ap_accepted_date = now();
        $user->user_ap_version = (string) config('privacy.notice_version');
        $user->user_ap_ip = $request->ip();
        $user->user_ap_user_agent = (string) $request->header('User-Agent');
        $user->save();

        return redirect()->intended('/dashboard')->with('status', 'Aviso de privacidad aceptado.');
    }
}


