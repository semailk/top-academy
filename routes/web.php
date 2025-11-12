<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

//use Illuminate\Support\Facades\Auth;


// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // Posts
    Route::resource('posts', PostController::class);

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // Profile
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/avatar-remove', [UserController::class, 'removeAvatar'])->name('avatar.remove');

    // About
    Route::get('/about', function () {
        return view('about');
    })->name('about');
    // Прямой роут для аватарок
    Route::get('/avatar/{filename}', function ($filename) {
        $path = storage_path('app/public/avatars/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        $mime = mime_content_type($path);
        $file = file_get_contents($path);

        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Length', filesize($path));
    })->name('avatar.direct');
});
// Route::get('/quick-check', function () {
//     return response()->json([
//         'is_authenticated' => auth()->check(),
//         'user_id' => auth()->id(),
//         'user' => auth()->user() ? [
//             'id' => auth()->user()->id,
//             'username' => auth()->user()->username,
//             'email' => auth()->user()->email
//         ] : null,
//         'session_id' => session()->getId()
//     ]);
// });
// Route::get('/test-auth', function () {
//     return "Auth ID: " . Auth::id() . " (type: " . gettype(Auth::id()) . ")";
// });

// Route::get('/check-avatars', function () {
//     $user = Auth::user();
//     $avatarFiles = Storage::files('public/avatars');

//     echo "<h1>Проверка аватарок</h1>";
//     echo "<p>Текущий пользователь: {$user->username}</p>";
//     echo "<p>Avatar в БД: " . ($user->avatar ?? 'NULL') . "</p>";

//     if ($user->avatar) {
//         $filePath = 'public/avatars/' . $user->avatar;
//         $fileExists = Storage::exists($filePath);
//         echo "<p>Файл существует: " . ($fileExists ? 'ДА' : 'НЕТ') . "</p>";

//         if ($fileExists) {
//             $fileSize = Storage::size($filePath);
//             $mimeType = Storage::mimeType($filePath);

//             echo "<p>Размер файла: " . $fileSize . " байт</p>";
//             echo "<p>MIME тип: " . $mimeType . "</p>";
//             echo "<p>URL: " . $user->getAvatarUrl() . "</p>";

//             // Прямая ссылка на файл
//             echo "<p>Прямая ссылка: <a href='" . $user->getAvatarUrl() . "' target='_blank'>Открыть в новой вкладке</a></p>";

//             echo "<img src='" . $user->getAvatarUrl() . "' style='max-width: 200px; border: 2px solid red'>";

//             // Проверим содержимое файла
//             $fileContent = Storage::get($filePath);
//             echo "<p>Первые 100 байт файла: " . substr($fileContent, 0, 100) . "</p>";
//         }
//     }
// });

// Route::get('/check-storage-details', function () {
//     echo "<h1>Детальная проверка Storage</h1>";

//     $user = Auth::user();
//     $avatarFile = $user->avatar;

//     echo "<h2>Информация об аватарке</h2>";
//     echo "Файл в БД: " . ($avatarFile ?? 'NULL') . "<br>";

//     if ($avatarFile) {
//         // Проверим в storage
//         $storagePath = 'public/avatars/' . $avatarFile;
//         echo "Путь в storage: " . $storagePath . "<br>";
//         echo "Файл в storage: " . (Storage::exists($storagePath) ? '✅ СУЩЕСТВУЕТ' : '❌ НЕ СУЩЕСТВУЕТ') . "<br>";

//         if (Storage::exists($storagePath)) {
//             echo "Размер файла: " . Storage::size($storagePath) . " байт<br>";
//         }

//         // Проверим через симлинк
//         $publicPath = 'storage/avatars/' . $avatarFile;
//         $fullPublicPath = public_path($publicPath);
//         echo "Путь через симлинк: " . $fullPublicPath . "<br>";
//         echo "Файл через симлинк: " . (file_exists($fullPublicPath) ? '✅ СУЩЕСТВУЕТ' : '❌ НЕ СУЩЕСТВУЕТ') . "<br>";

//         // Проверим URL
//         $url = asset($publicPath);
//         echo "URL: <a href='{$url}' target='_blank'>{$url}</a><br>";

//         // Прямая проверка файла
//         echo "<h3>Прямая проверка файла:</h3>";
//         $directStoragePath = storage_path('app/' . $storagePath);
//         echo "Прямой путь: " . $directStoragePath . "<br>";
//         echo "Файл существует: " . (file_exists($directStoragePath) ? '✅ ДА' : '❌ НЕТ') . "<br>";

//         if (file_exists($directStoragePath)) {
//             echo "Размер: " . filesize($directStoragePath) . " байт<br>";
//             echo "MIME тип: " . mime_content_type($directStoragePath) . "<br>";

//             // Попробуем показать изображение разными способами
//             echo "<h3>Тест отображения:</h3>";

//             // Способ 1: Через симлинк
//             echo "<h4>Через симлинк:</h4>";
//             echo "<img src='{$url}' style='max-width: 200px; border: 2px solid blue' onerror='this.style.border=\"2px solid red\"'>";

//             // Способ 2: Прямой роут
//             echo "<h4>Через прямой роут:</h4>";
//             $directUrl = route('avatar.direct', ['filename' => $avatarFile]);
//             echo "<img src='{$directUrl}' style='max-width: 200px; border: 2px solid green' onerror='this.style.border=\"2px solid red\"'>";
//         }
//     }
// });
// // Прямой роут для обслуживания аватарок
// Route::get('/avatar-file/{filename}', function ($filename) {
//     $path = storage_path('app/public/avatars/' . $filename);

//     if (!file_exists($path)) {
//         abort(404);
//     }

//     $mime = mime_content_type($path);
//     $file = file_get_contents($path);

//     return response($file, 200)
//            ->header('Content-Type', $mime)
//            ->header('Content-Length', filesize($path));
// })->name('avatar.direct');

// Route::get('/fix-storage-link', function () {
//     echo "<h1>Исправление Storage Link</h1>";

//     // Удаляем старый симлинк если существует
//     $symlinkPath = public_path('storage');
//     if (file_exists($symlinkPath)) {
//         if (is_link($symlinkPath)) {
//             unlink($symlinkPath);
//             echo "✅ Старый симлинк удален<br>";
//         } else {
//             // В Windows это может быть папка, а не симлинк
//             rmdir($symlinkPath);
//             echo "✅ Старая папка удалена<br>";
//         }
//     }

//     // Создаем новый симлинк
//     Artisan::call('storage:link');
//     echo "✅ Новый симлинк создан<br>";

//     // Проверим
//     if (file_exists($symlinkPath)) {
//         echo "✅ Симлинк существует: " . $symlinkPath . "<br>";
//         if (is_link($symlinkPath)) {
//             echo "✅ Это симлинк на: " . readlink($symlinkPath) . "<br>";
//         } else {
//             echo "❌ Это НЕ симлинк, а обычная папка<br>";
//         }
//     } else {
//         echo "❌ Симлинк не создался<br>";
//     }

//     return "<a href='/check-storage-details'>Проверить результат</a>";
// });
