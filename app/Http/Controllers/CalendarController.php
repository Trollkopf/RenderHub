<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\CalendarEvent;

class CalendarController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'recurrence' => 'nullable|in:daily,weekly,monthly,quarterly,yearly',
            'repeat_count' => 'nullable|integer|min:1',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string|max:500',

        ]);

        $baseEvent = CalendarEvent::create([
            'title' => $request->title,
            'start' => $request->date,
            'recurrence' => $request->recurrence,
            'repeat_count' => $request->repeat_count,
            'color' => $request->color,
            'description' => $request->description,

        ]);

        if ($request->has('admins')) {
            $baseEvent->admins()->sync($request->admins);
        }

        return response()->json($baseEvent, 201);
    }

    public function api()
    {

        $events = CalendarEvent::all();
        $expanded = collect();

        foreach ($events as $event) {
            $expanded->push([
                'title' => $event->title,
                'id' => $event->id,
                'start' => Carbon::parse($event->start)->toDateString(),
                'color' => $event->color,
                'description' => $event->description,
                'admins' => $event->admins->pluck('name')->toArray(),
            ]);

            if ($event->recurrence && $event->repeat_count) {
                $start = Carbon::parse($event->start);
                $interval = match ($event->recurrence) {
                    'daily' => '1 day',
                    'weekly' => '1 week',
                    'monthly' => '1 month',
                    'quarterly' => '3 months',
                    'yearly' => '1 year',
                    default => null,
                };

                if ($interval) {
                    $current = $start->copy();

                    for ($i = 1; $i < $event->repeat_count; $i++) {
                        $current->add($interval);

                        $expanded->push([
                            'title' => $event->title . ' (Repetido)',
                            'start' => $current->toDateString(),
                            'color' => $event->color,
                            'description' => $event->description,
                            'parent_id' => $event->id,
                        ]);
                    }
                }
            }
        }

        // ✅ Añadir los trabajos con fecha de entrega
        $works = Work::with('client.user')
            ->whereNotNull('due_date')
            ->where('estado', '!=', 'finalizado') // opcional
            ->get();

        foreach ($works as $work) {
            $expanded->push([
                'title' => "🗓️ Entrega: {$work->titulo}",
                'start' => Carbon::parse($work->due_date)->toDateString(),
                'color' => '#f87171', // rojo suave
                'description' => 'Trabajo de ' . $work->client->user->name,
                'url' => route('admin.works.show', $work->id),
                'type' => 'work',
                'id' => "work-{$work->id}",
            ]);
        }

        return $expanded;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string|max:500',
        ]);

        $event = CalendarEvent::findOrFail($id);
        $event->update([
            'title' => $request->title,
            'start' => $request->start,
            'color' => $request->color,
            'description' => $request->description,
        ]);

        if ($request->has('admins')) {
            $event->admins()->sync($request->admins);
        }

        return response()->json($event);
    }

    public function destroy($id)
    {
        $event = CalendarEvent::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Evento eliminado']);
    }


}
