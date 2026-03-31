@extends('layouts.main')

@section('title', 'Edit Hasil Skrining')

@section('content')
<section class="px-4 sm:px-4 lg:px-6 py-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Edit Hasil Skrining</h2>

    <div class="bg-white border border-[#00000033] rounded-xl shadow-sm p-6">

        <form action="{{ url('hasil-skrining/' . $data->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Tanggal Skrining --}}
            <div>
                <label class="block font-semibold mb-1 text-sm">Tanggal Skrining</label>
                <input type="date"
                       name="tanggal_skrining"
                       value="{{ $data->unit_rumah[0]->tanggal_skrining_kk ?? '' }}"
                       class="w-full border border-[#00000033] rounded-lg px-3 py-2 text-sm focus:ring-[#61359C]">
            </div>

            {{-- Kelurahan --}}
            <div>
                <label class="block font-semibold mb-1 text-sm">Kelurahan</label>
                <input type="text"
                       name="kelurahan"
                       value="{{ $data->unit_rumah[0]->kelurahan ?? '' }}"
                       class="w-full border border-[#00000033] rounded-lg px-3 py-2 text-sm focus:ring-[#61359C]">
            </div>

            {{-- Posyandu --}}
            <div>
                <label class="block font-semibold mb-1 text-sm">Posyandu</label>
                <input type="text"
                       name="posyandu"
                       value="{{ $data->unit_rumah[0]->posyandu ?? '' }}"
                       class="w-full border border-[#00000033] rounded-lg px-3 py-2 text-sm focus:ring-[#61359C]">
            </div>

            {{-- Nama Kader --}}
            <div>
                <label class="block font-semibold mb-1 text-sm">Nama Kader</label>
                <input type="text"
                       name="nama_kader"
                       value="{{ $data->nama_kader }}"
                       class="w-full border border-[#00000033] rounded-lg px-3 py-2 text-sm focus:ring-[#61359C]">
            </div>

            {{-- Alamat Unit Rumah --}}
            <div>
                <label class="block font-semibold mb-1 text-sm">Alamat Unit</label>
                <textarea name="alamat_unit"
                          rows="3"
                          class="w-full border border-[#00000033] rounded-lg px-3 py-2 text-sm focus:ring-[#61359C]">{{ $data->unit_rumah[0]->alamat_unit ?? '' }}</textarea>
            </div>

            {{-- Tombol --}}
            <div class="flex justify-between mt-6">
                <a href="{{ url('hasil-skrining') }}"
                   class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-sm">
                    Kembali
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-[#61359C] text-white rounded-lg text-sm hover:bg-[#61359C]/80">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</section>
@endsection