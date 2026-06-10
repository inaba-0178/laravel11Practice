<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Services\MailService;
use App\Application\UseCases\RoomInvite\ApproveInviteUseCase;
use App\Application\UseCases\RoomInvite\GetPendingInvitesUseCase;
use App\Application\UseCases\RoomInvite\RejectInviteUseCase;
use App\Domain\RoomInvite\Repositories\RoomInviteRepositoryInterface;
use App\Infrastructure\Repositories\RoomInvite\EloquentRoomInviteRepository;
use Illuminate\Support\ServiceProvider;

class RoomInviteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            RoomInviteRepositoryInterface::class,
            EloquentRoomInviteRepository::class,
        );

        $this->app->bind(GetPendingInvitesUseCase::class, function () {
            return new GetPendingInvitesUseCase(
                new EloquentRoomInviteRepository(),
            );
        });

        $this->app->bind(ApproveInviteUseCase::class, function () {
            return new ApproveInviteUseCase(
                new EloquentRoomInviteRepository(),
            );
        });

        $this->app->bind(RejectInviteUseCase::class, function () {
            return new RejectInviteUseCase(
                new EloquentRoomInviteRepository(),
                app(MailService::class),
            );
        });
    }

    public function boot(): void {}
}