<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MyMail extends Mailable
{
    use Queueable, SerializesModels;
    public $details;
    public $filename;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details, $filename)
    {
        $this->details = $details;
        $this->filename = $filename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mail from UPT Perpustakaan Universitas Lampung')
            ->view('emails.myMail')
            ->attach($this->filename);
    }
}
