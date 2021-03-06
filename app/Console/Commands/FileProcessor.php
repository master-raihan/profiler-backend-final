<?php

namespace App\Console\Commands;

use App\Contracts\Services\ContactContract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        try{
            $time_start = microtime(true);
            $this->contactService->uploadContact();
            $time_end = microtime(true);
            $execution_time = ($time_end - $time_start)/60;
            Log::info($execution_time);
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
        }

    }
}
