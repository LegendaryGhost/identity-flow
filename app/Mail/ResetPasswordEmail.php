<?php

namespace App\Mail;

use App\Models\Utilisateur;

class ResetPasswordEmail extends BaseEmail
{
    public function __construct($nameUser, $resetLink)
    {
        parent::__construct([
            'name' => $nameUser,
            'resetLink' => $resetLink,
        ]);

        $this->viewName = 'emails.reset_password';
        $this->subjectLine = 'Réinitialisez votre mot de passe';
    }
}

