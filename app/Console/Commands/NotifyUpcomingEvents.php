<?php

namespace App\Console\Commands;

use App\Helpers\NotificationHelper;
use App\Models\CalendarEvent;
use App\Models\Work;
use Illuminate\Console\Command;

class NotifyUpcomingEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-upcoming-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    protected $signature = 'notifications:upcoming-events';
    protected $description = 'Notifica eventos y trabajos asignados que vencen en 24h';

    public function handle()
    {
        $now = now();
        $target = $now->copy()->addDay()->format('Y-m-d');

        // Notificar trabajos con due_date mañana
        $works = Work::whereDate('due_date', $target)->get();
        foreach ($works as $work) {
            if ($work->assigned_to) {
                NotificationHelper::notify($work->assigned_to, "⏰ El trabajo \"{$work->titulo}\" vence mañana.", $work->id);
            }
        }

        // Notificar eventos de calendario asignados
        $events = CalendarEvent::with('admins')
            ->whereDate('start', $target)
            ->get();

        foreach ($events as $event) {
            foreach ($event->admins as $admin) {
                NotificationHelper::calendar($admin->id, "📅 Tienes un evento mañana: \"{$event->title}\".");
            }
        }

        $this->info('Notificaciones programadas enviadas.');
    }

    // AÑADIR EL SIGUIENTE CRON JOB AL SERVIDOR:
    // * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

}
