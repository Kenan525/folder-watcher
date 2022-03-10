<?php
namespace App\Console\Commands;

use App\Mail\Watcher;
use Exception;
use Illuminate\Console\Command;
use phpDocumentor\Reflection\Types\Void_;
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
                $this->handleEmail($path);
            })->start();
    }

    /**
     * @param string $path
     * @return void
     */
    public function handleEmail(string $path): void
    {
        $receiver = '';
        $body = '';
        $attachments = [];
        $openedFile = fopen($path, 'rb');
        while ($line = fgets($openedFile)) {
            $fileLines[] = $line;
        }

        foreach ($fileLines as $fileLine) {
            if (str_starts_with(trim($fileLine), '<email>')) {
                $receiver = str_replace(['<email>', '</email>'], '', trim($fileLine));
            }
            if (str_starts_with(trim($fileLine), '<body>')) {
                $body = str_replace(['<body>', '</body>'], '', trim($fileLine));
            }

            if ($receiver && $body) {
                $this->sendEmail($receiver, $body);
            }
        }
    }

    /**
     * @param string $receiver
     * @param string $body
     * @return void
     */
    protected function sendEmail(string $receiver, string $body): void
    {
        $data = [];
        Mail::to('kenan_mahmic@hotmail.com')->send(new Watcher());
    }
}
