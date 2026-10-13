<?php

declare(strict_types=1);

use Hwkdo\ConfigmgrLaravel\ConfigmgrLaravel;
use Illuminate\Support\Facades\DB;

afterEach(function (): void {
    Mockery::close();
    Illuminate\Support\Facades\Facade::clearResolvedInstances();
});

it('maps bios serials by exact uppercased computer name', function (): void {
    $connection = Mockery::mock();
    $connection->shouldReceive('select')
        ->once()
        ->andReturnUsing(function (string $sql): array {
            expect($sql)->toContain('v_GS_PC_BIOS')
                ->toContain('SerialNumber0')
                ->not->toContain('LIKE');

            return [
                (object) ['name' => ' pc-one ', 'serial_number' => ' AB-12 '],
                (object) ['name' => 'pc-one', 'serial_number' => ''],
                (object) ['name' => 'pc-two', 'serial_number' => null],
                (object) ['name' => '  ', 'serial_number' => 'IGNORED'],
            ];
        });

    DB::shouldReceive('connection')->with('sccm')->andReturn($connection);

    $api = new ConfigmgrLaravel;

    expect($api->getBiosSerialNumbersByComputerName())->toBe([
        'PC-ONE' => 'AB-12',
        'PC-TWO' => null,
    ]);
});
