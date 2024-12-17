<?php

namespace App\Http\Controllers;

use App\Mail\AuthMultiFacteur;
use App\Mail\ResetPasswordEmail;
use App\Mail\ValidationInscription;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestEmail extends Controller
{
    public function sendEmail()
    {
        $resetLink = url('/reset-password?token=' . urlencode("kennyandriantsirafychan@gmail.com"));
        Mail::to("kennyandriantsirafychan@gmail.com")->send(new ResetPasswordEmail("kenshy",$resetLink));

        Mail::to("kennyandriantsirafychan@gmail.com")->send(new ValidationInscription("kenshy", "https://google.com"));

        Mail::to("kennyandriantsirafychan@gmail.com")->send(new AuthMultiFacteur("kenshy", [2,4,5,6,5,6]));

        return 'Test email sent!';


    }

}
