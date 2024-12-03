<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Clase que manda la información a la vista para mandar el email.
class RegisterUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $subject;
    public $user;
    public $asunto;

    public function __construct($user, $asunto, $subject){
        $this->asunto = $asunto;
        $this->user = $user;
        $this->subject = $subject;
    }

    public function build(){
        return $this
            ->to('montesbetancurvladimir@gmail.com') // Envía una copia a este correo
            ->from('montesbetancurvladimir@gmail.com', $this->asunto) 
            ->subject($this->subject)
            ->markdown('mails.register_user',
                ['user' => $this->user]
            );
    }
}

