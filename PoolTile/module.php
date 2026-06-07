<?php

declare(strict_types=1);

class PoolTile extends IPSModuleStrict
{
    private const VARIABLE_PROPERTIES = [
        'FilterControlID',
        'FilterPressureID',
        'ElectrolysisCurrentID',
        'RedoxID',
        'PHID',
        'PHTankID',
        'SkimmerTemperatureID',
        'NozzleTemperatureID',
        'BackwashDaysID',
        'LastBackwashID',
        'SolarValveControlID',
        'SolarValvePositionID',
        'FilterPumpID',
        'ElectrolysisID',
        'AlgicideTankID',
        'SolarReturnTemperatureID',
        'CpuTemperatureID',
        'FlowMeasurementID',
        'LeftLightID',
        'LeftLightWhiteID',
        'LeftLightRgbID',
        'RightLightID',
        'RightLightWhiteID',
        'RightLightRgbID',
        'RobotID',
        'RobotRuntimePresetID',
        'RobotRuntimeRemainingID'
    ];

    public function Create(): void
    {
        parent::Create();

        foreach (self::VARIABLE_PROPERTIES as $property) {
            $this->RegisterPropertyInteger($property, 0);
        }

        $this->RegisterPropertyFloat('PHGoodMin', 7.00);
        $this->RegisterPropertyFloat('PHGoodMax', 7.40);
        $this->RegisterPropertyFloat('PHWarnMin', 6.80);
        $this->RegisterPropertyFloat('PHWarnMax', 7.60);
        $this->RegisterPropertyInteger('RedoxGoodMin', 700);
        $this->RegisterPropertyInteger('RedoxWarnMin', 650);
        $this->RegisterPropertyFloat('PressureWarnMax', 600);
        $this->RegisterPropertyFloat('PressureCriticalMax', 900);
        $this->RegisterPropertyFloat('TankWarnMin', 25);
        $this->RegisterPropertyFloat('TankCriticalMin', 10);
        $this->RegisterPropertyInteger('BackwashWarnDays', 7);
        $this->RegisterPropertyInteger('BackwashCriticalDays', 14);
        $this->RegisterPropertyBoolean('AllowActions', false);
    }

    public function ApplyChanges(): void
    {
        parent::ApplyChanges();

        $this->SetVisualizationType(1);

        foreach (self::VARIABLE_PROPERTIES as $property) {
            $variableID = $this->ReadPropertyInteger($property);
            if ($this->isValidObjectID($variableID)) {
                $this->RegisterMessage($variableID, VM_UPDATE);
            }
        }
    }

