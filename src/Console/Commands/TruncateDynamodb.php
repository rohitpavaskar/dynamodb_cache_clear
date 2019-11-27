<?php

namespace Rohitpavaskar\DynamodbCacheClear\Console\Commands;

use Illuminate\Console\Command;
use AWS;
use Config;
use Illuminate\Support\Facades\Artisan;

class TruncateDynamodb extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamodb:truncate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate dynamodb to clear cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        Config::set('cache.default', 'file');
        $dynamoDbTableName = Config::get('cache.stores.dynamodb.table');
        $dunamoDB = AWS::createClient('dynamodb');
        try {
            $status = $dunamoDB->deleteTable([
                'TableName' => $dynamoDbTableName, // REQUIRED
            ]);
        } catch (\Exception $e) {
            echo 'table not exist trying to create new one';
            $name = $this->anticipate('Do you want to create it?', ['Y', 'N']);
            if (strtolower($name) == 'y') {
                $this->createTable($dunamoDB, $dynamoDbTableName);
                exit;
            }
        }
        sleep(5);
        $this->createTable($dunamoDB, $dynamoDbTableName);
        Artisan::call('cache:clear');
        Config::set('cache.default', 'dynampdb');
    }

    function createTable($dunamoDB, $dynamoDbTableName) {
        $dunamoDB->createTable([
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'key',
                    'AttributeType' => 'S',
                ],
            ],
            'BillingMode' => 'PAY_PER_REQUEST',
            'KeySchema' => [
                [
                    'AttributeName' => 'key',
                    'KeyType' => 'HASH',
                ],
            ],
            'TableName' => $dynamoDbTableName,
        ]);
    }

}
