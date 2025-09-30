<?php

namespace App\Observers;

use App\Mail\ForgetPasswordOtp;
use App\Mail\OtpMail;
use App\Mail\ResendOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        

        if ($user->otp) {


            Mail::to($user->email)->send(new OtpMail($user));

        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
  

        if ($user->wasChanged('otp') && $user->otp_type === 'resend') {
            Mail::to($user->email)->send(new ResendOtpMail($user));
        }
        
        if ($user->wasChanged('otp') && $user->otp_type === 'forget') {
            Mail::to($user->email)->send(new ForgetPasswordOtp($user));
        }
        


    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
