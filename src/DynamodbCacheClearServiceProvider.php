<?php

namespace Rohitpavaskar\DynamodbCacheClear;

use Illuminate\Support\ServiceProvider;

class DynamodbCacheClearServiceProvider extends ServiceProvider {

    /**
     * Publishes configuration file.
     *
     * @return  void
     */
    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\TruncateDynamodb::class,
            ]);
        }
    }

}