    public function MessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data): void
    {
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);

        if ($Message === VM_UPDATE) {
            $this->UpdateVisualizationValue(json_encode($this->buildPayload(), JSON_UNESCAPED_UNICODE));
        }
    }

    public function GetVisualizationTile(): string
    {
        $html = file_get_contents(__DIR__ . '/module.html');
        if ($html === false) {
            return '';
        }

        return str_replace(
            '%%INITIAL_DATA%%',
            json_encode($this->buildPayload(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
            $html
        );
    }

    public function RequestAction(string $Ident, mixed $Value): void
    {
        if (!$this->ReadPropertyBoolean('AllowActions')) {
            throw new Exception('Schaltaktionen sind in dieser Pool-Kachel deaktiviert.');
        }

        if (!str_starts_with($Ident, 'var:')) {
            throw new Exception('Unbekannte Aktion.');
        }

        $variableID = (int) substr($Ident, 4);
        if (!$this->isValidObjectID($variableID)) {
            throw new Exception('Ungueltige Variable.');
        }

        RequestAction($variableID, $Value);
    }

    private function buildPayload(): array
    {
        $compact = [
            $this->metric('FilterControlID', 'Filtersteuerung', 'status', 'fan'),
            $this->metric('FilterPressureID', 'Filterdruck', 'pressure', 'gauge'),
            $this->metric('ElectrolysisCurrentID', 'Elektrolyse Strom', 'neutral', 'bolt'),
            $this->metric('RedoxID', 'Redox', 'redox', 'flask'),
            $this->metric('PHID', 'pH', 'ph', 'flask'),
            $this->metric('PHTankID', 'pH Tank', 'tank', 'droplet'),
            $this->metric('SkimmerTemperatureID', 'Skimmer/Bodenablauf', 'temperature', 'temperature'),
            $this->metric('NozzleTemperatureID', 'Einlaufduesen', 'temperature', 'temperature'),
            $this->metric('BackwashDaysID', 'Tage seit Rueckspuelung', 'backwash', 'clock'),
            $this->metric('LastBackwashID', 'Letztes Rueckspuelen', 'neutral', 'clock')
        ];

        $detail = [
            $this->metric('SolarValveControlID', 'Solarventil Steuerung', 'status', 'sun'),
            $this->metric('SolarValvePositionID', 'Solarventil Position', 'neutral', 'sun'),
            $this->metric('FilterPumpID', 'Filterpumpe', 'status', 'fan'),
            $this->metric('FilterPressureID', 'Filterdruck', 'pressure', 'gauge'),
            $this->metric('ElectrolysisID', 'Elektrolyse', 'status', 'bolt'),
            $this->metric('ElectrolysisCurrentID', 'Elektrolyse Strom', 'neutral', 'bolt'),
            $this->metric('RedoxID', 'Redox Sonde', 'redox', 'flask'),
            $this->metric('PHID', 'pH Sonde', 'ph', 'flask'),
            $this->metric('PHTankID', 'pH Tankinhalt', 'tank', 'droplet'),
            $this->metric('AlgicideTankID', 'Algizid Tankinhalt', 'tank', 'droplet'),
            $this->metric('SkimmerTemperatureID', 'Skimmer / Bodenablauf', 'temperature', 'temperature'),
            $this->metric('NozzleTemperatureID', 'Einlaufduesen', 'temperature', 'temperature'),
            $this->metric('SolarReturnTemperatureID', 'Solarruecklauf', 'temperature', 'temperature'),
            $this->metric('CpuTemperatureID', 'CPU Temp', 'neutral', 'microchip'),
            $this->metric('FlowMeasurementID', 'Durchfluss Messstrecke', 'neutral', 'water'),
            $this->metric('LastBackwashID', 'Letztes Rueckspuelen am', 'neutral', 'clock'),
            $this->metric('BackwashDaysID', 'Tage seit Rueckspuelung', 'backwash', 'clock')
        ];

        $lights = [
            $this->metric('LeftLightID', 'Scheinwerfer links', 'status', 'lightbulb', true),
            $this->metric('LeftLightWhiteID', 'Scheinwerfer links weiss', 'neutral', 'lightbulb', true),
            $this->metric('LeftLightRgbID', 'Scheinwerfer links RGB', 'color', 'palette', true),
            $this->metric('RightLightID', 'Scheinwerfer rechts', 'status', 'lightbulb', true),
            $this->metric('RightLightWhiteID', 'Scheinwerfer rechts weiss', 'neutral', 'lightbulb', true),
            $this->metric('RightLightRgbID', 'Scheinwerfer rechts RGB', 'color', 'palette', true)
        ];

        $robot = [
            $this->metric('RobotID', 'Poolroboter', 'status', 'robot', true),
            $this->metric('RobotRuntimePresetID', 'Laufzeit Vorgabe', 'neutral', 'clock', true),
            $this->metric('RobotRuntimeRemainingID', 'Laufzeit verbleibend', 'neutral', 'hourglass')
        ];

        return [
            'title' => 'Pool',
            'summary' => $this->buildSummary(),
            'allowActions' => $this->ReadPropertyBoolean('AllowActions'),
            'compact' => array_values(array_filter($compact)),
            'detail' => array_values(array_filter($detail)),
            'lights' => array_values(array_filter($lights)),
            'robot' => array_values(array_filter($robot))
        ];
    }

    private function metric(string $property, string $label, string $kind, string $icon, bool $actionable = false): ?array
    {
        $variableID = $this->ReadPropertyInteger($property);
        if (!$this->isValidObjectID($variableID)) {
            return null;
        }

        $value = GetValue($variableID);
        $formatted = GetValueFormatted($variableID);

        return [
            'id' => $variableID,
            'label' => $label,
            'value' => $value,
            'formatted' => $formatted,
            'state' => $this->evaluateState($kind, $value),
            'kind' => $kind,
            'icon' => $icon,
            'actionable' => $actionable
        ];
    }

    private function buildSummary(): array
    {
        $temperature = $this->metric('SkimmerTemperatureID', 'Wasser', 'temperature', 'temperature');
        $ph = $this->metric('PHID', 'pH', 'ph', 'flask');
        $redox = $this->metric('RedoxID', 'Redox', 'redox', 'flask');
        $pressure = $this->metric('FilterPressureID', 'Filterdruck', 'pressure', 'gauge');

        $states = array_filter([
            $ph['state'] ?? null,
            $redox['state'] ?? null,
            $pressure['state'] ?? null
        ]);

        $overall = 'good';
        if (in_array('critical', $states, true)) {
            $overall = 'critical';
        } elseif (in_array('warning', $states, true)) {
            $overall = 'warning';
        }

        return [
            'temperature' => $temperature['formatted'] ?? '',
            'headline' => $this->stateLabel($overall),
            'state' => $overall
        ];
    }

    private function evaluateState(string $kind, mixed $value): string
    {
        $numeric = is_numeric($value) ? (float) $value : null;

        if ($kind === 'ph' && $numeric !== null) {
            if ($numeric >= $this->ReadPropertyFloat('PHGoodMin') && $numeric <= $this->ReadPropertyFloat('PHGoodMax')) {
                return 'good';
            }
            if ($numeric >= $this->ReadPropertyFloat('PHWarnMin') && $numeric <= $this->ReadPropertyFloat('PHWarnMax')) {
                return 'warning';
            }
            return 'critical';
        }

        if ($kind === 'redox' && $numeric !== null) {
            if ($numeric >= $this->ReadPropertyInteger('RedoxGoodMin')) {
                return 'good';
            }
            if ($numeric >= $this->ReadPropertyInteger('RedoxWarnMin')) {
                return 'warning';
            }
            return 'critical';
        }

        if ($kind === 'pressure' && $numeric !== null) {
            if ($numeric >= $this->ReadPropertyFloat('PressureCriticalMax')) {
                return 'critical';
            }
            if ($numeric >= $this->ReadPropertyFloat('PressureWarnMax')) {
                return 'warning';
            }
            return 'good';
        }

        if ($kind === 'tank' && $numeric !== null) {
            if ($numeric <= $this->ReadPropertyFloat('TankCriticalMin')) {
                return 'critical';
            }
            if ($numeric <= $this->ReadPropertyFloat('TankWarnMin')) {
                return 'warning';
            }
            return 'good';
        }

        if ($kind === 'backwash' && $numeric !== null) {
            if ($numeric >= $this->ReadPropertyInteger('BackwashCriticalDays')) {
                return 'critical';
            }
            if ($numeric >= $this->ReadPropertyInteger('BackwashWarnDays')) {
                return 'warning';
            }
            return 'good';
        }

        return 'neutral';
    }

    private function stateLabel(string $state): string
    {
        return match ($state) {
            'good' => 'OK',
            'warning' => 'Achtung',
            'critical' => 'Kritisch',
            default => 'Status'
        };
    }

    private function isValidObjectID(int $objectID): bool
    {
        return $objectID > 0 && IPS_ObjectExists($objectID);
    }
}
