<?php

namespace App\Mail;

class ValidationInscription extends BaseEmail
{
    public function __construct($nameUser, $validationLink)
    {
        parent::__construct([
            'name' => $nameUser,
            'validationLink' => $validationLink,
        ]);
        $this->viewName = 'emails.validation_inscription';
        $this->subjectLine = 'Validez votre inscription';
    }
}
