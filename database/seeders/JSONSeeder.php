<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JSONSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data');
        $files = File::files($path);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'json') continue;

            $filename = $file->getFilenameWithoutExtension(); // e.g., "users"
            $modelName = 'App\\Models\\' . Str::studly(Str::singular($filename)); // e.g., App\Models\User

            if (!class_exists($modelName)) {
                $this->command->warn("⚠️ Model not found for: $modelName");
                continue;
            }

            $data = json_decode(File::get($file->getRealPath()), true);

            foreach ($data as $entry) {
                // Automatically hash passwords if present
                if (isset($entry['password'])) {
                    $entry['password'] = Hash::make($entry['password']);
                }

                // JSON encode array fields like 'permissions'
                foreach ($entry as $key => $value) {
                    if (is_array($value)) {
                        $entry[$key] = json_encode($value);
                    }
                }

                $modelName::updateOrCreate(
                    ['email' => $entry['email'] ?? $entry['username'] ?? null],
                    $entry
                );
            }

            $this->command->info("✅ Seeded " . count($data) . " record(s) into {$modelName} from {$file->getFilename()}");
        }
    }
}


