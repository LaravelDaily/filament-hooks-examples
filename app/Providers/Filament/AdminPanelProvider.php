<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\UsersCount;
use App\Filament\Widgets\VerifyUsers;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\View\TablesRenderHook;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ReflectionClass;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                UsersCount::class,
                VerifyUsers::class
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->authMiddleware([
                Authenticate::class,
            ]);

        // Panel Hooks
        $panelHooks = new ReflectionClass(PanelsRenderHook::class);
        // Table Hooks
        $tableHooks = new ReflectionClass(TablesRenderHook::class);
        // Widget Hooks
        $widgetHooks = new ReflectionClass(Widgets\View\WidgetsRenderHook::class);

        $panelHooks = $panelHooks->getConstants();
        $tableHooks = $tableHooks->getConstants();
        $widgetHooks = $widgetHooks->getConstants();

        foreach ($panelHooks as $hook) {
            $panel->renderHook($hook, function () use ($hook) {
                return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
                    'name' => Str::of($hook)->remove('tables::'),
                ]);
            });
        }
        foreach ($tableHooks as $hook) {
            $panel->renderHook($hook, function () use ($hook) {
                return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
                    'name' => Str::of($hook)->remove('tables::'),
                ]);
            });
        }
        foreach ($widgetHooks as $hook) {
            $panel->renderHook($hook, function () use ($hook) {
                return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
                    'name' => Str::of($hook)->remove('tables::'),
                ]);
            });
        }

        return $panel;
    }
}
