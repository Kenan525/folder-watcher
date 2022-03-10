<?php

namespace App\Mail;

use Illuminate\Auth\Authenticatable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Laravel\Lumen\Auth\Authorizable;

class Watcher extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @return $this
     */
    public function build(): self
    {
        return $this->view('email-watcher');
    }
}
