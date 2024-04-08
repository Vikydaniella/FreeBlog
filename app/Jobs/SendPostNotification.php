<?php

namespace App\Jobs;

use App\Models\Post;
use App\Mail\PostCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Notifications\PostNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPostNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;
    protected $user;

    public function __construct($user, $post)
    {
        $this->user = $user;
        $this->post = $post;
        
    }

    public function handle()
    {
        $this->user->notify(new PostNotification($this->post));
    }
}
