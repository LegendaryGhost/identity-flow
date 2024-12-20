<?php

namespace App\Mail;

class ValidationInscription extends BaseEmail
{
    public function __construct($nameUser, $validationLink)
    {
        parent::__construct([
            'name' => $nameUser,
            'validationLink' => $validationLink,
            'delai'=>env('DUREE_VIE_LIEN_INSCRIPTION', 90)
        ]);
        $this->viewName = 'emails.validation_inscription';
        $this->subjectLine = 'Validez votre inscription';
    }
}
