<?php

namespace Kuato\Commands;

use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DropDatabaseTables extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kuato:dropdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops the tables in the projects database';

    /**
     * The Filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Composer
     */
    private $composer;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $result = \DB::select("SELECT concat('DROP TABLE IF EXISTS ', table_name, ';') as 'query'
        FROM information_schema.tables
        WHERE table_schema = '" . \DB::connection()->getDatabaseName() . "';");

        foreach ($result as $query) {
            \DB::statement($query->query);
        }
    }
}
