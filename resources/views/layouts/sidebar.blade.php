<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('welcome') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Shipora</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ $menuDashboard ?? '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    @php
        $hak_akses = auth()->user()->hak_akses?->nama_hak_akses;
    @endphp

    @if (in_array($hak_akses, ['Sekretaris', 'Admin', 'HOA', 'Supervisor', 'PIC']))
        <!-- Heading -->
        <div class="sidebar-heading">
            MENU {{ strtoupper($hak_akses) }}
        </div>

        {{-- MENU: SEKRETARIS --}}
        @if ($hak_akses === 'Sekretaris')
            <li class="nav-item {{ $menuPort ?? '' }}">
                <a class="nav-link" href="{{ route('port') }}">
                    <i class="fas fa-anchor fa-fw"></i>
                    <span>Lokasi Port</span>
                </a>
            </li>

            <li class="nav-item {{ $menuUser ?? '' }}">
                <a class="nav-link" href="{{ route('user') }}">
                    <i class="fas fa-user fa-fw"></i>
                    <span>Data User</span>
                </a>
            </li>
        @endif

        {{-- PENGAJUAN SURAT --}}
        @if ($hak_akses === 'Admin' || $hak_akses === 'Sekretaris')
            <li class="nav-item">
                <a class="nav-link {{ ($menuPengajuan ?? '') == 'show' ? '' : 'collapsed' }}" 
                    href="#" 
                    data-toggle="collapse" 
                    data-target="#collapsePengajuan" 
                    aria-expanded="{{ ($menuPengajuan ?? '') == 'show' ? 'true' : 'false' }}" 
                    aria-controls="collapsePengajuan">
                    <i class="fas fa-file-alt fa-fw"></i>
                    <span>Pengajuan Surat</span>
                </a>

                <div id="collapsePengajuan" class="collapse {{ ($menuPengajuan ?? '') == 'show' ? 'show' : '' }}">
                    <div class="collapse-inner rounded bg-white py-2">

                        {{-- Menu Tanda Tangan hanya untuk Sekretaris --}}
                        @if ($hak_akses === 'Sekretaris')
                            <a class="collapse-item {{ $menuTtd ?? '' }}" href="{{ route('ttd') }}">
                                <i class="fas fa-signature fa-fw mr-2"></i> Tanda Tangan
                            </a>
                        @endif

                        {{-- Menu Data Pegawai dan Surat Cuti tetap untuk Admin & Sekretaris --}}
                        <a class="collapse-item {{ $menuPegawai ?? '' }}" href="{{ route('pegawai') }}">
                            <i class="fas fa-user-plus fa-fw mr-2"></i> Data Pegawai
                        </a>

                        <a class="collapse-item {{ $menuCuti ?? '' }}" href="{{ route('cuti') }}">
                            <i class="fas fa-file-signature fa-fw mr-2"></i> Surat Cuti
                        </a>

                        {{-- Menu TIjin Olah Gerak hanya untuk Admin --}}
                        @if ($hak_akses === 'Admin')
                            <a class="collapse-item {{ $menuIog ?? '' }}" href="{{ route('iog') }}">
                                <i class="fas fa-file-contract fa-fw mr-2"></i> Ijin Olah Gerak
                            </a>
                        @endif

                    </div>
                </div>
            </li>

        @elseif (in_array($hak_akses, ['HOA', 'Supervisor']))
            <li class="nav-item">
                <a class="nav-link {{ ($menuPengajuan ?? '') == 'show' ? '' : 'collapsed' }}" 
                    href="#" 
                    data-toggle="collapse" 
                    data-target="#collapsePengajuan" 
                    aria-expanded="{{ ($menuPengajuan ?? '') == 'show' ? 'true' : 'false' }}" 
                    aria-controls="collapsePengajuan">
                    <i class="fas fa-file-alt fa-fw"></i>
                    <span>Pengajuan Surat</span>
                </a>

                <div id="collapsePengajuan" class="collapse {{ ($menuPengajuan ?? '') == 'show' ? 'show' : '' }}">
                    <div class="collapse-inner rounded bg-white py-2">
                        <a class="collapse-item {{ $menuPegawai ?? '' }}" href="{{ route('pegawai') }}">
                            <i class="fas fa-user-plus fa-fw mr-2"></i> Data Pegawai
                        </a>
                        <a class="collapse-item {{ $menuCuti ?? '' }}" href="{{ route('cuti') }}">
                            <i class="fas fa-file-signature fa-fw mr-2"></i> Surat Cuti
                        </a>
                    </div>
                </div>
            </li>

        @elseif ($hak_akses === 'PIC')
            <li class="nav-item">
                <a class="nav-link {{ $menuCuti ?? '' }}" href="{{ route('cuti') }}">
                    <i class="fas fa-file-signature fa-fw mr-2"></i>
                    <span>Surat Cuti</span>
                </a>
            </li>
        @endif

        {{-- PERGERAKAN KAPAL --}}
        @if (in_array($hak_akses, ['Admin', 'Sekretaris', 'HOA', 'Supervisor']))
            <li class="nav-item {{ $menuPergerakan ?? '' }}">
                <a class="nav-link" href="{{ route('pergerakan') }}">
                    <i class="fas fa-ship fa-fw"></i>
                    <span>Pergerakan Kapal</span>
                </a>
            </li>
        @endif

        {{-- LAPORAN --}}
        @if (in_array($hak_akses, ['Admin', 'Sekretaris', 'HOA', 'Supervisor']))
            <li class="nav-item {{ $menuLaporan ?? '' }}">
                <a class="nav-link" href="{{ route('laporan') }}">
                    <i class="fas fa-tasks fa-fw"></i>
                    <span>Laporan</span>
                </a>
            </li>
        @endif
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->