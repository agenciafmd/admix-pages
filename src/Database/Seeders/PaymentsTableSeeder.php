<?php

namespace Agenciafmd\Payments\Database\Seeders;

use Agenciafmd\Payments\Models\Payment;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Http\File as HttpFile;
use Illuminate\Support\Facades\Storage;

class PaymentsTableSeeder extends Seeder
{
    protected int $total = 20;

    public function run()
    {
        Payment::query()
            ->truncate();

        $this->command->getOutput()
            ->progressStart($this->total);

        $faker = Factory::create('pt_BR');

        Payment::factory($this->total)
            ->create()
            ->each(function ($payment) use ($faker) {

                foreach (config('upload-configs.payment') as $key => $image) {
                    $fakerDir = __DIR__ . '/../Faker/payments/' . $key;

                    if ($image['faker_dir']) {
                        $fakerDir = $image['faker_dir'];
                    }

                    if ($image['multiple']) {
                        $items = $faker->numberBetween(0, 6);
                        for ($i = 0; $i < $items; $i++) {
                            $sourceFile = $faker->file($fakerDir, storage_path('admix/tmp'));
                            $targetFile = Storage::putFile('tmp', new HttpFile($sourceFile));

                            $payment->doUploadMultiple($targetFile, $key);
                        }
                    } else {
                        $sourceFile = $faker->file($fakerDir, storage_path('admix/tmp'));
                        $targetFile = Storage::putFile('tmp', new HttpFile($sourceFile));

                        $payment->doUpload($targetFile, $key);
                    }
                }

                $payment->save();

                $this->command->getOutput()
                    ->progressAdvance();
            });

        $this->command->getOutput()
            ->progressFinish();
    }
}
