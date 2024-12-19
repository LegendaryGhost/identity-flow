<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class BaseEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected string $viewName;
    protected string $subjectLine;
    protected array  $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function build(): BaseEmail
    {
        return $this->view($this->viewName)
            ->with($this->data)
            ->subject($this->subjectLine);
    }
}
