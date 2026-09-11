<x-app-layout>
  <div class="ph">
    <div>
      <h1 class="pt">Target & KPI CS</h1>
      <p class="ps">Atur target individual berdasarkan pengalaman, kapasitas database, aktivitas follow-up, conversion, dan retensi.</p>
    </div>
  </div>

  <div class="readonly">
    🎯 Benchmark: <b>Tier 1 Rp1 jt/hari</b> (±1 tahun) · <b>Tier 2 Rp2 jt/hari</b> (2–3 tahun) · <b>Tier 3 Rp3,5 jt/hari</b> (≥3 tahun). Rentang pengelolaan database ideal <b>15.000–30.000 kontak/CS</b>. Target individual tetap dapat dioverride Master Admin.
  </div>

  <form action="{{ route('kpi-targets.update-all') }}" method="POST" style="margin-top:16px">
    @csrf
    <div class="tw">
      <table>
        <thead>
          <tr>
            <th>CS</th>
            <th>Mulai Kerja</th>
            <th>Tier</th>
            <th>Database</th>
            <th>Target Harian</th>
            <th>Follow-up/Hari</th>
            <th>Conversion</th>
            <th>Retensi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($admins as $cs)
          @php
            $target = $cs->kpiTarget;
          @endphp
          <tr>
            <td>
              <b>{{ $cs->name }}</b>
              <div class="small muted">{{ $cs->username }}</div>
            </td>
            <td>
              <input class="kpi-edit" type="date" name="targets[{{ $cs->id }}][start_date]" value="{{ $target?->start_date?->format('Y-m-d') }}">
            </td>
            <td>
              <select class="kpi-edit" name="targets[{{ $cs->id }}][tier]">
                <option value="1" {{ ($target?->tier ?? 1) == 1 ? 'selected' : '' }}>Tier 1</option>
                <option value="2" {{ ($target?->tier ?? 1) == 2 ? 'selected' : '' }}>Tier 2</option>
                <option value="3" {{ ($target?->tier ?? 1) == 3 ? 'selected' : '' }}>Tier 3</option>
              </select>
            </td>
            <td><b>{{ $cs->contacts->count() }}</b></td>
            <td>
              <input class="kpi-edit" type="number" name="targets[{{ $cs->id }}][daily_target]" value="{{ $target?->daily_target ?? 1000000 }}" style="width:140px;font-weight:bold;color:var(--navy)">
            </td>
            <td>
              <input class="kpi-edit" type="number" name="targets[{{ $cs->id }}][followup_target]" value="{{ $target?->followup_target ?? 150 }}" style="width:80px">
            </td>
            <td>
              <input class="kpi-edit" type="number" step="0.1" name="targets[{{ $cs->id }}][conversion_target]" value="{{ $target?->conversion_target ?? 8.0 }}" style="width:70px"> %
            </td>
            <td>
              <input class="kpi-edit" type="number" step="0.1" name="targets[{{ $cs->id }}][retention_target]" value="{{ $target?->retention_target ?? 60.0 }}" style="width:70px"> %
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div style="margin-top:16px;display:flex;justify-content:flex-end">
      <button type="submit" class="btn btn-p">Simpan Target</button>
    </div>
  </form>
</x-app-layout>
