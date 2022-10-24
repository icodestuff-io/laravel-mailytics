<?php

namespace Icodestuff\Mailytics\Http\Controllers;

use Icodestuff\Mailytics\Jobs\ViewedEmail;
use Icodestuff\Mailytics\Models\Mailytics;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class MailyticsController extends Controller
{
    use ValidatesRequests;
    /** @var string */
    protected $period;

    public function trackView($pixel, Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(404);
        }

        $path = storage_path('app/public/mailytics/'.$pixel);
        if (! File::exists($path)) {
            abort(404);
        }

        ViewedEmail::dispatch($pixel);

        // Mark as Seen
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file);
        $response->header('Content-Type', $type);

        return $response;
    }

    public function trackClick($pixel, Request $request)
    {
        $this->validate($request, [
            'redirect_uri' => ['required'],
            'pixel' => ['required', 'string']
        ]);

        dd($request->get('redirect_uri'));

        if ($request->hasValidSignature()) {
            $mailytics = Mailytics::where('pixel', '=', $pixel)->first();

        }


        return redirect($request->get('redirect_uri', '/'));
    }

    public function dashboard()
    {
        $this->period = request()->get('period', '30_days');

        $sentEmailCount = Mailytics::query()->scopes(['filter' => [$this->period]])->count();

        $sentEmailChart = Mailytics::query()->scopes(['filter' => [$this->period]])
            ->select([DB::raw('DATE(sent_at) as sent_email_labels'), DB::raw('count(*) as sent_email_data')])
            ->groupBy('sent_email_labels')
            ->get();

        $openRateChart = Mailytics::query()->scopes(['filter' => [$this->period]])
            ->whereNotNull('seen_at')
            ->select(DB::raw('DATE(sent_at) as open_rate_labels'), DB::raw('count(*) as open_rate_data'))
            ->groupBy('open_rate_labels')
            ->get();

        return view('mailytics::dashboard', [
            'sent_emails' => [
                'count' => $sentEmailCount,
                'labels' => $sentEmailChart->pluck('sent_email_labels')->toArray(),
                'data' => $sentEmailChart->pluck('sent_email_data')->toArray()
            ],
            'open_rate' => [
                'percentage' => number_format($openRateChart->count() / $sentEmailCount* 100, 2),
                'labels' => $openRateChart->pluck('open_rate_labels')->toArray(),
                'data' => $openRateChart->pluck('open_rate_data')->toArray()
            ],
            'click_rate' => 0,
            'periods' => $this->periods(),
            'period' => $this->period
        ]);
    }

    protected function periods(): array
    {
        return [
            'today'     => 'Today',
            'yesterday' => 'Yesterday',
            '1_week'    => 'Last 7 days',
            '30_days'   => 'Last 30 days',
            '6_months'  => 'Last 6 months',
            '12_months' => 'Last 12 months',
            'all_time' => 'All Time',
        ];
    }
}
