<?php
namespace Config;
use App\Models\Model;

abstract class Seeder
{
    public function call($seeders)
    {
        if (!is_array($seeders)) {
            $seeders = [$seeders];
        }

        foreach ($seeders as $seeder) {
            $this->runSeeder($seeder);
        }
    }

    protected function runSeeder($seeder)
    {
        if (!class_exists($seeder)) {
            echo "Seeder class {$seeder} not found\n";
            return;
        }

        $instance = new $seeder();

        if (!method_exists($instance, 'run')) {
            echo "Seeder {$seeder} does not have run() method\n";
            return;
        }

        echo "Seeding: {$seeder}\n";
        $instance->run();
        echo "Seeded: {$seeder}\n\n";
    }
}
