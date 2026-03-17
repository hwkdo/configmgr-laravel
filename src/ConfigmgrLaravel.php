<?php

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
}
