<?php

namespace App\Jobs;

use App\Mail\PostCreated;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPostNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;
    protected $emailContent;

    public function __construct(Post $post, $emailContent)
    {
        $this->post = $post;
        $this->emailContent = $emailContent;
    }

    public function handle()
    {
        Mail::to($this->post->author->email)
            ->send(new PostCreated($this->post, $this->emailContent));
    }
}
