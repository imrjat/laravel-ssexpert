<?php

namespace Imrjat\SSExpert\Commands;

use Illuminate\Console\Command;
use Imrjat\SSExpert\Facades\SSExpertSms;
use Imrjat\SSExpert\Facades\SSExpertTemplate;

class SendTestSmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssexpert:test-sms 
                            {mobile : Mobile number to send test SMS to}
                            {--otp= : OTP value to send (defaults to a random 6-digit number)}
                            {--template=1707167402281919826 : DLT Template ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test SMS through SSExpertSystem gateway using a DLT template';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $mobile = (string) $this->argument('mobile');
        $otp = (string) ($this->option('otp') ?: (string) rand(100000, 999999));
        $templateId = (string) $this->option('template');

        $this->info("Sending test SMS via SSExpertSystem...");
        $this->table(
            ['Parameter', 'Value'],
            [
                ['Target Mobile', $mobile],
                ['Generated OTP', $otp],
                ['DLT Template ID', $templateId],
                ['Sender ID', config('ssexpert.sender_id', 'ORPATG')],
                ['Gateway Base URL', config('ssexpert.base_url')],
            ]
        );

        try {
            $template = SSExpertTemplate::findByDltTemplateId($templateId);
            if ($template) {
                $this->line("<comment>Found Approved Template:</comment> [{$template->templateName}]");
                $this->line("<comment>Template Format:</comment> {$template->messageTemplate}");
            }

            $response = SSExpertSms::sendOtp($mobile, $otp, $templateId);

            if ($response->isSuccess()) {
                $this->info("✔ SMS sent successfully!");
                $this->line("Message ID: <info>" . ($response->getMessageId() ?: 'Accepted') . "</info>");

                return self::SUCCESS;
            }

            $this->error("✖ Gateway returned error (Code: {$response->errorCode}): " . $response->getErrorMessage());

            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error("✖ Exception: " . $e->getMessage());

            return self::FAILURE;
        }
    }
}
