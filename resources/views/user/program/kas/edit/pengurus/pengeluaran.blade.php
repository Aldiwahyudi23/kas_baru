 @extends('user.layout.app')

 @section('content')
 <div class=" col-12">
     <!-- select2bs4 EXAMPLE -->
     <div class="card card-default ">
         <div class="card-header">
             <h3 class="card-title">Edit data dengan benar</h3>

             <div class="card-tools">
                 <button type="button" class="btn btn-tool" data-card-widget="collapse">
                     <i class="fas fa-plus"></i>
                 </button>
                 <button type="button" class="btn btn-tool" data-card-widget="remove">
                     <i class="fas fa-times"></i>
                 </button>
             </div>
         </div>
         <!-- /.card-header -->
         <div class="card-body">
             <form action="{{ route('pengeluaran.update',Crypt::encrypt($pengeluaran->id)) }}" method="POST"
                 enctype="multipart/form-data" id="adminForm">
                 @method('PATCH')
                 {{csrf_field()}}

                 <div class="form-group">
                     <label for="anggaran_id">Data Anggaran <span class="text-danger">*</span></label>
                     <select class="form-control @error('anggaran_id') is-invalid @enderror" name="anggaran_id"
                         wire:model="selectedAnggaran">
                         <option value="">--Pilih Anggaran--</option>
                         @foreach ($anggaran as $data)
                         <option value="{{ $data->id }}"
                             {{old('anggaran_id',$pengeluaran->anggaran_id) == $data->id ? 'selected' : '' }}
                             @if($data->is_active == 0 ||
                             $data->name === "Dana Pinjam")
                             disabled
                             @endif
                             >
                             {{ $data->name }}
                             @if($data->is_active == 0)
                             (Access Program Tidak Aktif)
                             @endif
                             @if ($anggaranStatus[$data->id])
                             ({{ $anggaranStatus[$data->id] }})
                             @endif
                         </option>
                         @endforeach
                     </select>
                 </div>

                 <!-- Jumlah Pembayaran -->
                 <div class="form-group">
                     <label for="amount">Jumlah Anggaran <span class="text-danger">*</span></label>
                     <input type="text" name="amount_display" id="amount_display"
                         value="{{ old('amount',$pengeluaran->amount) ? number_format(old('amount',$pengeluaran->amount), 2, ',', '.') : '' }}"
                         class="form-control col-12 @error('amount') is-invalid @enderror"
                         placeholder="Masukkan nominal yang diajukan" oninput="formatIndonesian(this)">
                     <input type="hidden" name="amount" id="amount" value="{{ old('amount',$pengeluaran->amount) }}">
                 </div>

                 <div class="form-group">
                     <label for="description" class="col-sm-12 col-form-label">Keterangan
                         <span class="text-danger">*</span></label>
                     <textarea
                         class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror"
                         name="description"
                         id="description">{{ old('description',$pengeluaran->description) }}</textarea>
                 </div>
                 <!-- Button Submit -->
                 <button type="submit" class="btn btn-success" id="submitBtns">Update Data</button>
             </form>
             <br>
             <!-- /.card-body -->
             <div class="card-footer">
                 Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                     <br>- Bertanda bintang Merah wajib di isi.
                     <br>- Jangan Pilih yang ada keterangannya
                 </p>
             </div>

         </div>
     </div>
     <!-- /.card -->
 </div>
 @endsection

 @section('script')

 <script>
     // Function to toggle the visibility of the transfer receipt input based on selected payment method
     function toggleTransferReceipt() {
         var paymentMethod = document.getElementById('payment_method').value;
         var transferReceipt = document.getElementById('transfer_receipt');
         transferReceipt.style.display = (paymentMethod === 'transfer') ? 'block' : 'none';
     }
 </script>
 @endsection