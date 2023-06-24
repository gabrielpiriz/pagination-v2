<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sammyjo20\SaloonPagination\TestPagedPaginator;
use Sammyjo20\SaloonPagination\Tests\Fixtures\TestConnector;
use Sammyjo20\SaloonPagination\Tests\Fixtures\SuperheroPagedRequest;

test('if the response fails it throws an exception', function () {
    $mockClient = new MockClient([
        MockResponse::make(['error' => 'Server Unavailable'], 500),
    ]);

    $connector = new TestConnector();
    $connector->withMockClient($mockClient);
    
    $request = new SuperheroPagedRequest();
    $paginator = new TestPagedPaginator($connector, $request);

    $this->expectException(Exception::class);
    $paginator->current();
});

test('total results counter is incremented with each page', function () {
    $connector = new TestConnector();
    
    $request = new SuperheroPagedRequest();
    $paginator = new TestPagedPaginator($connector, $request);

    $paginator->setMaxPages(3);

    $totalResultsHistory = [];

    foreach ($paginator as $response) {
        $totalResultsHistory[] = $paginator->getTotalResults();
    }
    
    expect($totalResultsHistory)->toEqual([5, 10, 15]);
});
