<?php

namespace Tests;

use App\Models\Role;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(
            \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
        );

        if (! Role::where('name', 'Customer')->exists()) {
            Role::create(['name' => 'Customer']);
        }
    }
}
