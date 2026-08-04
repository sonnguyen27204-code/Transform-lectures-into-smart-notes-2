<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InspireCommand extends Command
{
    protected $signature = 'inspire';

    protected $description = 'Display an inspiring quote';

    public function handle(): void
    {
        $this->comment('Học hôm nay, dẫn đầu ngày mai. 🚀');
    }
}