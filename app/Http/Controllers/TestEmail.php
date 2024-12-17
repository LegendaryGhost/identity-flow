<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestEmail extends Controller
{
    public function sendEmail()
    {
        $resetLink = url('/reset-password?token=' . Str::random(60) . '&email=' . urlencode("kennyandriantsirafychan@gmail.com"));
        Mail::to("kennyandriantsirafychan@gmail.com")->send(new ResetPasswordEmail("kenshy",$resetLink));
        return 'Test email sent!';
    }

}
