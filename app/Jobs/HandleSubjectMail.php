<?php

namespace App\Jobs;

use App\Mail\SendMailSubjects;
use App\Mail\SubjectMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class HandleSubjectMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subjects;
    protected $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subjects, $email)
    {
        //
        $this->subjects = $subjects;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Mail::to($this->email)->send(new SubjectMail($this->subjects));

    }
}
