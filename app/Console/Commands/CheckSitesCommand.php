<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\SiteCheckerService;
use Illuminate\Console\Command;

class CheckSitesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sites:check {--site= : Optional Site ID to check a single site}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run real-time uptime, SSL certificate, and renewal checks for client websites';

    /**
     * Execute the console command.
     */
    public function handle(SiteCheckerService $checker): int
    {
        $siteId = $this->option('site');

        if ($siteId) {
            $site = Site::find($siteId);
            if (! $site) {
                $this->error("Site with ID [{$siteId}] not found.");
                return Command::FAILURE;
            }
            $sites = collect([$site]);
        } else {
            $sites = Site::where('is_active', true)->get();
        }

        if ($sites->isEmpty()) {
            $this->info('No active sites configured for monitoring.');
            return Command::SUCCESS;
        }

        $this->info("Starting checks for {$sites->count()} website(s)...");

        $bar = $this->output->createProgressBar($sites->count());
        $bar->start();

        $online = 0;
        $down = 0;

        foreach ($sites as $site) {
            try {
                $res = $checker->check($site);
                if ($res['uptime']['is_online']) {
                    $online++;
                } else {
                    $down++;
                }
            } catch (\Throwable $e) {
                $down++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Check complete! Online: {$online} | Down: {$down}");

        return Command::SUCCESS;
    }
}
