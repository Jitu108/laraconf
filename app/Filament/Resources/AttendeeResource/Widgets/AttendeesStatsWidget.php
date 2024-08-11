<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;


use App\Models\Attendee;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\AttendeeResource\Pages\ListAttendees;

class AttendeesStatsWidget extends BaseWidget
{
    use InteractsWithPageTable;
    protected function getColumns(): int
    {
        return 2;
    }
    protected function getTablePage(): string
    {
        return ListAttendees::class;
    }

    protected function getStats(): array
    {
        return [
            //Stat::make('Attendees Count', Attendee::count())
            Stat::make('Attendees Count', $this->getPageTableQuery()->count())
                ->description('Total number of attendees')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([1, 2, 3, 4, 2, 1, 5, 7, 4, 6, 1])
                ->color('success'),
            //Stat::make('Total Revenue', Attendee::sum('ticket_cost') / 100),
            Stat::make('Total Revenue', '$' . $this->getPageTableQuery()->sum('ticket_cost')),

        ];
    }
}
