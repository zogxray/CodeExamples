<?php

namespace App\Mail;

use App\Models\Requests\RequestForLandingCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestForLandingCourseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $requestForLandingCourse;

    /**
     * RequestForLandingCourseMail constructor.
     * @param RequestForLandingCourse $requestForLandingCourse
     */
    public function __construct(RequestForLandingCourse $requestForLandingCourse)
    {
        $this->requestForLandingCourse = $requestForLandingCourse;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = trans('course.request');
        return $this->view('emails.requests.forLandingCourse')->subject($subject);
    }
}
