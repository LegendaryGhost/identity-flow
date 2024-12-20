<?php

namespace App\Mail;

class ResetTentative extends BaseEmail
{
    public function __construct($nameUser, $resetLink)
    {
        parent::__construct([
            'name' => $nameUser,
            'resetLink' => $resetLink,
            'delai'=>env('DUREE_VIE_TENTATIVE', 86400)
        ]);
        $this->viewName = 'emails.reinitialisation_tentative_auth';
        $this->subjectLine = 'Réinitialisez votre tentative de connection';
    }
}
