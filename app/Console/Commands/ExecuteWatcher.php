<?php
namespace App\Console\Commands;

use App\Mail\Watcher;
use Illuminate\Console\Command;
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
                } catch (\Exception $exception) {
                    print_r($exception->getMessage());
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
        $data = [
            'subject' => (string)$xml->subject ?: 'Staff24',
            'receiver' => (string)$xml->to,
            'body' => (string)$xml->body,
            'attachment' => (string)$xml->attachment
        ];
        $this->sendEmail($data);
    }

    /**
     * @param array $data
     * @return void
     */
    protected function sendEmail(array $data): void
    {
        Mail::to($data['receiver'])->send(new Watcher($data));
    }
}
