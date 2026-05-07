<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
  @page { margin: 36px 48px; }

  body {
    font-family: Helvetica, Arial, sans-serif;
    font-size: 11px;
    color: #1a1a1a;
    margin: 0;
    padding: 0;
  }

  table {
    border-collapse: collapse;
  }

  /* ===== EN-TÊTE MÉDECIN ===== */
  .doctor-header {
    text-align: center;
    margin-bottom: 8px;
  }
  .doctor-name {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 4px;
  }
  .doctor-sub {
    font-size: 11px;
    color: #444;
    margin: 2px 0;
  }
  .doctor-contact {
    font-size: 11px;
    color: #444;
    margin-top: 3px;
  }

  /* ===== SÉPARATEURS ===== */
  .divider-thick {
    border: none;
    border-top: 3px solid #1a1a1a;
    margin: 14px 0 12px 0;
  }

  /* ===== INFORMATIONS PATIENT ===== */
  table.info-table {
    width: 100%;
    margin-bottom: 4px;
  }
  table.info-table td {
    padding: 4px 6px;
    vertical-align: bottom;
  }

  /* ===== TITRE ORDONNANCE ===== */
  .rx-title {
    text-align: center;
    margin: 18px 0 6px 0;
  }
  .rx-fr {
    font-size: 16px;
    font-weight: bold;
    color: #1a1a1a;
  }

  /* ===== NUMÉRO ===== */
  .rx-num {
    text-align: right;
    font-size: 10px;
    color: #666;
    margin: 6px 0 10px 0;
    font-style: italic;
  }

  /* ===== TABLEAU MÉDICAMENTS ===== */
  table.med-table {
    width: 100%;
    margin-bottom: 22px;
  }
  table.med-table th {
    border: 1px solid #333;
    padding: 6px 8px;
    font-weight: bold;
    font-size: 10px;
    background: #f0f0f0;
    text-align: center;
  }
  table.med-table td {
    border: 1px solid #888;
    padding: 7px 8px;
    vertical-align: top;
  }
  table.med-table tr:nth-child(even) td {
    background: #fafafa;
  }

  /* ===== INSTRUCTIONS ===== */
  .instructions-block {
    font-size: 10px;
    color: #333;
    margin-bottom: 16px;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-left: 4px solid #555;
    background: #f9f9f9;
    line-height: 1.5;
  }

  /* ===== SIGNATURE ===== */
  table.sig-table {
    width: 100%;
    margin-top: 36px;
  }
  table.sig-table td {
    vertical-align: top;
    padding-top: 10px;
  }
  .sig-label {
    font-size: 11px;
    color: #555;
    font-style: italic;
    margin-bottom: 40px;
  }
  .sig-line {
    border-top: 1px solid #1a1a1a;
    width: 200px;
    margin: 0 auto 5px auto;
  }
  .sig-name {
    font-size: 10px;
    color: #666;
    font-style: italic;
  }
</style>
</head>
<body>

  {{-- ===== EN-TÊTE MÉDECIN ===== --}}
  <div class="doctor-header">
    <div class="doctor-name">Dr. {{ $ordonnance->doctor?->user?->name ?? $ordonnance->doctor?->name ?? '-' }}</div>
    <div class="doctor-sub">{{ $ordonnance->doctor?->specialty ?? 'Medecin Generaliste' }}</div>
    <div class="doctor-contact">
      Tel: {{ $ordonnance->doctor?->phone ?? '-' }} | N Ordre: {{ $ordonnance->doctor?->license_number ?? '-' }}
    </div>
  </div>

  <hr class="divider-thick">

  {{-- ===== INFORMATIONS PATIENT ===== --}}
  <table class="info-table">
    <tr>
      <td style="width:50%;">
        <span style="font-size:10px; color:#555;">Nom et Prenom:</span>
        <span style="font-weight:bold;">{{ $ordonnance->patient?->name ?? '-' }}</span>
      </td>
      <td style="width:16%;">
        <span style="font-size:10px; color:#555;">Age:</span>
        <span style="font-weight:bold;">{{ $ordonnance->patient?->age ?? '-' }} ans</span>
      </td>
      <td style="width:16%;">
        <span style="font-size:10px; color:#555;">Sexe:</span>
        <span style="font-weight:bold;">{{ ucfirst($ordonnance->patient?->gender ?? '-') }}</span>
      </td>
      <td style="width:18%; text-align:right;">
        <span style="font-size:10px; color:#555;">Date:</span>
        <span style="font-weight:bold;">{{ $ordonnance->issued_date?->format('d/m/Y') ?? '-' }}</span>
      </td>
    </tr>
  </table>

  <hr class="divider-thick">

  {{-- ===== TITRE ORDONNANCE ===== --}}
  <div class="rx-title">
    <div class="rx-fr">ORDONNANCE MEDICALE</div>
  </div>

  {{-- ===== NUMÉRO DE LA PRESCRIPTION ===== --}}
  <div class="rx-num">
    N ORD-{{ str_pad($ordonnance->id, 6, '0', STR_PAD_LEFT) }} / {{ $ordonnance->issued_date?->format('Y') ?? '-' }}
  </div>

  {{-- ===== TABLEAU DES MÉDICAMENTS ===== --}}
  <table class="med-table">
    <thead>
      <tr>
        <th style="width:5%;">N</th>
        <th style="width:28%;">MEDICAMENT</th>
        <th style="width:14%;">DOSAGE</th>
        <th style="width:22%;">FREQUENCE</th>
        <th style="width:16%;">DUREE</th>
        <th style="width:15%;">REMARQUES</th>
      </tr>
    </thead>
    <tbody>
      @forelse($ordonnance->medications as $i => $med)
      <tr>
        <td style="text-align:center; font-weight:bold;">{{ $i + 1 }}</td>
        <td>{{ $med['name'] ?? '-' }}</td>
        <td>{{ $med['dosage'] ?? '-' }}</td>
        <td>{{ $med['frequency'] ?? '-' }}</td>
        <td>{{ $med['duration'] ?? '-' }}</td>
        <td>{{ $med['notes'] ?? '' }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="6" style="text-align:center; color:#888; padding:16px;">
          Aucun medicament prescrit.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>

  {{-- ===== INSTRUCTIONS ===== --}}
  @if($ordonnance->instructions)
  <div class="instructions-block">
    <strong style="font-size:10px; display:block; margin-bottom:4px;">Instructions au patient:</strong>
    {{ $ordonnance->instructions }}
  </div>
  @endif

  {{-- ===== INFORMATIONS COMPLÉMENTAIRES ===== --}}
  @if($ordonnance->explanation)
  <div class="instructions-block" style="border-left-color:#888;">
    <strong style="font-size:10px; display:block; margin-bottom:4px;">Informations complementaires:</strong>
    {{ $ordonnance->explanation }}
  </div>
  @endif

  {{-- ===== SIGNATURE ===== --}}
  <table class="sig-table">
    <tr>
      <td style="width:60%; text-align:left; font-size:9px; color:#777;">
        Document genere le {{ now()->format('d/m/Y a H:i') }}
        <br>
        Document confidentiel - usage medical strict.
      </td>
      <td style="width:40%; text-align:center;">
        <div style="font-size:11px; color:#555; margin-bottom:40px;">Signature et Cachet du Medecin</div>
        <div style="border-top:1px solid #1a1a1a; width:200px; margin:0 auto 5px auto;"></div>
        <div style="font-size:10px; color:#666;">Dr. {{ $ordonnance->doctor?->user?->name ?? $ordonnance->doctor?->name ?? '-' }}</div>
      </td>
    </tr>
  </table>

</body>
</html>