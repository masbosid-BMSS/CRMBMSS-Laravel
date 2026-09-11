<x-app-layout>
  <div class="ph" x-data="{ userModal: false }">
    <div>
      <h1 class="pt">Admin & User</h1>
      <p class="ps">Hanya Master Admin yang dapat membuat akun, reset password, dan mengatur ownership data.</p>
    </div>
    <div>
      <button type="button" @click="userModal = true" class="btn btn-p">＋ Tambah Admin</button>
    </div>

    <!-- Modal Tambah User -->
    <div class="modalbg" x-show="userModal" x-cloak style="display:flex">
      <div class="modal" @click.outside="userModal = false">
        <div class="sh">
          <h3>Buat Admin Baru</h3>
          <button type="button" class="icon" @click="userModal = false">×</button>
        </div>
        <form action="{{ route('users.store') }}" method="POST">
          @csrf
          <div class="field">
            <label>Nama Lengkap</label>
            <input class="input" name="name" required placeholder="Contoh: Dina">
          </div>
          <div class="field">
            <label>Username</label>
            <input class="input" name="username" required placeholder="Contoh: dina.cs">
          </div>
          <div class="field">
            <label>Password Sementara</label>
            <input class="input" type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter">
          </div>
          <div class="q" style="margin-top:16px">
            <button type="button" @click="userModal = false" class="btn btn-s">Batal</button>
            <button type="submit" class="btn btn-p">Buat Admin</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="tw">
    <table>
      <thead>
        <tr>
          <th>Nama</th>
          <th>Username</th>
          <th>Role</th>
          <th>Status</th>
          <th>Database</th>
          <th>Last Login</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
        <tr x-data="{ transferModal: false }">
          <td>
            <div class="person">
              <div class="ava">{{ $u->initials }}</div>
              <b>{{ $u->name }}</b>
            </div>
          </td>
          <td>{{ $u->username }}</td>
          <td><span class="status {{ $u->isMaster() ? 's-master' : 's-loyal' }}">{{ $u->isMaster() ? 'Master Admin' : 'Admin / CS' }}</span></td>
          <td><span class="status s-active">Aktif</span></td>
          <td>{{ $u->contacts()->count() }}</td>
          <td>{{ $u->last_login_at?->diffForHumans() ?: '-' }}</td>
          <td>
            <div class="q">
              <form action="{{ route('users.reset-password', $u) }}" method="POST" onsubmit="return confirm('Reset password {{ $u->name }} menjadi admin12345?')">
                @csrf
                <button type="submit" class="mini p">Reset Password</button>
              </form>

              @if(! $u->isMaster())
                <button type="button" @click="transferModal = true" class="mini y">Transfer Data</button>

                <!-- Modal Transfer Data -->
                <div class="modalbg" x-show="transferModal" x-cloak style="display:flex">
                  <div class="modal" @click.outside="transferModal = false">
                    <div class="sh">
                      <h3>Transfer Ownership</h3>
                      <button type="button" class="icon" @click="transferModal = false">×</button>
                    </div>
                    <p class="ps">Pindahkan seluruh database milik <b>{{ $u->name }}</b> ke admin lain.</p>
                    <form action="{{ route('users.transfer', $u) }}" method="POST" style="margin-top:14px">
                      @csrf
                      <div class="field">
                        <label>Transfer ke</label>
                        <select class="input" name="target_user_id" required>
                          @foreach($users->where('id', '!=', $u->id)->where('role', 'admin') as $other)
                            <option value="{{ $other->id }}">{{ $other->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="q" style="margin-top:16px">
                        <button type="button" @click="transferModal = false" class="btn btn-s">Batal</button>
                        <button type="submit" class="btn btn-g">Transfer Semua Data</button>
                      </div>
                    </form>
                  </div>
                </div>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-app-layout>
