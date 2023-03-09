<?php

namespace oval\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Mail\Mailer;
use Exception;

/**
 * This class handles sending of Emails.
 * @author Ken
 */
class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Important, will throw an error without it
    protected $opt;

    /**
     * Create a new SendEmail job instance.
     *
     * @param  array  $opt
     *   Parameters for sending out the email, must contains
     *   "email" => which email template to use
     *   "to" => recipient of the email
     *   "subject" => email subject
     *   "params" => parameters for the email template
     * @return void
     */
    public function __construct(array $opt = [])
    {
        $this->opt = $opt;
    }

    /**
     * Execute the job.
     *
     * @param  Mailer  $mailer
     *   Injected mailer object
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        // echo json_encode($this->opt) . "\n";
        
        $mailer->send($this->opt["email"], $this->opt["params"], function ($message) {
            $message->from(env('MAIL_FROM', 'oval@example.com'), env('MAIL_NAME', 'OVAL'));
            // $message->sender($address, $name = null);
            $message->to($this->opt["to"]);
            // $message->cc($address, $name = null);
            // $message->bcc($address, $name = null);
            // $message->replyTo($address, $name = null);
            $message->subject($this->opt["subject"]);
            // $message->priority($level);
            // $message->attach($pathToFile, array $options = []);
        });
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        echo 'Caught exception: ',  $exception->getMessage(), "\n";
    }
}

