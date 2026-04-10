<?php

declare(strict_types=1);

use Hwkdo\ConfigmgrLaravel\ConfigmgrLaravel;
use Illuminate\Support\Facades\DB;

afterEach(function (): void {
    Mockery::close();
    Illuminate\Support\Facades\Facade::clearResolvedInstances();
});

it('returns distinct normalized computer names without domain filter', function () {
    $connection = Mockery::mock();
    $connection->shouldReceive('select')
        ->once()
        ->andReturnUsing(function (string $sql, array $bindings = []) {
            expect($bindings)->toBe([]);
            expect($sql)->toContain('v_R_System')->not->toContain('Resource_Domain_OR_Workgr0');

            return [
                (object) ['name' => 'pc-one'],
                (object) ['name' => ' PC-TWO '],
                (object) ['name' => 'pc-one'],
            ];
        });

    DB::shouldReceive('connection')->with('sccm')->andReturn($connection);

    $api = new ConfigmgrLaravel;

    expect($api->getDistinctComputerNamesByResourceDomains(null))->toBe(['PC-ONE', 'PC-TWO']);
});

it('filters by resource domains with uppercased bindings', function () {
    $connection = Mockery::mock();
    $connection->shouldReceive('select')
        ->once()
        ->andReturnUsing(function (string $sql, array $bindings) {
            expect($bindings)->toBe(['MYDOM', 'OTHER']);
            expect($sql)->toContain('Resource_Domain_OR_Workgr0')->toContain('IN (?,?)')->toContain('v_R_System');

            return [(object) ['name' => 'Client']];
        });

    DB::shouldReceive('connection')->with('sccm')->andReturn($connection);

    $api = new ConfigmgrLaravel;

    expect($api->getDistinctComputerNamesByResourceDomains(['MyDom', ' other ', '', null]))->toBe(['CLIENT']);
});

it('ignores empty trimmed names in result rows', function () {
    $connection = Mockery::mock();
    $connection->shouldReceive('select')->once()->andReturn([
        (object) ['name' => '  '],
        (object) ['name' => 'ok'],
    ]);

    DB::shouldReceive('connection')->with('sccm')->andReturn($connection);

    $api = new ConfigmgrLaravel;

    expect($api->getDistinctComputerNamesByResourceDomains(null))->toBe(['OK']);
});
