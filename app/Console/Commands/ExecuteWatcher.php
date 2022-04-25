<?php
namespace App\Console\Commands;

use App\Mail\Watcher;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Spatie\Watcher\Exceptions\CouldNotStartWatcher;
use Spatie\Watcher\Watch;
use Illuminate\Support\Facades\Mail;


/**
 * Class ExecuteWatcher
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class ExecuteWatcher extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "app:execute-watcher";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Execute folder watcher!";


    /**
     * Execute the console command.
     *
     * @return void
     * @throws CouldNotStartWatcher
     */
    public function handle(): void
    {
        Watch::path('E:\\EMAIL')->onFileCreated(
            function (string $path) {
				$extractedPath = preg_split('/\r\n|\r|\n/', $path);
                try {
                    $this->handleEmail($extractedPath[0]);
                    print_r("Email was sent!");
                    echo '<br/>';
                } catch (\Exception $exception) {
                    print_r('Email was not sent!');
                    echo '<br/>';
                    print_r($exception->getMessage());
                    echo '<br/>';
                } finally {
                    $this->moveFile($extractedPath[0]);
                }
            })->start();
    }

    /**
     * @param string $path
     * @return void
     */
    public function handleEmail(string $path): void
    {
        if (pathinfo($path, PATHINFO_EXTENSION) !== 'xml') {
            return;
        }
        $file = file_get_contents($path);
        $xml = simplexml_load_string($file);
        $attachments = [];
        foreach ($xml->attachments as $xmlAttachments) {
            foreach ($xmlAttachments as $attachment) {
                $attachments[] = (string)$attachment;
            }
        }

        $data = [
            'subject' => (string)$xml->subject ?: 'Staff24',
            'from' => (string)$xml->from ?: 'thomas.wiesinger@staff24.com',
            'receiver' => (string)$xml->to,
            'cc' => $xml->cc ? (string)$xml->cc : '',
            'body' => (string)$xml->body,
            'attachments' => $attachments,
            'signature' => (string)$xml->signatur
        ];
        $this->sendEmail($data);
    }

    /**
     * @param array $data
     * @return void
     */
    protected function sendEmail(array $data): void
    {
        if ($data['cc']) {
            Mail::to($data['receiver'])->cc($data['cc'])->send(new Watcher($data));
        } else {
            Mail::to($data['receiver'])->send(new Watcher($data));
        }
    }

    /**
     * @param string $path
     * @return void
     */
    protected function moveFile(string $path): void
    {
        $fileName = basename($path);
        try {
            rename($path, 'E:\\EMAIL_ARCHIV\\' . $fileName);
        } catch (\Exception $exception) {
            print_r("File could not be moved!");
        }
    }
}
