<?php

namespace Filament\Resources\Pages;

use Filament\Pages\Page as BasePage;
use Filament\Panel;
use Filament\Resources\Resource;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

abstract class Page extends BasePage
{
    protected static ?string $breadcrumb = null;

    protected static string $resource;

    public static function route(string $path): PageRegistration
    {
        return new PageRegistration(
            page: static::class,
            route: fn (Panel $panel): Route => RouteFacade::get($path, static::class)
                ->middleware(static::getRouteMiddleware($panel)),
        );
    }

    public static function getEmailVerifiedMiddleware(Panel $panel): string
    {
        return static::getResource()::getEmailVerifiedMiddleware($panel);
    }

    public static function isEmailVerificationRequired(Panel $panel): bool
    {
        return static::getResource()::isEmailVerificationRequired($panel);
    }

    public static function getTenantSubscribedMiddleware(Panel $panel): string
    {
        return static::getResource()::getTenantSubscribedMiddleware($panel);
    }

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return static::getResource()::isTenantSubscriptionRequired($panel);
    }

    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb ?? static::getTitle();
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $breadcrumb = $this->getBreadcrumb();

        return [
            $resource::getUrl() => $resource::getBreadcrumb(),
            ...(filled($breadcrumb) ? [$breadcrumb] : []),
        ];
    }

    public static function authorizeResourceAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }

    public function getModel(): string
    {
        return static::getResource()::getModel();
    }

    /**
     * @return class-string<resource>
     */
    public static function getResource(): string
    {
        return static::$resource;
    }
}
