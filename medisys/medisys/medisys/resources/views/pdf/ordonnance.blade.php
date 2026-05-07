<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a2e; margin: 0; }
  .header { background: linear-gradient(135deg, #0f3460, #16213e); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; }
  .clinic-name { font-size: 22px; font-weight: bold; letter-spacing: 1px; }
  .clinic-sub  { font-size: 11px; opacity: 0.8; margin-top: 4px; }
  .badge { background: rgba(255,255,255,0.15); border-radius: 20px; padding: 6px 16px; font-size: 13px; font-weight: bold; }
  .content { padding: 20px 30px; }
  .section { margin-bottom: 18px; }
  .section-title { font-size: 11px; text-transform: uppercase; color: #0f3460; letter-spacing: 1.5px; border-bottom: 2px solid #0f3460; padding-bottom: 4px; margin-bottom: 10px; }
  .info-row { display: flex; margin-bottom: 5px; }
  .info-label { color: #666; width: 120px; flex-shrink: 0; }
  .info-value { font-weight: 600; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #0f3460; color: white; padding: 8px 10px; text-align: left; font-size: 11px; }
  td { padding: 8px 10px; border-bottom: 1px solid #e8ecf0; font-size: 11px; }
  tr:nth-child(even) td { background: #f7f9fc; }
  .instructions-box { background: #f0f4ff; border-left: 4px solid #0f3460; padding: 10px 14px; border-radius: 4px; }
  .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 15px; display: flex; justify-content: space-between; }
  .signature-box { text-align: center; }
  .signature-line { border-top: 1px solid #333; width: 180px; margin: 40px auto 5px; }
  .stamp { width: 80px; height: 80px; border: 2px solid #0f3460; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-align: center; font-size: 9px; color: #0f3460; padding: 5px; }
  .valid-label { background: #e8f5e9; color: #2e7d32; border-radius: 4px; padding: 2px 8px; font-size: 10px; font-weight: bold; }
</style>
</head>
<body>

<div class="header">
  <div>
    <div class="clinic-name">🏥 MediSys</div>
    <div class="clinic-sub">Système de Gestion Hospitalière</div>
  </div>
  <div style="text-align:right;">
    <div class="badge">
      @if($ordonnance->type === 'laboratory')
        PRESCRIPTION D'ANALYSES (LABORATOIRE)
      @elseif($ordonnance->type === 'nurse')
        PRESCRIPTION DE SOINS (INFIRMIER)
      @else
        ORDONNANCE MÉDICALE
      @endif
    </div>
    <div style="font-size:10px;opacity:0.7;margin-top:5px;">N° ORD-{{ str_pad($ordonnance->id, 6, '0', STR_PAD_LEFT) }}</div>
  </div>
</div>

<div class="content">

  <div style="display:flex;gap:20px;">
    <div class="section" style="flex:1;">
      <div class="section-title">Informations Patient</div>
      <div class="info-row"><span class="info-label">Nom :</span><span class="info-value">{{ $ordonnance->patient->name }}</span></div>
      <div class="info-row"><span class="info-label">Âge :</span><span class="info-value">{{ $ordonnance->patient->age }} ans</span></div>
      <div class="info-row"><span class="info-label">Sexe :</span><span class="info-value">{{ ucfirst($ordonnance->patient->gender) }}</span></div>
      @if($ordonnance->patient->blood_type)
      <div class="info-row"><span class="info-label">Groupe :</span><span class="info-value">{{ $ordonnance->patient->blood_type }}</span></div>
      @endif
    </div>
    <div class="section" style="flex:1;">
      <div class="section-title">Médecin Prescripteur</div>
      <div class="info-row"><span class="info-label">Nom :</span><span class="info-value">{{ $ordonnance->doctor->user->name }}</span></div>
      <div class="info-row"><span class="info-label">Spécialité :</span><span class="info-value">{{ $ordonnance->doctor->specialty }}</span></div>
      @if($ordonnance->doctor->license_number)
      <div class="info-row"><span class="info-label">N° Ordre :</span><span class="info-value">{{ $ordonnance->doctor->license_number }}</span></div>
      @endif
      <div class="info-row"><span class="info-label">Date :</span><span class="info-value">{{ $ordonnance->issued_date->format('d/m/Y') }}</span></div>
      @if($ordonnance->valid_until)
      <div class="info-row"><span class="info-label">Valide jusqu'au :</span><span class="valid-label">{{ $ordonnance->valid_until->format('d/m/Y') }}</span></div>
      @endif
    </div>
  </div>

  <div class="section">
    <div class="section-title">
      @if($ordonnance->type === 'laboratory')
        Analyses Demandées
      @elseif($ordonnance->type === 'nurse')
        Soins & Procédures
      @else
        Médicaments Prescrits
      @endif
    </div>
    <table>
      <thead>
        <tr>
          @if($ordonnance->type === 'laboratory')
            <th>Nom de l'Analyse</th>
          @elseif($ordonnance->type === 'nurse')
            <th>Nom du Soin / Procédure</th>
          @else
            <th>Médicament</th>
            <th>Dosage</th>
            <th>Fréquence</th>
            <th>Durée</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @foreach($ordonnance->medications as $med)
        <tr>
          <td><strong>{{ $med['name'] }}</strong></td>
          @if($ordonnance->type !== 'laboratory' && $ordonnance->type !== 'nurse')
            <td>{{ $med['dosage'] ?? '' }}</td>
            <td>{{ $med['frequency'] ?? '' }}</td>
            <td>{{ $med['duration'] ?? '' }}</td>
          @endif
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  @if($ordonnance->instructions)
  <div class="section">
    <div class="section-title">Instructions & Recommandations</div>
    <div class="instructions-box">{{ $ordonnance->instructions }}</div>
  </div>
  @endif

  <div class="footer">
    <div style="font-size:10px;color:#888;">
      <div>Généré par MediSys le {{ now()->format('d/m/Y à H:i') }}</div>
      <div style="margin-top:3px;">Ce document est confidentiel — Usage médical uniquement</div>
    </div>
    <div class="signature-box">
      <div class="signature-line"></div>
      <div style="font-size:10px;color:#555;">Signature & Cachet du Médecin</div>
    </div>
  </div>

</div>
</body>
</html>
