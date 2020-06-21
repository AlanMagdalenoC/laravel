<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;

class simplePost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute-request:post {--attempts=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute Simple post request';

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
        $attempts = (int)$this->option('attempts');
        $client = new Client();
        //Guzzle request  Handle question 4
        $requests = function ($total) {
            #uri = 'https://postman-echo.com/post';
            $uri = 'https://atomic.incfile.com/fakepost';
            for ($i = 0; $i < $total; $i++) {
                yield new Request('POST', $uri,[
                    'json' => ['param1' => 'test']
                ]);
            }
        };
        /*
            Handle Question 5
            Using pool and promise handle question 5, because using the pool we can manage the attempts of request
            With promise force to complete the process and continue with the next request

        */

        $pool = new Pool($client, $requests($attempts), [
            'concurrency' => 5,
            'fulfilled' => function (Response $response, $index) {
                // this is delivered each successful response
                echo $response->getStatusCode()."\n";
                echo $response->getReasonPhrase()."\n";
                echo $response->getBody()."\n";
            },
            'rejected' => function (RequestException $reason, $index) {
                // this is delivered each failed request
                echo $reason->getMessage()."\n";
            },
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();

        // Force the pool of requests to complete.
        $promise->wait();




    }
}
