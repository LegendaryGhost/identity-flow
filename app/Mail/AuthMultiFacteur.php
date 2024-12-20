<?php

namespace App\Mail;

class AuthMultiFacteur extends BaseEmail
{
    public function __construct($nameUser, $code)
    {
        parent::__construct([
            'name' => $nameUser,
            'code' => $code,
            'delai'=>env('DUREE_VIE_PIN', 90)
        ]);
        $this->viewName = 'emails.authentification_multi_facteur';
        $this->subjectLine = 'Code de vérification';
    }

}
