 @extends('user.layout.app')

 @section('content')
 <div class=" col-12">
     <!-- select2bs4 EXAMPLE -->
     <div class="card card-default ">
         <div class="card-header">
             <h3 class="card-title">Edit Pengajuan Pinjaman</h3>

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
             <form action="{{ route('pinjaman.update',Crypt::encrypt($pinjaman->id)) }}" method="POST"
                 enctype="multipart/form-data" id="adminForm">
                 @method('PATCH')
                 {{csrf_field()}}
                 <!-- Jumlah Pembayaran -->
                 <div class="form-group">
                     <label for="amount">Jumlah Pembayaran <span class="text-danger">*</span></label>
                     <input type="text" name="amount_display" id="amount_display"
                         value="{{ old('amount',$pinjaman->loan_amount) ? number_format(old('amount',$pinjaman->loan_amount), 2, ',', '.') : '' }}"
                         class="form-control col-12 @error('amount') is-invalid @enderror"
                         placeholder="Masukkan nominal yang diajukan" oninput="formatIndonesian(this)">
                     <input type="hidden" name="amount" id="amount" value="{{ old('amount',$pinjaman->loan_amount) }}">
                 </div>


                 <div class="form-group">
                     <label for="description" class="col-sm-12 col-form-label">Alasan
                         <span class="text-danger">*</span></label>
                     <textarea class="form-control col-12 @error('description') is-invalid @enderror" name="description"
                         id="description">{{ old('description',$pinjaman->description) }}</textarea>
                 </div>
                 <input type="hidden" name="data_warga_id" value="{{$pinjaman->data_warga_id}}">
                 <!-- Button Submit -->
                 <button type="submit" class="btn btn-success" id="submitBtns">Update Pengajuan</button>
             </form>
             <br>
             <!-- /.card-body -->
             <div class="card-footer">
                 Catatan: <p>- Untuk edit proses Transfer atau ambil langsung hanya di keterangan.
                     <br>- Masukan di keterangan atau Alasan secara detail.
                     <br>- Cantumkan proses pengambilannya kalau Transfer cantumkan No Req.
                 </p>
             </div>

         </div>
     </div>
     <!-- /.card -->
 </div>
 @endsection

 @section('script')

 @endsection