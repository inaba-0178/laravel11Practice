<?php
namespace App\Application\UseCases\Auth;

use App\Domain\Auth\ValueObjects\LoginCredentials;
use Illuminate\Support\Facades\Auth;

class LoginUseCase
{
    /**
     * ログイン
     * 
     * @return LoginOutputData
     */
    public function execute(LoginCredentials $credentials): LoginOutputData
    {

        if (!Auth::attempt([
            'email'    => $credentials->email,
            'password' => $credentials->password,
        ])) {
            throw new \RuntimeException('メールアドレスまたはパスワードが正しくありません');
        }

        $user  = Auth::user();
        $token = $user->createToken('chat-token')->plainTextToken;

        return new LoginOutputData(user: $user, token: $token);

    }
}