<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Exports\PostExporter;
use App\Filament\Imports\PostImporter;
use App\Filament\Resources\PostResource;
use App\Filament\Resources\PostResource\Widgets\PostsChart;
use App\Filament\Resources\PostResource\Widgets\StatsOverview;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
                ->importer(PostImporter::class),
            ExportAction::make()
            ->exporter(PostExporter::class)
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            PostsChart::class,
        ];
    }
}
