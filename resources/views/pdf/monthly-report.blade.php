<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('report.title') }}</title>
    <style>
        body {
            font-family: sans-serif;
            color: #1a1a1a;
            font-size: 14px;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 0;
        }

        .subtitle {
            color: #555555;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 32px;
        }

        td {
            border: 1px solid #cccccc;
            padding: 12px;
        }

        .stat-label {
            width: 70%;
        }

        .stat-value {
            width: 30%;
            text-align: right;
            font-weight: bold;
        }

        .generated-at {
            margin-top: 32px;
            color: #888888;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <h1>{{ __('report.shelter_name') }}</h1>
    <p class="subtitle">{{ __('report.title') }}</p>
    <p class="subtitle">
        {{ __('report.period', ['start' => $start->translatedFormat('d/m/Y'), 'end' => $end->translatedFormat('d/m/Y')]) }}
    </p>

    <table>
        <tr>
            <td class="stat-label">{{ __('report.stats.animals_received') }}</td>
            <td class="stat-value">{{ $statistics['animalsReceived'] }}</td>
        </tr>
        <tr>
            <td class="stat-label">{{ __('report.stats.successful_adoptions') }}</td>
            <td class="stat-value">{{ $statistics['successfulAdoptions'] }}</td>
        </tr>
        <tr>
            <td class="stat-label">
                {{ __('report.stats.animals_still_present', ['date' => $end->translatedFormat('d/m/Y')]) }}
            </td>
            <td class="stat-value">{{ $statistics['animalsStillPresent'] }}</td>
        </tr>
    </table>

    <p class="generated-at">
        {{ __('report.generated_at', ['date' => now()->translatedFormat('d/m/Y H:i')]) }}
    </p>
</body>
</html>