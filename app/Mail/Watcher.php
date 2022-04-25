<?php

namespace App\Mail;

use Illuminate\Auth\Authenticatable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Laravel\Lumen\Auth\Authorizable;

class Watcher extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var array
     */
    public array $data;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return $this
     */
    public function build(): self
    {
        if ($this->data['attachments']) {
            $mail = $this->from($this->data['from'])->view('email-watcher')->subject($this->data['subject']);

            foreach ($this->data['attachments'] as $attachment) {
                $attachmentPath = 'E:\\ATTACHMENTS/' . $attachment;
                $mail->attach($attachmentPath);
            }
            return $mail;
        }

        return $this->from($this->data['from'])->view('email-watcher')->subject($this->data['subject']);
    }
}
