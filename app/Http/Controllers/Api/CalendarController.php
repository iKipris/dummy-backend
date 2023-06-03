<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use DateTime;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Maybe this should be in a Request class specificly for this request?
        $requestData = $request->all();
        $events      = $request->user()->calendarEvents->whereIn('calendar', explode(',', $requestData['calendars']));

        $eventsResponse = [];
        //Resources? no time
        if ($events) {
            foreach ($events as $item) {
                $eventsResponse[] = [
                    "id"            => $item["id"],
                    "title"         => $item["title"],
                    "start"         => $item["start"],
                    "end"           => $item["end"],
                    "extendedProps" => [
                        "calendar"    => $item["calendar"],
                        "guests"      => $item["guests"] !== '' ? explode(', ', $item["guests"]) : [],
                        "url"         => $item["url"],
                        "description" => $item["description"],
                    ],
                ];
            }
        }

        return response()->json(['events' => $eventsResponse]);
    }

    public function store(Request $request)
    {
        $requestData = $request->all();
        $user_id     = $request->user()->id;

        $start = DateTime::createFromFormat(
            'Y-m-d H:i',
            $requestData['event']['start'],
            new DateTimeZone('Europe/Athens')
        );
        $start->setTimezone(new DateTimeZone('UTC'));
        $utcStart = $start->format('D, d M Y H:i:s \G\M\T');

        $end = DateTime::createFromFormat('Y-m-d H:i', $requestData['event']['end'], new DateTimeZone('Europe/Athens'));
        $end->setTimezone(new DateTimeZone('UTC'));
        $utcEnd = $start->format('D, d M Y H:i:s \G\M\T');

        CalendarEvent::create([
            'user_id'     => $user_id,
            'title'       => $requestData['event']['title'],
            'start'       => $utcStart,
            'end'         => $utcEnd,
            'calendar'    => $requestData['event']['extendedProps']['calendar'] ?? '',
            'url'         => $requestData['event']['extendedProps']['url'] ?? '',
            'guests'      => implode(', ', $requestData['event']['extendedProps']['guests']) ?? '',
            'description' => $requestData['event']['extendedProps']['description'] ?? '',
        ]);

        return response()->json([
            'message' => 'Event successfully added'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        $user_id     = $request->user()->id;
        $calendar = CalendarEvent::find($id);
        $calendar->update([
            'user_id'     => $user_id,
            'title'       => $requestData['event']['title'],
            'start'       => $requestData['event']['start'],
            'end'         => $requestData['event']['end'],
            'calendar'    => $requestData['event']['extendedProps']['calendar'] ?? '',
            'url'         => $requestData['event']['extendedProps']['url'] ?? '',
            'guests'      => implode(', ', $requestData['event']['extendedProps']['guests']) ?? '',
            'description' => $requestData['event']['extendedProps']['description'] ?? '',
        ]);

        return response()->json([
            'message' => 'Event successfully added'
        ], 200);
    }

    public function delete($id)
    {
        $calendar = CalendarEvent::find($id);
        $calendar->delete();

        return response()->json([
            'message' => 'Event successfully added'
        ], 200);
    }
}
