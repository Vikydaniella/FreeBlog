<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;

class PostCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $emailContent;

    public function __construct(Post $post, $emailContent)
    {
        $this->post = $post;
        $this->emailContent = $emailContent; 
    }

    public function build()
    {
        return $this->subject('New Post Created')
                    ->text($this->emailContent);
    }
}