@extends('layouts.dashboard')

@section('title','Edit Pesanan')

@section('content')

<div class="card-custom">

    <h3 class="mb-4">

        Edit Pesanan BBM

    </h3>

    <form action="{{ route('admin.pesanan.update',$pesanan->id) }}"
        method="POST">

        @csrf
        @method('PUT')

        <div class="mb-3">

            <label class="form-label">

                Mitra Kerja

            </label>

            <div class="mb-3">
                <label class="form-label">Mitra</label>

                <input
                    type="text"
                    class="form-control"
                    value="{{ $pesanan->mitra->nama_perusahaan }}"
                    readonly>
            </div>

        </div>

        <div class="mb-3">

            <label class="form-label">

                Tanggal

            </label>

            <input
                type="date"
                name="tanggal"
                class="form-control"
                value="{{ $pesanan->tanggal }}">

        </div>

        <div class="mb-3">

            <label class="form-label">

                Volume BBM

            </label>

            <input
                type="number"
                name="jumlah_bbm"
                class="form-control"
                value="{{ $pesanan->jumlah_bbm }}">

        </div>

        <div class="mb-4">

            <label class="form-label">

                Status

            </label>

            <select
                name="status"
                class="form-select">

                <option value="pending"
                    {{ $pesanan->status=='pending' ? 'selected':'' }}>

                    Pending

                </option>

                <option value="diproses"
                    {{ $pesanan->status=='diproses' ? 'selected':'' }}>

                    Diproses

                </option>

                <option value="selesai"
                    {{ $pesanan->status=='selesai' ? 'selected':'' }}>

                    Selesai

                </option>

            </select>

        </div>

        <button class="btn btn-primary">

            <i class="fa-solid fa-floppy-disk"></i>

            Simpan Perubahan

        </button>

        <a href="{{ route('admin.pesanan.index') }}"
            class="btn btn-secondary">

            Kembali

        </a>

    </form>

</div>

@endsection