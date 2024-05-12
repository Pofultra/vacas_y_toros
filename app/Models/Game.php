<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_name',
        'user_age',
        'secret_code',
        'attempts',
        'remaining_time',
        'status',
        'token',
    ];

    protected $casts = [
        'attempts' => 'array',
    ];

    public static function isGameDataFileValid($fileName)
    {
        $filePath = storage_path($fileName . '.json');
        if (!Storage::exists($filePath)) {
            return false;
        }
        $fileContent = Storage::get($filePath);
        if ($fileContent === false) {
            return false;
        }
        return true;
    }
    public static function createGameDataFile($inputString)
    {
        $fileName = storage_path($inputString . '.json');
        $fileContent = json_encode([]);

        // Check if file already exists
        if (Storage::exists($fileName)) {
            // Delete the previous file
            Storage::delete($fileName);
        }

        Storage::put($fileName, $fileContent);
    }
    public static function updateGameDataFile($fileName, $newContent)
    {
        $filePath = storage_path($fileName . '.json');

        $fileContent = json_encode($newContent);

        Storage::put($filePath, $fileContent);
    }
    public static function getGameDataFile($fileName)
    {
        $filePath = storage_path($fileName . '.json');
        if (!Storage::exists($filePath)) {
            return false;
        }
        $fileContent = Storage::get($filePath);
        $parsedContent = json_decode($fileContent, true);

        return $parsedContent;
    }
    public static function deleteGameDataFile($fileName)
    {
        $filePath = storage_path($fileName . '.json');

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
            return true;
        }

        return false;
    }
    public static function getGameByUserNameAndAge($userName, $userAge)
    {
        return self::where('user_name', $userName)
            ->where('user_age', $userAge)
            ->first();
    }
}
