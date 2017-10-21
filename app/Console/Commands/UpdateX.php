<?php

namespace App\Console\Commands;

use App\Jobs\SaveX;
use Illuminate\Console\Command;

class UpdateX extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:x';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '三合一, 一次请求, 三个归档';

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
     * @return mixed
     */
    public function handle()
    {
        SaveX::dispatch();
    }
}