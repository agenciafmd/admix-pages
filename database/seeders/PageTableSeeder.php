<?php

namespace Agenciafmd\Pages\Database\Seeders;

use Agenciafmd\Pages\Models\Page;
use Illuminate\Database\Seeder;

class PageTableSeeder extends Seeder
{
    protected int $total = 20;

    public function run(): void
    {
        Page::query()
            ->truncate();

        $this->command->getOutput()
            ->progressStart($this->total);

        collect(range(1, $this->total))
            ->each(function () {
                Page::factory()
                    ->create();

                $this->command->getOutput()
                    ->progressAdvance();
            });

        $this->command->getOutput()
            ->progressFinish();
    }
}
