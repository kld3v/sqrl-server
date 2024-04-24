<?php

namespace App\Jobs;

use App\Models\DnsRecord;
use App\Models\MonitoredDomain;
use App\Services\DomainNameService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckMonitoredDomains implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $domains = MonitoredDomain::all();
        $maxDistance = 3; // Max levenshtein distance for similar domains
        $domainNameService = new DomainNameService();

        foreach ($domains as $domain) {
            $similarDomains = $domainNameService->findSimilarDomains($domain->domain, $maxDistance);
            foreach ($similarDomains as $simDomain) {
                DnsRecord::updateOrCreate(
                    ['possible_domain' => $simDomain],
                    ['dns_data' => json_encode(dns_get_record($simDomain, DNS_A | DNS_NS))]
                );
            }
        }
    }
}