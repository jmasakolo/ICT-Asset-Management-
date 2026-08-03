<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 13px; color: #555; margin-top: 24px; margin-bottom: 8px; }
        .meta { color: #777; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #ddd; }
        th { color: #555; font-weight: normal; border-bottom: 1px solid #999; }
        .stats { width: 100%; margin-bottom: 8px; }
        .stats td { border: none; padding: 4px 8px; }
        .stats .label { color: #777; }
        .stats .value { font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ setting('site_name', 'To Do') }} — Report</h1>
    <p class="meta">Generated {{ now()->format('M j, Y g:ia') }}</p>

    <h2>Maintenance</h2>
    <table class="stats">
        <tr>
            <td>
                <div class="label">Records logged</div>
                <div class="value">{{ $maintenance['count'] }}</div>
            </td>
            <td>
                <div class="label">Total cost</div>
                <div class="value">{{ setting('currency_symbol', '$') }}{{ number_format($maintenance['totalCost'], 2) }}</div>
            </td>
            <td>
                <div class="label">Average cost</div>
                <div class="value">{{ setting('currency_symbol', '$') }}{{ number_format($maintenance['averageCost'], 2) }}</div>
            </td>
        </tr>
    </table>

    <h2>Users</h2>
    <table class="stats">
        <tr>
            <td>
                <div class="label">Total users</div>
                <div class="value">{{ $users['total'] }}</div>
            </td>
            <td>
                <div class="label">With assets assigned</div>
                <div class="value">{{ $users['withAssets'] }}</div>
            </td>
            <td>
                <div class="label">Without assets assigned</div>
                <div class="value">{{ $users['withoutAssets'] }}</div>
            </td>
        </tr>
    </table>

    <h2>Top assets by maintenance cost</h2>
    <table>
        <thead>
            <tr>
                <th>Asset</th>
                <th>Records</th>
                <th>Total cost</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topAssetsByCost as $asset)
                <tr>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->maintenance_records_count }}</td>
                    <td>{{ setting('currency_symbol', '$') }}{{ number_format($asset->maintenance_records_sum_cost ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No maintenance costs logged yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
