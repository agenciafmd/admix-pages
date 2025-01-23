<?php

namespace Agenciafmd\Pages\Database\Seeders;

use Agenciafmd\Pages\Models\Page;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Http\File as HttpFile;
use Illuminate\Support\Facades\Storage;

class PagesTableSeeder extends Seeder
{
    protected int $total = 20;

    public function run(): void
    {
        Page::query()
            ->truncate();

        $this->command->getOutput()
            ->progressStart($this->total);

        $faker = Factory::create('pt_BR');

        Page::factory($this->total)
            ->create()
            ->each(function ($page) use ($faker) {

                foreach (config('upload-configs.page') as $key => $image) {
                    $fakerDir = __DIR__ . '/../Faker/pages/' . $key;

                    if ($image['faker_dir']) {
                        $fakerDir = $image['faker_dir'];
                    }

                    if ($image['multiple']) {
                        $items = $faker->numberBetween(0, 6);
                        for ($i = 0; $i < $items; $i++) {
                            $sourceFile = $faker->file($fakerDir, storage_path('admix/tmp'));
                            $targetFile = Storage::putFile('tmp', new HttpFile($sourceFile));

                            $page->doUploadMultiple($targetFile, $key);
                        }
                    } else {
                        $sourceFile = $faker->file($fakerDir, storage_path('admix/tmp'));
                        $targetFile = Storage::putFile('tmp', new HttpFile($sourceFile));

                        $page->doUpload($targetFile, $key);
                    }
                }

                $page->save();

                $this->command->getOutput()
                    ->progressAdvance();
            });

        $this->command->getOutput()
            ->progressFinish();
    }
}
