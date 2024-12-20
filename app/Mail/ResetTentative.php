<?php

namespace App\Mail;

class ResetTentative extends BaseEmail
{
    public function __construct($nameUser, $resetLink)
    {
        parent::__construct([
            'name' => $nameUser,
            'resetLink' => $resetLink,
        ]);
        $this->viewName = 'emails.reinitialisation_tentative_auth';
        $this->subjectLine = 'Réinitialisez votre tentative de connection';
    }
}
