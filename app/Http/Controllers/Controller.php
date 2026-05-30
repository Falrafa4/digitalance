<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Ensure currently authenticated freelancer is approved.
     * If not approved, returns a redirect or JSON response which the caller should return.
     */
    protected function ensureFreelancerApproved()
    {
        $freelancer = auth('freelancer')->user();
        if (!$freelancer) {
            if (request()->expectsJson())
                return response()->json(['message' => 'Unauthorized'], 401);
            abort(403);
        }

        if ($freelancer->status !== 'Approved') {
            $msg = 'Akses terbatas. Mohon ajukan verifikasi ke admin melalui panduan onboarding.';
            if (request()->expectsJson())
                return response()->json(['message' => $msg], 403);
            return redirect()->route('freelancer.dashboard')->with('warning', $msg);
        }

        return null;
    }
}
