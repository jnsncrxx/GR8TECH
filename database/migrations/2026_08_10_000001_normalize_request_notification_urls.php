<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const URLS = [
        'leave' => '/leave-management?scope=mine',
        'overtime' => '/overtime?scope=mine',
        'official_business' => '/official-business?scope=mine',
    ];

    public function up(): void
    {
        DB::table('notifications')
            ->select(['id', 'data'])
            ->orderBy('id')
            ->each(function (object $notification) {
                $data = json_decode($notification->data, true);
                $requestType = $data['request_type'] ?? null;

                if (!isset(self::URLS[$requestType])) {
                    return;
                }

                $data['url'] = self::URLS[$requestType];

                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update(['data' => json_encode($data)]);
            });
    }

    public function down(): void
    {
        // Relative URLs remain valid regardless of deployment host or subfolder.
    }
};
