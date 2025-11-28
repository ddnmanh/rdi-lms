<?php

namespace App\Console\Commands;

use Symfony\Component\Process\Process;
use Illuminate\Console\Command;

class TestFfmpeg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */ 
    protected $signature = 'test:ffmpeg';

    
    /**
     * The console command description.
    *
    * @var string
    */
    protected $description = 'Kiểm tra ffmpeg có hoạt động trong Laravel hay không';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ffmpeg = env('FFMPEG_PATH', 'ffmpeg');

        $this->info("Using ffmpeg: {$ffmpeg}");

        $process = new Process([$ffmpeg, '-version']);
        $process->setTimeout(10);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('FFmpeg failed:');
            $this->error($process->getErrorOutput());
            return Command::FAILURE;
        }

        $this->info('FFmpeg OK:');
        $this->line($process->getOutput());
        return Command::SUCCESS;
    }
}
