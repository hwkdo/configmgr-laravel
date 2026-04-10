<?php

declare(strict_types=1);

namespace Hwkdo\ConfigmgrLaravel;

use Illuminate\Support\Facades\DB;

class ConfigmgrLaravel
{
    /**
     * SCCM-Systemdaten anhand des Computernamens abrufen (v_R_System + MAC-Adressen).
     * Pro gefundener MAC-Adresse ein Datensatz; Rechnername/SMBIOS/Last-Logon-User pro Zeile.
     *
     * @return array<int, object{rechnername: string|null, smbios_guid: string|null, last_logon_user: string|null, mac_adresse: string|null}>
     */
    public function getSystemDataByComputerName(string $computerName): array
    {
        $result = DB::connection('sccm')->select(
            "SELECT
                sys.NAME0 AS rechnername,
                sys.SMBIOS_GUID0 AS smbios_guid,
                sys.USER_NAME0 AS last_logon_user,
                mac.MAC_Addresses0 AS mac_adresse
             FROM v_R_System sys
             LEFT JOIN v_RA_System_MACAddresses mac ON sys.ResourceID = mac.ResourceID
             WHERE sys.NAME0 LIKE ?
             ORDER BY sys.NAME0",
            ['%'.trim($computerName).'%']
        );

        return $result;
    }

    /**
     * Alle distinct Rechnernamen aus v_R_System (optional gefiltert nach AD-Ressourcendomäne).
     *
     * Filter nutzt Spalte {@code Resource_Domain_OR_Workgr0} in {@code v_R_System} (NetBIOS-Domäne / Workgroup; je nach SCCM-Version kann der Spaltenname abweichen).
     *
     * @param  array<int, string|null>|null  $resourceDomains  Nicht-leere Werte werden getrimmt, leere Einträge ignoriert; Vergleich case-insensitive (UPPER).
     * @return array<int, string> Eindeutige Namen, normalisiert mit strtoupper(trim()) für Abgleiche mit AD-Hostnamen
     */
    public function getDistinctComputerNamesByResourceDomains(?array $resourceDomains = null): array
    {
        $sql = <<<'SQL'
            SELECT DISTINCT LTRIM(RTRIM(sys.NAME0)) AS name
            FROM v_R_System sys
            WHERE sys.NAME0 IS NOT NULL AND LTRIM(RTRIM(sys.NAME0)) <> ''
            SQL;

        $bindings = [];

        $domains = array_values(array_filter(
            array_map(
                static fn ($d): string => is_string($d) ? trim($d) : '',
                $resourceDomains ?? []
            ),
            static fn (string $d): bool => $d !== ''
        ));

        if ($domains !== []) {
            $upperDomains = array_map(static fn (string $d): string => strtoupper($d), $domains);
            $placeholders = implode(',', array_fill(0, count($upperDomains), '?'));
            $sql .= " AND UPPER(LTRIM(RTRIM(sys.Resource_Domain_OR_Workgr0))) IN ({$placeholders})";
            $bindings = $upperDomains;
        }

        $rows = DB::connection('sccm')->select($sql, $bindings);

        $names = [];
        foreach ($rows as $row) {
            $name = isset($row->name) ? trim((string) $row->name) : '';
            if ($name === '') {
                continue;
            }
            $names[] = strtoupper($name);
        }

        $names = array_values(array_unique($names));
        sort($names, SORT_STRING);

        return $names;
    }
}
