<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Existing installs don't re-run PermissionsTableSeeder, so create the
     * four `relations` permissions here and grant each to every role that
     * already holds the equivalent `dates` permission. That mirrors the
     * access level roles already have for contact sub-resources, so nobody
     * has to hand-edit roles after deploy. New permissions cache is flushed.
     */
    public function up(): void
    {
        foreach (['view', 'create', 'edit', 'delete'] as $action) {
            $relationName = $action . ' relations';

            $relationId = DB::table('permissions')->where('name', $relationName)->value('id')
                ?? DB::table('permissions')->insertGetId([
                    'name' => $relationName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            $datesId = DB::table('permissions')->where('name', $action . ' dates')->value('id');

            if (! $datesId) {
                continue;
            }

            $roleIds = DB::table('permission_role')
                ->where('permission_id', $datesId)
                ->pluck('role_id');

            foreach ($roleIds as $roleId) {
                DB::table('permission_role')->insertOrIgnore([
                    'permission_id' => $relationId,
                    'role_id' => $roleId,
                ]);
            }
        }

        $this->flushPermissionCache();
    }

    public function down(): void
    {
        $ids = DB::table('permissions')
            ->whereIn('name', ['view relations', 'create relations', 'edit relations', 'delete relations'])
            ->pluck('id');

        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();

        $this->flushPermissionCache();
    }

    /**
     * PermissionRegistrar caches the permission set per team under the key
     * "permissions-{team_id}". There's no authenticated user during a
     * migration, so forget each team's key explicitly.
     */
    private function flushPermissionCache(): void
    {
        foreach (DB::table('teams')->pluck('id') as $teamId) {
            app('cache')->forget('permissions-' . $teamId);
        }
    }
};
