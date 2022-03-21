<?php

namespace App\Console\Commands;

use App\Contracts\Services\ContactContract;
use Illuminate\Console\Command;

class FileProcessor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    private $contactService;
    protected $signature = 'process:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ContactContract $contactService)
    {
        parent::__construct();
        $this->contactService = $contactService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->contactService->uploadContact();
    }
}
