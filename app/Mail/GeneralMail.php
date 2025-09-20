<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneralMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;

    public $content;

    public $isPlainText;

    /**
     * Create a new message instance.
     */
    public function __construct($subjectText, $content, $isPlainText = false)
    {
        $this->subjectText = $subjectText;
        $this->content = $content;
        $this->isPlainText = $isPlainText;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->subject($this->subjectText);

        if ($this->isPlainText) {
            // Use plain text template
            return $mail->text('emails.general_plain')
                ->with(['content' => $this->content]);
        } else {
            // Use HTML template
            return $mail->view('emails.general')
                ->with(['content' => $this->content]);
        }
    }
}
